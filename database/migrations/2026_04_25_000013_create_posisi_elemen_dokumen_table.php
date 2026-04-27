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
        Schema::create('posisi_elemen_dokumen', function (Blueprint $table) {
            $table->id('posisi_id');
            $table->foreignId('dokumen_id')->constrained('dokumen', 'dokumen_id')->cascadeOnDelete();
            $table->enum('elemen', ['nomor_surat', 'tanggal_surat', 'tte']);
            $table->unsignedInteger('halaman')->default(1);
            $table->decimal('posisi_x', 10, 2);
            $table->decimal('posisi_y', 10, 2);
            $table->decimal('lebar', 10, 2)->nullable();
            $table->decimal('tinggi', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['dokumen_id', 'elemen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posisi_elemen_dokumen');
    }
};
