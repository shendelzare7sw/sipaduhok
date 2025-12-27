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
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kalender_akademik_id')->nullable()->constrained('kalender_akademik')->onDelete('set null');
            $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('isi_pengumuman');
            $table->date('tanggal_pengumuman');
            $table->enum('prioritas', ['biasa', 'penting', 'mendesak'])->default('biasa');
            $table->string('lampiran_surat')->nullable(); // File PDF
            $table->boolean('is_from_kalender')->default(false); // Otomatis dari kalender atau manual
            $table->enum('status', ['draft', 'aktif', 'arsip'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};