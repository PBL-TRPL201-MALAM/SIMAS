<?php

namespace App\Services;

use App\Models\Dokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

// Service ini khusus membuat FINAL_PDF Surat Keputusan dari data database.
// SK tidak memakai overlay Surat Biasa karena seluruh isi dokumen dirender dari Blade.
class SuratKeputusanPdfGenerator
{
    public function __construct(
        protected QrCodePdfService $qrCodePdfService
    ) {
    }

    // Method publik ini membuat PDF final SK dan menyimpannya ke disk public.
    public function generateFinal(Dokumen $dokumen, string $validationUrl, string $outputPath): string
    {
        $dokumen->loadMissing([
            'pemohon',
            'penandatangan',
            'suratKeputusan.skMenimbang',
            'suratKeputusan.skDasarHukum.dasarHukum',
            'suratKeputusan.skMemutuskan',
        ]);

        $sk = $dokumen->suratKeputusan;

        if (! $sk) {
            throw new RuntimeException('Data Surat Keputusan tidak ditemukan.');
        }

        if (! $sk->nomor_sk || ! $sk->tanggal_sk || ! $dokumen->penandatangan) {
            throw new RuntimeException('Nomor, tanggal, atau penandatangan SK belum lengkap.');
        }

        $pdf = Pdf::loadView('pdf.surat-keputusan', [
            'dokumen' => $dokumen,
            'sk' => $sk,
            'penandatangan' => $dokumen->penandatangan,
            'qrDataUri' => $this->qrCodePdfService->validationSvgDataUri($validationUrl, 320),
            'tanggalSkLabel' => $sk->tanggal_sk->translatedFormat('d F Y'),
            'tentangUpper' => Str::upper((string) ($sk->tentang ?: $sk->judul_sk)),
            'menetapkanText' => $this->menetapkanText($sk->judul_sk, $sk->tentang),
            'menimbangItems' => $this->menimbangItems($sk->skMenimbang),
            'mengingatItems' => $this->mengingatItems($sk->skDasarHukum),
            'memutuskanItems' => $this->memutuskanItems($sk->skMemutuskan),
        ])->setPaper('a4', 'portrait');

        Storage::disk('public')->put($outputPath, $pdf->output());

        return $outputPath;
    }

    private function menetapkanText(?string $judulSk, ?string $tentang): string
    {
        if (filled($judulSk)) {
            return (string) $judulSk;
        }

        return 'Keputusan Direktur Politeknik Negeri Batam tentang ' . Str::lower((string) $tentang);
    }

    private function menimbangItems(Collection $items): Collection
    {
        return $items
            ->sortBy('urutan')
            ->pluck('isi_menimbang')
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();
    }

    private function mengingatItems(Collection $items): Collection
    {
        return $items
            ->sortBy('urutan')
            ->map(fn ($item) => $item->dasarHukum?->labelMengingat())
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();
    }

    private function memutuskanItems(Collection $items): Collection
    {
        return $items
            ->sortBy('urutan')
            ->pluck('isi_memutuskan')
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();
    }
}
