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
            $table->text('template_text')
                  ->comment('Reusable competency achievement description text');
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')->onDelete('set null')
                  ->comment('User who created this template (pustaka private per wali)');
            $table->timestamps();
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
