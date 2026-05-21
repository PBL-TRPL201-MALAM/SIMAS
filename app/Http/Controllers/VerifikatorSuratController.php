<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Models\Verifikasi;
use App\Services\PreviewSuratBiasaPdfGenerator;
use App\Support\SuratPdfDownloadName;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini menangani area Verifikator: melihat dokumen yang menunggu, membaca detail, preview/unduh PDF, lalu memberi keputusan.
// Alur bisnis penting di sini adalah verifikasi bertingkat: level berikutnya baru boleh bekerja setelah level sebelumnya menyetujui.
class VerifikatorSuratController extends Controller
{
    // Service PDF dipakai ulang untuk memperbarui preview setelah verifikator menyetujui dokumen.
    public function __construct(
        protected PreviewSuratBiasaPdfGenerator $previewSuratBiasaPdfGenerator
    ) {
    }

    // Method ini menampilkan surat biasa yang sedang menunggu keputusan verifikator login.
    public function menunggu(Request $request): View
    {
        $status = $this->normalizeVerificationStatus($request->query('status'));

        $suratQuery = $status === 'MENUNGGU'
            ? $this->activePendingVerificationQuery($request, 'SURAT_BIASA')
            : $this->baseQuery($request)->where('status_verifikasi', $status);

        return view('verifikator.surat-menunggu', [
            'suratMenunggu' => $suratQuery
                ->when(
                    $status === 'MENUNGGU',
                    fn (Builder $query) => $query->orderBy('created_at'),
                    fn (Builder $query) => $query->latest('verified_at')->latest('updated_at')
                )
                ->get(),
            'activeStatus' => strtolower($status),
            'statusCounts' => $this->assignedVerificationStatusCounts($request, 'SURAT_BIASA'),
        ]);
    }

    // Method ini menampilkan SK yang menunggu verifikasi dengan aturan level bertingkat yang sama.
    public function skMenunggu(Request $request): View
    {
        $status = $this->normalizeVerificationStatus($request->query('status'));

        $skQuery = $status === 'MENUNGGU'
            ? $this->activePendingVerificationQuery($request, 'SURAT_KEPUTUSAN')
            : $this->baseSkQuery($request)->where('status_verifikasi', $status);

        return view('verifikator.sk-menunggu', [
            'skMenunggu' => $skQuery
                ->when(
                    $status === 'MENUNGGU',
                    fn (Builder $query) => $query->orderBy('created_at'),
                    fn (Builder $query) => $query->latest('verified_at')->latest('updated_at')
                )
                ->get(),
            'activeStatus' => strtolower($status),
            'statusCounts' => $this->assignedVerificationStatusCounts($request, 'SURAT_KEPUTUSAN'),
        ]);
    }

    // Method ini menampilkan riwayat surat biasa yang pernah disetujui oleh verifikator login.
    public function disetujui(): RedirectResponse
    {
        return redirect()->route('verifikator.surat-menunggu', ['status' => 'disetujui']);
    }

    // Method ini menampilkan riwayat surat biasa yang pernah ditolak oleh verifikator login.
    public function ditolak(): RedirectResponse
    {
        return redirect()->route('verifikator.surat-menunggu', ['status' => 'ditolak']);
    }

    // Method ini menampilkan seluruh surat biasa di sistem untuk kebutuhan monitoring Verifikator.
    public function semua(): View
    {
        return view('verifikator.surat-semua', [
            'suratSemua' => $this->allDokumenQuery('SURAT_BIASA')
                ->latest('created_at')
                ->get(),
        ]);
    }

    // Method ini menampilkan seluruh SK di sistem untuk kebutuhan monitoring Verifikator.
    public function skSemua(): View
    {
        return view('verifikator.sk-semua', [
            'skSemua' => $this->allDokumenQuery('SURAT_KEPUTUSAN')
                ->latest('created_at')
                ->get(),
        ]);
    }

    // Method ini mengunduh PDF dokumen yang ditugaskan kepada verifikator.
    public function downloadPdf(Request $request, Dokumen $dokumen): BinaryFileResponse
    {
        // Guard ini memastikan URL unduh tidak bisa dipakai oleh verifikator yang tidak ditugaskan.
        $this->ensureAssignedToVerifikator($request, $dokumen);

        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);
        abort_unless($pdfFile, 404);

