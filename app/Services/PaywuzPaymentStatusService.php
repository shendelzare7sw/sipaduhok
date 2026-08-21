<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\Pembayaran;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
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
        $duplicateGatewayOrders = collect();

        $changed = DB::transaction(function () use ($orderId, $transaction, $newStatus, &$baruDisetujui, &$duplicateGatewayOrders): bool {
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
                $authoritativeStatus = $oldStatus === 'disetujui' && $newStatus !== 'disetujui'
                    ? 'disetujui'
                    : $newStatus;
                $updates = [
                    'transaction_id' => (string) ($transaction['id'] ?? $payment->transaction_id),
                    'payment_type' => (string) ($transaction['paymentMethod'] ?? $payment->payment_type),
                    'gateway_total' => (int) ($transaction['totalPayment'] ?? $payments->sum('jumlah_bayar')),
                    'gateway_response' => $transaction,
                    'payment_expires_at' => filled($transaction['expiresAt'] ?? null)
                        ? CarbonImmutable::parse((string) $transaction['expiresAt'])
                        : $payment->payment_expires_at,
                ];

                // Status pembayaran yang sudah disetujui tidak boleh diturunkan
                // oleh webhook terlambat/keliru. Selain itu, status Paywuz adalah
                // sumber kebenaran dan boleh memperbaiki status lokal yang sempat
                // salah akibat kegagalan parsial.
                if ($oldStatus !== 'disetujui' || $newStatus === 'disetujui') {
                    $updates['gateway_status'] = (string) ($transaction['status'] ?? $payment->gateway_status);
                }

                if (filled($transaction['paymentUrl'] ?? null)) {
                    $updates['payment_url'] = (string) $transaction['paymentUrl'];
                    $updates['gateway_error'] = null;
                }

                if ($oldStatus !== $authoritativeStatus) {
                    $updates['status_validasi'] = $authoritativeStatus;
                    $updates['tanggal_validasi'] = $authoritativeStatus === 'pending' ? null : now();
                    if ($authoritativeStatus === 'disetujui' && ($transaction['status'] ?? null) === 'settlement') {
                        $updates['gateway_settled_at'] = now();
                    }
                }

                $payment->update($updates);

                if ($oldStatus !== $payment->fresh()->status_validasi) {
                    $changed = true;
                    $this->audit($payment, $oldStatus, $authoritativeStatus, $orderId);

                    if ($authoritativeStatus === 'disetujui') {
                        $baruDisetujui->push($payment);
                        $payment->tagihan->updateStatusBayar();

                        $duplicates = Pembayaran::query()
                            ->where('tagihan_id', $payment->tagihan_id)
                            ->where('siswa_id', $payment->siswa_id)
                            ->whereNotIn('id', $groupIds)
                            ->where('status_validasi', 'pending')
                            ->get();

                        $duplicateGatewayOrders->push(...$duplicates
                            ->where('payment_gateway', 'paywuz')
                            ->pluck('order_id')
                            ->filter()
                            ->all());

                        // Pembayaran Paywuz tidak boleh ditolak secara lokal
                        // sebelum pembatalan remote dikonfirmasi. Metode manual
                        // tidak memiliki transaksi remote sehingga aman ditutup.
                        Pembayaran::query()
                            ->whereIn('id', $duplicates
                                ->where('payment_gateway', '!=', 'paywuz')
                                ->pluck('id'))
                            ->update([
                                'status_validasi' => 'ditolak',
                                'tanggal_validasi' => now(),
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

        foreach ($duplicateGatewayOrders->filter()->unique() as $duplicateOrderId) {
            try {
                $this->cancelIfPending((string) $duplicateOrderId);
            } catch (Throwable $exception) {
                // Pertahankan pending secara lokal jika pembatalan remote belum
                // pasti. Ini lebih aman daripada menyembunyikan tagihan yang
                // masih mungkin dibayar pelanggan.
                Log::critical('Transaksi Paywuz duplikat belum berhasil dibatalkan.', [
                    'paid_order_id' => $orderId,
                    'duplicate_order_id' => $duplicateOrderId,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $changed;
    }

    /**
     * Batalkan order lama secara aman sebelum membuat kanal pengganti.
     *
     * @return bool true jika order lama aman diganti; false jika ternyata sudah dibayar
     */
    public function cancelIfPending(string $orderId): bool
    {
        $payments = Pembayaran::query()
            ->where('payment_gateway', 'paywuz')
            ->where('order_id', $orderId)
            ->get();

        if ($payments->isEmpty()) {
            throw new RuntimeException('Data pembayaran lama tidak ditemukan.');
        }

        $amount = (int) $payments->sum('jumlah_bayar');
        $environment = $payments->first()->payment_environment;
        $transaction = $this->paywuz->findTransactionStatus($orderId, $amount, $environment);

        if ($transaction === null) {
            // 404 terverifikasi: tidak ada transaksi remote yang dapat tertinggal.
            $this->markCancelledWithoutRemoteTransaction($orderId);

            return true;
        }

        $mappedStatus = $this->paywuz->mapStatus((string) ($transaction['status'] ?? 'pending'));
        if ($mappedStatus === 'disetujui') {
            $this->apply($orderId, $transaction);

            return false;
        }

        if ($mappedStatus === 'pending') {
            $cancelled = $this->paywuz->cancelTransaction($orderId, $amount, $environment);

            if (strtolower((string) ($cancelled['status'] ?? '')) !== 'cancelled') {
                throw new RuntimeException('Penyedia pembayaran belum mengonfirmasi pembatalan transaksi lama.');
            }

            // Endpoint cancel dapat mengembalikan payload minimal. Pertahankan
            // amount, metode, dan URL dari hasil GET yang sudah tervalidasi.
            $transaction = array_replace($transaction, $cancelled);
        }

        $this->apply($orderId, $transaction);

        return ! Pembayaran::query()
            ->where('payment_gateway', 'paywuz')
            ->where('order_id', $orderId)
            ->where('status_validasi', 'disetujui')
            ->exists();
    }

    private function markCancelledWithoutRemoteTransaction(string $orderId): void
    {
        DB::transaction(function () use ($orderId): void {
            $payments = Pembayaran::query()
                ->where('payment_gateway', 'paywuz')
                ->where('order_id', $orderId)
                ->lockForUpdate()
                ->get();

            foreach ($payments as $payment) {
                if ($payment->status_validasi === 'disetujui') {
                    throw new RuntimeException('Pembayaran yang sudah lunas tidak dapat dibatalkan.');
                }

                $oldStatus = $payment->status_validasi;
                $payment->update([
                    'status_validasi' => 'ditolak',
                    'tanggal_validasi' => now(),
                    'gateway_status' => 'cancelled',
                    'gateway_error' => null,
                    'catatan' => trim(($payment->catatan ? $payment->catatan."\n" : '').'Transaksi lokal dibatalkan setelah Paywuz memastikan order tidak ditemukan.'),
                ]);

                if ($oldStatus !== 'ditolak') {
                    $this->audit($payment, $oldStatus, 'ditolak', $orderId);
                }
            }
        });
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
