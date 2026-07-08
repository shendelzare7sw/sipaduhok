<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('tenaga_pendidik')->onDelete('cascade');
            $table->string('judul_materi');
            $table->enum('kategori', ['materi', 'modul_ajar'])->default('materi');
            $table->text('deskripsi')->nullable();
            $table->string('file_materi')->nullable();
            $table->string('url_materi')->nullable();
            $table->enum('tipe_file', ['pdf', 'video', 'ppt', 'doc', 'link'])->nullable();
            $table->date('tanggal_upload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
