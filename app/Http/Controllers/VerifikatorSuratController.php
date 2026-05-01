<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Models\Verifikasi;
use App\Services\PreviewVerifikasiPdfGenerator;
use App\Support\SuratPdfDownloadName;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VerifikatorSuratController extends Controller
{
    public function __construct(
        protected PreviewVerifikasiPdfGenerator $previewVerifikasiPdfGenerator
    ) {
    }

    public function menunggu(Request $request): View
    {
        // Halaman ini hanya menampilkan surat yang memang sudah terbuka untuk diproses oleh verifikator login.
        return view('verifikator.surat-menunggu', [
            'suratMenunggu' => $this->baseQuery($request)
                ->where('status_verifikasi', 'MENUNGGU')
                ->whereHas('dokumen', fn (Builder $query) => $query->where('status_dokumen', 'MENUNGGU_VERIFIKASI'))
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('verifikasi as previous_levels')
                        ->whereColumn('previous_levels.dokumen_id', 'verifikasi.dokumen_id')
                        ->whereColumn('previous_levels.level', '<', 'verifikasi.level')
                        ->where('previous_levels.status_verifikasi', '!=', 'DISETUJUI');
                })
                ->orderBy('created_at')
                ->get(),
        ]);
    }

    public function skMenunggu(Request $request): View
    {
        return view('verifikator.sk-menunggu', [
            'skMenunggu' => $this->baseSkQuery($request)
                ->where('status_verifikasi', 'MENUNGGU')
                ->whereHas('dokumen', fn (Builder $query) => $query->where('status_dokumen', 'MENUNGGU_VERIFIKASI'))
                ->whereNotExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('verifikasi as previous_levels')
                        ->whereColumn('previous_levels.dokumen_id', 'verifikasi.dokumen_id')
                        ->whereColumn('previous_levels.level', '<', 'verifikasi.level')
                        ->where('previous_levels.status_verifikasi', '!=', 'DISETUJUI');
                })
                ->orderBy('created_at')
                ->get(),
        ]);
    }

    public function disetujui(Request $request): View
    {
        // Riwayat ini membantu verifikator melihat dokumen apa saja yang pernah ia setujui.
        return view('verifikator.surat-disetujui', [
            'suratDisetujui' => $this->baseQuery($request)
                ->where('status_verifikasi', 'DISETUJUI')
                ->latest('verified_at')
                ->get(),
        ]);
    }

    public function ditolak(Request $request): View
    {
        // Riwayat penolakan dipakai untuk meninjau ulang dokumen yang pernah dikembalikan ke Admin/TU.
        return view('verifikator.surat-ditolak', [
            'suratDitolak' => $this->baseQuery($request)
                ->where('status_verifikasi', 'DITOLAK')
                ->latest('verified_at')
                ->get(),
        ]);
    }

    public function semua(Request $request): View
    {
        // Halaman semua surat merangkum seluruh dokumen yang pernah masuk ke verifikator, apa pun status tahapnya.
        return view('verifikator.surat-semua', [
            'suratSemua' => $this->baseQuery($request)
                ->latest('created_at')
                ->get(),
        ]);
    }

    public function downloadPdf(Request $request, Dokumen $dokumen): BinaryFileResponse
    {
        $this->ensureAssignedToVerifikator($request, $dokumen);

        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);
        abort_unless($pdfFile, 404);

        return response()->download(
            Storage::disk('public')->path($pdfFile->file_path),
            SuratPdfDownloadName::forDokumen($dokumen)
        );
    }

    public function previewPdf(Request $request, Dokumen $dokumen): StreamedResponse
    {
        $this->ensureAssignedToVerifikator($request, $dokumen);

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

    public function detailSurat(Request $request, Dokumen $dokumen): View
    {
        // Detail surat dipakai verifikator untuk membaca dokumen lengkap sebelum memberi keputusan setuju atau tolak.
        $verifikasi = $this->resolveVerifikasiForDokumen(
            $request,
            $dokumen,
            'SURAT_BIASA',
            [
                'dokumen.pemohon',
                'dokumen.suratBiasa',
                'dokumen.dokumenFiles',
                'dokumen.verifikasi',
            ]
        );

        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);
        $previewPdfUrl = null;

        if ($pdfFile && Storage::disk('public')->exists($pdfFile->file_path)) {
            $previewPdfUrl = '/storage/' . ltrim($pdfFile->file_path, '/');
        }

        return view('verifikator.surat-detail', [
            'verifikasi' => $verifikasi,
            'dokumen' => $dokumen,
            'previewPdfUrl' => $previewPdfUrl,
            'downloadPdfUrl' => $pdfFile ? route('verifikator.surat.unduh-pdf', $dokumen) : null,
            'canProcess' => $this->canProcessVerifikasi($verifikasi),
            'defaultDecision' => old('keputusan', $request->query('aksi') === 'tolak' ? 'tolak' : 'setuju'),
        ]);
    }

    public function detailSk(Request $request, Dokumen $dokumen): View
    {
        $verifikasi = $this->resolveVerifikasiForDokumen(
            $request,
            $dokumen,
            'SURAT_KEPUTUSAN',
            [
                'dokumen.pemohon',
                'dokumen.suratKeputusan.dasarHukum',
                'dokumen.suratKeputusan.skMenimbang',
                'dokumen.suratKeputusan.skMemutuskan',
                'dokumen.dokumenFiles',
                'dokumen.verifikasi',
            ]
        );

        $pdfFile = $this->resolvePdfFileForDokumen($dokumen);

        return view('verifikator.sk-detail', [
            'verifikasi' => $verifikasi,
            'dokumen' => $dokumen,
            'suratKeputusan' => $dokumen->suratKeputusan,
            'previewPdfUrl' => $pdfFile ? route('verifikator.sk.preview-pdf', $dokumen) : null,
            'downloadPdfUrl' => $pdfFile ? route('verifikator.sk.unduh-pdf', $dokumen) : null,
            'canProcess' => $this->canProcessVerifikasi($verifikasi),
            'defaultDecision' => old('keputusan', $request->query('aksi') === 'tolak' ? 'tolak' : 'setuju'),
        ]);
    }

    public function proses(Request $request, Verifikasi $verifikasi): RedirectResponse
    {
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
                ->withErrors(['catatan' => 'Catatan penolakan wajib diisi saat menolak dokumen.'])
                ->withInput();
        }

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
                    // Jika level terakhir sudah setuju, Admin/TU boleh melanjutkan ke tahap publish.
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

            // Penolakan mengembalikan dokumen ke Admin/TU untuk diperbaiki sebelum bisa dikirim ulang.
            $verifikasi->dokumen->update([
                'status_dokumen' => 'PERLU_REVISI',
            ]);
        });

        // Preview PDF diperbarui setelah persetujuan agar nama verifikator yang sudah setuju langsung tampil.
        if ($validated['keputusan'] === 'setuju' && $verifikasi->dokumen->jenis_dokumen === 'SURAT_BIASA') {
            $this->refreshPreviewVerifikasiPdf($verifikasi, $request->user()->user_id);
        }

        return redirect()
            ->route($verifikasi->dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN'
                ? 'verifikator.sk-menunggu'
                : 'verifikator.surat-menunggu')
            ->with('status', $validated['keputusan'] === 'setuju'
                ? 'Dokumen berhasil disetujui.'
                : 'Dokumen berhasil ditolak dan dikembalikan untuk revisi.');
    }

    protected function baseQuery(Request $request): Builder
    {
        // Query dasar ini dipakai ulang oleh beberapa halaman verifikator agar sumber datanya tetap konsisten.
        return Verifikasi::query()
            ->with([
                'dokumen.pemohon',
                'dokumen.suratBiasa',
                'dokumen.dokumenFiles' => fn (Builder $query) => $query
                    ->whereIn('file_type', ['PREVIEW_VERIFIKASI_PDF', 'HASIL_PEMERIKSAAN_PDF'])
                    ->latest('file_id'),
            ])
            ->whereHas('dokumen', fn (Builder $query) => $query->where('jenis_dokumen', 'SURAT_BIASA'))
            ->where('verifikator_id', $request->user()->user_id);
    }

    protected function baseSkQuery(Request $request): Builder
    {
        return Verifikasi::query()
            ->with([
                'dokumen.pemohon',
                'dokumen.suratKeputusan',
                'dokumen.dokumenFiles' => fn (Builder $query) => $query
                    ->whereIn('file_type', ['FINAL_PDF', 'HASIL_PEMERIKSAAN_PDF'])
                    ->latest('file_id'),
            ])
            ->whereHas('dokumen', fn (Builder $query) => $query->where('jenis_dokumen', 'SURAT_KEPUTUSAN'))
            ->where('verifikator_id', $request->user()->user_id);
    }

    protected function resolveVerifikasiForDokumen(Request $request, Dokumen $dokumen, string $jenisDokumen, array $with = []): Verifikasi
    {
        abort_unless($dokumen->jenis_dokumen === $jenisDokumen, 404);

        // Satu verifikator hanya boleh membuka detail dokumen yang memang ditugaskan kepadanya.
        return Verifikasi::query()
            ->with($with)
            ->where('dokumen_id', $dokumen->dokumen_id)
            ->where('verifikator_id', $request->user()->user_id)
            ->firstOrFail();
    }

    protected function ensureAssignedToVerifikator(Request $request, Dokumen $dokumen): void
    {
        // Guard tambahan ini menutup akses langsung ke URL unduh/preview jika user bukan bagian dari jalur verifikasi dokumen itu.
        abort_unless(
            $dokumen->verifikasi()->where('verifikator_id', $request->user()->user_id)->exists(),
            403
        );
    }

    protected function resolvePdfFileForDokumen(Dokumen $dokumen): ?DokumenFile
    {
        $preferredTypes = $dokumen->jenis_dokumen === 'SURAT_KEPUTUSAN'
            ? ['FINAL_PDF', 'HASIL_PEMERIKSAAN_PDF']
            : ['PREVIEW_VERIFIKASI_PDF', 'HASIL_PEMERIKSAAN_PDF'];

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

    protected function refreshPreviewVerifikasiPdf(Verifikasi $verifikasi, int $actorId): void
    {
        $dokumen = $verifikasi->dokumen()->with([
            'suratBiasa',
            'posisiElemenDokumen',
            'verifikasi.verifikator',
            'dokumenFiles',
        ])->first();

        if (! $dokumen) {
            return;
        }

        $sourcePdf = $dokumen->dokumenFiles
            ->where('file_type', 'HASIL_PEMERIKSAAN_PDF')
            ->sortByDesc('file_id')
            ->first();

        if (! $sourcePdf || ! Storage::disk('public')->exists($sourcePdf->file_path)) {
            return;
        }

        try {
            // Preview diregenerate setiap ada persetujuan agar PDF terbaru selalu mencerminkan siapa saja yang sudah menyetujui.
            $previewPath = $this->previewVerifikasiPdfGenerator->generate($dokumen, $sourcePdf->file_path);
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
