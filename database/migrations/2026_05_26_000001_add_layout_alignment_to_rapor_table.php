<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            if (!Schema::hasColumn('rapor', 'catatan_alignment')) {
                $table->string('catatan_alignment', 12)->default('center')->after('catatan_wali_kelas');
            }

            if (!Schema::hasColumn('rapor', 'deskripsi_alignment')) {
                $table->string('deskripsi_alignment', 12)->default('left')->after('catatan_alignment');
            }

            if (!Schema::hasColumn('rapor', 'keterangan_ekstra_alignment')) {
                $table->string('keterangan_ekstra_alignment', 12)->default('left')->after('deskripsi_alignment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('rapor', 'catatan_alignment') ? 'catatan_alignment' : null,
                Schema::hasColumn('rapor', 'deskripsi_alignment') ? 'deskripsi_alignment' : null,
                Schema::hasColumn('rapor', 'keterangan_ekstra_alignment') ? 'keterangan_ekstra_alignment' : null,
            ]));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
