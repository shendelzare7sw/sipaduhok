<?php

namespace App\Services;

use App\Models\InfoPembayaran;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    protected $infoPembayaran;

    public function __construct()
    {
        $this->infoPembayaran = InfoPembayaran::getInstance();
        $this->configureMidtrans();
    }

    /**
     * Configure Midtrans settings
     */
    protected function configureMidtrans()
    {
        Config::$serverKey = $this->infoPembayaran->getDecryptedServerKey();
        Config::$clientKey = $this->infoPembayaran->midtrans_client_key;
        Config::$isProduction = $this->infoPembayaran->midtrans_is_production ?? false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Fix SSL certificate issue for Windows/Laragon
        $cacertPath = base_path('cacert.pem');

        if (file_exists($cacertPath)) {
            Config::$curlOptions[CURLOPT_CAINFO] = $cacertPath;
        } else {
            // Fallback: disable SSL verification for development only
            // WARNING: Never use this in production!
            if (!Config::$isProduction) {
                Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
                Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = 0;
                \Log::warning('Midtrans: SSL verification disabled for development. DO NOT use in production!');
            }
        }
    }

    /**
     * Check if Midtrans is configured
     */
    public function isConfigured()
    {
        return $this->infoPembayaran->hasMidtrans();
    }

    /**
     * Create Snap transaction token
     *
     * @param array $params
     * @return string Snap token
     * @throws \Exception
     */
    public function createSnapToken($params)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Midtrans belum dikonfigurasi. Silakan hubungi administrator.');
        }

        try {
            // Log request params for debugging
            \Log::info('Midtrans Create Snap Token Request', [
                'order_id' => $params['transaction_details']['order_id'] ?? 'N/A',
                'amount' => $params['transaction_details']['gross_amount'] ?? 'N/A',
                'server_key_length' => strlen(Config::$serverKey ?? ''),
                'client_key_length' => strlen(Config::$clientKey ?? ''),
                'is_production' => Config::$isProduction,
            ]);

            // Suppress PHP warnings from Midtrans SDK (known issue with array key access)
            $snapToken = @Snap::getSnapToken($params);

            \Log::info('Midtrans Snap Token Created Successfully', [
                'order_id' => $params['transaction_details']['order_id'],
                'token_length' => strlen($snapToken),
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Token Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'server_key_configured' => !empty(Config::$serverKey),
                'client_key_configured' => !empty(Config::$clientKey),
            ]);

            throw new \Exception('Gagal membuat token pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Get Snap URL for payment
     *
     * @param string $snapToken
     * @return string
     */
    public function getSnapUrl($snapToken)
    {
        $isProduction = $this->infoPembayaran->midtrans_is_production ?? false;

        if ($isProduction) {
            return "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}";
        } else {
            return "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}";
        }
    }

    /**
     * Build transaction parameters for Midtrans
     *
     * @param string $orderId
     * @param int $amount
     * @param array $customerDetails
     * @param array $itemDetails
     * @return array
     */
    public function buildTransactionParams($orderId, $amount, $customerDetails, $itemDetails = [])
    {
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
            'enabled_payments' => [
                'credit_card',
                'bca_va',
                'bni_va',
                'bri_va',
                'permata_va',
                'other_va',
                'gopay',
                'shopeepay',
                'qris',
            ],
            'credit_card' => [
                'secure' => true,
                'bank' => 'bca',
                'installment' => [
                    'required' => false,
                ],
            ],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'days',
                'duration' => 1,
            ],
        ];

        return $params;
    }

    /**
     * Verify signature from Midtrans notification
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $signatureKey
     * @return bool
     */
    public function verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)
    {
        $serverKey = $this->infoPembayaran->getDecryptedServerKey();
        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return $mySignature === $signatureKey;
    }

    /**
     * Get transaction status from Midtrans
     *
     * @param string $orderId
     * @return object
     */
    public function getTransactionStatus($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);
            return $status;
        } catch (\Exception $e) {
            \Log::error('Midtrans Get Status Error: ' . $e->getMessage());
            throw new \Exception('Gagal mendapatkan status transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Map Midtrans transaction status to our payment status
     *
     * @param string $transactionStatus
     * @param string $fraudStatus
     * @return string (pending|disetujui|ditolak)
     */
    public function mapTransactionStatus($transactionStatus, $fraudStatus = 'accept')
    {
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                return 'disetujui';
            }
        } elseif ($transactionStatus == 'settlement') {
            return 'disetujui';
        } elseif ($transactionStatus == 'pending') {
            return 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            return 'ditolak';
        }

        return 'pending';
    }
}
