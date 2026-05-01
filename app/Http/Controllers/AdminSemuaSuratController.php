<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Support\SuratPdfDownloadName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminSemuaSuratController extends Controller
{
    public function index(): View
    {
        $suratList = Dokumen::query()
            ->with([
                'pemohon',
                'suratBiasa',
                'dokumenFiles' => fn ($query) => $query->latest('file_id'),
                'verifikasi' => fn ($query) => $query
                    ->with('verifikator')
                    ->orderByDesc('verified_at')
                    ->orderByDesc('verifikasi_id'),
            ])
            ->where('jenis_dokumen', 'SURAT_BIASA')
            ->latest('created_at')
            ->get();

        return view('admin.semua-surat', [
            'suratList' => $suratList,
        ]);
    }

    public function previewFinal(Dokumen $dokumen): BinaryFileResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        $file = $this->resolvePreviewablePdf($dokumen);
        abort_unless($file && Storage::disk('public')->exists($file->file_path), 404);

        return response()->file(
            Storage::disk('public')->path($file->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
            ]
        );
    }

    public function downloadFinal(Dokumen $dokumen): BinaryFileResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        $file = $this->resolvePreviewablePdf($dokumen);
        abort_unless($file && Storage::disk('public')->exists($file->file_path), 404);

        return response()->download(
            Storage::disk('public')->path($file->file_path),
            SuratPdfDownloadName::forDokumen($dokumen)
        );
    }

    public function publish(Dokumen $dokumen): RedirectResponse
    {
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->status_dokumen === 'SIAP_PUBLISH',
            404
        );

        $dokumen->loadMissing('dokumenFiles');

        $sourcePdf = $this->resolvePublishSourcePdf($dokumen);
        $finalPdfPayload = null;

        if ($sourcePdf && Storage::disk('public')->exists($sourcePdf->file_path)) {
            // Final PDF diambil dari preview verifikasi terbaru agar isi publish sama dengan yang sudah dilihat verifikator.
            $extension = pathinfo($sourcePdf->file_name, PATHINFO_EXTENSION) ?: 'pdf';
            $finalFileName = pathinfo($sourcePdf->file_name, PATHINFO_FILENAME) . '-final.' . $extension;
            $finalFilePath = 'dokumen/final/' . $dokumen->dokumen_id . '/' . $finalFileName;

            Storage::disk('public')->makeDirectory(dirname($finalFilePath));
            Storage::disk('public')->copy($sourcePdf->file_path, $finalFilePath);

            $finalPdfPayload = [
                'file_name' => $finalFileName,
                'file_path' => $finalFilePath,
            ];
        }

        DB::transaction(function () use ($dokumen, $finalPdfPayload): void {
            // Token verifikasi disiapkan saat publish agar nanti bisa dipakai untuk QR/validasi publik.
            $dokumen->update([
                'status_dokumen' => 'PUBLISHED',
                'published_at' => now(),
                'verification_token' => $dokumen->verification_token ?: Str::random(40),
                'file_final_path' => $finalPdfPayload['file_path'] ?? $dokumen->file_final_path,
            ]);

            if ($finalPdfPayload) {
                DokumenFile::query()->updateOrCreate(
                    [
                        'dokumen_id' => $dokumen->dokumen_id,
                        'file_type' => 'FINAL_PDF',
                    ],
                    [
                        'file_name' => $finalPdfPayload['file_name'],
                        'file_path' => $finalPdfPayload['file_path'],
                        'uploaded_by' => auth()->id(),
                        'uploaded_at' => now(),
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.semua-surat')
            ->with('status', 'Dokumen berhasil dipublish.');
    }

    protected function resolvePreviewablePdf(Dokumen $dokumen): ?DokumenFile
    {
        foreach (['FINAL_PDF', 'PREVIEW_VERIFIKASI_PDF', 'HASIL_PEMERIKSAAN_PDF'] as $fileType) {
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

    protected function resolvePublishSourcePdf(Dokumen $dokumen): ?DokumenFile
    {
        foreach (['PREVIEW_VERIFIKASI_PDF', 'HASIL_PEMERIKSAAN_PDF'] as $fileType) {
            $file = $dokumen->dokumenFiles
                ->first(fn (DokumenFile $item) => $item->file_type === $fileType);

            if ($file) {
                return $file;
            }
        }

        return null;
    }
}
