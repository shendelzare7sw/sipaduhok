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
        Schema::table('info_pembayaran', function (Blueprint $table) {
            $table->string('tunai_lokasi')->nullable()->after('midtrans_is_production');
            $table->string('tunai_jam_operasional')->nullable()->after('tunai_lokasi');
            $table->text('tunai_deskripsi')->nullable()->after('tunai_jam_operasional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('info_pembayaran', function (Blueprint $table) {
            $table->dropColumn(['tunai_lokasi', 'tunai_jam_operasional', 'tunai_deskripsi']);
        });
    }
};
