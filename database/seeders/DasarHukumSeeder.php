<?php

namespace Database\Seeders;

use App\Models\DasarHukum;
use Illuminate\Database\Seeder;

class DasarHukumSeeder extends Seeder
{
    public function run(): void
    {
        $dasarHukumList = [
            [
                'judul_hukum' => 'Undang-Undang Nomor 20 Tahun 2003',
                'keterangan' => 'tentang Sistem Pendidikan Nasional',
            ],
            [
                'judul_hukum' => 'Undang-Undang Nomor 12 Tahun 2012',
                'keterangan' => 'tentang Pendidikan Tinggi',
            ],
            [
                'judul_hukum' => 'Peraturan Menteri Pendidikan Nasional Nomor 26 Tahun 2010',
                'keterangan' => 'tentang Pendirian, Organisasi, dan Tata Kerja Politeknik Negeri Batam',
            ],
            [
                'judul_hukum' => 'Peraturan Menteri Riset, Teknologi, dan Pendidikan Tinggi Nomor 41 Tahun 2016',
                'keterangan' => 'tentang Statuta Politeknik Negeri Batam',
            ],
            [
                'judul_hukum' => 'Keputusan Menteri Pendidikan dan Kebudayaan Nomor 62067/MPK/RHS/KP/2020',
                'keterangan' => 'tentang Pengangkatan Direktur Politeknik Negeri Batam Periode Tahun 2020-2024',
            ],
        ];

        foreach ($dasarHukumList as $dasarHukum) {
            // Seeder memakai judul_hukum sebagai kunci agar data contoh tidak dobel saat seeder dijalankan ulang.
            DasarHukum::query()->updateOrCreate(
                ['judul_hukum' => $dasarHukum['judul_hukum']],
                [
                    'keterangan' => $dasarHukum['keterangan'],
                    // Semua contoh dasar hukum SK dibuat aktif agar langsung muncul pada pilihan Mengingat.
                    'is_active' => true,
                ]
            );
        }
    }
}
