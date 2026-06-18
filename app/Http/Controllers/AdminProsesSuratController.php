<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Models\PosisiElemenDokumen;
use App\Models\RiwayatDokumen;
use App\Models\SuratBiasa;
use App\Models\User;
use App\Models\Verifikasi;
use App\Services\PreviewSuratBiasaPdfGenerator;
use App\Support\UserReferenceOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as LaravelValidator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini mengatur wizard proses surat oleh Admin Surat.
// Alurnya: pilih pengajuan, cek PDF pemohon, lengkapi metadata, atur posisi elemen, lalu kirim ke verifikator dan penandatangan final.
class AdminProsesSuratController extends Controller
{
    // DRAFT_PDF dari Pemohon menjadi satu-satunya sumber proses Surat Biasa.
    private const PROCESS_SOURCE_PDF_TYPES = ['DRAFT_PDF'];

    // Service generator dimasukkan lewat dependency injection Laravel agar controller tidak membuat object service sendiri.
    public function __construct(
        protected PreviewSuratBiasaPdfGenerator $previewSuratBiasaPdfGenerator
    ) {
    }

    // Method ini menampilkan halaman proses surat berdasarkan dokumen yang dipilih dari pengajuan masuk.
    public function show(Request $request): View|RedirectResponse
    {
        $dokumenId = $request->integer('dokumen');

        if (!$dokumenId) {
            // Jika tidak ada dokumen di query string, Admin Surat dikembalikan ke daftar pengajuan untuk memilih ulang.
            return redirect()
                ->route('admin.pengajuan-masuk')
                ->with('error', 'Dokumen yang akan diproses belum dipilih.');
        }

        // Seluruh data pendukung dipanggil sekaligus agar halaman proses surat bisa dibuka ulang pada step mana pun.
        // Eager loading relasi membuat view bisa membaca pemohon, surat, posisi, verifikasi, dan file tanpa query berulang.
        $dokumen = Dokumen::query()
            ->with([
                'pemohon',
                'penandatangan',
                'suratBiasa',
                'posisiElemenDokumen',
                'verifikasi.verifikator',
                'dokumenFiles' => fn ($query) => $query->latest('file_id'),
            ])
            ->where('jenis_dokumen', 'SURAT_BIASA')
            ->findOrFail($dokumenId);

        if (in_array($dokumen->status_dokumen, ['PERLU_REVISI', 'DITOLAK'], true)) {
            return redirect()
                ->route('admin.semua-surat')
                ->with('error', 'Dokumen sedang menunggu perbaikan dari pemohon dan tidak bisa diproses ulang.');
        }

        // File preview dicari dari koleksi file dokumen yang sudah diload; prioritasnya DRAFT_PDF dari Pemohon.
        $previewFile = $this->resolveProcessSourcePdf($dokumen);
        $previewFileExists = $previewFile && Storage::disk('local')->exists($previewFile->file_path);

        // Jumlah halaman PDF dikirim ke Blade agar UI Admin Surat bisa memilih halaman 1, 2, dan seterusnya saat mengatur posisi.
        $previewPdfPageCount = $this->countPdfPages($previewFile);

        // Posisi elemen dipetakan per nama elemen agar frontend mudah mengisi ulang koordinat yang pernah disimpan.
        $existingPositions = $dokumen->posisiElemenDokumen
            ->mapWithKeys(fn (PosisiElemenDokumen $posisi) => [
                $posisi->elemen => [
                    'elemen' => $posisi->elemen,
                    // Data lama tetap aman: jika halaman kosong/0, Laravel menganggapnya halaman 1.
                    'halaman' => max(1, (int) ($posisi->halaman ?: 1)),
                    'posisi_x' => (float) $posisi->posisi_x,
                    'posisi_y' => (float) $posisi->posisi_y,
                    'lebar' => $posisi->lebar !== null ? (float) $posisi->lebar : null,
                    'tinggi' => $posisi->tinggi !== null ? (float) $posisi->tinggi : null,
                ],
            ]);

        // Query ini mengambil calon penandatangan final dari user aktif dengan jabatan yang berwenang menandatangani surat.
        $penandatangans = User::query()
            ->where('role', 'PENANDATANGAN')
            ->where('is_active', true)
            ->whereIn('jabatan', UserReferenceOptions::signerJabatans())
            ->orderBy('jabatan')
            ->orderBy('nama')
            ->get(['user_id', 'nama', 'jabatan', 'unit_kerja']);

        // Jika form kembali karena validasi gagal, pilihan lama diprioritaskan melalui old().
        $selectedPenandatanganId = old('penanda_tangan', $dokumen->penandatangan_id);

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
            'initialStep' => $this->resolveInitialStep($request->query('step', 1)),
            // URL preview ini mengarah ke endpoint inline DRAFT_PDF agar Blade bisa menanam PDF Pemohon langsung di halaman.
            'previewPdfUrl' => $previewFileExists
                ? route('admin.proses-surat.preview-pdf', $dokumen)
                : null,
            'previewPdfName' => $previewFile?->file_name,
            'previewPdfPageCount' => $previewPdfPageCount,
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

    private function resolveInitialStep(mixed $step): int
    {
        if (is_string($step) && strtolower($step) === 'verifikasi') {
            return 3;
        }

        return max(1, min(3, (int) $step));
    }

    // Helper ini menghitung jumlah halaman PDF dari object /Page.
    // Nilai ini hanya untuk navigasi preview Admin Surat; jika struktur PDF tidak terbaca, default 1 menjaga PDF lama tetap bisa diproses.
    private function countPdfPages(?DokumenFile $previewFile): int
    {
        if (! $previewFile || ! Storage::disk('local')->exists($previewFile->file_path)) {
            return 1;
        }

        $pdfContent = Storage::disk('local')->get($previewFile->file_path);

        preg_match_all('/\/Type\s*\/Page\b/', $pdfContent, $matches);

        return max(1, count($matches[0] ?? []));
    }

    // Helper ini memilih PDF sumber proses surat biasa dari DRAFT_PDF yang diunggah Pemohon.
    private function resolveProcessSourcePdf(Dokumen $dokumen): ?DokumenFile
    {
        if ($dokumen->relationLoaded('dokumenFiles')) {
            foreach (self::PROCESS_SOURCE_PDF_TYPES as $fileType) {
                $file = $dokumen->dokumenFiles
                    ->where('file_type', $fileType)
                    ->sortByDesc('file_id')
                    ->first();

                if ($file) {
                    return $file;
                }
            }
        }

        foreach (self::PROCESS_SOURCE_PDF_TYPES as $fileType) {
            $file = $dokumen->dokumenFiles()
                ->where('file_type', $fileType)
                ->latest('file_id')
                ->first();

            if ($file) {
                return $file;
            }
        }

        return null;
    }

    // Method ini memproses step 1 wizard Admin Surat: cek PDF pemohon dan lengkapi metadata resmi surat.
    public function storeDraft(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        // Relasi diload agar controller bisa mengecek DRAFT_PDF pemohon dan metadata surat tanpa query manual berulang.
        $dokumen->loadMissing(['suratBiasa', 'dokumenFiles']);

        if ($request->input('aksi') === 'minta_revisi') {
            return $this->returnToPemohonForRevision($request, $dokumen);
        }

        $sourcePdf = $this->resolveProcessSourcePdf($dokumen);

        if (! $sourcePdf || ! Storage::disk('local')->exists($sourcePdf->file_path)) {
            // PDF pemohon wajib ada karena Admin Surat tidak lagi mengunggah ulang PDF pada alur surat biasa baru.
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 1,
                ])
                ->with('error', 'PDF draft dari pemohon belum tersedia. Silakan minta pemohon mengajukan ulang file PDF.');
        }

