<?php

namespace App\Support;

use App\Models\Dokumen;
use Illuminate\Support\Str;

class SuratPdfDownloadName
{
    // Helper ini memisahkan nama file unduhan dari nama file storage agar hasil download selalu rapi untuk pengguna.
    public static function forDokumen(Dokumen $dokumen): string
    {
        $dokumen->loadMissing('suratBiasa');

        // Nomor surat dibersihkan lebih dulu supaya aman dipakai sebagai nama file pada semua sistem operasi.
        $nomorSurat = (string) ($dokumen->suratBiasa?->nomor_surat ?: 'tanpa-nomor');
        $nomorSurat = str_replace(['/', '\\', ' '], '-', $nomorSurat);
        $nomorSurat = preg_replace('/-+/', '-', $nomorSurat) ?? 'tanpa-nomor';
        $nomorSurat = trim($nomorSurat, '-');
        $nomorSurat = Str::limit($nomorSurat !== '' ? $nomorSurat : 'tanpa-nomor', 120, '');

        return 'SIMAS-Surat-' . $nomorSurat . '.pdf';
    }
}
