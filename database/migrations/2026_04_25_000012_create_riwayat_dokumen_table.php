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
        Schema::create('riwayat_dokumen', function (Blueprint $table) {
            $table->id('riwayat_id');
            $table->foreignId('dokumen_id')->constrained('dokumen', 'dokumen_id')->cascadeOnDelete();
            $table->string('aksi', 100);
            $table->string('status_lama', 50)->nullable();
            $table->string('status_baru', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('actor_id')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_dokumen');
    }
};
