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
        Schema::create('surat_keputusan', function (Blueprint $table) {
            $table->id('sk_id');
            $table->foreignId('dokumen_id')->unique()->constrained('dokumen', 'dokumen_id')->cascadeOnDelete();
            $table->string('nomor_sk', 100)->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->string('judul_sk', 255);
            $table->text('tentang')->nullable();
            $table->string('tempat_penetapan', 150)->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keputusan');
    }
};
