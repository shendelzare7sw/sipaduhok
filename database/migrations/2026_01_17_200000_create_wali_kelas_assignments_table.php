<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create pivot table for wali kelas assignments (many-to-many)
        Schema::create('wali_kelas_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenaga_pendidik_id')->constrained('tenaga_pendidik')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['tenaga_pendidik_id', 'kelas_id'], 'unique_wali_kelas_assignment');
        });

        // Migrate existing data from kelas.wali_kelas_id to new pivot table
        $existingAssignments = DB::table('kelas')
            ->whereNotNull('wali_kelas_id')
            ->select('id as kelas_id', 'wali_kelas_id as tenaga_pendidik_id')
            ->get();

        foreach ($existingAssignments as $assignment) {
            DB::table('wali_kelas_assignments')->insert([
                'tenaga_pendidik_id' => $assignment->tenaga_pendidik_id,
                'kelas_id' => $assignment->kelas_id,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_kelas_assignments');
    }
};
