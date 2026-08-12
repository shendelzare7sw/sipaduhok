<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Standardize existing agama values to the 6 official religions in Indonesia.
     * This ensures consistency with the new dropdown input (replacing free-text).
     */
    public function up(): void
    {
        // Mapping of common variations to standardized values
        $mappings = [
            // Islam
            'Islam' => ['muslim', 'moslem', 'moeslim'],
            // Kristen (Protestan)
            'Kristen' => ['kristen protestan', 'protestan', 'protestant', 'kristen protestant'],
            // Katolik
            'Katolik' => ['katholik', 'katolik roma', 'kristen katolik', 'catholic', 'roman catholic'],
            // Hindu
            'Hindu' => ['hinduism', 'hindu dharma'],
            // Buddha
            'Buddha' => ['budha', 'buddhis', 'buddhism', 'buddhist'],
            // Konghucu
            'Konghucu' => ['khonghucu', 'kong hu cu', 'konfusius', 'confucianism', 'confucian'],
        ];

        foreach ($mappings as $standardized => $variations) {
            foreach ($variations as $variation) {
                DB::table('siswa')
                    ->whereRaw('LOWER(agama) = ?', [strtolower($variation)])
                    ->update(['agama' => $standardized]);
            }
        }

        // Also fix case variations of the standard names themselves
        // e.g., 'islam' -> 'Islam', 'KRISTEN' -> 'Kristen'
        $standardNames = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
        foreach ($standardNames as $name) {
            DB::table('siswa')
                ->whereRaw('LOWER(agama) = ?', [strtolower($name)])
                ->where('agama', '!=', $name)
                ->update(['agama' => $name]);
        }
    }

    public function down(): void
    {
        // Data normalization cannot be reversed
    }
};
