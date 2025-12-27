<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('tenaga_pendidik')->onDelete('cascade');
            $table->string('judul_tugas');
            $table->text('deskripsi');
            $table->string('file_tugas')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_deadline');
            $table->timestamps();
        });

        // Tabel pivot untuk pengumpulan tugas siswa
        Schema::create('tugas_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->string('file_jawaban')->nullable();
            $table->text('jawaban_text')->nullable();
            $table->timestamp('tanggal_submit')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->enum('status', ['belum_dikerjakan', 'dikerjakan', 'terlambat', 'dinilai'])->default('belum_dikerjakan');
            $table->text('feedback_guru')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_siswa');
        Schema::dropIfExists('tugas');
    }
};