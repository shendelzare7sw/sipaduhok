<?php

namespace Tests\Feature;

use App\Models\InfoPembayaran;
use App\Services\PaywuzService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use ReflectionClass;
use Tests\TestCase;

class PaywuzServiceContractTest extends TestCase
{
    public function test_kanal_default_memilih_qris_lalu_va_sesuai_batas_nominal(): void
    {
        config(['services.paywuz.base_url' => 'https://api.paywuz.id/v1']);
        Cache::flush();

        $service = $this->makeService();

        Http::fake([
            'https://api.paywuz.id/v1/payment-methods' => Http::response([
                'data' => [
                    [
                        'code' => 'QRIS',
                        'name' => 'QRIS',
                        'type' => 'qris',
                        'limits' => ['minIdr' => 1000, 'maxIdr' => 10000000],
                    ],
                    [
                        'code' => 'VA',
                        'name' => 'Virtual Account',
                        'type' => 'meta',
                        'limits' => ['minIdr' => 1000, 'maxIdr' => 50000000],
                    ],
                ],
            ]),
        ]);

        $this->assertSame('QRIS', $service->defaultPaymentMethod(150000));
        $this->assertSame('VA', $service->defaultPaymentMethod(15000000));
    }

    public function test_transaksi_dibuat_dengan_nominal_server_kanal_dan_kebijakan_biaya(): void
    {
        config([
            'services.paywuz.base_url' => 'https://api.paywuz.id/v1',
            'services.paywuz.expiry_minutes' => 720,
        ]);
        Cache::flush();

        $service = $this->makeService();

        Http::fake(function (Request $request) {
            if ($request->method() === 'GET' && str_ends_with($request->url(), '/payment-methods')) {
                return Http::response([
                    'data' => [[
                        'code' => 'QRIS',
                        'name' => 'QRIS',
                        'type' => 'qris',
                        'fee' => ['flatIdr' => 0, 'percentBps' => 70],
                        'limits' => ['minIdr' => 1000, 'maxIdr' => 10000000],
                    ]],
                ]);
            }

            return Http::response([
                'data' => [
                    'id' => 'trx-contract-test',
                    'orderId' => 'SPH-CONTRACT-001',
                    'amount' => 150000,
                    'totalPayment' => 151050,
                    'paymentMethod' => 'QRIS',
                    'status' => 'pending',
                    'paymentUrl' => 'https://checkout.paywuz.id/pay/trx-contract-test',
                    'expiresAt' => now()->addHours(12)->toIso8601String(),
                ],
            ]);
        });

        $transaction = $service->createTransaction(
            'SPH-CONTRACT-001',
            150000,
            'QRIS',
            10,
            20,
            'https://app.sipaduhok.id/wali-siswa/pembayaran/digital/10',
            'sandbox',
        );

        $this->assertSame('trx-contract-test', $transaction['id']);
        $this->assertSame(151050, $transaction['totalPayment']);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && str_ends_with($request->url(), '/transactions')
                && $request['orderId'] === 'SPH-CONTRACT-001'
                && $request['amount'] === 150000
                && $request['paymentMethod'] === 'QRIS'
                && $request['feeByMerchant'] === false
                && data_get($request->data(), 'metadata.pembayaran_id') === 10
                && data_get($request->data(), 'metadata.siswa_id') === 20;
        });
    }

    public function test_status_transaksi_memulihkan_url_pembayaran_dari_id_terverifikasi(): void
    {
        config([
            'services.paywuz.base_url' => 'https://api.paywuz.id/v1',
            'services.paywuz.checkout_url' => 'https://paywuz.id/pay',
        ]);

        $service = $this->makeService();

        Http::fake([
            'https://api.paywuz.id/v1/transactions/SPH-RECOVERY-001' => Http::response([
                'data' => [
                    'id' => 'trx-recovery-test',
                    'orderId' => 'SPH-RECOVERY-001',
                    'amount' => 10000,
                    'totalPayment' => 10360,
                    'paymentMethod' => 'QRIS',
                    'status' => 'pending',
                    'expiresAt' => now()->addHour()->toIso8601String(),
                ],
            ]),
        ]);

        $transaction = $service->getTransactionStatus('SPH-RECOVERY-001', 10000, 'sandbox');

        $this->assertSame('https://paywuz.id/pay/trx-recovery-test', $transaction['paymentUrl']);
    }

    public function test_status_404_dibedakan_dari_gangguan_api(): void
    {
        config(['services.paywuz.base_url' => 'https://api.paywuz.id/v1']);
        $service = $this->makeService();

        Http::fake([
            'https://api.paywuz.id/v1/transactions/SPH-NOT-FOUND' => Http::response([
                'message' => 'Order tidak ditemukan',
                'code' => 'not_found',
            ], 404),
        ]);

        $this->assertNull($service->findTransactionStatus('SPH-NOT-FOUND', 10000, 'sandbox'));
    }

    public function test_respons_cancel_minimal_resmi_tetap_valid(): void
    {
        config(['services.paywuz.base_url' => 'https://api.paywuz.id/v1']);
        $service = $this->makeService();

        Http::fake([
            'https://api.paywuz.id/v1/transactions/SPH-CANCEL-001/cancel' => Http::response([
                'data' => [
                    'id' => 'trx-cancel-test',
                    'orderId' => 'SPH-CANCEL-001',
                    'status' => 'cancelled',
                ],
            ]),
        ]);

        $transaction = $service->cancelTransaction('SPH-CANCEL-001', 10000, 'sandbox');

        $this->assertSame('cancelled', $transaction['status']);
        $this->assertArrayNotHasKey('amount', $transaction);
    }

    private function makeService(): PaywuzService
    {
        $info = new class extends InfoPembayaran
        {
            public function __construct()
            {
                parent::__construct();
                $this->paywuz_fee_by_merchant = false;
                $this->paywuz_is_production = false;
            }

            public function isPaywuzEnabled(): bool
            {
                return true;
            }

            public function getPaywuzApiKey(string $environment): ?string
            {
                return 'pk_sand_'.str_repeat('a', 32);
            }
        };

        $reflection = new ReflectionClass(PaywuzService::class);
        /** @var PaywuzService $service */
        $service = $reflection->newInstanceWithoutConstructor();
        $reflection->getProperty('infoPembayaran')->setValue($service, $info);

        return $service;
    }
}
