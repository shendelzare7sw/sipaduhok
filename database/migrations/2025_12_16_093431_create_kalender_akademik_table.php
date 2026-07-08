<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kalender_akademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('jenis_kegiatan', 100)->default('lainnya');
            $table->string('lampiran_surat')->nullable(); // File PDF atau Link URL
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('aktif');
            $table->boolean('is_hidden_siswa')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kalender_akademik');
    }
};
