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
            $table->boolean('midtrans_enabled')->default(true)->after('midtrans_is_production');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('info_pembayaran', function (Blueprint $table) {
            $table->dropColumn('midtrans_enabled');
        });
    }
};
