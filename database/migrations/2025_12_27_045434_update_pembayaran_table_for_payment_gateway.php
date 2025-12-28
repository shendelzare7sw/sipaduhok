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
        Schema::table('pembayaran', function (Blueprint $table) {
            // Add payment gateway fields
            $table->string('payment_gateway')->nullable()->after('metode_pembayaran'); // midtrans, xendit, manual
            $table->string('transaction_id')->nullable()->after('payment_gateway'); // from gateway
            $table->string('order_id')->nullable()->after('transaction_id'); // internal reference
            $table->string('payment_type')->nullable()->after('order_id'); // credit_card, bank_transfer, qris, etc
            $table->json('gateway_response')->nullable()->after('payment_type'); // full response from gateway

            // Track which parent paid (if from parent portal)
            $table->foreignId('paid_by_parent_id')->nullable()->after('siswa_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['paid_by_parent_id']);
            $table->dropColumn([
                'payment_gateway',
                'transaction_id',
                'order_id',
                'payment_type',
                'gateway_response',
                'paid_by_parent_id'
            ]);
        });
    }
};
