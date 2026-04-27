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
        Schema::table('surat_biasa', function (Blueprint $table) {
            $table->string('penandatangan', 150)->nullable()->after('tanggal_surat');
            $table->string('unit_kerja', 150)->nullable()->after('catatan_admin');
            $table->text('catatan')->nullable()->after('unit_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_biasa', function (Blueprint $table) {
            $table->dropColumn(['penandatangan', 'unit_kerja', 'catatan']);
        });
    }
};
