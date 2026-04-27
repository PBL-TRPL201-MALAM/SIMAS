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
                'PDF_REVIEW',
                'HASIL_PEMERIKSAAN_PDF',
                'PDF_FINAL',
                'FINAL_PDF',
                'LAMPIRAN'
            ) NOT NULL
        ");

        DB::table('dokumen_file')
            ->where('file_type', 'PDF_REVIEW')
            ->update(['file_type' => 'HASIL_PEMERIKSAAN_PDF']);

        DB::table('dokumen_file')
            ->where('file_type', 'PDF_FINAL')
            ->update(['file_type' => 'FINAL_PDF']);

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE dokumen_file
            MODIFY file_type ENUM(
                'DRAFT_DOCX',
                'PDF_REVIEW',
                'HASIL_PEMERIKSAAN_PDF',
                'PDF_FINAL',
                'FINAL_PDF',
                'LAMPIRAN'
            ) NOT NULL
        ");

        DB::table('dokumen_file')
            ->where('file_type', 'HASIL_PEMERIKSAAN_PDF')
            ->update(['file_type' => 'PDF_REVIEW']);

        DB::table('dokumen_file')
            ->where('file_type', 'FINAL_PDF')
            ->update(['file_type' => 'PDF_FINAL']);

        DB::statement("
            ALTER TABLE dokumen_file
            MODIFY file_type ENUM(
                'DRAFT_DOCX',
                'PDF_REVIEW',
                'PDF_FINAL',
                'LAMPIRAN'
            ) NOT NULL
        ");
    }
};
