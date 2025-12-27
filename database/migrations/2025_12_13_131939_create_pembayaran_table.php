<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->string('kode_pembayaran')->unique();
            $table->decimal('jumlah_bayar', 10, 2);
            $table->date('tanggal_bayar');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'midtrans']);
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status_validasi', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->foreignId('divalidasi_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_validasi')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};