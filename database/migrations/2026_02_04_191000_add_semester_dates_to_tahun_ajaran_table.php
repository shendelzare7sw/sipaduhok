<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            // Add semester genap start date
            // Semester Ganjil: tanggal_mulai → tanggal_mulai_genap - 1 day
            // Semester Genap: tanggal_mulai_genap → tanggal_selesai
            $table->date('tanggal_mulai_genap')->nullable()->after('tanggal_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->dropColumn('tanggal_mulai_genap');
        });
    }
};
