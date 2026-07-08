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

            // Carryover / pengalihan tunggakan antar tagihan
            $table->foreignId('tagihan_asal_id')->nullable();
            $table->foreignId('dialihkan_ke_id')->nullable();
            $table->timestamp('dialihkan_pada')->nullable();

            $table->string('jenis_tagihan'); // SPP, Daftar Ulang, Seragam, Buku, dll
            $table->string('keterangan')->nullable();
            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status', ['belum_bayar', 'sudah_bayar', 'terlambat', 'cicilan'])->nullable()->default('belum_bayar');
            $table->timestamps();

            $table->index('tagihan_asal_id', 'tagihan_asal_idx');
            $table->index('dialihkan_ke_id', 'tagihan_dialihkan_idx');
            $table->foreign('tagihan_asal_id')->references('id')->on('tagihan')->onDelete('set null');
            $table->foreign('dialihkan_ke_id')->references('id')->on('tagihan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
