<?php
// database/migrations/2025_12_16_create_catatan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('isi_catatan');
            $table->enum('tipe_penerima', ['semua', 'role', 'individu'])->default('semua');
            $table->string('role_penerima')->nullable(); // untuk tipe 'role'
            $table->foreignId('penerima_id')->nullable()->constrained('users')->onDelete('cascade'); // untuk tipe 'individu'
            $table->enum('prioritas', ['biasa', 'penting', 'mendesak'])->default('biasa');
            $table->boolean('is_read')->default(false);
            $table->timestamp('tanggal_kirim')->useCurrent();
            $table->timestamps();
        });

        // Tabel pivot untuk tracking siapa saja yang sudah baca
        Schema::create('catatan_dibaca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catatan_id')->constrained('catatan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('dibaca_pada')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_dibaca');
        Schema::dropIfExists('catatan');
    }
};