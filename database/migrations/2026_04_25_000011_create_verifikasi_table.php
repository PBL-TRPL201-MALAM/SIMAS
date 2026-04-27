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
        Schema::create('verifikasi', function (Blueprint $table) {
            $table->id('verifikasi_id');
            $table->foreignId('dokumen_id')->constrained('dokumen', 'dokumen_id')->cascadeOnDelete();
            $table->foreignId('verifikator_id')->constrained('users', 'user_id')->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->enum('status_verifikasi', ['MENUNGGU', 'DISETUJUI', 'DITOLAK'])->default('MENUNGGU');
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['dokumen_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi');
    }
};
