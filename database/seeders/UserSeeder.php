<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
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
                'password' => Hash::make('12345678'),
                'remember_token' => null,
                'role' => 'SUPER_ADMIN',
                'unit_kerja' => 'Direktorat',
                'nip_nik' => null,
                'jabatan' => 'Staff TU',
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
                'password' => Hash::make('12345678'),
                'remember_token' => null,
                'role' => 'ADMIN_TU',
                'unit_kerja' => 'Tata Usaha',
                'nip_nik' => null,
                'jabatan' => 'Staff TU',
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
                'password' => Hash::make('12345678'),
                'remember_token' => null,
                'role' => 'PEMOHON',
                'unit_kerja' => 'Program Studi TRPL',
                'nip_nik' => null,
                'jabatan' => 'Dosen',
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
                'password' => Hash::make('12345678'),
                'remember_token' => null,
                'role' => 'VERIFIKATOR',
                'unit_kerja' => 'Akademik',
                'nip_nik' => '199001012020121001',
                'jabatan' => 'Dosen',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 5,
                'nama' => 'Direktur Polibatam',
                'username' => 'direktur',
                'email' => 'direktur@gmail.com',
                'email_verified_at' => $now,
                'password' => Hash::make('12345678'),
                'remember_token' => null,
                'role' => 'VERIFIKATOR',
                'unit_kerja' => 'Direktorat',
                'nip_nik' => '197706252012121003',
                'jabatan' => 'Direktur',
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