<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('info_pembayaran', function (Blueprint $table) {
            $table->text('paywuz_sandbox_api_key')->nullable()->after('midtrans_enabled');
            $table->text('paywuz_production_api_key')->nullable()->after('paywuz_sandbox_api_key');
            $table->boolean('paywuz_is_production')->default(false)->after('paywuz_production_api_key');
            $table->boolean('paywuz_enabled')->default(false)->after('paywuz_is_production');
            $table->boolean('paywuz_fee_by_merchant')->default(false)->after('paywuz_enabled');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            // Ubah enum lama menjadi string agar riwayat Midtrans tetap dapat dibaca
            // sementara transaksi baru menggunakan nilai "paywuz".
            $table->string('metode_pembayaran', 30)->change();
            $table->text('payment_url')->nullable()->after('gateway_response');
            $table->decimal('gateway_total', 12, 2)->nullable()->after('payment_url');
            $table->string('payment_environment', 20)->nullable()->after('gateway_total');
            $table->string('gateway_status', 30)->nullable()->after('payment_environment');
            $table->timestamp('payment_expires_at')->nullable()->after('gateway_status');
            $table->timestamp('gateway_settled_at')->nullable()->after('payment_expires_at');
            $table->index(['payment_gateway', 'order_id']);
        });

        Schema::create('payment_webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30);
            $table->string('delivery_id', 100)->unique();
            $table->string('event', 100);
            $table->foreignId('pembayaran_id')->nullable()->constrained('pembayaran')->nullOnDelete();
            $table->string('payload_hash', 64);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_deliveries');

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropIndex(['payment_gateway', 'order_id']);
            $table->dropColumn([
                'payment_url',
                'gateway_total',
                'payment_environment',
                'gateway_status',
                'payment_expires_at',
                'gateway_settled_at',
            ]);
        });

        Schema::table('info_pembayaran', function (Blueprint $table) {
            $table->dropColumn([
                'paywuz_sandbox_api_key',
                'paywuz_production_api_key',
                'paywuz_is_production',
                'paywuz_enabled',
                'paywuz_fee_by_merchant',
            ]);
        });
    }
};
