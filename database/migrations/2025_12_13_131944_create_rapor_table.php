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
            $table->text('catatan_wali_kelas')->nullable();
            $table->integer('jumlah_sakit')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->integer('jumlah_alpha')->default(0);
            $table->enum('status', ['draft', 'diterbitkan'])->default('draft');
            $table->date('tanggal_terbit')->nullable();
            $table->timestamps();
        });

        // Detail nilai per mapel di rapor
        Schema::create('rapor_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapor')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('nilai_id')->constrained('nilai')->onDelete('cascade');
            $table->decimal('nilai_angka', 5, 2);
            $table->string('nilai_huruf', 2); // A, B, C, D, E
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_nilai');
        Schema::dropIfExists('rapor');
    }
};