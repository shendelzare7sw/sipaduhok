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
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_pelajaran', 'siswa_ids')) {
                $table->json('siswa_ids')->nullable()->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_pelajaran', 'siswa_ids')) {
                $table->dropColumn('siswa_ids');
            }
        });
    }
};
