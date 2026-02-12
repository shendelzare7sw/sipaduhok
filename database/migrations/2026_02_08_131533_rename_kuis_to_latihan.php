<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Expand enum to include 'latihan' (keeping 'kuis' temporarily for data migration)
        DB::statement("ALTER TABLE ujian MODIFY COLUMN tipe_ujian ENUM(
            'harian', 'uts', 'uas',
            'ulangan_harian', 'kuis', 'latihan',
            'pts_ganjil', 'pas_ganjil',
            'pts_genap', 'pas_genap',
            'to_1', 'to_2', 'to_3',
            'upk', 'ujian_praktek'
        ) NOT NULL DEFAULT 'ulangan_harian'");

        // Rename existing kuis records to latihan
        DB::table('ujian')->where('tipe_ujian', 'kuis')->update(['tipe_ujian' => 'latihan']);
    }

    public function down(): void
    {
        // Revert latihan back to kuis
        DB::table('ujian')->where('tipe_ujian', 'latihan')->update(['tipe_ujian' => 'kuis']);

        DB::statement("ALTER TABLE ujian MODIFY COLUMN tipe_ujian ENUM(
            'harian', 'uts', 'uas',
            'ulangan_harian', 'kuis',
            'pts_ganjil', 'pas_ganjil',
            'pts_genap', 'pas_genap',
            'to_1', 'to_2', 'to_3',
            'upk', 'ujian_praktek'
        ) NOT NULL DEFAULT 'ulangan_harian'");
    }
};
