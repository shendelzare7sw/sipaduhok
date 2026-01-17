<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modify status enum to include 'cicilan' for partial payments
     */
    public function up(): void
    {
        // For MySQL, we need to modify the enum column
        DB::statement("ALTER TABLE tagihan MODIFY COLUMN status ENUM('belum_bayar', 'sudah_bayar', 'terlambat', 'cicilan') DEFAULT 'belum_bayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        // First, update any 'cicilan' records to 'belum_bayar'
        DB::table('tagihan')->where('status', 'cicilan')->update(['status' => 'belum_bayar']);
        
        DB::statement("ALTER TABLE tagihan MODIFY COLUMN status ENUM('belum_bayar', 'sudah_bayar', 'terlambat') DEFAULT 'belum_bayar'");
    }
};
