<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add semester column to nilai table
        Schema::table('nilai', function (Blueprint $table) {
            $table->enum('semester', ['ganjil', 'genap'])
                  ->default('ganjil')
                  ->after('tahun_ajaran_id');
        });

        // Update existing data based on created_at month
        // July-December = Ganjil, January-June = Genap
        DB::statement("
            UPDATE nilai 
            SET semester = CASE 
                WHEN MONTH(created_at) >= 7 AND MONTH(created_at) <= 12 THEN 'ganjil'
                ELSE 'genap'
            END
        ");
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
