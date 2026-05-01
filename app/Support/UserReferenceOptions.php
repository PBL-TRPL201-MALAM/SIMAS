<?php

namespace App\Support;

class UserReferenceOptions
{
    /**
     * @return array<int, string>
     */
    public static function roles(): array
    {
        return ['SUPER_ADMIN', 'ADMIN_TU', 'PEMOHON', 'VERIFIKATOR'];
    }

    /**
     * @return array<int, string>
     */
    public static function jabatans(): array
    {
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
        return [
            'Direktur',
            'Wakil Direktur I',
            'Wakil Direktur II',
            'Wakil Direktur III',
            'Kepala Jurusan',
        ];
    }
}
