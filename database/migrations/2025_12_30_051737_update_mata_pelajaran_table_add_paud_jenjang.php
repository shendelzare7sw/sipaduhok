<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing PAUD records to KB temporarily
        DB::statement("UPDATE mata_pelajaran SET jenjang = 'SD' WHERE jenjang = 'PAUD'");

        // Ubah enum jenjang untuk menambahkan KB, TKA, TKB
        DB::statement("ALTER TABLE mata_pelajaran MODIFY COLUMN jenjang ENUM('KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum lama
        DB::statement("ALTER TABLE mata_pelajaran MODIFY COLUMN jenjang ENUM('PAUD', 'SD', 'SMP', 'SMA') NOT NULL");
    }
};
