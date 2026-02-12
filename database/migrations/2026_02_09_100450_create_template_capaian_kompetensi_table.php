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
        Schema::create('template_capaian_kompetensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')
                  ->constrained('mata_pelajaran')->onDelete('cascade')
                  ->comment('FK to mata_pelajaran table');
            $table->string('nama_template', 100)
                  ->comment('Template name: Sangat Baik, Baik, Cukup, Kurang');
            $table->text('template_text')
                  ->comment('Reusable competency achievement description text');
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')->onDelete('set null')
                  ->comment('User who created this template');
            $table->timestamps();

            // Index for faster lookups by subject (custom name to avoid MySQL 64 char limit)
            $table->index(['mata_pelajaran_id', 'nama_template'], 'idx_template_mapel_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_capaian_kompetensi');
    }
};
