<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk pengajuan override oleh Ketua PKBM ketika Bendahara menolak rapor.
     * Flow: Bendahara tolak → buat record di sini → Ketua review → jika setuju → akses terbuka
     */
    public function up(): void
    {
        Schema::create('pengajuan_rapor_ketua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('diajukan_oleh')->constrained('users')->comment('Bendahara yang mengajukan');
            $table->text('alasan')->comment('Alasan penolakan / keterangan pengajuan');
            $table->enum('tipe', ['rapor', 'ujian'])->default('rapor');
            $table->enum('periode', ['pts_ganjil', 'pas_ganjil', 'pts_genap', 'pas_genap', 'ujian_akhir'])->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('diputuskan_oleh')->nullable()->constrained('users')->comment('Ketua PKBM yang memutuskan');
            $table->text('catatan_ketua')->nullable()->comment('Catatan/keputusan dari Ketua PKBM');
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_keputusan')->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_rapor_ketua');
    }
};
