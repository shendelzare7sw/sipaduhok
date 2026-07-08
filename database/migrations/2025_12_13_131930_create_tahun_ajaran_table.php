<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tahun_ajaran'); // 2024/2025
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('tanggal_mulai_genap')->nullable();
            $table->date('tanggal_akhir_pts_ganjil')->nullable();
            $table->date('tanggal_akhir_pts_genap')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
