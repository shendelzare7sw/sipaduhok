<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flyer', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('gambar_flyer'); // Path gambar
            $table->string('link_url')->nullable(); // Link eksternal jika diperlukan
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('target_audience', ['semua', 'siswa', 'guru', 'wali_kelas', 'orang_tua'])->default('siswa');
            $table->integer('urutan_tampil')->default(1); // Urutan tampil pop-up
            $table->enum('status', ['draft', 'aktif', 'nonaktif'])->default('aktif');
            $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flyer');
    }
};