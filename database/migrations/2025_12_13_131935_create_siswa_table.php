<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cabang_id')->constrained('cabang')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('nisn')->unique();
            $table->string('nis')->unique()->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('agama')->nullable();
            $table->string('pelajaran_agama')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('telepon_orangtua')->nullable();
            $table->string('foto')->nullable();
            $table->date('tanggal_masuk');
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'keluar'])->default('aktif');

            // Validasi akses ujian
            $table->boolean('validasi_ujian_bendahara')->default(false);
            $table->boolean('validasi_ujian_wali')->default(false);
            $table->timestamp('tanggal_validasi_ujian_bendahara')->nullable();
            $table->timestamp('tanggal_validasi_ujian_wali')->nullable();
            $table->foreignId('validasi_ujian_oleh')->nullable()->constrained('users')->onDelete('set null');

            // Validasi akses rapor
            $table->boolean('validasi_rapor_bendahara')->default(false);
            $table->boolean('validasi_rapor_wali')->default(false);
            $table->boolean('validasi_rapor_ketua')->default(false);
            $table->timestamp('tanggal_validasi_rapor_bendahara')->nullable();
            $table->timestamp('tanggal_validasi_rapor_wali')->nullable();
            $table->timestamp('tanggal_validasi_rapor_ketua')->nullable();
            $table->foreignId('validasi_rapor_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('validasi_rapor_ketua_oleh')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
