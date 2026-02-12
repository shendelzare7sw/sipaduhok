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
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->enum('kelompok', ['A', 'B'])
                  ->nullable()
                  ->after('jenjang')
                  ->comment('Subject category: A (Core subjects) or B (Elective/Local content)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->dropColumn('kelompok');
        });
    }
};
