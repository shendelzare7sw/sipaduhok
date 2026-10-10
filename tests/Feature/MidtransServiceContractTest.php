<?php

namespace Tests\Feature;

use App\Models\InfoPembayaran;
use App\Services\MidtransService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use ReflectionClass;
use RuntimeException;
use Tests\TestCase;

class MidtransServiceContractTest extends TestCase
{
    private const SERVER_KEY = 'SB-Mid-server-contractTestKey123';

    public function test_kanal_mengikuti_konfigurasi_dan_default_memilih_qris_lalu_va(): void
    {
        config(['services.midtrans.channels' => ['qris', 'VA', 'TIDAK_ADA', 'QRIS']]);
        $service = $this->makeService();

        $this->assertSame(['QRIS', 'VA'], collect($service->paymentMethods())->pluck('code')->all());
        $this->assertSame('QRIS', $service->defaultPaymentMethod(150000));
        $this->assertSame('VA', $service->defaultPaymentMethod(15000000));

        $this->expectException(RuntimeException::class);
        $service->assertPaymentMethodAvailable('GOPAY', 150000);
    }

    public function test_transaksi_snap_dibuat_dengan_nominal_kanal_item_dan_kredensial_server(): void
    {
        config([
            'services.midtrans.channels' => ['QRIS', 'VA'],
            'services.midtrans.expiry_minutes' => 60,
        ]);
        $service = $this->makeService();

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-contract',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-token-contract',
            ], 201),
        ]);

        $transaction = $service->createTransaction(
            'SPH-CONTRACT-001',
            150000,
            'VA',
            [
                ['id' => 'TAG-1', 'name' => 'SPP Juli', 'price' => 100000],
                ['id' => 'TAG-2', 'name' => str_repeat('Nama panjang ', 10), 'price' => 50000],
            ],
            ['first_name' => 'Wali Uji', 'email' => 'wali@example.test'],
            'https://app.sipaduhok.id/wali-siswa/pembayaran/digital/10',
            'sandbox',
        );

        $this->assertSame('https://app.sandbox.midtrans.com/snap/v4/redirection/snap-token-contract', $transaction['paymentUrl']);
        $this->assertSame('pending', $transaction['status']);
        $this->assertSame('VA', $transaction['paymentMethod']);
        $this->assertSame(150000, $transaction['totalPayment']);
        $this->assertNotNull($transaction['expiresAt']);

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && $request->hasHeader('Authorization', 'Basic '.base64_encode(self::SERVER_KEY.':'))
                && data_get($request->data(), 'transaction_details.order_id') === 'SPH-CONTRACT-001'
                && data_get($request->data(), 'transaction_details.gross_amount') === 150000
                && in_array('bca_va', $request['enabled_payments'], true)
                && ! in_array('qris', $request['enabled_payments'], true)
                && count($request['item_details']) === 2
                && mb_strlen($request['item_details'][1]['name']) <= 50
                && data_get($request->data(), 'callbacks.finish') === 'https://app.sipaduhok.id/wali-siswa/pembayaran/digital/10'
                && data_get($request->data(), 'expiry.unit') === 'minute'
                && data_get($request->data(), 'expiry.duration') === 60;
        });
    }

    public function test_item_tidak_dikirim_jika_total_tidak_sama_dan_url_asing_ditolak(): void
    {
        config(['services.midtrans.channels' => ['QRIS']]);
        $service = $this->makeService();

        Http::fake([
            '*' => Http::response([
                'token' => 'snap-token',
                'redirect_url' => 'https://evil.example.com/snap/v4/redirection/snap-token',
            ], 201),
        ]);

        try {
            $service->createTransaction('SPH-EVIL-001', 10000, 'QRIS', [
                ['id' => 'TAG-1', 'name' => 'SPP', 'price' => 9000],
            ], [], 'https://app.test/finish', 'sandbox');
            $this->fail('URL pembayaran di luar domain Midtrans harus ditolak.');
        } catch (RuntimeException $exception) {
            $this->assertSame('URL pembayaran digital tidak valid.', $exception->getMessage());
        }

        Http::assertSent(fn (Request $request): bool => ! array_key_exists('item_details', $request->data()));
    }

    public function test_status_core_api_dinormalisasi_ke_kanal_dan_waktu_kedaluwarsa(): void
    {
        $service = $this->makeService();

        Http::fake([
            'https://api.sandbox.midtrans.com/v2/SPH-STATUS-001/status' => Http::response([
                'status_code' => '201',
                'transaction_id' => 'trx-status-001',
                'order_id' => 'SPH-STATUS-001',
                'gross_amount' => '50000.00',
                'payment_type' => 'bank_transfer',
                'va_numbers' => [['bank' => 'bni', 'va_number' => '9881234']],
                'transaction_status' => 'pending',
                'expiry_time' => '2026-10-11 10:00:00',
                'signature_key' => str_repeat('a', 128),
            ]),
        ]);

        $transaction = $service->getTransactionStatus('SPH-STATUS-001', 50000, 'sandbox');

        $this->assertSame('trx-status-001', $transaction['id']);
        $this->assertSame('BNIVA', $transaction['paymentMethod']);
        $this->assertSame('pending', $transaction['status']);
        $this->assertSame('2026-10-11T10:00:00+07:00', $transaction['expiresAt']);
        $this->assertArrayNotHasKey('signature_key', $transaction['raw']);
    }

    public function test_status_404_dibedakan_dari_gangguan_api_dan_nominal_berbeda(): void
    {
        $service = $this->makeService();

        Http::fake([
            '*/SPH-HTTP-404/status' => Http::response(['status_code' => '404', 'status_message' => "Transaction doesn't exist."], 404),
            '*/SPH-BODY-404/status' => Http::response(['status_code' => '404', 'status_message' => "Transaction doesn't exist."]),
            '*/SPH-DOWN/status' => Http::response(['status_message' => 'Internal error'], 500),
            '*/SPH-AMOUNT/status' => Http::response([
                'status_code' => '200',
                'order_id' => 'SPH-AMOUNT',
                'gross_amount' => '1000.00',
                'transaction_status' => 'settlement',
            ]),
        ]);

        $this->assertNull($service->findTransactionStatus('SPH-HTTP-404', 10000, 'sandbox'));
        $this->assertNull($service->findTransactionStatus('SPH-BODY-404', 10000, 'sandbox'));

        foreach (['SPH-DOWN', 'SPH-AMOUNT'] as $orderId) {
            try {
                $service->findTransactionStatus($orderId, 10000, 'sandbox');
                $this->fail("{$orderId} harus melempar exception.");
            } catch (RuntimeException) {
                $this->assertTrue(true);
            }
        }
    }

    public function test_penutupan_memakai_expire_untuk_pending_dan_cancel_untuk_kartu_ditinjau(): void
    {
        $service = $this->makeService();

        Http::fake([
            '*/SPH-PENDING/expire' => Http::response([
                'status_code' => '407',
                'order_id' => 'SPH-PENDING',
                'transaction_status' => 'expire',
            ]),
            '*/SPH-CHALLENGE/cancel' => Http::response([
                'status_code' => '200',
                'order_id' => 'SPH-CHALLENGE',
                'transaction_status' => 'cancel',
            ]),
        ]);

        $this->assertSame('expire', $service->cancelTransaction('SPH-PENDING', 'pending', 'sandbox')['status']);
        $this->assertSame('cancel', $service->cancelTransaction('SPH-CHALLENGE', 'capture', 'sandbox')['status']);
    }

    public function test_pemetaan_status_dan_verifikasi_signature(): void
    {
        $service = $this->makeService();

        $this->assertSame('disetujui', $service->mapStatus('settlement'));
        $this->assertSame('disetujui', $service->mapStatus('capture', 'accept'));
        $this->assertSame('pending', $service->mapStatus('capture', 'challenge'));
        $this->assertSame('ditolak', $service->mapStatus('capture', 'deny'));
        $this->assertSame('pending', $service->mapStatus('pending'));
        $this->assertSame('pending', $service->mapStatus('refund'));
        foreach (['deny', 'cancel', 'expire', 'failure'] as $status) {
            $this->assertSame('ditolak', $service->mapStatus($status));
        }

        $signature = hash('sha512', 'SPH-SIG-001'.'200'.'50000.00'.self::SERVER_KEY);
        $this->assertTrue($service->verifySignature('SPH-SIG-001', '200', '50000.00', $signature, [self::SERVER_KEY]));
        $this->assertTrue($service->verifySignature('SPH-SIG-001', '200', '50000.00', strtoupper($signature), [self::SERVER_KEY]));
        $this->assertFalse($service->verifySignature('SPH-SIG-001', '200', '50000', $signature, [self::SERVER_KEY]));
        $this->assertFalse($service->verifySignature('SPH-SIG-001', '200', '50000.00', $signature, ['SB-Mid-server-lain']));
        $this->assertFalse($service->verifySignature('SPH-SIG-001', '200', '50000.00', 'bukan-hex', [self::SERVER_KEY]));
    }

    public function test_kredensial_lengkap_tetapi_kanal_dinonaktifkan_ditolak_server_side(): void
    {
        $service = $this->makeService(enabled: false);

        $this->assertFalse($service->isConfigured());

        $this->expectException(RuntimeException::class);
        $service->createTransaction('SPH-OFF-001', 10000, 'QRIS', [], [], 'https://app.test/finish');
    }

    private function makeService(bool $enabled = true): MidtransService
    {
        $info = new class($enabled) extends InfoPembayaran
        {
            public function __construct(private bool $enabledForTest = true)
            {
                parent::__construct();
                $this->midtrans_is_production = false;
            }

            public function isMidtransEnabled(): bool
            {
                return $this->enabledForTest;
            }

            public function getMidtransServerKey(string $environment): ?string
            {
                return 'SB-Mid-server-contractTestKey123';
            }
        };

        $reflection = new ReflectionClass(MidtransService::class);
        /** @var MidtransService $service */
        $service = $reflection->newInstanceWithoutConstructor();
        $reflection->getProperty('infoPembayaran')->setValue($service, $info);

        return $service;
    }
}
