<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_download_rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapor')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // orang tua yang request
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('alasan')->nullable();
            $table->foreignId('diputuskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_admin')->nullable();
            $table->timestamp('tanggal_request')->nullable();
            $table->timestamp('tanggal_keputusan')->nullable();
            $table->timestamp('download_expired_at')->nullable(); // link expires
            $table->string('download_token', 64)->nullable()->unique();
            $table->timestamps();

            $table->index(['siswa_id', 'status']);
            $table->index(['rapor_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_download_rapor');
    }
};
