<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hapus kolom `nama_template` dari template_capaian_kompetensi.
 * Tujuan template hanya mengisi deskripsi capaian di rapor, jadi nama template
 * (mis. "Sangat Baik") mubazir — predikat cukup ditulis di dalam template_text.
 *
 * Guarded dengan hasColumn agar aman di DB lama (punya kolom+index) maupun instalasi
 * baru (create migration sudah tanpa kolom → migrasi ini jadi no-op).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('template_capaian_kompetensi', 'nama_template')) {
            return; // instalasi baru: kolom memang sudah tidak ada
        }

        Schema::table('template_capaian_kompetensi', function (Blueprint $table) {
            // Index lama memuat nama_template → harus dilepas sebelum drop kolom.
            $table->dropIndex('idx_template_mapel_nama');
        });

        Schema::table('template_capaian_kompetensi', function (Blueprint $table) {
            $table->dropColumn('nama_template');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('template_capaian_kompetensi', 'nama_template')) {
            return;
        }

        Schema::table('template_capaian_kompetensi', function (Blueprint $table) {
            $table->string('nama_template', 100)->nullable()->after('mata_pelajaran_id');
            $table->index(['mata_pelajaran_id', 'nama_template'], 'idx_template_mapel_nama');
        });
    }
};
