<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all existing schedules with a kelas_id
        $schedules = DB::table('jadwal_pelajaran')->whereNotNull('kelas_id')->get();

        foreach ($schedules as $schedule) {
            // Check if already exists in pivot to avoid duplicates if re-run
            $exists = DB::table('jadwal_kelas')
                ->where('jadwal_pelajaran_id', $schedule->id)
                ->where('kelas_id', $schedule->kelas_id)
                ->exists();

            if (!$exists) {
                DB::table('jadwal_kelas')->insert([
                    'jadwal_pelajaran_id' => $schedule->id,
                    'kelas_id' => $schedule->kelas_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: clear pivot table on rollback
        DB::table('jadwal_kelas')->truncate();
    }
};
