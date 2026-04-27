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
        Schema::create('dokumen_file', function (Blueprint $table) {
            $table->id('file_id');
            $table->foreignId('dokumen_id')->constrained('dokumen', 'dokumen_id')->cascadeOnDelete();
            $table->enum('file_type', ['DRAFT_DOCX', 'PDF_REVIEW', 'PDF_FINAL', 'LAMPIRAN']);
            $table->string('file_name', 255);
            $table->text('file_path');
            $table->foreignId('uploaded_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_file');
    }
};
