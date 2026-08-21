<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaywuzPaymentStatusService
{
    public function __construct(private readonly PaywuzService $paywuz) {}

    public function sync(string $orderId): bool
    {
        $payments = Pembayaran::query()
            ->where('payment_gateway', 'paywuz')
            ->where('order_id', $orderId)
            ->get();

        if ($payments->isEmpty()) {
            return false;
        }

        try {
            $transaction = $this->paywuz->getTransactionStatus(
                $orderId,
                (int) $payments->sum('jumlah_bayar'),
                $payments->first()->payment_environment,
            );
        } catch (Throwable $exception) {
            Log::warning('Sinkronisasi pembayaran digital gagal.', [
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }

        return $this->apply($orderId, $transaction);
    }

    /** @param array<string, mixed> $transaction */
    public function apply(string $orderId, array $transaction): bool
    {
        $newStatus = $this->paywuz->mapStatus((string) ($transaction['status'] ?? 'pending'));
        $baruDisetujui = collect();

        $changed = DB::transaction(function () use ($orderId, $transaction, $newStatus, &$baruDisetujui): bool {
            $payments = Pembayaran::query()
                ->with('tagihan')
                ->where('payment_gateway', 'paywuz')
                ->where('order_id', $orderId)
                ->lockForUpdate()
                ->get();

            if ($payments->isEmpty() || (int) $payments->sum('jumlah_bayar') !== (int) ($transaction['amount'] ?? 0)) {
                return false;
            }

            $changed = false;
            $groupIds = $payments->pluck('id');

            foreach ($payments as $payment) {
                $oldStatus = $payment->status_validasi;
                $updates = [
                    'transaction_id' => (string) ($transaction['id'] ?? $payment->transaction_id),
                    'payment_type' => (string) ($transaction['paymentMethod'] ?? $payment->payment_type),
                    'gateway_status' => (string) ($transaction['status'] ?? $payment->gateway_status),
                    'gateway_total' => (int) ($transaction['totalPayment'] ?? $payments->sum('jumlah_bayar')),
                    'gateway_response' => $transaction,
                    'payment_expires_at' => filled($transaction['expiresAt'] ?? null) ? $transaction['expiresAt'] : $payment->payment_expires_at,
                ];

                if (filled($transaction['paymentUrl'] ?? null)) {
                    $updates['payment_url'] = (string) $transaction['paymentUrl'];
                    $updates['gateway_error'] = null;
                }

                if ($oldStatus === 'pending' && $newStatus !== 'pending') {
                    $updates['status_validasi'] = $newStatus;
                    $updates['tanggal_validasi'] = $newStatus === 'disetujui' ? now() : null;
                    if ($newStatus === 'disetujui' && ($transaction['status'] ?? null) === 'settlement') {
                        $updates['gateway_settled_at'] = now();
                    }
                }

                $payment->update($updates);

                if ($oldStatus !== $payment->fresh()->status_validasi) {
                    $changed = true;
                    $this->audit($payment, $oldStatus, $newStatus, $orderId);

                    if ($newStatus === 'disetujui') {
                        $baruDisetujui->push($payment);
                        $payment->tagihan->updateStatusBayar();

                        Pembayaran::query()
                            ->where('tagihan_id', $payment->tagihan_id)
                            ->where('siswa_id', $payment->siswa_id)
                            ->whereNotIn('id', $groupIds)
                            ->where('status_validasi', 'pending')
                            ->update([
                                'status_validasi' => 'ditolak',
                                'catatan' => 'Otomatis dibatalkan karena tagihan telah dibayar melalui transaksi lain (Order ID: '.$orderId.').',
                            ]);
                    }
                }
            }

            return $changed;
        });

        if ($baruDisetujui->isNotEmpty()) {
            try {
                app(NotificationService::class)->notifyPembayaranDigitalBerhasil($baruDisetujui);
            } catch (Throwable $exception) {
                Log::error('Gagal mengirim notifikasi pembayaran digital.', ['message' => $exception->getMessage()]);
            }
        }

        return $changed;
    }

    private function audit(Pembayaran $payment, string $oldStatus, string $newStatus, string $orderId): void
    {
        FinancialAuditLog::create([
            'user_id' => null,
            'action' => 'update_status',
            'model_type' => 'Pembayaran',
            'model_id' => $payment->id,
            'old_values' => ['status_validasi' => $oldStatus],
            'new_values' => ['status_validasi' => $newStatus],
            'description' => "Pembayaran digital {$orderId} diperbarui dari {$oldStatus} menjadi {$newStatus} melalui status terverifikasi.",
            'ip_address' => request()->ip(),
            'user_agent' => 'Paywuz Webhook/Status Sync',
        ]);
    }
}
