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
            $table->string('jenis_peraturan', 100);
            $table->string('nomor_peraturan', 100)->nullable();
            $table->string('tahun_peraturan', 10)->nullable();
            $table->text('judul_peraturan');
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
