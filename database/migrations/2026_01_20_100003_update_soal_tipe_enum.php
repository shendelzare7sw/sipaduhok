<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Expand tipe_soal to include 4 new formats
        DB::statement("ALTER TABLE soal_ujian MODIFY COLUMN tipe_soal ENUM(
            'pilihan_ganda', 'essay',
            'pilihan_ganda_kompleks',
            'benar_salah',
            'isian_singkat',
            'uraian'
        ) NOT NULL DEFAULT 'pilihan_ganda'");

        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->integer('urutan')->nullable()->after('ujian_id');
            $table->text('kunci_jawaban')->nullable()->after('jawaban_benar'); // For essay/uraian grading guide
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE soal_ujian MODIFY COLUMN tipe_soal ENUM('pilihan_ganda', 'essay') NOT NULL DEFAULT 'pilihan_ganda'");

        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->dropColumn(['urutan', 'kunci_jawaban']);
        });
    }
};
