<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds columns required for individual promotion and rollback functionality.
     */
    public function up(): void
    {
        Schema::table('status_naik_kelas_siswa', function (Blueprint $table) {
            // Track original kelas_id for rollback capability
            $table->foreignId('original_kelas_id')->nullable()->after('kelas_tujuan')
                  ->constrained('kelas')->onDelete('set null');
            
            // Track if promotion has been processed (vs just simulated)
            $table->boolean('is_processed')->default(false)->after('tanggal_eksekusi');
            
            // Rollback tracking
            $table->timestamp('rolled_back_at')->nullable()->after('is_processed');
            $table->foreignId('rolled_back_by')->nullable()->after('rolled_back_at')
                  ->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_naik_kelas_siswa', function (Blueprint $table) {
            $table->dropForeign(['original_kelas_id']);
            $table->dropColumn('original_kelas_id');
            $table->dropColumn('is_processed');
            $table->dropColumn('rolled_back_at');
            $table->dropForeign(['rolled_back_by']);
            $table->dropColumn('rolled_back_by');
        });
    }
};
