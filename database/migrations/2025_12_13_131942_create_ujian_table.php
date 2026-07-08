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
            $table->enum('tipe_ujian', [
                'harian', 'uts', 'uas', 'ulangan_harian', 'kuis', 'latihan',
                'pts_ganjil', 'pas_ganjil', 'pts_genap', 'pas_genap',
                'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek',
            ])->default('ulangan_harian');
            $table->integer('urutan')->nullable();
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->integer('durasi_menit');
            $table->boolean('is_active')->default(true);
            $table->boolean('tampilkan_nilai')->default(false);
            $table->boolean('bisa_diulang')->default(false);
            $table->integer('batas_pengulangan')->nullable();
            $table->boolean('tampilkan_riwayat')->default(false);
            $table->timestamps();
        });

        // Tabel untuk soal ujian
        Schema::create('soal_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->onDelete('cascade');
            $table->text('narasi')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('urutan')->nullable();
            $table->text('pertanyaan');
            $table->enum('tipe_soal', [
                'pilihan_ganda', 'essay', 'pilihan_ganda_kompleks',
                'benar_salah', 'isian_singkat', 'uraian',
            ])->default('pilihan_ganda');
            $table->integer('jumlah_pilihan')->default(5);
            $table->json('pilihan_jawaban')->nullable();
            $table->string('jawaban_benar')->nullable();
            $table->text('kunci_jawaban')->nullable();
            $table->integer('bobot_nilai')->default(1);
            $table->timestamps();
        });

        // Sesi pengerjaan ujian per siswa (termasuk data pengawasan)
        Schema::create('ujian_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('current_soal_ujian_id')->nullable()->constrained('soal_ujian')->onDelete('set null');
            $table->unsignedInteger('current_nomor_soal')->nullable();
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->integer('pengulangan_ke')->default(1);
            $table->decimal('nilai_terbaik', 5, 2)->nullable();
            $table->unsignedInteger('answered_count')->default(0);
            $table->unsignedInteger('doubt_count')->default(0);
            $table->unsignedInteger('visited_count')->default(0);
            $table->unsignedInteger('focus_lost_count')->default(0);
            $table->unsignedInteger('focus_lost_total_seconds')->default(0);
            $table->timestamp('active_focus_lost_at')->nullable();
            $table->enum('status', ['belum_mulai', 'sedang_mengerjakan', 'selesai', 'dinilai'])->default('belum_mulai');
            $table->timestamps();
        });

        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_siswa_id')->constrained('ujian_siswa')->onDelete('cascade');
            $table->foreignId('soal_ujian_id')->constrained('soal_ujian')->onDelete('cascade');
            $table->text('jawaban');
            $table->decimal('nilai_soal', 5, 2)->nullable();
            $table->text('feedback')->nullable();
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
