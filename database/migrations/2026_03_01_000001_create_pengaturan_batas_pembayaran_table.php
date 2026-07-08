<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_batas_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->enum('periode', ['pts_ganjil', 'pas_ganjil', 'pts_genap', 'pas_genap', 'ujian_akhir']);
            $table->json('jenis_tagihan_required'); // array of jenis_tagihan strings
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->unique(['tahun_ajaran_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_batas_pembayaran');
    }
};
