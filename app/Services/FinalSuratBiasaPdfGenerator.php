<?php

namespace App\Services;

use App\Models\Dokumen;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

// Service ini khusus membuat FINAL_PDF Surat Biasa saat Admin Surat mem-publish dokumen.
class FinalSuratBiasaPdfGenerator
{
    // Final memakai teks hitam dan QR validasi asli agar PDF resmi bisa dipindai publik.
    private const FINAL_OVERLAY_TEXT_COLOR = '0 0 0';

    public function __construct(
        protected PdfOverlayService $pdfOverlayService,
        protected QrCodePdfService $qrCodePdfService
    ) {
    }

    // Method publik ini tetap bernama generateFinal() karena dipakai untuk membuat FINAL_PDF.
    public function generateFinal(Dokumen $dokumen, string $sourceRelativePath, string $validationUrl, string $outputPath): string
    {
        $dokumen->loadMissing([
            'suratBiasa',
            'posisiElemenDokumen',
            'verifikasi.verifikator',
        ]);

        $disk = Storage::disk('local');

        if (! $disk->exists($sourceRelativePath)) {
            throw new RuntimeException('File PDF sumber tidak ditemukan.');
        }

        $sourcePdf = $disk->get($sourceRelativePath);
        // FINAL_PDF memakai aturan multi-page yang sama dengan preview: semua halaman asli dipertahankan, overlay hanya pada halaman tersimpan.
        $positions = $this->pdfOverlayService->groupPositionsByPage($dokumen->posisiElemenDokumen);

        if ($positions->isEmpty()) {
            throw new RuntimeException('Posisi elemen dokumen belum tersedia.');
        }

        // FINAL_PDF wajib memakai QR asli yang berisi URL route verifikasi.public dari APP_URL.
        $pdfWithFinalQr = $this->pdfOverlayService->appendSuratBiasaOverlays(
            $sourcePdf,
            $dokumen,
            $positions,
            self::FINAL_OVERLAY_TEXT_COLOR,
            fn (float $x, float $y, float $width, float $height): string => $this->qrCodePdfService->validationBlackCommands($validationUrl, $x, $y, $width, $height)
        );

        $disk->put($outputPath, $pdfWithFinalQr);

        return $outputPath;
    }
}
