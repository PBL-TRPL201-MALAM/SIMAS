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
        Schema::create('sk_menimbang', function (Blueprint $table) {
            $table->id('sk_menimbang_id');
            $table->foreignId('sk_id')->constrained('surat_keputusan', 'sk_id')->cascadeOnDelete();
            $table->unsignedInteger('urutan');
            $table->text('isi_menimbang');
            $table->timestamps();

            $table->unique(['sk_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_menimbang');
    }
};
