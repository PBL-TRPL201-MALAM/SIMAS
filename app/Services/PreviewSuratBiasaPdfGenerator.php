<?php

namespace App\Services;

use App\Models\Dokumen;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

// Service ini khusus membuat PREVIEW_VERIFIKASI_PDF untuk Surat Biasa sebelum dokumen dibaca verifikator.
class PreviewSuratBiasaPdfGenerator
{
    // Preview memakai teks merah dan QR dummy merah agar Admin Surat/verifikator tahu file ini belum final.
    private const PREVIEW_OVERLAY_TEXT_COLOR = '1 0 0';

    public function __construct(
        protected PdfOverlayService $pdfOverlayService,
        protected QrCodePdfService $qrCodePdfService
    ) {
    }

    // Method publik ini tetap bernama generate() karena dipakai untuk membuat PREVIEW_VERIFIKASI_PDF.
    public function generate(Dokumen $dokumen, string $sourceRelativePath): string
    {
        // Relasi diload agar generator punya metadata surat, posisi elemen, dan daftar verifikator tanpa query manual di controller.
        $dokumen->loadMissing([
            'suratBiasa',
            'posisiElemenDokumen',
            'verifikasi.verifikator',
        ]);

        // Generator ini tidak merender HTML; ia menempelkan konten tambahan langsung ke PDF sumber yang sudah ada.
        $disk = Storage::disk('public');

        if (! $disk->exists($sourceRelativePath)) {
            throw new RuntimeException('File PDF sumber tidak ditemukan.');
        }

        $sourcePdf = $disk->get($sourceRelativePath);
        // Multi-page PDF: posisi dikelompokkan per nomor halaman agar overlay hanya ditempel ke halaman yang dipilih Admin Surat.
        $positions = $this->pdfOverlayService->groupPositionsByPage($dokumen->posisiElemenDokumen);

        if ($positions->isEmpty()) {
            throw new RuntimeException('Posisi elemen dokumen belum tersedia.');
        }

        // PREVIEW_VERIFIKASI_PDF memakai nomor/tanggal merah dan QR dummy merah, sesuai perilaku lama.
        $pdfWithPreview = $this->pdfOverlayService->appendSuratBiasaOverlays(
            $sourcePdf,
            $dokumen,
            $positions,
            self::PREVIEW_OVERLAY_TEXT_COLOR,
            fn (float $x, float $y, float $width, float $height): string => $this->qrCodePdfService->dummyRedCommands($x, $y, $width, $height)
        );
        $outputPath = 'dokumen/preview-verifikasi/' . pathinfo($sourceRelativePath, PATHINFO_FILENAME) . '-preview.pdf';

        $disk->put($outputPath, $pdfWithPreview);

        return $outputPath;
    }
}
