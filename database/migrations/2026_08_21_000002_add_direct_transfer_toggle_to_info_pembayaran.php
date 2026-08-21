<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('info_pembayaran', function (Blueprint $table) {
            $table->boolean('direct_transfer_enabled')
                ->default(true)
                ->after('atas_nama');
        });
    }

    public function down(): void
    {
        Schema::table('info_pembayaran', function (Blueprint $table) {
            $table->dropColumn('direct_transfer_enabled');
        });
    }
};
