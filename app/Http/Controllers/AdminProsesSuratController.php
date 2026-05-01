<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Models\PosisiElemenDokumen;
use App\Models\SuratBiasa;
use App\Models\User;
use App\Models\Verifikasi;
use App\Services\PreviewVerifikasiPdfGenerator;
use App\Support\UserReferenceOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminProsesSuratController extends Controller
{
    public function __construct(
        protected PreviewVerifikasiPdfGenerator $previewVerifikasiPdfGenerator
    ) {
    }

    public function show(Request $request): View|RedirectResponse
    {
        $dokumenId = $request->integer('dokumen');

        if (!$dokumenId) {
            return redirect()
                ->route('admin.pengajuan-masuk')
                ->with('error', 'Dokumen yang akan diproses belum dipilih.');
        }

        // Seluruh data pendukung dipanggil sekaligus agar halaman proses surat bisa dibuka ulang pada step mana pun.
        $dokumen = Dokumen::query()
            ->with([
                'pemohon',
                'suratBiasa',
                'posisiElemenDokumen',
                'verifikasi.verifikator',
                'dokumenFiles' => fn ($query) => $query->latest('file_id'),
            ])
            ->where('jenis_dokumen', 'SURAT_BIASA')
            ->findOrFail($dokumenId);

        $previewFile = $dokumen->dokumenFiles
            ->first(fn (DokumenFile $file) => in_array($file->file_type, ['HASIL_PEMERIKSAAN_PDF', 'PDF_REVIEW'], true));

        $existingPositions = $dokumen->posisiElemenDokumen
            ->mapWithKeys(fn (PosisiElemenDokumen $posisi) => [
                $posisi->elemen => [
                    'elemen' => $posisi->elemen,
                    'halaman' => (int) $posisi->halaman,
                    'posisi_x' => (float) $posisi->posisi_x,
                    'posisi_y' => (float) $posisi->posisi_y,
                    'lebar' => $posisi->lebar !== null ? (float) $posisi->lebar : null,
                    'tinggi' => $posisi->tinggi !== null ? (float) $posisi->tinggi : null,
                ],
            ]);

        $penandatangans = User::query()
            ->where('role', 'VERIFIKATOR')
            ->where('is_active', true)
            ->whereIn('jabatan', UserReferenceOptions::signerJabatans())
            ->orderBy('jabatan')
            ->orderBy('nama')
            ->get(['user_id', 'nama', 'jabatan', 'unit_kerja']);

        $selectedPenandatanganId = old('penanda_tangan');

        if (! $selectedPenandatanganId && $dokumen->suratBiasa?->penandatangan) {
            $selectedPenandatanganId = $penandatangans
                ->firstWhere('nama', $dokumen->suratBiasa->penandatangan)
                ?->user_id;
        }

        // Dropdown verifikator sengaja tidak memuat penandatangan final agar tahap otorisasi akhir tetap terpisah.
        $verifikators = User::query()
            ->where('role', 'VERIFIKATOR')
            ->where('is_active', true)
            ->when($selectedPenandatanganId, fn ($query) => $query->where('user_id', '!=', $selectedPenandatanganId))
            ->orderBy('nama')
            ->get(['user_id', 'nama', 'email', 'jabatan', 'unit_kerja']);

        // Jika dokumen sudah pernah disimpan, level verifikasi non-penandatangan dipetakan ulang ke slot Level 1/2/3 di form.
        $selectedVerifikators = $dokumen->verifikasi
            ->sortBy('level')
            ->reject(fn (Verifikasi $verifikasi) => (string) $verifikasi->verifikator_id === (string) $selectedPenandatanganId)
            ->values()
            ->mapWithKeys(fn (Verifikasi $verifikasi, int $index) => [$index + 1 => $verifikasi->verifikator_id]);

        return view('admin.proses-surat', [
            'dokumen' => $dokumen,
            'initialStep' => max(1, min(3, $request->integer('step', 1))),
            'previewPdfUrl' => $previewFile
                ? route('admin.proses-surat.preview-pdf', $dokumen)
                : null,
            'previewPdfName' => $previewFile?->file_name,
            'existingPositions' => $existingPositions,
            'verifikators' => $verifikators,
            // Penandatangan tetap berasal dari role verifikator, tetapi disaring ke jabatan pejabat yang berwenang.
            'penandatangans' => $penandatangans,
            'selectedPenandatanganId' => $selectedPenandatanganId,
            'selectedPenandatangan' => $penandatangans->firstWhere('user_id', $selectedPenandatanganId),
            'jenisSuratOptions' => UserReferenceOptions::jenisSuratBiasa(),
            'selectedVerifikators' => $selectedVerifikators,
        ]);
    }

    public function storeDraft(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        $dokumen->loadMissing(['suratBiasa', 'dokumenFiles']);

        $existingProcessedPdf = $dokumen->dokumenFiles
            ->first(fn (DokumenFile $file) => in_array($file->file_type, ['HASIL_PEMERIKSAAN_PDF', 'PDF_REVIEW'], true));

        // Pada tahap ini Admin/TU mengunggah PDF hasil pemeriksaan dan melengkapi metadata resmi surat.
        $validated = $request->validate([
            'hasil_pemeriksaan_pdf' => [
                $existingProcessedPdf ? 'nullable' : 'required',
                'file',
                'mimetypes:application/pdf',
                'max:10240',
            ],
            'penanda_tangan' => [
                'required',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)
                    ->whereIn('jabatan', UserReferenceOptions::signerJabatans())),
            ],
            'jenis_surat' => ['required', Rule::in(UserReferenceOptions::jenisSuratBiasa())],
            'sifat_surat' => ['required', 'string', 'max:100'],
            'nomor_surat' => ['required', 'string', 'max:100'],
            'tanggal_surat' => ['required', 'date'],
            'catatan_tambahan' => ['nullable', 'string'],
        ], [
            'hasil_pemeriksaan_pdf.required' => 'PDF hasil pemeriksaan wajib diunggah.',
            'hasil_pemeriksaan_pdf.mimetypes' => 'File hasil pemeriksaan harus berformat PDF.',
            'hasil_pemeriksaan_pdf.max' => 'Ukuran PDF maksimal 10 MB.',
        ]);

        $user = $request->user();
        $uploadedPdf = $request->file('hasil_pemeriksaan_pdf');
        $penandatangan = User::query()
            ->where('user_id', $validated['penanda_tangan'])
            ->where('role', 'VERIFIKATOR')
            ->where('is_active', true)
            ->whereIn('jabatan', UserReferenceOptions::signerJabatans())
            ->firstOrFail();

        DB::transaction(function () use ($dokumen, $validated, $user, $uploadedPdf, $penandatangan): void {
            if ($uploadedPdf) {
                // PDF hasil pemeriksaan disimpan terpisah dari DOCX draft pemohon agar riwayat file tetap utuh.
                $storedPath = $uploadedPdf->store('dokumen/pdf', 'public');

                DokumenFile::create([
                    'dokumen_id' => $dokumen->dokumen_id,
                    'file_type' => 'HASIL_PEMERIKSAAN_PDF',
                    'file_name' => $uploadedPdf->getClientOriginalName(),
                    'file_path' => $storedPath,
                    'uploaded_by' => $user->user_id,
                    'uploaded_at' => now(),
                ]);
            }

            $suratBiasa = $dokumen->suratBiasa ?? new SuratBiasa([
                'dokumen_id' => $dokumen->dokumen_id,
                'hal' => $dokumen->suratBiasa?->hal,
                'ringkasan_isi' => $dokumen->suratBiasa?->ringkasan_isi,
            ]);

            $suratBiasa->fill([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                // Nama penandatangan disimpan ke metadata surat agar bisa ditampilkan kembali pada preview/final PDF.
                'penandatangan' => $penandatangan->nama,
                'jenis_surat' => $validated['jenis_surat'],
                'sifat_surat' => $validated['sifat_surat'],
                'lampiran' => $suratBiasa->lampiran,
                'keterangan_tambahan' => $validated['catatan_tambahan'] ?? null,
                'catatan_admin' => $validated['catatan_tambahan'] ?? null,
                'unit_kerja' => $user->unit_kerja,
                'catatan' => $validated['catatan_tambahan'] ?? null,
            ]);
            $suratBiasa->save();

            $dokumen->update([
                // Setelah PDF dan metadata resmi masuk, dokumen berpindah ke status diproses sebelum masuk jalur verifikasi.
                'status_dokumen' => 'DIPROSES',
            ]);
        });

        return redirect()
            ->route('admin.proses-surat', [
                'dokumen' => $dokumen->dokumen_id,
                'step' => 2,
            ])
            ->with('status', 'PDF hasil pemeriksaan dan metadata surat berhasil disimpan.');
    }

    public function previewPdf(Dokumen $dokumen): BinaryFileResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        // Preview PDF yang dilihat admin selalu memakai file hasil pemeriksaan terbaru yang berhasil diunggah.
        $previewFile = $dokumen->dokumenFiles()
            ->whereIn('file_type', ['HASIL_PEMERIKSAAN_PDF', 'PDF_REVIEW'])
            ->latest('file_id')
            ->firstOrFail();

        return response()->file(
            Storage::disk('public')->path($previewFile->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $previewFile->file_name . '"',
            ]
        );
    }

    public function storePosisiElemen(Request $request, Dokumen $dokumen): JsonResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        // Posisi elemen disimpan dalam koordinat relatif preview supaya bisa dipakai ulang saat generate PDF.
        $validated = $request->validate([
            'elemen' => ['required', 'in:nomor_surat,tanggal_surat,tte'],
            'halaman' => ['required', 'integer', 'min:1'],
            'posisi_x' => ['required', 'numeric', 'min:0'],
            'posisi_y' => ['required', 'numeric', 'min:0'],
            'lebar' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
        ]);

        $posisi = PosisiElemenDokumen::query()->updateOrCreate(
            [
                'dokumen_id' => $dokumen->dokumen_id,
                'elemen' => $validated['elemen'],
            ],
            [
                'halaman' => $validated['halaman'],
                'posisi_x' => $validated['posisi_x'],
                'posisi_y' => $validated['posisi_y'],
                'lebar' => $validated['lebar'] ?? null,
                'tinggi' => $validated['tinggi'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Posisi elemen berhasil disimpan.',
            'data' => [
                'elemen' => $posisi->elemen,
                'halaman' => (int) $posisi->halaman,
                'posisi_x' => (float) $posisi->posisi_x,
                'posisi_y' => (float) $posisi->posisi_y,
                'lebar' => $posisi->lebar !== null ? (float) $posisi->lebar : null,
                'tinggi' => $posisi->tinggi !== null ? (float) $posisi->tinggi : null,
            ],
        ]);
    }

    public function storeVerifikasi(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        $dokumen->loadMissing([
            'suratBiasa',
            'posisiElemenDokumen',
            'dokumenFiles',
        ]);

        // Verifikator level bersifat opsional, tetapi penandatangan final dari metadata selalu wajib ikut dalam flow approval.
        $rules = [
            'verifikator_1' => [
                'nullable',
                'different:verifikator_2',
                'different:verifikator_3',
                'different:penandatangan_final',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
            'verifikator_2' => [
                'nullable',
                'different:verifikator_1',
                'different:verifikator_3',
                'different:penandatangan_final',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
            'verifikator_3' => [
                'nullable',
                'different:verifikator_1',
                'different:verifikator_2',
                'different:penandatangan_final',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
            'penandatangan_final' => [
                'required',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)
                    ->whereIn('jabatan', UserReferenceOptions::signerJabatans())),
            ],
        ];

        try {
            $validated = $request->validate($rules, [
                'penandatangan_final.required' => 'Penandatangan final wajib dipilih di metadata surat.',
                'verifikator_2.different' => 'Verifikator Level 2 tidak boleh sama dengan level lain.',
                'verifikator_3.different' => 'Verifikator Level 3 tidak boleh sama dengan level lain.',
                'verifikator_1.different' => 'Verifikator Level 1 tidak boleh sama dengan level lain atau penandatangan final.',
                'verifikator_2.different' => 'Verifikator Level 2 tidak boleh sama dengan level lain atau penandatangan final.',
                'verifikator_3.different' => 'Verifikator Level 3 tidak boleh sama dengan level lain atau penandatangan final.',
            ]);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 3,
                ])
                ->withErrors($exception->validator)
                ->withInput();
        }

        // Admin bebas memilih level verifikator yang diperlukan; penandatangan final akan ditambahkan otomatis setelahnya.
        $approvalFlow = array_values(array_filter([
            $validated['verifikator_1'] ?? null,
            $validated['verifikator_2'] ?? null,
            $validated['verifikator_3'] ?? null,
        ]));

        $finalFlow = [];

        foreach ($approvalFlow as $index => $verifikatorId) {
            $finalFlow[] = [
                'level' => $index + 1,
                'verifikator_id' => $verifikatorId,
            ];
        }

        // Penandatangan selalu menjadi tahap terakhir agar surat baru sah setelah seluruh pemeriksa sebelumnya selesai.
        $finalFlow[] = [
            'level' => count($finalFlow) + 1,
            'verifikator_id' => $validated['penandatangan_final'],
        ];

        $sourcePdf = $dokumen->dokumenFiles
            ->where('file_type', 'HASIL_PEMERIKSAAN_PDF')
            ->sortByDesc('file_id')
            ->first();

        if (! $sourcePdf) {
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 3,
                ])
                ->with('error', 'PDF hasil pemeriksaan belum tersedia. Silakan unggah PDF lebih dulu.');
        }

        if ($dokumen->posisiElemenDokumen->isEmpty()) {
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 2,
                ])
                ->with('error', 'Posisi elemen dokumen belum diatur.');
        }

        if (! $dokumen->suratBiasa?->nomor_surat || ! $dokumen->suratBiasa?->tanggal_surat) {
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 1,
                ])
                ->with('error', 'Metadata surat belum lengkap. Silakan lengkapi metadata lebih dulu.');
        }

        try {
            // Preview verifikasi dibuat lebih dulu agar verifikator langsung melihat PDF yang sudah ditempeli metadata.
            $previewPath = $this->previewVerifikasiPdfGenerator->generate($dokumen, $sourcePdf->file_path);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 3,
                ])
                ->with('error', 'Preview PDF verifikasi belum berhasil dibuat. Silakan cek file PDF dan posisi elemen.');
        }

        DB::transaction(function () use ($dokumen, $finalFlow, $previewPath, $sourcePdf, $request): void {
            Verifikasi::query()
                ->where('dokumen_id', $dokumen->dokumen_id)
                ->delete();

            // Seluruh flow verifikasi dibangun ulang agar nomor level tetap rapi mengikuti pilihan Admin/TU terbaru.
            foreach ($finalFlow as $step) {
                Verifikasi::query()->create([
                    'dokumen_id' => $dokumen->dokumen_id,
                    'level' => $step['level'],
                    'verifikator_id' => $step['verifikator_id'],
                    'status_verifikasi' => 'MENUNGGU',
                    'catatan' => null,
                    'verified_at' => null,
                ]);
            }

            $dokumen->update([
                // Status dokumen berubah ke menunggu verifikasi setelah preview dan jalur approval selesai disiapkan.
                'status_dokumen' => 'MENUNGGU_VERIFIKASI',
            ]);

            DokumenFile::query()->updateOrCreate(
                [
                    'dokumen_id' => $dokumen->dokumen_id,
                    'file_type' => 'PREVIEW_VERIFIKASI_PDF',
                ],
                [
                    'file_name' => pathinfo($sourcePdf->file_name, PATHINFO_FILENAME) . '-preview-verifikasi.pdf',
                    'file_path' => $previewPath,
                    'uploaded_by' => $request->user()->user_id,
                    'uploaded_at' => now(),
                ]
            );
        });

        return redirect()
            ->route('admin.pengajuan-masuk')
            ->with('status', 'Surat berhasil dikirim ke verifikator.');
    }
}
