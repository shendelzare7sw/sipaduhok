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
            // Validasi Akses Ujian
            $table->boolean('validasi_ujian_bendahara')->default(false)->after('status');
            $table->boolean('validasi_ujian_wali')->default(false)->after('validasi_ujian_bendahara');
            $table->timestamp('tanggal_validasi_ujian_bendahara')->nullable()->after('validasi_ujian_wali');
            $table->timestamp('tanggal_validasi_ujian_wali')->nullable()->after('tanggal_validasi_ujian_bendahara');
            
            // Validasi Akses Rapor
            $table->boolean('validasi_rapor_bendahara')->default(false)->after('tanggal_validasi_ujian_wali');
            $table->boolean('validasi_rapor_wali')->default(false)->after('validasi_rapor_bendahara');
            $table->timestamp('tanggal_validasi_rapor_bendahara')->nullable()->after('validasi_rapor_wali');
            $table->timestamp('tanggal_validasi_rapor_wali')->nullable()->after('tanggal_validasi_rapor_bendahara');
            
            // User yang memvalidasi
            $table->foreignId('validasi_ujian_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('validasi_rapor_oleh')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['validasi_ujian_oleh']);
            $table->dropForeign(['validasi_rapor_oleh']);
            
            $table->dropColumn([
                'validasi_ujian_bendahara',
                'validasi_ujian_wali',
                'tanggal_validasi_ujian_bendahara',
                'tanggal_validasi_ujian_wali',
                'validasi_rapor_bendahara',
                'validasi_rapor_wali',
                'tanggal_validasi_rapor_bendahara',
                'tanggal_validasi_rapor_wali',
                'validasi_ujian_oleh',
                'validasi_rapor_oleh',
            ]);
        });
    }
};