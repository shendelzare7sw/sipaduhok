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
        Schema::table('student_parents', function (Blueprint $table) {
            // Change relationship column from ENUM to VARCHAR to support custom values
            $table->string('relationship', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_parents', function (Blueprint $table) {
            // Revert back to ENUM
            $table->enum('relationship', [
                'ayah_kandung',
                'ibu_kandung',
                'ayah_tiri',
                'ibu_tiri',
                'kakek',
                'nenek',
                'paman',
                'bibi',
                'wali',
                'lainnya'
            ])->change();
        });
    }
};
