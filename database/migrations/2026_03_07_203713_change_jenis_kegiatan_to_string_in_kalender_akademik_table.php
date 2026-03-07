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
        // Use raw statement to avoid doctrine/dbal requirement for ENUM change
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE kalender_akademik MODIFY COLUMN jenis_kegiatan VARCHAR(100) NOT NULL DEFAULT 'lainnya'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE kalender_akademik MODIFY COLUMN jenis_kegiatan ENUM('field_trip', 'outing', 'live_in', 'hokfest', 'pts', 'pas', 'libur', 'ujian', 'acara_sekolah', 'lainnya') NOT NULL DEFAULT 'lainnya'");
    }
};
