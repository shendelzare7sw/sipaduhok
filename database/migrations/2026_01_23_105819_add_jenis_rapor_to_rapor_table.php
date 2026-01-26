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
        Schema::table('rapor', function (Blueprint $table) {
            $table->enum('jenis_rapor', ['tengah_semester', 'akhir_semester'])
                  ->default('akhir_semester')
                  ->after('semester');
        });
    }

    public function down(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            $table->dropColumn('jenis_rapor');
        });
    }
};
