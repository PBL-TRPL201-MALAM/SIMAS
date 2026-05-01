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
        Schema::create('surat_biasa', function (Blueprint $table) {
            $table->id('surat_biasa_id');
            $table->foreignId('dokumen_id')->unique()->constrained('dokumen', 'dokumen_id')->cascadeOnDelete();
            $table->enum('jenis_surat', [
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
            ])->nullable();
            $table->string('nomor_surat', 100)->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('hal', 255)->nullable();
            $table->text('ringkasan_isi')->nullable();
            $table->string('sifat_surat', 100)->nullable();
            $table->string('lampiran', 150)->nullable();
            $table->text('kepada_tujuan')->nullable();
            $table->text('tembusan')->nullable();
            $table->text('keterangan_tambahan')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_biasa');
    }
};
