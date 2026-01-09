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
        // Modify the ENUM to include wakil_kepala_sekolah
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'ketua_pkbm', 'wakil_kepala_sekolah', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar', 'siswa', 'orang_tua') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove wakil_kepala_sekolah from ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'ketua_pkbm', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar', 'siswa', 'orang_tua') NOT NULL");
    }
};
