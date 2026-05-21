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
        Schema::create('dasar_hukum', function (Blueprint $table) {
            $table->id('dasar_hukum_id');
            // Struktur master dibuat sederhana agar Admin cukup mengisi teks dasar hukum yang siap dipakai pada bagian Mengingat SK.
            $table->string('judul_hukum');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dasar_hukum');
    }
};
