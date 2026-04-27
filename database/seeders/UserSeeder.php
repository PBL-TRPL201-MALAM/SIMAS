<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();

        DB::table('users')->upsert([
            [
                'user_id' => 1,
                'nama' => 'Super Admin SIMAS',
                'username' => 'superadmin',
                'email' => 'superadmin@gmail.com',
                'email_verified_at' => $now,
                'password' => bcrypt('12345678'),
                'remember_token' => null,
                'role' => 'SUPER_ADMIN',
                'unit_kerja' => 'Administrasi Sistem',
                'nip_nik' => null,
                'jabatan' => 'Super Admin',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'nama' => 'Admin TU SIMAS',
                'username' => 'admintu',
                'email' => 'admintu@gmail.com',
                'email_verified_at' => $now,
                'password' => bcrypt('12345678'),
                'remember_token' => null,
                'role' => 'ADMIN_TU',
                'unit_kerja' => 'Tata Usaha',
                'nip_nik' => null,
                'jabatan' => 'Admin TU',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 3,
                'nama' => 'Pemohon SIMAS',
                'username' => 'pemohon',
                'email' => 'pemohon@gmail.com',
                'email_verified_at' => $now,
                'password' => bcrypt('12345678'),
                'remember_token' => null,
                'role' => 'PEMOHON',
                'unit_kerja' => 'Unit Pemohon',
                'nip_nik' => null,
                'jabatan' => 'Pemohon',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 4,
                'nama' => 'Verifikator SIMAS',
                'username' => 'verifikator',
                'email' => 'verifikator@gmail.com',
                'email_verified_at' => $now,
                'password' => bcrypt('12345678'),
                'remember_token' => null,
                'role' => 'VERIFIKATOR',
                'unit_kerja' => 'Unit Verifikasi',
                'nip_nik' => null,
                'jabatan' => 'Verifikator',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['user_id'], [
            'nama',
            'username',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'role',
            'unit_kerja',
            'nip_nik',
            'jabatan',
            'is_active',
            'updated_at',
        ]);
    }
}
