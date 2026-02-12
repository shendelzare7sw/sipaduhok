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
        Schema::table('rapor', function (Blueprint $table) {
            // Add PDF upload capability
            $table->string('uploaded_pdf_path')->nullable()
                  ->after('tanggal_terbit')
                  ->comment('Path to uploaded PDF file (for TK-SD manual upload mode)');

            // Input mode: auto-generate from nilai OR upload PDF
            $table->enum('input_mode', ['auto_generate', 'upload_pdf'])
                  ->default('auto_generate')
                  ->after('uploaded_pdf_path')
                  ->comment('How rapor was created: auto from nilai or manual PDF upload');

            // Download access control
            $table->boolean('allow_download')->default(false)
                  ->after('input_mode')
                  ->comment('Toggle to allow parents/students to download PDF');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            $table->dropColumn([
                'uploaded_pdf_path',
                'input_mode',
                'allow_download',
            ]);
        });
    }
};
