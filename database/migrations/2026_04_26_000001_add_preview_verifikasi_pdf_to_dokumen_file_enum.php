<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE dokumen_file
            MODIFY file_type ENUM(
                'DRAFT_DOCX',
                'HASIL_PEMERIKSAAN_PDF',
                'PREVIEW_VERIFIKASI_PDF',
                'FINAL_PDF',
                'LAMPIRAN'
            ) NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('dokumen_file')
            ->where('file_type', 'PREVIEW_VERIFIKASI_PDF')
            ->update(['file_type' => 'HASIL_PEMERIKSAAN_PDF']);

        DB::statement("
            ALTER TABLE dokumen_file
            MODIFY file_type ENUM(
                'DRAFT_DOCX',
                'HASIL_PEMERIKSAAN_PDF',
                'FINAL_PDF',
                'LAMPIRAN'
            ) NOT NULL
        ");
    }
};
