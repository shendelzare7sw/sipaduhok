<?php

namespace App\Services;

use App\Models\InfoPembayaran;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class MidtransService
{
    public const GATEWAY = 'midtrans';

    /**
     * Katalog kanal yang dipetakan ke parameter enabled_payments Snap.
     * Batas nominal mengikuti batas umum kanal; kanal tetap harus aktif di
     * dashboard Midtrans agar dapat dipakai.
     *
     * @var array<string, array{name: string, type: string, enabled_payments: list<string>, min: int, max: int}>
     */
    private const CHANNELS = [
        'QRIS' => [
            'name' => 'QRIS',
            'type' => 'qris',
            'enabled_payments' => ['qris'],
            'min' => 1,
            'max' => 10_000_000,
        ],
        'GOPAY' => [
            'name' => 'GoPay',
            'type' => 'ewallet',
            'enabled_payments' => ['gopay'],
            'min' => 1,
            'max' => 10_000_000,
        ],
        'SHOPEEPAY' => [
            'name' => 'ShopeePay',
            'type' => 'ewallet',
            'enabled_payments' => ['shopeepay'],
            'min' => 1,
            'max' => 10_000_000,
        ],
        'VA' => [
            'name' => 'Virtual Account (Pilih Bank)',
            'type' => 'meta',
            'enabled_payments' => ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'cimb_va', 'echannel', 'other_va'],
            'min' => 10_000,
            'max' => 100_000_000,
        ],
        'INDOMARET' => [
            'name' => 'Indomaret',
            'type' => 'retail',
            'enabled_payments' => ['indomaret'],
            'min' => 10_000,
            'max' => 5_000_000,
        ],
        'ALFAMART' => [
            'name' => 'Alfamart',
            'type' => 'retail',
            'enabled_payments' => ['alfamart'],
            'min' => 10_000,
            'max' => 5_000_000,
        ],
    ];

    /** Status Core API yang dianggap respons sah (bukan error). */
    private const VALID_STATUS_CODES = ['200', '201', '202', '407'];

    private InfoPembayaran $infoPembayaran;

    public function __construct()
    {
        $this->infoPembayaran = InfoPembayaran::getInstance();
    }

    public function isConfigured(): bool
    {
        return $this->infoPembayaran->isMidtransEnabled();
    }

    public function environment(): string
    {
        return $this->infoPembayaran->midtrans_is_production ? 'production' : 'sandbox';
    }

    public function serverKey(?string $environment = null): ?string
    {
        return $this->infoPembayaran->getMidtransServerKey($environment ?? $this->environment());
    }

    /** @return list<string> */
    public function serverKeys(): array
    {
        return collect([
            $this->serverKey('sandbox'),
            $this->serverKey('production'),
        ])->filter()->unique()->values()->all();
    }

    /** @return list<array<string, int|string>> */
    public function paymentMethods(): array
    {
        return collect((array) config('services.midtrans.channels', []))
            ->map(fn (mixed $code): string => strtoupper(trim((string) $code)))
            ->unique()
            ->filter(fn (string $code): bool => array_key_exists($code, self::CHANNELS))
            ->map(fn (string $code): array => [
                'code' => $code,
                'name' => self::CHANNELS[$code]['name'],
                'type' => self::CHANNELS[$code]['type'],
                // Midtrans memotong MDR dari dana settlement sekolah, bukan dari
                // wali siswa. Kunci biaya dipertahankan agar kontrak view stabil.
                'fee_flat' => 0,
                'fee_percent_bps' => 0,
                'min_amount' => self::CHANNELS[$code]['min'],
                'max_amount' => self::CHANNELS[$code]['max'],
            ])
            ->values()
            ->all();
    }

    public function assertPaymentMethodAvailable(string $code, int $amount): void
    {
        $method = collect($this->paymentMethods())->firstWhere('code', strtoupper(trim($code)));

        if (! $method) {
            throw new RuntimeException('Metode pembayaran digital tidak tersedia.');
        }

        if ($amount < $method['min_amount'] || $amount > $method['max_amount']) {
            throw new RuntimeException('Nominal tagihan berada di luar batas metode pembayaran yang dipilih.');
        }
    }

    public function defaultPaymentMethod(int $amount): string
    {
        $availableMethods = collect($this->availablePaymentMethods($amount));

        if ($availableMethods->isEmpty()) {
            throw new RuntimeException('Tidak ada kanal pembayaran digital yang cocok untuk nominal tagihan ini.');
        }

        foreach (['QRIS', 'VA'] as $preferredCode) {
            if ($availableMethods->contains('code', $preferredCode)) {
                return $preferredCode;
            }
        }

        return (string) $availableMethods->first()['code'];
    }

    /** @return list<array<string, int|string>> */
    public function availablePaymentMethods(int $amount): array
    {
        return collect($this->paymentMethods())
            ->filter(fn (array $method): bool => $amount >= $method['min_amount'] && $amount <= $method['max_amount'])
            ->values()
            ->all();
    }

    /**
     * Membuat transaksi Snap dan mengembalikan data ternormalisasi.
     *
     * @param  list<array{id: string, name: string, price: int, quantity?: int}>  $itemDetails
     * @param  array<string, string>  $customerDetails
     * @return array<string, mixed>
     */
    public function createTransaction(
        string $orderId,
        int $amount,
        string $paymentMethod,
        array $itemDetails,
        array $customerDetails,
        string $finishUrl,
        ?string $environment = null,
    ): array {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Pembayaran digital belum dikonfigurasi.');
        }

        $paymentMethod = strtoupper(trim($paymentMethod));
        if ($paymentMethod === '') {
            $paymentMethod = $this->defaultPaymentMethod($amount);
        }

        $this->assertPaymentMethodAvailable($paymentMethod, $amount);

        $environment ??= $this->environment();
        $expiryMinutes = max(5, min((int) config('services.midtrans.expiry_minutes', 720), 10080));
        $startTime = CarbonImmutable::now('Asia/Jakarta');

        $payload = array_filter([
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => $this->normalizeItems($itemDetails, $amount),
            'customer_details' => $customerDetails ?: null,
            'enabled_payments' => self::CHANNELS[$paymentMethod]['enabled_payments'],
            'callbacks' => ['finish' => $finishUrl],
            'expiry' => [
                'start_time' => $startTime->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => $expiryMinutes,
            ],
        ], fn (mixed $value): bool => $value !== null);

        // Pembuatan transaksi tidak di-retry otomatis: order_id Midtrans hanya
        // dapat dipakai sekali sehingga retry buta justru mengunci order.
        $response = $this->request($environment)
            ->post($this->snapUrl($environment).'/transactions', $payload);
        $data = $this->json($response);

        $token = (string) ($data['token'] ?? '');
        $paymentUrl = (string) ($data['redirect_url'] ?? '');

        if ($token === '' || ! $this->isTrustedPaymentUrl($paymentUrl)) {
            throw new RuntimeException('URL pembayaran digital tidak valid.');
        }

        return [
            'id' => null,
            'orderId' => $orderId,
            'amount' => $amount,
            'totalPayment' => $amount,
            'status' => 'pending',
            'fraudStatus' => null,
            'paymentMethod' => $paymentMethod,
            'paymentUrl' => $paymentUrl,
            'token' => $token,
            'expiresAt' => $startTime->addMinutes($expiryMinutes)->toIso8601String(),
        ];
    }

    /** @return array<string, mixed> */
    public function getTransactionStatus(string $orderId, int $amount, ?string $environment = null): array
    {
        $data = $this->findTransactionStatus($orderId, $amount, $environment);

        if ($data === null) {
            throw new RuntimeException('Transaksi pembayaran digital tidak ditemukan.');
        }

        return $data;
    }

    /**
     * Status transaksi dari Core API. Null berarti Midtrans belum memiliki
     * transaksi untuk order ini (Snap belum dipakai memilih kanal).
     *
     * @return array<string, mixed>|null
     */
    public function findTransactionStatus(string $orderId, int $amount, ?string $environment = null): ?array
    {
        $response = $this->request($environment)
            ->retry(2, 300, throw: false)
            ->get($this->apiUrl($environment).'/'.rawurlencode($orderId).'/status');

        if ($response->notFound() || (string) $response->json('status_code') === '404') {
            return null;
        }

        $data = $this->json($response);

        if (! in_array((string) ($data['status_code'] ?? ''), self::VALID_STATUS_CODES, true)) {
            throw new RuntimeException(Str::limit((string) ($data['status_message'] ?? 'Status transaksi tidak valid.'), 300));
        }

        return $this->normalizeTransaction($data, $orderId, $amount);
    }

    /**
     * Menutup transaksi yang masih menunggu. Transaksi pending ditutup dengan
     * endpoint expire, sedangkan kartu yang masih ditinjau (challenge) dengan cancel.
     *
     * @return array<string, mixed>
     */
    public function cancelTransaction(string $orderId, string $currentStatus, ?string $environment = null): array
    {
        $action = strtolower(trim($currentStatus)) === 'pending' ? 'expire' : 'cancel';
        $response = $this->request($environment)
            ->retry(2, 300, throw: false)
            ->post($this->apiUrl($environment).'/'.rawurlencode($orderId).'/'.$action);
        $data = $this->json($response);
        $status = strtolower((string) ($data['transaction_status'] ?? ''));

        if (! hash_equals($orderId, (string) ($data['order_id'] ?? ''))
            || ! in_array($status, ['expire', 'cancel'], true)) {
            throw new RuntimeException(Str::limit(
                (string) ($data['status_message'] ?? 'Respons pembatalan transaksi pembayaran digital tidak lengkap.'),
                300,
            ));
        }

        return [
            'orderId' => $orderId,
            'status' => $status,
            'fraudStatus' => $data['fraud_status'] ?? null,
        ];
    }

    public function mapStatus(string $status, ?string $fraudStatus = null): string
    {
        $fraudStatus = strtolower(trim((string) $fraudStatus));

        return match (strtolower(trim($status))) {
            'settlement' => 'disetujui',
            'capture' => match ($fraudStatus) {
                '', 'accept' => 'disetujui',
                'deny' => 'ditolak',
                default => 'pending',
            },
            'deny', 'cancel', 'expire', 'failure' => 'ditolak',
            default => 'pending',
        };
    }

    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signature, array $serverKeys): bool
    {
        if (preg_match('/^[a-f0-9]{128}$/i', $signature) !== 1) {
            return false;
        }

        foreach ($serverKeys as $serverKey) {
            if (filled($serverKey) && hash_equals(
                hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey),
                strtolower($signature),
            )) {
                return true;
            }
        }

        return false;
    }

    public function isTrustedPaymentUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));

        return ($parts['scheme'] ?? null) === 'https'
            && in_array($host, ['app.midtrans.com', 'app.sandbox.midtrans.com'], true);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeTransaction(array $data, string $orderId, int $amount): array
    {
        if (! hash_equals($orderId, (string) ($data['order_id'] ?? ''))
            || blank($data['transaction_status'] ?? null)
            || $this->parseAmount($data['gross_amount'] ?? null) !== $amount) {
            throw new RuntimeException('Respons transaksi pembayaran digital tidak cocok dengan data sekolah.');
        }

        return [
            'id' => filled($data['transaction_id'] ?? null) ? (string) $data['transaction_id'] : null,
            'orderId' => $orderId,
            'amount' => $amount,
            'totalPayment' => $amount,
            'status' => strtolower((string) $data['transaction_status']),
            'fraudStatus' => filled($data['fraud_status'] ?? null) ? strtolower((string) $data['fraud_status']) : null,
            'paymentMethod' => $this->channelCode($data),
            'expiresAt' => $this->parseDate($data['expiry_time'] ?? null),
            'raw' => collect($data)->except(['signature_key'])->all(),
        ];
    }

    /** @param array<string, mixed> $data */
    private function channelCode(array $data): ?string
    {
        $type = strtolower((string) ($data['payment_type'] ?? ''));

        return match ($type) {
            '' => null,
            'qris' => 'QRIS',
            'gopay' => 'GOPAY',
            'shopeepay' => 'SHOPEEPAY',
            'echannel' => 'MANDIRIVA',
            'credit_card' => 'CREDIT_CARD',
            'cstore' => filled($data['store'] ?? null) ? strtoupper((string) $data['store']) : 'CSTORE',
            'bank_transfer' => match (true) {
                filled($data['permata_va_number'] ?? null) => 'PERMATAVA',
                filled(data_get($data, 'va_numbers.0.bank')) => strtoupper((string) data_get($data, 'va_numbers.0.bank')).'VA',
                default => 'VA',
            },
            default => strtoupper($type),
        };
    }

    private function parseAmount(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $amount = (float) $value;

        return floor($amount) === $amount ? (int) $amount : null;
    }

    private function parseDate(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            // Core API mengirim waktu lokal Jakarta tanpa offset.
            return CarbonImmutable::parse((string) $value, 'Asia/Jakarta')->toIso8601String();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  list<array{id: string, name: string, price: int, quantity?: int}>  $items
     * @return list<array<string, int|string>>|null
     */
    private function normalizeItems(array $items, int $amount): ?array
    {
        $normalized = collect($items)->map(fn (array $item): array => [
            'id' => Str::limit((string) $item['id'], 50, ''),
            'name' => Str::limit(trim((string) $item['name']) ?: 'Tagihan sekolah', 50, ''),
            'price' => (int) $item['price'],
            'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
        ]);

        // Midtrans menolak transaksi jika total item tidak sama dengan
        // gross_amount; dalam kondisi itu rincian item tidak dikirim.
        $total = $normalized->sum(fn (array $item): int => $item['price'] * $item['quantity']);

        return $normalized->isNotEmpty() && $total === $amount ? $normalized->values()->all() : null;
    }

    private function request(?string $environment): PendingRequest
    {
        return Http::withBasicAuth($this->requiredServerKey($environment), '')
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(15);
    }

    private function snapUrl(?string $environment): string
    {
        return rtrim((string) config('services.midtrans.snap_url.'.$this->resolveEnvironment($environment)), '/');
    }

    private function apiUrl(?string $environment): string
    {
        return rtrim((string) config('services.midtrans.api_url.'.$this->resolveEnvironment($environment)), '/');
    }

    private function resolveEnvironment(?string $environment): string
    {
        return ($environment ?? $this->environment()) === 'production' ? 'production' : 'sandbox';
    }

    private function requiredServerKey(?string $environment): string
    {
        $serverKey = $this->serverKey($this->resolveEnvironment($environment));

        if (blank($serverKey)) {
            throw new RuntimeException('Server key pembayaran digital belum dikonfigurasi.');
        }

        return $serverKey;
    }

    /** @return array<string, mixed> */
    private function json(Response $response): array
    {
        $data = $response->json();

        if (! $response->successful() || ! is_array($data)) {
            $message = is_array($data)
                ? (collect($data['error_messages'] ?? [])->filter()->implode(' ') ?: ($data['status_message'] ?? null))
                : null;

            throw new RuntimeException(Str::limit((string) ($message ?: 'Permintaan ke penyedia pembayaran gagal.'), 300));
        }

        return $data;
    }
}
