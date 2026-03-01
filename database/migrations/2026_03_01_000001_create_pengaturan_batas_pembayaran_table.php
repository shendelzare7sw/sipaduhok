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

        // Add tipe and periode to pengajuan_rapor_ketua for dispensasi
        Schema::table('pengajuan_rapor_ketua', function (Blueprint $table) {
            $table->enum('tipe', ['rapor', 'ujian'])->default('rapor')->after('alasan');
            $table->enum('periode', ['pts_ganjil', 'pas_ganjil', 'pts_genap', 'pas_genap', 'ujian_akhir'])->nullable()->after('tipe');
        });

        // Add catatan_revisi_ketua and status_review_ketua to rapor (for Fase 3)
        Schema::table('rapor', function (Blueprint $table) {
            $table->text('catatan_revisi_ketua')->nullable()->after('tanggal_rilis');
            $table->enum('status_review_ketua', ['pending', 'perlu_revisi', 'disetujui'])->default('pending')->after('catatan_revisi_ketua');
        });
    }

    public function down(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            $table->dropColumn(['catatan_revisi_ketua', 'status_review_ketua']);
        });

        Schema::table('pengajuan_rapor_ketua', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'periode']);
        });

        Schema::dropIfExists('pengaturan_batas_pembayaran');
    }
};
