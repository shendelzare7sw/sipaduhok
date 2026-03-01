<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah tanggal_rilis ke rapor
        Schema::table('rapor', function (Blueprint $table) {
            $table->date('tanggal_rilis')->nullable()->after('tanggal_terbit');
        });

        // Tambah urutan, is_visible, kelompok_override ke rapor_nilai
        Schema::table('rapor_nilai', function (Blueprint $table) {
            $table->integer('urutan')->default(0)->after('deskripsi');
            $table->boolean('is_visible')->default(true)->after('urutan');
            $table->string('kelompok_override', 5)->nullable()->after('is_visible');
        });

        // Data fix: set urutan ascending per rapor berdasarkan ID
        DB::statement('
            UPDATE rapor_nilai rn
            JOIN (
                SELECT id, ROW_NUMBER() OVER (PARTITION BY rapor_id ORDER BY id) - 1 as row_num
                FROM rapor_nilai
            ) ranked ON rn.id = ranked.id
            SET rn.urutan = ranked.row_num
        ');

        // Data fix: siswa yang sudah validasi_rapor_bendahara tapi belum ketua
        DB::statement('
            UPDATE siswa SET validasi_rapor_ketua = 1
            WHERE validasi_rapor_bendahara = 1 AND validasi_rapor_ketua = 0
        ');
    }

    public function down(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            $table->dropColumn('tanggal_rilis');
        });

        Schema::table('rapor_nilai', function (Blueprint $table) {
            $table->dropColumn(['urutan', 'is_visible', 'kelompok_override']);
        });
    }
};
