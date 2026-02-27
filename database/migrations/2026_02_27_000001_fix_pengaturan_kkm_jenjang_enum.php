<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand pengaturan_kkm.jenjang enum to include PAUD and SD.
     * Remove SMK which is not used by this institution.
     * Old: ['SMP', 'SMA']
     * New: ['PAUD', 'SD', 'SMP', 'SMA']
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pengaturan_kkm MODIFY COLUMN jenjang ENUM('PAUD', 'SD', 'SMP', 'SMA') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengaturan_kkm MODIFY COLUMN jenjang ENUM('SMP', 'SMA') NOT NULL");
    }
};
