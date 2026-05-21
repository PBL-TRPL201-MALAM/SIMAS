<?php

namespace App\Support;

use App\Models\Dokumen;
use Illuminate\Support\Str;

// Helper ini khusus membentuk nama file PDF saat user/admin mengunduh surat.
// Dengan helper terpisah, aturan nama file konsisten di Pemohon, Admin Surat, dan Verifikator.
class SuratPdfDownloadName
{
    // Helper ini memisahkan nama file unduhan dari nama file storage agar hasil download selalu rapi untuk pengguna.
    public static function forDokumen(Dokumen $dokumen): string
    {
        // loadMissing hanya mengambil relasi suratBiasa jika belum diload, sehingga aman dipanggil dari berbagai controller.
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
