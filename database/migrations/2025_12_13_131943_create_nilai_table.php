<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->foreignId('guru_id')->constrained('tenaga_pendidik')->onDelete('cascade');

            // Komponen nilai (hasil perhitungan/aktif)
            $scoreColumns = [
                'tugas_1', 'tugas_2', 'tugas_3', 'tugas_4', 'tugas_5', 'rata_tugas',
                'latihan_1', 'latihan_2', 'latihan_3', 'latihan_4', 'latihan_5', 'rata_latihan',
                'uh_1', 'uh_2', 'uh_3', 'uh_4', 'uh_5', 'rata_uh',
                'pts', 'pas', 'nilai_akhir', 'keterampilan',
                'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek',
            ];
            foreach ($scoreColumns as $col) {
                $table->decimal($col, 5, 2)->nullable();
            }

            // Snapshot input asli guru (untuk audit edit oleh wali kelas)
            $snapshotColumns = [
                'tugas_1_guru', 'tugas_2_guru', 'tugas_3_guru', 'tugas_4_guru', 'tugas_5_guru',
                'latihan_1_guru', 'latihan_2_guru', 'latihan_3_guru', 'latihan_4_guru', 'latihan_5_guru',
                'uh_1_guru', 'uh_2_guru', 'uh_3_guru', 'uh_4_guru', 'uh_5_guru',
                'pts_guru', 'pas_guru', 'keterampilan_guru',
                'to_1_guru', 'to_2_guru', 'to_3_guru', 'upk_guru', 'ujian_praktek_guru',
            ];
            foreach ($snapshotColumns as $col) {
                $table->decimal($col, 5, 2)->nullable();
            }

            $table->timestamp('guru_terakhir_simpan_at')->nullable();
            $table->timestamp('wali_terakhir_edit_at')->nullable();
            $table->foreignId('edited_by_wali_id')->nullable()->constrained('tenaga_pendidik')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
