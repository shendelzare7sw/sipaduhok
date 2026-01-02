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
        Schema::create('pengaturan_istirahat', function (Blueprint $table) {
            $table->id();
            $table->enum('jenjang', ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA']);
            $table->integer('urutan')->default(1)->comment('1 untuk istirahat pertama, 2 untuk istirahat kedua');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->json('hari_aktif')->comment('Array hari: ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"]');
            $table->string('nama_istirahat')->default('Istirahat')->comment('Nama tampilan: Istirahat, Istirahat 1, Istirahat 2, dll');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk query cepat
            $table->index(['jenjang', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_istirahat');
    }
};
