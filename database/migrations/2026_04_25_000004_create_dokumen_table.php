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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('dokumen_id');
            $table->enum('jenis_dokumen', ['SURAT_BIASA', 'SURAT_KEPUTUSAN']);
            $table->foreignId('pemohon_id')->constrained('users', 'user_id')->restrictOnDelete();
            $table->foreignId('penandatangan_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->nullOnDelete();
            $table->enum('status_dokumen', [
                'DIAJUKAN',
                'DIPROSES',
                'MENUNGGU_VERIFIKASI',
                'DISETUJUI',
                'DITOLAK',
                'PERLU_REVISI',
                'SIAP_PUBLISH',
                'PUBLISHED',
            ])->default('DIAJUKAN');
            $table->string('verification_token', 191)->nullable()->unique();
            $table->timestamp('published_at')->nullable();
            $table->text('file_final_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
