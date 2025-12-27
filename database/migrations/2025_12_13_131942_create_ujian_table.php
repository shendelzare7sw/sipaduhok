<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('tenaga_pendidik')->onDelete('cascade');
            $table->string('judul_ujian');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_ujian', ['harian', 'uts', 'uas']);
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->integer('durasi_menit');
            $table->timestamps();
        });

        // Tabel untuk soal ujian
        Schema::create('soal_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->enum('tipe_soal', ['pilihan_ganda', 'essay']);
            $table->json('pilihan_jawaban')->nullable(); // untuk pilihan ganda
            $table->string('jawaban_benar')->nullable(); // untuk pilihan ganda (A/B/C/D)
            $table->integer('bobot_nilai')->default(1);
            $table->timestamps();
        });

        // Tabel untuk jawaban siswa
        Schema::create('ujian_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->enum('status', ['belum_mulai', 'sedang_mengerjakan', 'selesai', 'dinilai'])->default('belum_mulai');
            $table->timestamps();
        });

        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_siswa_id')->constrained('ujian_siswa')->onDelete('cascade');
            $table->foreignId('soal_ujian_id')->constrained('soal_ujian')->onDelete('cascade');
            $table->text('jawaban');
            $table->decimal('nilai_soal', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswa');
        Schema::dropIfExists('ujian_siswa');
        Schema::dropIfExists('soal_ujian');
        Schema::dropIfExists('ujian');
    }
};