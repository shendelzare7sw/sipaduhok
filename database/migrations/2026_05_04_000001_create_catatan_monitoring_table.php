<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catatan_monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('users')->onDelete('cascade');
            $table->string('pengirim_role', 50);
            $table->foreignId('guru_id')->constrained('tenaga_pendidik')->onDelete('cascade');
            $table->string('konten_type', 20); // 'materi' | 'tugas' | 'ujian'
            $table->unsignedBigInteger('konten_id');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->text('isi_catatan');
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->index(['guru_id', 'dibaca_pada']);
            $table->index(['konten_type', 'konten_id']);
            $table->index(['pengirim_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_monitoring');
    }
};