        // Pada tahap ini Admin Surat hanya melengkapi metadata; file sumber tetap DRAFT_PDF yang diunggah Pemohon.
        $validated = $request->validate([
            'penanda_tangan' => [
                'required',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'PENANDATANGAN')
                    ->where('is_active', true)
                    ->whereIn('jabatan', UserReferenceOptions::signerJabatans())),
            ],
            'jenis_surat' => ['required', Rule::in(UserReferenceOptions::jenisSuratBiasa())],
            'sifat_surat' => ['required', 'string', 'max:100'],
            'nomor_surat' => ['required', 'string', 'max:100'],
            'tanggal_surat' => ['required', 'date'],
            'isi_ringkasan' => ['required', 'string'],
            'catatan_tambahan' => ['nullable', 'string'],
        ], [
            'isi_ringkasan.required' => 'Isi/Ringkasan surat wajib diisi.',
        ]);

        $user = $request->user();
        // Penandatangan divalidasi lagi lewat query agar user yang dipakai benar-benar aktif dan berjabatan penandatangan.
        $penandatangan = User::query()
            ->where('user_id', $validated['penanda_tangan'])
            ->where('role', 'PENANDATANGAN')
            ->where('is_active', true)
            ->whereIn('jabatan', UserReferenceOptions::signerJabatans())
            ->firstOrFail();

        // Transaction menjaga metadata surat dan status dokumen tersimpan konsisten sebagai satu tahap proses.
        DB::transaction(function () use ($dokumen, $validated, $user, $penandatangan): void {
            $suratBiasa = $dokumen->suratBiasa ?? new SuratBiasa([
                'dokumen_id' => $dokumen->dokumen_id,
                'hal' => $dokumen->suratBiasa?->hal,
                'ringkasan_isi' => $dokumen->suratBiasa?->ringkasan_isi,
            ]);

            $suratBiasa->fill([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                // Nama penandatangan tetap disalin untuk tampilan lama, sedangkan sumber utama relasi ada di dokumen.penandatangan_id.
                'penandatangan' => $penandatangan->nama,
                'jenis_surat' => $validated['jenis_surat'],
                'sifat_surat' => $validated['sifat_surat'],
                // Ringkasan dapat direvisi Admin Surat sebagai hasil pengecekan sebelum dokumen masuk verifikasi.
                'ringkasan_isi' => $validated['isi_ringkasan'],
                'lampiran' => $suratBiasa->lampiran,
                'keterangan_tambahan' => $validated['catatan_tambahan'] ?? null,
                'unit_kerja' => $user->unit_kerja,
                'catatan' => $validated['catatan_tambahan'] ?? null,
            ]);
            $suratBiasa->save();

            $dokumen->update([
                // Setelah metadata resmi masuk, dokumen berpindah ke status diproses sebelum masuk jalur verifikasi.
                'penandatangan_id' => $penandatangan->user_id,
                'status_dokumen' => 'DIPROSES',
            ]);
        });

        // Setelah metadata tersimpan, wizard diarahkan ke step 2 untuk mengatur posisi elemen pada PDF pemohon.
        return redirect()
            ->route('admin.proses-surat', [
                'dokumen' => $dokumen->dokumen_id,
                'step' => 2,
            ])
            ->with('status', 'Metadata surat berhasil disimpan. PDF pemohon digunakan sebagai sumber proses.');
    }

    // Method ini mengembalikan dokumen ke Pemohon dari step 1 sebelum dokumen masuk jalur verifikasi.
    private function returnToPemohonForRevision(Request $request, Dokumen $dokumen): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_revisi' => ['required', 'string'],
        ], [
            'catatan_revisi.required' => 'Catatan revisi wajib diisi sebelum mengembalikan surat ke Pemohon.',
        ]);

        $catatanRevisi = trim($validated['catatan_revisi']);
        $statusLama = $dokumen->status_dokumen;
        $actor = $request->user();

        DB::transaction(function () use ($dokumen, $catatanRevisi, $statusLama, $actor): void {
            $suratBiasa = $dokumen->suratBiasa ?? new SuratBiasa([
                'dokumen_id' => $dokumen->dokumen_id,
                'hal' => $dokumen->suratBiasa?->hal,
                'ringkasan_isi' => $dokumen->suratBiasa?->ringkasan_isi,
            ]);

            // Catatan pengembalian dari Admin Surat disimpan di surat_biasa.catatan_admin agar Pemohon melihat sumber catatan yang tepat.
            $suratBiasa->fill([
                'catatan_admin' => $catatanRevisi,
            ]);
            $suratBiasa->save();

            // Dokumen berhenti di status PERLU_REVISI sehingga tidak lanjut ke pengaturan posisi atau verifikasi.
            $dokumen->update([
                'status_dokumen' => 'PERLU_REVISI',
            ]);

            // Riwayat dicatat tanpa membuat record verifikasi karena dokumen belum masuk tahap verifikator.
            RiwayatDokumen::query()->create([
                'dokumen_id' => $dokumen->dokumen_id,
                'aksi' => 'REVISI_ADMIN_SURAT',
                'status_lama' => $statusLama,
                'status_baru' => 'PERLU_REVISI',
                'catatan' => $catatanRevisi,
                'actor_id' => $actor?->user_id,
            ]);
        });

        return redirect()
            ->route('admin.pengajuan-masuk')
            ->with('status', 'Surat dikembalikan ke Pemohon untuk revisi.');
    }

    // Method ini menampilkan PDF pemohon secara inline untuk preview di halaman Admin Surat.
    public function previewPdf(Dokumen $dokumen): BinaryFileResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        // Preview PDF yang dilihat Admin Surat memakai DRAFT_PDF Pemohon.
        $previewFile = $this->resolveProcessSourcePdf($dokumen);
        abort_unless($previewFile && Storage::disk('local')->exists($previewFile->file_path), 404);

        return response()->file(
            Storage::disk('local')->path($previewFile->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $previewFile->file_name . '"',
            ]
        );
    }

    // Method ini menerima request AJAX dari UI drag/drop posisi nomor surat, tanggal, dan QR/TTE.
    public function storePosisiElemen(Request $request, Dokumen $dokumen): JsonResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        // Posisi elemen disimpan dalam koordinat relatif preview supaya bisa dipakai ulang saat generate PDF.
        // Field halaman ikut disimpan agar PDF multi-page tahu elemen ini ditempel di halaman aktif yang diklik admin.
        // JsonResponse dipakai karena frontend hanya perlu menyimpan koordinat tanpa reload halaman.
        $validated = $request->validate([
            'elemen' => ['required', 'in:nomor_surat,tanggal_surat,tte'],
            'halaman' => ['required', 'integer', 'min:1'],
            'posisi_x' => ['required', 'numeric', 'min:0'],
            'posisi_y' => ['required', 'numeric', 'min:0'],
            'lebar' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
        ]);

        // updateOrCreate memastikan satu elemen pada satu dokumen selalu diperbarui, bukan dibuat duplikat.
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

        // Response JSON mengembalikan data posisi hasil simpan agar UI bisa sinkron dengan database.
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

    // Method ini menyusun jalur verifikasi bertingkat dan mengirim dokumen ke verifikator.
    public function storeVerifikasi(Request $request, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        // Relasi diload agar validasi bisnis bisa mengecek metadata, posisi elemen, dan file sumber PDF.
        $dokumen->loadMissing([
            'suratBiasa',
            'posisiElemenDokumen',
            'dokumenFiles',
        ]);

        if (! $dokumen->penandatangan_id) {
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 1,
                ])
                ->with('error', 'Metadata surat belum lengkap. Silakan pilih penandatangan terlebih dahulu.');
        }

        // Nilai hidden di UI hanya untuk tampilan; controller selalu memakai metadata utama dokumen sebagai penandatangan final.
        $request->merge([
            'penandatangan_final' => $dokumen->penandatangan_id,
        ]);

        // Verifikator level bersifat opsional, tetapi penandatangan final dari metadata selalu wajib ikut dalam flow approval.
        $rules = [
            'verifikator_1' => [
                'nullable',
                'integer',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
            'verifikator_2' => [
                'nullable',
                'integer',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
            'verifikator_3' => [
                'nullable',
                'integer',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'VERIFIKATOR')
                    ->where('is_active', true)),
            ],
            'penandatangan_final' => [
                'required',
                Rule::exists('users', 'user_id')->where(fn ($query) => $query
                    ->where('role', 'PENANDATANGAN')
                    ->where('is_active', true)
                    ->whereIn('jabatan', UserReferenceOptions::signerJabatans())),
            ],
        ];

        try {
            $validator = validator($request->all(), $rules, [
                'penandatangan_final.required' => 'Penandatangan final wajib dipilih di metadata surat.',
            ]);

            $this->addUniqueVerifierFlowValidation($validator, $request, [
                'verifikator_1' => 'Verifikator Level 1',
                'verifikator_2' => 'Verifikator Level 2',
                'verifikator_3' => 'Verifikator Level 3',
            ]);

            $validated = $validator->validate();
        } catch (ValidationException $exception) {
            // Jika validasi level gagal, Admin Surat dikembalikan ke step verifikasi dengan input sebelumnya tetap tersedia.
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
            'verifikator_id' => (int) $dokumen->penandatangan_id,
        ];

        // PDF sumber berasal dari DRAFT_PDF Pemohon agar Admin Surat tidak perlu upload ulang sebelum verifikasi.
        $sourcePdf = $this->resolveProcessSourcePdf($dokumen);

        if (! $sourcePdf || ! Storage::disk('local')->exists($sourcePdf->file_path)) {
            // Redirect ke step verifikasi dengan pesan error karena flow approval belum bisa dibuat tanpa PDF sumber.
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 3,
                ])
                ->with('error', 'PDF draft dari pemohon belum tersedia. Silakan cek pengajuan pemohon lebih dulu.');
        }

        if ($dokumen->posisiElemenDokumen->isEmpty()) {
            // Posisi elemen wajib karena generator perlu tahu lokasi nomor surat, tanggal, dan QR/TTE pada PDF.
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 2,
                ])
                ->with('error', 'Posisi elemen dokumen belum diatur.');
        }

        if (! $dokumen->suratBiasa?->nomor_surat || ! $dokumen->suratBiasa?->tanggal_surat || ! $dokumen->penandatangan_id) {
            // Metadata wajib lengkap sebelum masuk verifikasi agar preview yang dilihat verifikator sudah representatif.
            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 1,
                ])
                ->with('error', 'Metadata surat belum lengkap. Silakan lengkapi metadata lebih dulu.');
        }

        try {
            // Preview verifikasi dibuat lebih dulu agar verifikator langsung melihat PDF yang sudah ditempeli metadata.
            $previewPath = $this->previewSuratBiasaPdfGenerator->generate($dokumen, $sourcePdf->file_path);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.proses-surat', [
                    'dokumen' => $dokumen->dokumen_id,
                    'step' => 3,
                ])
                ->with('error', 'Preview PDF verifikasi belum berhasil dibuat. Silakan cek file PDF dan posisi elemen.');
        }

        // Transaction dipakai karena flow verifikasi, status dokumen, dan file preview harus berubah bersama.
        // Jika salah satu gagal, dokumen tidak akan masuk status MENUNGGU_VERIFIKASI secara setengah jadi.
        DB::transaction(function () use ($dokumen, $finalFlow, $previewPath, $sourcePdf, $request): void {
            // Flow lama dihapus agar pengiriman ulang setelah revisi tidak meninggalkan level verifikasi yang sudah tidak dipakai.
            Verifikasi::query()
                ->where('dokumen_id', $dokumen->dokumen_id)
                ->delete();

            // Seluruh flow verifikasi dibangun ulang agar nomor level tetap rapi mengikuti pilihan Admin Surat terbaru.
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

        // Setelah jalur verifikasi siap, Admin Surat kembali ke pengajuan masuk untuk melanjutkan antrean kerja.
        return redirect()
            ->route('admin.pengajuan-masuk')
            ->with('status', 'Surat berhasil dikirim ke verifikator.');
    }

    private function addUniqueVerifierFlowValidation(LaravelValidator $validator, Request $request, array $levelLabels): void
    {
        $validator->after(function (LaravelValidator $validator) use ($request, $levelLabels): void {
            $penandatanganFinal = (string) $request->input('penandatangan_final', '');
            $selectedVerifierLevels = [];

            foreach ($levelLabels as $field => $label) {
                $value = $request->input($field);

                if ($value === null || $value === '') {
                    continue;
                }

                $verifikatorId = (string) $value;

                if ($penandatanganFinal !== '' && $verifikatorId === $penandatanganFinal) {
                    // Penandatangan final adalah tahap terakhir otomatis, jadi tidak boleh dipakai ulang sebagai verifikator pemeriksa.
                    $validator->errors()->add($field, "{$label} tidak boleh sama dengan penandatangan final.");
                }

                if (isset($selectedVerifierLevels[$verifikatorId])) {
                    // Setiap user hanya boleh muncul satu kali di jalur verifikasi bertingkat.
                    $validator->errors()->add($field, "{$label} tidak boleh sama dengan {$selectedVerifierLevels[$verifikatorId]}.");
                    continue;
                }

                $selectedVerifierLevels[$verifikatorId] = $label;
            }
        });
    }
}