        // response()->download mengirim file sebagai attachment dengan nama unduhan resmi SIMAS.
        return response()->download(
            Storage::disk('public')->path($pdfFile->file_path),
            SuratPdfDownloadName::forDokumen($dokumen)
        );
    }

    // Method ini memberi akses unduh lampiran untuk verifikator yang memang masuk dalam flow dokumen.
    public function downloadLampiran(Request $request, DokumenFile $file): BinaryFileResponse
    {
        $file->loadMissing('dokumen');
        $dokumen = $file->dokumen;

        abort_unless(
            $dokumen
                && $file->file_type === 'LAMPIRAN'
                && $dokumen->jenis_dokumen === 'SURAT_BIASA',
            404
        );

        $this->ensureAssignedToVerifikator($request, $dokumen);

        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return response()->download(
            Storage::disk('public')->path($file->file_path),
            $file->file_name
        );
    }

    // Method ini membuka lampiran pendukung secara inline untuk verifikator tanpa mengubah sumber PDF verifikasi.
    public function previewLampiran(Request $request, DokumenFile $file): BinaryFileResponse
    {
        $file->loadMissing('dokumen');
        $dokumen = $file->dokumen;

        abort_unless(
            $dokumen
                && $file->file_type === 'LAMPIRAN'
                && $dokumen->jenis_dokumen === 'SURAT_BIASA'
                && $file->isPreviewableLampiran(),
            404
        );

        $this->ensureAssignedToVerifikator($request, $dokumen);

        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return response()->file(
            Storage::disk('public')->path($file->file_path),
            [
                'Content-Type' => $file->lampiranPreviewContentType() ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . addslashes($file->file_name) . '"',
            ]
        );
    }

    // Method ini menampilkan PDF secara inline agar verifikator bisa membaca dokumen sebelum mengambil keputusan.
    public function previewPdf(Request $request, Dokumen $dokumen): StreamedResponse
    {
        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);
        abort_unless($pdfFile, 404);

        abort_unless(Storage::disk('public')->exists($pdfFile->file_path), 404);

        return Storage::disk('public')->response(
            $pdfFile->file_path,
            $pdfFile->file_name,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . addslashes($pdfFile->file_name) . '"',
            ]
        );
    }

    // Method ini menampilkan detail surat biasa beserta file preview dan status apakah masih bisa diproses.
    public function detailSurat(Request $request, Dokumen $dokumen): View
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        $dokumen->loadMissing([
            'pemohon',
            'suratBiasa',
            'dokumenFiles',
            'verifikasi.verifikator',
        ]);

        $verifikasi = $this->resolveAssignedVerifikasiForDokumen($request, $dokumen);
        $isReadOnly = $request->query('from') === 'semua' || ! $verifikasi;

        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);
        $previewPdfUrl = null;

        if ($pdfFile && Storage::disk('public')->exists($pdfFile->file_path)) {
            $previewPdfUrl = route('verifikator.surat.preview-pdf', $dokumen);
        }

        return view('verifikator.surat-detail', [
            'verifikasi' => $verifikasi,
            'dokumen' => $dokumen,
            'previewPdfUrl' => $previewPdfUrl,
            'downloadPdfUrl' => $pdfFile && $verifikasi && ! $isReadOnly ? route('verifikator.surat.unduh-pdf', $dokumen) : null,
            'canProcess' => $verifikasi && ! $isReadOnly && $this->canProcessVerifikasi($verifikasi),
            'canAccessAssignedFiles' => $verifikasi && ! $isReadOnly,
            'isReadOnly' => $isReadOnly,
            'activePage' => $isReadOnly ? 'surat-semua' : 'surat-menunggu',
            'backUrl' => $isReadOnly ? route('verifikator.surat-semua') : route('verifikator.surat-menunggu'),
            'defaultDecision' => old('keputusan', $request->query('aksi') === 'tolak' ? 'tolak' : 'setuju'),
        ]);
    }

    // Method ini menampilkan detail Surat Keputusan dengan relasi khusus SK.
    public function detailSk(Request $request, Dokumen $dokumen): View
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN', 404);

        $dokumen->loadMissing([
            'pemohon',
            'penandatangan',
            'suratKeputusan.dasarHukum',
            'suratKeputusan.skMenimbang',
            'suratKeputusan.skMemutuskan',
            'dokumenFiles',
            'verifikasi.verifikator',
        ]);

        $verifikasi = $this->resolveAssignedVerifikasiForDokumen($request, $dokumen);
        $isReadOnly = $request->query('from') === 'semua' || ! $verifikasi;
        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);

        return view('verifikator.sk-detail', [
            'verifikasi' => $verifikasi,
            'dokumen' => $dokumen,
            'suratKeputusan' => $dokumen->suratKeputusan,
            'previewPdfUrl' => $pdfFile ? route('verifikator.sk.preview-pdf', $dokumen) : null,
            'downloadPdfUrl' => $pdfFile && $verifikasi && ! $isReadOnly ? route('verifikator.sk.unduh-pdf', $dokumen) : null,
            'canProcess' => $verifikasi && ! $isReadOnly && $this->canProcessVerifikasi($verifikasi),
            'isReadOnly' => $isReadOnly,
            'activePage' => $isReadOnly ? 'sk-semua' : 'sk-menunggu',
            'backUrl' => $isReadOnly ? route('verifikator.sk-semua') : route('verifikator.sk-menunggu'),
            'defaultDecision' => old('keputusan', $request->query('aksi') === 'tolak' ? 'tolak' : 'setuju'),
        ]);
    }

    // Method ini memproses keputusan setuju/tolak dari form verifikasi.
    public function proses(Request $request, Verifikasi $verifikasi): RedirectResponse
    {
        // Verifikator hanya boleh memproses baris verifikasi yang memang ditugaskan kepadanya.
        abort_unless($verifikasi->verifikator_id === $request->user()->user_id, 403);

        $verifikasi->loadMissing(['dokumen.verifikasi']);

        abort_unless($verifikasi->status_verifikasi === 'MENUNGGU', 403);

        abort_unless($this->canProcessVerifikasi($verifikasi), 403);

        // Keputusan verifikator hanya dua: setuju atau tolak. Catatan menjadi wajib jika memilih tolak.
        $validated = $request->validate([
            'keputusan' => ['required', 'in:setuju,tolak'],
            'catatan' => ['nullable', 'string'],
        ], [
            'keputusan.required' => 'Keputusan verifikasi wajib dipilih.',
        ]);

        if ($validated['keputusan'] === 'tolak' && blank($validated['catatan'] ?? null)) {
            return back()
                ->withErrors(['catatan' => 'Catatan revisi wajib diisi saat mengembalikan dokumen.'])
                ->withInput();
        }

        // Transaction dipakai karena perubahan status verifikasi dan status dokumen harus konsisten.
        // Contohnya, jika level terakhir setuju, dokumen harus ikut berubah menjadi SIAP_PUBLISH.
        DB::transaction(function () use ($verifikasi, $validated): void {
            if ($validated['keputusan'] === 'setuju') {
                $verifikasi->update([
                    'status_verifikasi' => 'DISETUJUI',
                    'verified_at' => now(),
                    'catatan' => null,
                ]);

                $nextLevel = $verifikasi->dokumen->verifikasi
                    ->where('level', '>', $verifikasi->level)
                    ->sortBy('level')
                    ->first();

                // Verifikasi SIMAS bersifat bertingkat: level berikutnya baru aktif setelah level saat ini disetujui.
                if ($nextLevel) {
                    $nextLevel->update([
                        'status_verifikasi' => 'MENUNGGU',
                    ]);

                    // Dokumen tetap berada di tahap verifikasi selama masih ada level berikutnya yang harus memeriksa.
                    $verifikasi->dokumen->update([
                        'status_dokumen' => 'MENUNGGU_VERIFIKASI',
                    ]);
                } else {
                    // Jika level terakhir sudah setuju, Admin Surat boleh melanjutkan ke tahap publish.
                    $verifikasi->dokumen->update([
                        'status_dokumen' => 'SIAP_PUBLISH',
                    ]);
                }

                return;
            }

            $verifikasi->update([
                'status_verifikasi' => 'DITOLAK',
                'verified_at' => now(),
                'catatan' => $validated['catatan'],
            ]);

            // Pengembalian revisi membuat dokumen kembali ke Pemohon sebelum bisa dikirim ulang ke alur verifikasi.
            $verifikasi->dokumen->update([
                'status_dokumen' => 'PERLU_REVISI',
            ]);
        });

        // Preview PDF diperbarui setelah persetujuan agar nama verifikator yang sudah setuju langsung tampil.
        if ($validated['keputusan'] === 'setuju' && $verifikasi->dokumen->jenis_dokumen === 'SURAT_BIASA') {
            $this->refreshPreviewVerifikasiPdf($verifikasi, $request->user()->user_id);
        }

        // Setelah keputusan disimpan, user kembali ke daftar menunggu sesuai jenis dokumennya.
        return redirect()
            ->route($verifikasi->dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN'
                ? 'verifikator.sk-menunggu'
                : 'verifikator.surat-menunggu')
            ->with('status', $validated['keputusan'] === 'setuju'
                ? 'Dokumen berhasil disetujui.'
                : 'Dokumen berhasil dikembalikan untuk revisi.');
    }

    // Helper query dasar surat biasa yang ditugaskan ke verifikator login.
    protected function baseQuery(Request $request): Builder
    {
        return $this->baseVerificationQuery($request, 'SURAT_BIASA');
    }

    // Helper query dasar SK yang ditugaskan ke verifikator login.
    protected function baseSkQuery(Request $request): Builder
    {
        return $this->baseVerificationQuery($request, 'SURAT_KEPUTUSAN');
    }

    // Helper query dasar dokumen yang ditugaskan ke verifikator login.
    protected function baseVerificationQuery(Request $request, string $jenisDokumen): Builder
    {
        $detailRelation = $jenisDokumen === 'SURAT_KEPUTUSAN'
            ? 'dokumen.suratKeputusan'
            : 'dokumen.suratBiasa';

        return Verifikasi::query()
            ->with([
                'dokumen.pemohon',
                $detailRelation,
                'dokumen.dokumenFiles' => fn (Builder $query) => $query
                    ->whereIn('file_type', ['FINAL_PDF', 'PREVIEW_VERIFIKASI_PDF', 'DRAFT_PDF'])
                    ->latest('file_id'),
            ])
            ->whereHas('dokumen', fn (Builder $query) => $query->where('jenis_dokumen', $jenisDokumen))
            ->where('verifikator_id', $request->user()->user_id);
    }

    // Helper query ini mengambil tugas menunggu yang levelnya sudah aktif untuk diproses.
    protected function activePendingVerificationQuery(Request $request, string $jenisDokumen): Builder
    {
        return $this->baseVerificationQuery($request, $jenisDokumen)
            ->where('status_verifikasi', 'MENUNGGU')
            ->whereHas('dokumen', fn (Builder $query) => $query->where('status_dokumen', 'MENUNGGU_VERIFIKASI'))
            // Subquery ini memastikan level saat ini belum muncul jika level sebelumnya belum DISETUJUI.
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('verifikasi as previous_levels')
                    ->whereColumn('previous_levels.dokumen_id', 'verifikasi.dokumen_id')
                    ->whereColumn('previous_levels.level', '<', 'verifikasi.level')
                    ->where('previous_levels.status_verifikasi', '!=', 'DISETUJUI');
            });
    }

    protected function assignedVerificationStatusCounts(Request $request, string $jenisDokumen): array
    {
        return [
            'menunggu' => $this->activePendingVerificationQuery($request, $jenisDokumen)->count(),
            'disetujui' => $this->baseVerificationQuery($request, $jenisDokumen)
                ->where('status_verifikasi', 'DISETUJUI')
                ->count(),
            'ditolak' => $this->baseVerificationQuery($request, $jenisDokumen)
                ->where('status_verifikasi', 'DITOLAK')
                ->count(),
        ];
    }

    protected function allDokumenQuery(string $jenisDokumen): Builder
    {
        $relations = [
            'pemohon',
            'penandatangan',
            'dokumenFiles' => fn (Builder $query) => $query
                ->whereIn('file_type', ['FINAL_PDF', 'PREVIEW_VERIFIKASI_PDF', 'DRAFT_PDF'])
                ->latest('file_id'),
            'verifikasi.verifikator',
        ];

        if ($jenisDokumen === 'SURAT_KEPUTUSAN') {
            $relations[] = 'suratKeputusan.dasarHukum';
            $relations[] = 'suratKeputusan.skMenimbang';
            $relations[] = 'suratKeputusan.skMemutuskan';
        } else {
            $relations[] = 'suratBiasa';
        }

        return Dokumen::query()
            ->with($relations)
            ->where('jenis_dokumen', $jenisDokumen);
    }

    // Helper ini mengambil baris verifikasi milik user login jika dokumen memang menjadi tugasnya.
    protected function resolveAssignedVerifikasiForDokumen(Request $request, Dokumen $dokumen): ?Verifikasi
    {
        return Verifikasi::query()
            ->where('dokumen_id', $dokumen->dokumen_id)
            ->where('verifikator_id', $request->user()->user_id)
            ->first();
    }

    protected function normalizeVerificationStatus(mixed $status): string
    {
        return match (strtolower((string) $status)) {
            'disetujui' => 'DISETUJUI',
            'ditolak' => 'DITOLAK',
            default => 'MENUNGGU',
        };
    }

    // Helper guard akses file: hanya verifikator yang ada dalam flow dokumen boleh preview/unduh.
    protected function ensureAssignedToVerifikator(Request $request, Dokumen $dokumen): void
    {
        // Guard tambahan ini menutup akses langsung ke URL unduh/preview jika user bukan bagian dari jalur verifikasi dokumen itu.
        abort_unless(
            $dokumen->verifikasi()->where('verifikator_id', $request->user()->user_id)->exists(),
            403
        );
    }

    // Helper ini memilih PDF terbaik yang bisa dibaca verifikator sesuai jenis dokumen.
    protected function resolvePdfFileForDokumen(Dokumen $dokumen): ?DokumenFile
    {
        $preferredTypes = $dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN'
            ? ['FINAL_PDF', 'PREVIEW_VERIFIKASI_PDF', 'DRAFT_PDF']
            : ['FINAL_PDF', 'PREVIEW_VERIFIKASI_PDF', 'DRAFT_PDF'];

        // Verifikator diprioritaskan melihat file preview terbaru agar keputusan diambil dari versi dokumen yang sudah ditempeli metadata.
        foreach ($preferredTypes as $fileType) {
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

    // Helper ini menentukan apakah level verifikasi saat ini sudah boleh diproses.
    protected function canProcessVerifikasi(Verifikasi $verifikasi): bool
    {
        if ($verifikasi->status_verifikasi !== 'MENUNGGU') {
            return false;
        }

        $verifikasi->loadMissing('dokumen.verifikasi');

        // Level ini baru boleh memutuskan jika semua level di atasnya sudah selesai menyetujui.
        return ! $verifikasi->dokumen->verifikasi
            ->where('level', '<', $verifikasi->level)
            ->contains(fn (Verifikasi $item) => $item->status_verifikasi !== 'DISETUJUI');
    }

    // Helper ini membuat ulang preview PDF setelah ada persetujuan agar tanda verifikasi terbaru ikut terlihat.
    protected function refreshPreviewVerifikasiPdf(Verifikasi $verifikasi, int $actorId): void
    {
        // Relasi diload ulang dari database agar generator membaca status verifikasi paling baru.
        $dokumen = $verifikasi->dokumen()->with([
            'suratBiasa',
            'posisiElemenDokumen',
            'verifikasi.verifikator',
            'dokumenFiles',
        ])->first();

        if (! $dokumen) {
            return;
        }

        // File sumber tetap PDF awal dari Pemohon.
        $sourcePdf = null;

        foreach (['DRAFT_PDF'] as $fileType) {
            $sourcePdf = $dokumen->dokumenFiles
                ->where('file_type', $fileType)
                ->sortByDesc('file_id')
                ->first();

            if ($sourcePdf) {
                break;
            }
        }

        if (! $sourcePdf || ! Storage::disk('public')->exists($sourcePdf->file_path)) {
            return;
        }

        try {
            // Preview diregenerate setiap ada persetujuan agar PDF terbaru selalu mencerminkan siapa saja yang sudah menyetujui.
            $previewPath = $this->previewSuratBiasaPdfGenerator->generate($dokumen, $sourcePdf->file_path);
        } catch (\Throwable $exception) {
            report($exception);
            return;
        }

        // File preview yang sama diperbarui terus agar halaman detail verifikator selalu membaca versi terbaru.
        DokumenFile::query()->updateOrCreate(
            [
                'dokumen_id' => $dokumen->dokumen_id,
                'file_type' => 'PREVIEW_VERIFIKASI_PDF',
            ],
            [
                'file_name' => pathinfo($sourcePdf->file_name, PATHINFO_FILENAME) . '-preview-verifikasi.pdf',
                'file_path' => $previewPath,
                'uploaded_by' => $actorId,
                'uploaded_at' => now(),
            ]
        );
    }
}
