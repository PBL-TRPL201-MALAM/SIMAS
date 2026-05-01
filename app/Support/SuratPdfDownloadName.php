<?php

namespace App\Support;

use App\Models\Dokumen;
use Illuminate\Support\Str;

class SuratPdfDownloadName
{
    public static function forDokumen(Dokumen $dokumen): string
    {
        $dokumen->loadMissing('suratBiasa');

        $nomorSurat = (string) ($dokumen->suratBiasa?->nomor_surat ?: 'tanpa-nomor');
        $nomorSurat = str_replace(['/', '\\', ' '], '-', $nomorSurat);
        $nomorSurat = preg_replace('/-+/', '-', $nomorSurat) ?? 'tanpa-nomor';
        $nomorSurat = trim($nomorSurat, '-');
        $nomorSurat = Str::limit($nomorSurat !== '' ? $nomorSurat : 'tanpa-nomor', 120, '');

        return 'SIMAS-Surat-' . $nomorSurat . '.pdf';
    }
}
