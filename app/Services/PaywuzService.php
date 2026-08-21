<?php

namespace App\Services;

use App\Models\InfoPembayaran;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PaywuzService
{
    private InfoPembayaran $infoPembayaran;

    public function __construct()
    {
        $this->infoPembayaran = InfoPembayaran::getInstance();
    }

    public function isConfigured(): bool
    {
        return $this->infoPembayaran->isPaywuzEnabled();
    }

    public function environment(): string
    {
        return $this->infoPembayaran->paywuz_is_production ? 'production' : 'sandbox';
    }

    public function apiKey(?string $environment = null): ?string
    {
        return $this->infoPembayaran->getPaywuzApiKey($environment ?? $this->environment());
    }

    /** @return list<string> */
    public function apiKeys(): array
    {
        return collect([
            $this->apiKey('sandbox'),
            $this->apiKey('production'),
        ])->filter()->unique()->values()->all();
    }

    /** @return list<array<string, int|string>> */
    public function paymentMethods(): array
    {
        $apiKey = $this->requiredApiKey();

        return Cache::remember('paywuz:payment-methods:'.sha1($apiKey), now()->addMinutes(5), function (): array {
            $data = $this->data($this->client()->get('/payment-methods'));

            if (! is_array($data)) {
                throw new RuntimeException('Respons metode pembayaran digital tidak valid.');
            }

            $methods = collect($data)->map(function (mixed $method): ?array {
                if (! is_array($method) || blank($method['code'] ?? null) || blank($method['name'] ?? null)) {
                    return null;
                }

                return [
                    'code' => (string) $method['code'],
                    'name' => (string) $method['name'],
                    'type' => (string) ($method['type'] ?? 'unknown'),
                    'fee_flat' => max(0, (int) data_get($method, 'fee.flatIdr', 0)),
                    'fee_percent_bps' => max(0, (int) data_get($method, 'fee.percentBps', 0)),
                    'min_amount' => max(1, (int) data_get($method, 'limits.minIdr', 1)),
                    'max_amount' => max(1, (int) data_get($method, 'limits.maxIdr', PHP_INT_MAX)),
                ];
            })->filter();

            // VA meta-method membuka pilihan bank di halaman pembayaran. Jika tersedia,
            // opsi VA per bank tidak perlu memenuhi modal dengan pilihan duplikat.
            if ($methods->contains(fn (array $method) => $method['code'] === 'VA' && $method['type'] === 'meta')) {
                $methods = $methods->reject(fn (array $method) => $method['type'] === 'virtual_account');
            }

            return $methods->values()->all();
        });
    }

    public function assertPaymentMethodAvailable(string $code, int $amount): void
    {
        $method = collect($this->paymentMethods())->firstWhere('code', $code);

        if (! $method) {
            throw new RuntimeException('Metode pembayaran digital tidak tersedia.');
        }

        if ($amount < $method['min_amount'] || $amount > $method['max_amount']) {
            throw new RuntimeException('Nominal tagihan berada di luar batas metode pembayaran yang dipilih.');
        }
    }

    public function defaultPaymentMethod(int $amount): string
    {
        $availableMethods = collect($this->paymentMethods())
            ->filter(fn (array $method): bool => $amount >= $method['min_amount'] && $amount <= $method['max_amount'])
            ->values();

        if ($availableMethods->isEmpty()) {
            throw new RuntimeException('Tidak ada kanal pembayaran digital yang cocok untuk nominal tagihan ini.');
        }

        foreach (['QRIS', 'VA'] as $preferredCode) {
            $method = $availableMethods->first(
                fn (array $method): bool => strtoupper((string) $method['code']) === $preferredCode
            );

            if ($method) {
                return (string) $method['code'];
            }
        }

        return (string) $availableMethods->first()['code'];
    }

    /** @return array<string, mixed> */
    public function createTransaction(
        string $orderId,
        int $amount,
        string $paymentMethod,
        int $pembayaranId,
        int $siswaId,
        string $redirectUrl,
        ?string $environment = null,
    ): array {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Pembayaran digital belum dikonfigurasi.');
        }

        if (blank($paymentMethod)) {
            $paymentMethod = $this->defaultPaymentMethod($amount);
        }

        $this->assertPaymentMethodAvailable($paymentMethod, $amount);

        $data = $this->data($this->client($environment)->post('/transactions', [
            'orderId' => $orderId,
            'amount' => $amount,
            'paymentMethod' => $paymentMethod,
            'expiryMinutes' => max(5, min((int) config('services.paywuz.expiry_minutes', 720), 10080)),
            'redirectUrl' => $redirectUrl,
            'feeByMerchant' => (bool) $this->infoPembayaran->paywuz_fee_by_merchant,
            'metadata' => [
                'pembayaran_id' => $pembayaranId,
                'siswa_id' => $siswaId,
            ],
        ]));

        $this->assertTransactionResponse($data, $orderId, $amount);

        $paymentUrl = filled($data['paymentUrl'] ?? null) ? (string) $data['paymentUrl'] : null;
        if (! $paymentUrl || ! $this->isTrustedPaymentUrl($paymentUrl)) {
            throw new RuntimeException('URL pembayaran digital tidak valid.');
        }

        return $data;
    }

    /** @return array<string, mixed> */
    public function getTransactionStatus(string $orderId, int $amount, ?string $environment = null): array
    {
        $data = $this->data($this->client($environment)->get('/transactions/'.rawurlencode($orderId)));
        $this->assertTransactionResponse($data, $orderId, $amount, requirePaymentUrl: false);

        return $data;
    }

    /** @return array<string, mixed> */
    public function cancelTransaction(string $orderId, int $amount, ?string $environment = null): array
    {
        $data = $this->data($this->client($environment)->post('/transactions/'.rawurlencode($orderId).'/cancel'));
        $this->assertTransactionResponse($data, $orderId, $amount, requirePaymentUrl: false);

        return $data;
    }

    public function mapStatus(string $status): string
    {
        return match ($status) {
            'settlement', 'success' => 'disetujui',
            'failed', 'cancelled', 'expired' => 'ditolak',
            default => 'pending',
        };
    }

    private function client(?string $environment = null): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.paywuz.base_url', 'https://api.paywuz.id/v1'), '/'))
            ->withToken($this->requiredApiKey($environment))
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->retry(2, 300, throw: false);
    }

    private function requiredApiKey(?string $environment = null): string
    {
        $apiKey = $this->apiKey($environment);

        if (blank($apiKey)) {
            throw new RuntimeException('API key pembayaran digital belum dikonfigurasi.');
        }

        return $apiKey;
    }

    private function data(Response $response): mixed
    {
        if (! $response->successful()) {
            $message = $response->json('message') ?: 'Permintaan ke penyedia pembayaran gagal.';
            throw new RuntimeException(Str::limit((string) $message, 300));
        }

        return $response->json('data');
    }

    private function assertTransactionResponse(mixed $data, string $orderId, int $amount, bool $requirePaymentUrl = true): void
    {
        if (! is_array($data)
            || blank($data['id'] ?? null)
            || blank($data['status'] ?? null)
            || ! hash_equals($orderId, (string) ($data['orderId'] ?? ''))
            || (int) ($data['amount'] ?? 0) !== $amount
            || ($requirePaymentUrl && blank($data['paymentUrl'] ?? null))) {
            throw new RuntimeException('Respons transaksi pembayaran digital tidak lengkap.');
        }

        $totalPayment = (int) ($data['totalPayment'] ?? $amount);
        if ($totalPayment < $amount) {
            throw new RuntimeException('Nominal transaksi pembayaran digital tidak cocok.');
        }
    }

    private function isTrustedPaymentUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));

        return ($parts['scheme'] ?? null) === 'https'
            && ($host === 'paywuz.id'
                || str_ends_with($host, '.paywuz.id')
                || $host === 'paywuz.com'
                || str_ends_with($host, '.paywuz.com'));
    }
}
