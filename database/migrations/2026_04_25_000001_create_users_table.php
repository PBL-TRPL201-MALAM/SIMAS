<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('nama', 150);
            $table->string('username', 100)->unique();
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['SUPER_ADMIN', 'ADMIN_SURAT', 'PEMOHON', 'VERIFIKATOR']);
            $table->enum('unit_kerja', [
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
            ])->nullable();
            $table->string('nip_nik', 50)->nullable();
            $table->enum('jabatan', [
                'Direktur',
                'Wakil Direktur I',
                'Wakil Direktur II',
                'Wakil Direktur III',
                'Kepala Jurusan',
                'Sekretaris Jurusan',
                'Koordinator Program Studi',
                'Dosen',
                'Staff TU',
            ])->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
