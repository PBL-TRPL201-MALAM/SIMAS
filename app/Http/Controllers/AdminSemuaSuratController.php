<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DokumenFile;
use App\Services\PreviewVerifikasiPdfGenerator;
use App\Support\SuratPdfDownloadName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

// Controller ini menangani halaman arsip semua surat biasa untuk Admin/TU.
// Di sini Admin/TU bisa melihat preview final, mengunduh PDF, dan mem-publish dokumen yang sudah selesai diverifikasi.
class AdminSemuaSuratController extends Controller
{
    // Service PDF di-inject agar proses publish bisa membuat PDF final tanpa menaruh detail manipulasi PDF di controller.
    public function __construct(
        protected PreviewVerifikasiPdfGenerator $previewVerifikasiPdfGenerator
    ) {
    }

    // Method ini mengambil semua surat biasa beserta pemohon, file, dan riwayat verifikasinya untuk halaman arsip.
    public function index(): View
    {
        // Query memakai eager loading agar relasi pemohon, metadata surat, file, dan verifikator tidak memicu N+1 query di view.
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

    // Method ini menampilkan PDF yang bisa dipreview admin langsung di browser.
    public function previewFinal(Dokumen $dokumen): BinaryFileResponse
    {
        // Route model binding mengisi $dokumen; guard ini memastikan route hanya dipakai untuk surat biasa.
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        // File previewable dipilih dari prioritas final, preview verifikasi, lalu PDF hasil pemeriksaan.
        $file = $this->resolvePreviewablePdf($dokumen);
        abort_unless($file && Storage::disk('public')->exists($file->file_path), 404);

        // response()->file menampilkan PDF inline, berbeda dengan download yang memaksa unduhan.
        return response()->file(
            Storage::disk('public')->path($file->file_path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
            ]
        );
    }

    // Method ini mengunduh PDF final/preview dengan nama file yang rapi untuk Admin/TU.
    public function downloadFinal(Dokumen $dokumen): BinaryFileResponse
    {
        abort_unless($dokumen->jenis_dokumen === 'SURAT_BIASA', 404);

        // Query file tetap melalui helper agar aturan fallback sama dengan preview.
        $file = $this->resolvePreviewablePdf($dokumen);
        abort_unless($file && Storage::disk('public')->exists($file->file_path), 404);

        return response()->download(
            Storage::disk('public')->path($file->file_path),
            SuratPdfDownloadName::forDokumen($dokumen)
        );
    }

    // Method ini mengubah dokumen yang sudah SIAP_PUBLISH menjadi PUBLISHED dan membuat record file final.
    public function publish(Dokumen $dokumen): RedirectResponse
    {
        abort_unless(
            $dokumen->jenis_dokumen === 'SURAT_BIASA' && $dokumen->status_dokumen === 'SIAP_PUBLISH',
            404
        );

        $dokumen->loadMissing('dokumenFiles');

        $sourcePdf = $this->resolvePublishSourcePdf($dokumen);
        $finalPdfPayload = null;
        $verificationToken = $this->resolveVerificationToken($dokumen);

        if (! $sourcePdf || ! Storage::disk('public')->exists($sourcePdf->file_path)) {
            return redirect()
                ->route('admin.semua-surat')
                ->with('error', 'File PDF sumber belum tersedia untuk dipublish.');
        }

        // URL QR dibangun dari APP_URL/config app.url, bukan dari host request atau alamat yang di-hardcode.
        $validationUrl = rtrim((string) config('app.url'), '/') . route('verifikasi.public', $verificationToken, false);
        $extension = pathinfo($sourcePdf->file_name, PATHINFO_EXTENSION) ?: 'pdf';
        $finalFileName = pathinfo($sourcePdf->file_name, PATHINFO_FILENAME) . '-final.' . $extension;
        $finalFilePath = 'dokumen/final/' . $dokumen->dokumen_id . '/' . $finalFileName;

        Storage::disk('public')->makeDirectory(dirname($finalFilePath));

        try {
            // FINAL_PDF digenerate ulang dari PDF hasil pemeriksaan agar QR dummy preview diganti QR validasi asli.
            $this->previewVerifikasiPdfGenerator->generateFinal(
                $dokumen,
                $sourcePdf->file_path,
                $validationUrl,
                $finalFilePath
            );
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.semua-surat')
                ->with('error', 'PDF final dengan QR validasi belum berhasil dibuat. Silakan cek file PDF dan posisi elemen.');
        }

        $finalPdfPayload = [
            'file_name' => $finalFileName,
            'file_path' => $finalFilePath,
        ];

        // Transaction dipakai agar update status dokumen dan pencatatan file FINAL_PDF terjadi sebagai satu kesatuan.
        DB::transaction(function () use ($dokumen, $finalPdfPayload, $verificationToken): void {
            // Token verifikasi disimpan saat publish dan tidak digenerate ulang jika dokumen sudah memilikinya.
            $dokumen->update([
                'status_dokumen' => 'PUBLISHED',
                'published_at' => now(),
                'verification_token' => $verificationToken,
                'file_final_path' => $finalPdfPayload['file_path'] ?? $dokumen->file_final_path,
            ]);

            if ($finalPdfPayload) {
                // Record FINAL_PDF disimpan terpisah agar riwayat file draft, preview, dan hasil publish tetap terbaca jelas.
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

        // Setelah publish selesai, Admin/TU dikembalikan ke arsip semua surat dengan pesan sukses.
        return redirect()
            ->route('admin.semua-surat')
            ->with('status', 'Dokumen berhasil dipublish.');
    }

    // Helper ini memastikan token QR unik, tetapi mempertahankan token lama jika dokumen sudah pernah memilikinya.
    protected function resolveVerificationToken(Dokumen $dokumen): string
    {
        if (filled($dokumen->verification_token)) {
            return $dokumen->verification_token;
        }

        do {
            $token = Str::random(40);
        } while (Dokumen::query()->where('verification_token', $token)->exists());

        return $token;
    }

    // Helper ini memilih file terbaik untuk dipreview atau diunduh dari kumpulan file dokumen.
    protected function resolvePreviewablePdf(Dokumen $dokumen): ?DokumenFile
    {
        // Urutan prioritas menjaga agar file FINAL_PDF dipakai jika sudah tersedia, lalu fallback ke preview/proses.
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

    // Helper ini memilih sumber PDF yang akan disalin menjadi file final saat publish.
    protected function resolvePublishSourcePdf(Dokumen $dokumen): ?DokumenFile
    {
        // FINAL_PDF dibuat dari PDF hasil pemeriksaan agar overlay nomor, tanggal, verifikator, dan QR asli tidak dobel.
        foreach (['HASIL_PEMERIKSAAN_PDF', 'PREVIEW_VERIFIKASI_PDF'] as $fileType) {
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
}
