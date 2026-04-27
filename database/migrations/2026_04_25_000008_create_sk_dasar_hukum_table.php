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
        Schema::create('sk_dasar_hukum', function (Blueprint $table) {
            $table->id('sk_dasar_hukum_id');
            $table->foreignId('sk_id')->constrained('surat_keputusan', 'sk_id')->cascadeOnDelete();
            $table->foreignId('dasar_hukum_id')->constrained('dasar_hukum', 'dasar_hukum_id')->restrictOnDelete();
            $table->unsignedInteger('urutan');
            $table->timestamps();

            $table->unique(['sk_id', 'dasar_hukum_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_dasar_hukum');
    }
};
