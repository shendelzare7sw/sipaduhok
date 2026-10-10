<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\Pembayaran;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class MidtransPaymentStatusService
{
    /** Toleransi selisih jam sebelum token Snap tak terpakai dianggap kedaluwarsa. */
    private const EXPIRY_GRACE_MINUTES = 5;

    public function __construct(private readonly MidtransService $midtrans) {}

    /**
     * Sinkronkan status order dari Core API Midtrans.
     *
     * @param  bool  $throw  lempar error API (dipakai webhook agar Midtrans mengulang)
     */
    public function sync(string $orderId, bool $throw = false): bool
    {
        $payments = $this->orderQuery($orderId)->get();

        if ($payments->isEmpty()) {
            return false;
        }

        try {
            $transaction = $this->midtrans->findTransactionStatus(
                $orderId,
                (int) $payments->sum('jumlah_bayar'),
                $payments->first()->payment_environment,
            );
        } catch (Throwable $exception) {
            Log::warning('Sinkronisasi pembayaran digital gagal.', [
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);

            if ($throw) {
                throw $exception;
            }

            return false;
        }

        if ($transaction === null) {
            return $this->expireUnusedSnapOrder($orderId);
        }

        return $this->apply($orderId, $transaction);
    }

    /** @param array<string, mixed> $transaction */
    public function apply(string $orderId, array $transaction): bool
    {
        $gatewayStatus = strtolower((string) ($transaction['status'] ?? 'pending'));
        $newStatus = $this->midtrans->mapStatus($gatewayStatus, $transaction['fraudStatus'] ?? null);
        $baruDisetujui = collect();
        $duplicateGatewayOrders = collect();

        if (in_array($gatewayStatus, ['refund', 'partial_refund', 'chargeback', 'partial_chargeback'], true)) {
            // Pengembalian dana diproses manual oleh bendahara; status lunas
            // tidak diturunkan otomatis agar riwayat kas tetap konsisten.
            Log::critical('Midtrans melaporkan refund/chargeback untuk pembayaran sekolah.', [
                'order_id' => $orderId,
                'status' => $gatewayStatus,
            ]);
        }

        $changed = DB::transaction(function () use ($orderId, $transaction, $gatewayStatus, $newStatus, &$baruDisetujui, &$duplicateGatewayOrders): bool {
            $payments = $this->orderQuery($orderId)
                ->with('tagihan')
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
                    'gateway_total' => (int) ($transaction['totalPayment'] ?? $payments->sum('jumlah_bayar')),
                    'gateway_response' => $transaction['raw'] ?? $transaction,
                    'payment_expires_at' => filled($transaction['expiresAt'] ?? null)
                        ? CarbonImmutable::parse((string) $transaction['expiresAt'])
                        : $payment->payment_expires_at,
                ];

                if (filled($transaction['id'] ?? null)) {
                    $updates['transaction_id'] = (string) $transaction['id'];
                }

                if (filled($transaction['paymentMethod'] ?? null)) {
                    $updates['payment_type'] = (string) $transaction['paymentMethod'];
                }

                // Status pembayaran yang sudah disetujui tidak boleh diturunkan
                // oleh notifikasi terlambat/keliru. Selain itu, status Midtrans
                // adalah sumber kebenaran dan boleh memperbaiki status lokal.
                if ($oldStatus !== 'disetujui' || $newStatus === 'disetujui') {
                    $updates['gateway_status'] = $gatewayStatus;
                }

                if (filled($transaction['paymentUrl'] ?? null)) {
                    $updates['payment_url'] = (string) $transaction['paymentUrl'];
                    $updates['gateway_error'] = null;
                }

                if ($oldStatus !== $authoritativeStatus) {
                    $updates['status_validasi'] = $authoritativeStatus;
                    $updates['tanggal_validasi'] = $authoritativeStatus === 'pending' ? null : now();
                    if ($authoritativeStatus === 'disetujui') {
                        $updates['gateway_settled_at'] = now();
                    }
                }

                $payment->update($updates);

                if ($oldStatus !== $payment->status_validasi) {
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
                            ->where('payment_gateway', MidtransService::GATEWAY)
                            ->pluck('order_id')
                            ->filter()
                            ->all());

                        // Transaksi Midtrans tidak boleh ditolak secara lokal
                        // sebelum penutupan remote dikonfirmasi. Metode manual
                        // tidak memiliki transaksi remote sehingga aman ditutup.
                        Pembayaran::query()
                            ->whereIn('id', $duplicates
                                ->where('payment_gateway', '!=', MidtransService::GATEWAY)
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
                Log::critical('Transaksi Midtrans duplikat belum berhasil dibatalkan.', [
                    'paid_order_id' => $orderId,
                    'duplicate_order_id' => $duplicateOrderId,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $changed;
    }

    /**
     * Tutup order lama secara aman sebelum membuat kanal pengganti.
     *
     * @return bool true jika order lama aman diganti; false jika ternyata sudah dibayar
     */
    public function cancelIfPending(string $orderId): bool
    {
        $payments = $this->orderQuery($orderId)->get();

        if ($payments->isEmpty()) {
            throw new RuntimeException('Data pembayaran lama tidak ditemukan.');
        }

        $amount = (int) $payments->sum('jumlah_bayar');
        $environment = $payments->first()->payment_environment;
        $transaction = $this->midtrans->findTransactionStatus($orderId, $amount, $environment);

        if ($transaction === null) {
            // Belum ada transaksi Core API: wali belum memilih kanal di Snap
            // sehingga tidak ada kode bayar yang perlu ditutup di Midtrans.
            $this->markClosedWithoutRemoteTransaction($orderId, 'cancel', 'Transaksi lokal ditutup sebelum kanal dipilih di halaman pembayaran Midtrans.');

            return true;
        }

        $mappedStatus = $this->midtrans->mapStatus((string) $transaction['status'], $transaction['fraudStatus'] ?? null);
        if ($mappedStatus === 'disetujui') {
            $this->apply($orderId, $transaction);

            return false;
        }

        if ($mappedStatus === 'pending') {
            $closed = $this->midtrans->cancelTransaction($orderId, (string) $transaction['status'], $environment);

            // Respons expire/cancel minimal; pertahankan nominal dan kanal dari
            // hasil status yang sudah tervalidasi.
            $transaction = array_replace($transaction, $closed);
        }

        $this->apply($orderId, $transaction);

        return ! $this->orderQuery($orderId)
            ->where('status_validasi', 'disetujui')
            ->exists();
    }

    /**
     * Token Snap yang kedaluwarsa tanpa pernah dipakai tidak tercatat di Core
     * API. Tutup order lokalnya agar tagihan dapat dibayar ulang.
     */
    private function expireUnusedSnapOrder(string $orderId): bool
    {
        $payments = $this->orderQuery($orderId)->get();
        $expiresAt = $payments->pluck('payment_expires_at')->filter()->max();

        if (! $payments->contains('status_validasi', 'pending')
            || ! $expiresAt
            || $expiresAt->copy()->addMinutes(self::EXPIRY_GRACE_MINUTES)->isFuture()) {
            return false;
        }

        $this->markClosedWithoutRemoteTransaction($orderId, 'expire', 'Batas waktu pembayaran berakhir sebelum kanal dipilih.');

        return true;
    }

    private function markClosedWithoutRemoteTransaction(string $orderId, string $gatewayStatus, string $note): void
    {
        DB::transaction(function () use ($orderId, $gatewayStatus, $note): void {
            $payments = $this->orderQuery($orderId)
                ->lockForUpdate()
                ->get();

            foreach ($payments as $payment) {
                if ($payment->status_validasi === 'disetujui') {
                    throw new RuntimeException('Pembayaran yang sudah lunas tidak dapat dibatalkan.');
                }

                if ($payment->status_validasi !== 'pending') {
                    continue;
                }

                $payment->update([
                    'status_validasi' => 'ditolak',
                    'tanggal_validasi' => now(),
                    'gateway_status' => $gatewayStatus,
                    'gateway_error' => null,
                    'catatan' => trim(($payment->catatan ? $payment->catatan."\n" : '').$note),
                ]);

                $this->audit($payment, 'pending', 'ditolak', $orderId);
            }
        });
    }

    private function orderQuery(string $orderId)
    {
        return Pembayaran::query()
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $orderId);
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
            'user_agent' => 'Midtrans Notification/Status Sync',
        ]);
    }
}
