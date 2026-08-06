<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\FinancialAuditLog;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    protected $midtransService;

    public function __construct()
    {
        $this->midtransService = new MidtransService();
    }

    /**
     * Handle Midtrans notification webhook
     */
    public function notification(Request $request)
    {
        try {
            // Get notification data
            $notification = $request->all();

            Log::info('Midtrans Webhook Received', $notification);

            // Extract transaction data
            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? 'accept';
            $statusCode = $notification['status_code'] ?? null;
            $grossAmount = $notification['gross_amount'] ?? null;
            $signatureKey = $notification['signature_key'] ?? null;
            $transactionId = $notification['transaction_id'] ?? null;
            $paymentType = $notification['payment_type'] ?? null;

            // Verify signature
            if (!$this->midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
                Log::error('Midtrans Invalid Signature', [
                    'order_id' => $orderId,
                    'signature' => $signatureKey,
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid signature'
                ], 403);
            }

            // Find pembayaran by order_id
            $pembayaranList = Pembayaran::where('order_id', $orderId)->get();

            if ($pembayaranList->isEmpty()) {
                Log::error('Midtrans Pembayaran Not Found', ['order_id' => $orderId]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment not found'
                ], 404);
            }

            // Map transaction status
            $newStatus = $this->midtransService->mapTransactionStatus($transactionStatus, $fraudStatus);
            $firstPembayaran = $pembayaranList->first();
            $oldStatus = $firstPembayaran->status_validasi;

            if ($oldStatus !== $newStatus) {
                foreach ($pembayaranList as $pembayaran) {
                    // Update pembayaran
                    $pembayaran->update([
                        'status_validasi' => $newStatus,
                        'transaction_id' => $transactionId,
                        'payment_type' => $paymentType,
                        'gateway_response' => json_encode($notification),
                        'tanggal_validasi' => $newStatus === 'disetujui' ? now() : null,
                    ]);

                    // Create audit log for bendahara tracking
                    FinancialAuditLog::create([
                        'user_id' => null, // System action
                        'action' => 'update_status',
                        'model_type' => 'Pembayaran',
                        'model_id' => $pembayaran->id,
                        'old_values' => json_encode(['status_validasi' => $pembayaran->getOriginal('status_validasi')]),
                        'new_values' => json_encode([
                            'status_validasi' => $newStatus,
                            'transaction_id' => $transactionId,
                            'payment_type' => $paymentType,
                        ]),
                        'description' => "Pembayaran digital {$orderId} status updated via Midtrans webhook: {$transactionStatus} → {$newStatus}",
                        'ip_address' => request()->ip(),
                        'user_agent' => 'Midtrans Webhook',
                    ]);

                    // Auto-update status tagihan if payment approved
                    if ($newStatus === 'disetujui') {
                        $pembayaran->tagihan->updateStatusBayar();

                        Log::info('Tagihan status auto-updated', [
                            'tagihan_id' => $pembayaran->tagihan_id,
                            'new_tagihan_status' => $pembayaran->tagihan->fresh()->status,
                        ]);

                        // PENTING: Batalkan semua pembayaran pending lainnya untuk tagihan yang sama
                        // Ini mencegah double payment untuk produk/tagihan yang sama
                        $cancelledCount = Pembayaran::where('tagihan_id', $pembayaran->tagihan_id)
                            ->where('siswa_id', $pembayaran->siswa_id)
                            ->where('id', '!=', $pembayaran->id) // Kecuali pembayaran yang baru saja sukses
                            ->where('status_validasi', 'pending')
                            ->update([
                                'status_validasi' => 'ditolak',
                                'catatan' => 'Otomatis dibatalkan karena tagihan sudah dibayar via transaksi lain (Order ID: ' . $orderId . ')',
                            ]);

                        if ($cancelledCount > 0) {
                            Log::info('Auto-cancelled duplicate pending payments', [
                                'tagihan_id' => $pembayaran->tagihan_id,
                                'siswa_id' => $pembayaran->siswa_id,
                                'cancelled_count' => $cancelledCount,
                                'successful_order_id' => $orderId,
                            ]);

                            // Audit log untuk pembatalan otomatis
                            FinancialAuditLog::create([
                                'user_id' => null,
                                'action' => 'auto_cancel_duplicates',
                                'model_type' => 'Pembayaran',
                                'model_id' => $pembayaran->id,
                                'old_values' => null,
                                'new_values' => json_encode([
                                    'cancelled_count' => $cancelledCount,
                                    'reason' => 'duplicate_payment_prevention',
                                ]),
                                'description' => "Otomatis membatalkan {$cancelledCount} pembayaran pending lainnya untuk tagihan yang sama setelah pembayaran {$orderId} berhasil",
                                'ip_address' => request()->ip(),
                                'user_agent' => 'Midtrans Webhook - Auto Cancel',
                            ]);
                        }
                    }
                }

                // Jika pembayaran berhasil disetujui, kirim notifikasi!
                if ($newStatus === 'disetujui') {
                    try {
                        app(\App\Services\NotificationService::class)->notifyPembayaranDigitalBerhasil($pembayaranList);
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim notifikasi Midtrans: ' . $e->getMessage());
                    }
                }
            }

            Log::info('Midtrans Webhook Processed', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'audit_logged' => $oldStatus !== $newStatus,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
