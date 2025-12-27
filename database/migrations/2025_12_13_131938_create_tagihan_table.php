<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->string('jenis_tagihan'); // SPP, Daftar Ulang, Seragam, Buku, dll
            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status', ['belum_bayar', 'sudah_bayar', 'terlambat'])->default('belum_bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};