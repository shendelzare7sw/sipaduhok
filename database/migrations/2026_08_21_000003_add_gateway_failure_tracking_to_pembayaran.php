<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->text('gateway_error')->nullable()->after('gateway_settled_at');
            $table->unsignedTinyInteger('gateway_attempts')->default(0)->after('gateway_error');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['gateway_error', 'gateway_attempts']);
        });
    }
};
