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

        return view('admin.proses-surat', [
            'dokumen' => $dokumen,
            'initialStep' => max(1, min(3, $request->integer('step', 1))),
            'previewPdfUrl' => $previewFile
                ? route('admin.proses-surat.preview-pdf', $dokumen)
                : null,
            'previewPdfName' => $previewFile?->file_name,
            'existingPositions' => $existingPositions,
            'verifikators' => User::query()
                ->where('role', 'VERIFIKATOR')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get(['user_id', 'nama', 'email', 'jabatan', 'unit_kerja']),
            // Penandatangan tetap berasal dari role verifikator, tetapi disaring ke jabatan pejabat yang berwenang.
            'penandatangans' => $penandatangans,
            'selectedPenandatanganId' => $selectedPenandatanganId,
            'jenisSuratOptions' => UserReferenceOptions::jenisSuratBiasa(),
            'selectedVerifikators' => $dokumen->verifikasi
                ->keyBy('level')
                ->map(fn (Verifikasi $verifikasi) => $verifikasi->verifikator_id),
        ]);
    }

    public function storeDraft(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        $dokumen->loadMissing(['suratBiasa', 'dokumenFiles']);

        $existingProcessedPdf = $dokumen->dokumenFiles
            ->first(fn (DokumenFile $file) => in_array($file->file_type, ['HASIL_PEMERIKSAAN_PDF', 'PDF_REVIEW'], true));

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
            'tujuan' => ['required', 'string'],
            'tembusan' => ['nullable', 'string'],
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
                'penandatangan' => $penandatangan->nama,
                'jenis_surat' => $validated['jenis_surat'],
                'sifat_surat' => $validated['sifat_surat'],
                'lampiran' => $suratBiasa->lampiran,
                'kepada_tujuan' => $validated['tujuan'],
                'tembusan' => $validated['tembusan'] ?? null,
                'keterangan_tambahan' => $validated['catatan_tambahan'] ?? null,
                'catatan_admin' => $validated['catatan_tambahan'] ?? null,
                'unit_kerja' => $user->unit_kerja,
                'catatan' => $validated['catatan_tambahan'] ?? null,
            ]);
            $suratBiasa->save();

            $dokumen->update([
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

        $rules = [
            'verifikator_1' => [
                'required',
                'different:verifikator_2',
                'different:verifikator_3',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
            'verifikator_2' => [
                'nullable',
                'different:verifikator_1',
                'different:verifikator_3',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
            'verifikator_3' => [
                'nullable',
                'different:verifikator_1',
                'different:verifikator_2',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
        ];

        try {
            $validated = $request->validate($rules, [
                'verifikator_1.required' => 'Verifikator Level 1 wajib dipilih.',
                'verifikator_2.different' => 'Verifikator Level 2 tidak boleh sama dengan level lain.',
                'verifikator_3.different' => 'Verifikator Level 3 tidak boleh sama dengan level lain.',
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

        $selectedLevels = array_filter([
            1 => $validated['verifikator_1'] ?? null,
            2 => $validated['verifikator_2'] ?? null,
            3 => $validated['verifikator_3'] ?? null,
        ]);

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

        DB::transaction(function () use ($dokumen, $selectedLevels, $previewPath, $sourcePdf, $request): void {
            Verifikasi::query()
                ->where('dokumen_id', $dokumen->dokumen_id)
                ->whereNotIn('level', array_keys($selectedLevels))
                ->delete();

            // Semua level disiapkan sekaligus, tetapi halaman verifikator hanya membuka level berikutnya setelah level sebelumnya setuju.
            foreach ($selectedLevels as $level => $verifikatorId) {
                Verifikasi::query()->updateOrCreate(
                    [
                        'dokumen_id' => $dokumen->dokumen_id,
                        'level' => $level,
                    ],
                    [
                        'verifikator_id' => $verifikatorId,
                        'status_verifikasi' => 'MENUNGGU',
                        'catatan' => null,
                        'verified_at' => null,
                    ]
                );
            }

            $dokumen->update([
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
