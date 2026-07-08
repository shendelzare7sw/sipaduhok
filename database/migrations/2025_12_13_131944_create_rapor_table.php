<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->enum('jenis_rapor', ['tengah_semester', 'akhir_semester'])->default('akhir_semester');
            $table->text('catatan_wali_kelas')->nullable();
            $table->string('catatan_alignment', 12)->default('center');
            $table->string('deskripsi_alignment', 12)->default('left');
            $table->string('keterangan_ekstra_alignment', 12)->default('left');
            $table->integer('jumlah_sakit')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->integer('jumlah_alpha')->default(0);
            $table->enum('status', ['draft', 'diterbitkan'])->default('draft');
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_rilis')->nullable();
            $table->text('catatan_revisi_ketua')->nullable();
            $table->enum('status_review_ketua', ['pending', 'perlu_revisi', 'disetujui'])->default('pending');
            $table->string('uploaded_pdf_path')->nullable()
                ->comment('Path to uploaded PDF file (for TK-SD manual upload mode)');
            $table->enum('input_mode', ['auto_generate', 'upload_pdf'])->default('auto_generate')
                ->comment('How rapor was created: auto from nilai or manual PDF upload');
            $table->boolean('allow_download')->default(false)
                ->comment('Toggle to allow parents/students to download PDF');
            $table->timestamps();
        });

        // Detail nilai per mapel di rapor
        Schema::create('rapor_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapor')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('nilai_id')->constrained('nilai')->onDelete('cascade');
            $table->decimal('nilai_angka', 5, 2);
            $table->string('nilai_huruf', 2);
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->string('kelompok_override', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_nilai');
        Schema::dropIfExists('rapor');
    }
};
