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
        Schema::table('siswa', function (Blueprint $table) {
            // Add Ketua PKBM validation for rapor access (3rd validator)
            $table->boolean('validasi_rapor_ketua')->default(false)
                  ->after('validasi_rapor_wali');
            $table->timestamp('tanggal_validasi_rapor_ketua')->nullable()
                  ->after('validasi_rapor_ketua');
            $table->foreignId('validasi_rapor_ketua_oleh')->nullable()
                  ->after('tanggal_validasi_rapor_ketua')
                  ->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['validasi_rapor_ketua_oleh']);
            $table->dropColumn([
                'validasi_rapor_ketua',
                'tanggal_validasi_rapor_ketua',
                'validasi_rapor_ketua_oleh',
            ]);
        });
    }
};
