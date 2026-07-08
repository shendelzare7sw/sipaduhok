<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabang')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->foreignId('wali_kelas_id')->nullable()->constrained('tenaga_pendidik')->onDelete('set null');
            $table->string('nama_kelas');
            $table->enum('jenjang', ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA']);
            $table->string('kode_kelas')->unique();
            $table->integer('kuota_siswa')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
