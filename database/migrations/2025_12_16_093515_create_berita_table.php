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
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi_singkat'); // Preview di card
            $table->string('gambar_thumbnail'); // Gambar preview
            $table->string('url_berita'); // Link ke website eksternal
            $table->enum('kategori', [
                'kegiatan',
                'prestasi',
                'pengumuman',
                'artikel',
                'ujian'
            ])->default('kegiatan');
            $table->date('tanggal_berita');
            $table->boolean('is_featured')->default(false); // Tampil di berita utama
            $table->integer('urutan_tampil')->default(999); // Urutan tampil (kecil = atas)
            $table->enum('status', ['draft', 'aktif', 'arsip'])->default('aktif');
            $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};