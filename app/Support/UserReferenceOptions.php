<?php

namespace App\Support;

// Helper ini menyimpan daftar referensi statis untuk user dan surat.
// Controller dan validasi memanggil helper yang sama agar pilihan di UI tidak berbeda dengan aturan backend.
class UserReferenceOptions
{
    // Helper ini menjadi sumber tunggal pilihan referensi agar dropdown, validasi, dan enum database tetap sejalan.
    /**
     * @return array<int, string>
     */
    public static function roles(): array
    {
        // Daftar role ini dipakai oleh validasi user dan pembagian akses route.
        return ['SUPER_ADMIN', 'ADMIN_SURAT', 'PEMOHON', 'VERIFIKATOR', 'PENANDATANGAN'];
    }

    /**
     * @return array<int, string>
     */
    public static function jabatans(): array
    {
        // Jabatan dipakai pada data profil user dan filter calon penandatangan.
        return [
            'Direktur',
            'Wakil Direktur I',
            'Wakil Direktur II',
            'Wakil Direktur III',
            'Kepala Jurusan',
            'Sekretaris Jurusan',
            'Koordinator Program Studi',
            'Dosen',
            'Staff TU',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function unitKerjas(): array
    {
        // Unit kerja menjadi pilihan standar agar penulisan unit tidak berbeda-beda antar user.
        return [
            'Direktorat',
            'Akademik',
            'Kemahasiswaan',
            'Keuangan',
            'Tata Usaha',
            'Jurusan Teknik Informatika',
            'Jurusan Teknik Elektro',
            'Jurusan Teknik Mesin',
            'Jurusan Manajemen Bisnis',
            'Program Studi TRPL',
            'Program Studi Informatika',
            'Program Studi Teknik Multimedia dan Jaringan',
            'Program Studi Animasi',
            'Program Studi Akuntansi',
            'Program Studi Administrasi Bisnis',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function jenisSuratBiasa(): array
    {
        // Jenis surat biasa dipakai pada metadata surat yang dilengkapi Admin Surat.
        return [
            'Surat Undangan',
            'Surat Tugas',
            'Surat Pengantar',
            'Surat Pernyataan',
            'Surat Keterangan',
            'Surat Rekomendasi',
            'Surat Perintah',
            'Surat Kuasa',
            'Berita Acara',
            'Pengumuman',
            'Sertifikat',
            'Nota Dinas',
            'Surat Pernyataan Rencana Penempatan',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function signerJabatans(): array
    {
        // Daftar ini dipakai untuk memfilter siapa saja yang sah dipilih sebagai penandatangan final surat.
        return [
            'Direktur',
            'Wakil Direktur I',
            'Wakil Direktur II',
            'Wakil Direktur III',
            'Kepala Jurusan',
        ];
    }
}
