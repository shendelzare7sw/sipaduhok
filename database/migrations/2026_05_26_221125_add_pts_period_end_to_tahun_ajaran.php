<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah field tanggal_akhir_pts_ganjil & tanggal_akhir_pts_genap
 * untuk konfigurasi periode PTS yang fleksibel (sebelumnya hardcoded
 * 3 bulan pertama, sekarang admin bisa atur via /admin/tahun-ajaran).
 *
 * Logic periode rapor (post-migration):
 * - PTS Ganjil: tanggal_mulai → tanggal_akhir_pts_ganjil
 * - PAS Ganjil: tanggal_mulai → tanggal_mulai_genap - 1 day
 * - PTS Genap : tanggal_mulai_genap → tanggal_akhir_pts_genap
 * - PAS Genap : tanggal_mulai_genap → tanggal_selesai
 *
 * Field nullable — kalau kosong, fallback ke 3 bulan pertama (auto).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->date('tanggal_akhir_pts_ganjil')->nullable()->after('tanggal_mulai_genap');
            $table->date('tanggal_akhir_pts_genap')->nullable()->after('tanggal_akhir_pts_ganjil');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->dropColumn(['tanggal_akhir_pts_ganjil', 'tanggal_akhir_pts_genap']);
        });
    }
};
