<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Recalculate existing ujian_siswa.nilai from raw bobot sum to 0-100 scale.
     * Formula: (sum_of_nilai_soal / total_bobot_ujian) * 100
     */
    public function up(): void
    {
        $ujianSiswaRecords = DB::table('ujian_siswa')
            ->whereNotNull('nilai')
            ->where('nilai', '>', 0)
            ->whereIn('status', ['selesai', 'dinilai'])
            ->get();

        $recalculatedCount = 0;

        foreach ($ujianSiswaRecords as $record) {
            // Get total bobot for this ujian
            $totalBobot = DB::table('soal_ujian')
                ->where('ujian_id', $record->ujian_id)
                ->sum('bobot_nilai');

            if ($totalBobot <= 0) {
                continue; // Skip if no soal or zero bobot
            }

            // Get sum of actual scores from jawaban_siswa
            $totalNilaiSoal = DB::table('jawaban_siswa')
                ->where('ujian_siswa_id', $record->id)
                ->sum('nilai_soal');

            // Normalize to 0-100
            $nilaiNormalized = round(($totalNilaiSoal / $totalBobot) * 100, 1);

            // Only update if the value actually changes (avoid unnecessary writes)
            if (abs($record->nilai - $nilaiNormalized) > 0.01) {
                DB::table('ujian_siswa')
                    ->where('id', $record->id)
                    ->update(['nilai' => $nilaiNormalized]);

                $recalculatedCount++;
            }
        }

        Log::info("Recalculated {$recalculatedCount} ujian_siswa records to 0-100 scale.");
    }

    /**
     * Reverse is not practical as original raw values are lost.
     */
    public function down(): void
    {
        // Cannot reverse: original raw values are lost after normalization
        Log::warning('Cannot reverse nilai normalization - original raw values are not stored.');
    }
};
