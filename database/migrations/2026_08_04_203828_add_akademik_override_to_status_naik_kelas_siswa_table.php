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
        Schema::table('status_naik_kelas_siswa', function (Blueprint $table) {
            // Menandai kenaikan yang di-ACC manual oleh admin lewat "Naikkan Terpilih"
            // untuk siswa yang akademiknya tidak bisa diukur karena kelasnya belum
            // punya Jadwal Pelajaran sama sekali (data lama/dummy yang belum dirapikan).
            // persentase_nilai_tuntas/jumlah_mapel_tuntas/total_mapel TETAP diisi 0
            // apa adanya (jujur, tidak ada yang benar-benar terukur) - kolom ini yang
            // memberi tahu tampilan riwayat untuk menampilkan "Aman" alih-alih "0% Tuntas".
            $table->boolean('akademik_override_tanpa_jadwal')->default(false)->after('total_mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_naik_kelas_siswa', function (Blueprint $table) {
            $table->dropColumn('akademik_override_tanpa_jadwal');
        });
    }
};
