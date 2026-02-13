<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add LULUS_TUNGGAKAN to status_kelulusan enum to fix database insertion failures
     * for graduates with financial dispensation.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE status_naik_kelas_siswa
            MODIFY COLUMN status_kelulusan
            ENUM('NAIK_KELAS', 'LULUS', 'TIDAK_NAIK_KELAS', 'NAIK_KELAS_TUNGGAKAN', 'LULUS_TUNGGAKAN')
            NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if any records use LULUS_TUNGGAKAN before rolling back
        $count = DB::table('status_naik_kelas_siswa')
            ->where('status_kelulusan', 'LULUS_TUNGGAKAN')
            ->count();

        if ($count > 0) {
            throw new \Exception("Cannot rollback: {$count} records use LULUS_TUNGGAKAN status. Please migrate data first.");
        }

        DB::statement("ALTER TABLE status_naik_kelas_siswa
            MODIFY COLUMN status_kelulusan
            ENUM('NAIK_KELAS', 'LULUS', 'TIDAK_NAIK_KELAS', 'NAIK_KELAS_TUNGGAKAN')
            NOT NULL");
    }
};
