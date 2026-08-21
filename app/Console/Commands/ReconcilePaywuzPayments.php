<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use App\Services\PaywuzPaymentStatusService;
use Illuminate\Console\Command;
use Throwable;

class ReconcilePaywuzPayments extends Command
{
    protected $signature = 'paywuz:reconcile
        {--cancel-abandoned : Batalkan order Paywuz yang telanjur tidak aktif di database lokal}
        {--order= : Batasi rekonsiliasi ke satu order ID}
        {--limit=200 : Batas order per eksekusi}';

    protected $description = 'Rekonsiliasi status transaksi Paywuz dengan data pembayaran sekolah';

    public function handle(PaywuzPaymentStatusService $statusService): int
    {
        $limit = max(1, min((int) $this->option('limit'), 1000));
        $orders = Pembayaran::query()
            ->where('payment_gateway', 'paywuz')
            ->whereNotNull('order_id')
            ->when($this->option('order'), fn ($query, $orderId) => $query->where('order_id', $orderId))
            ->select('order_id')
            ->distinct()
            ->orderBy('order_id')
            ->limit($limit)
            ->pluck('order_id');

        $synced = 0;
        $cancelled = 0;
        $failed = 0;

        foreach ($orders as $orderId) {
            try {
                $payments = Pembayaran::query()
                    ->where('payment_gateway', 'paywuz')
                    ->where('order_id', $orderId)
                    ->get();

                $isAbandoned = $payments->isNotEmpty()
                    && $payments->every(fn (Pembayaran $payment): bool => $payment->status_validasi === 'ditolak')
                    && $payments->contains(fn (Pembayaran $payment): bool => in_array(
                        strtolower((string) $payment->gateway_status),
                        ['', 'pending', 'replaced'],
                        true,
                    ));

                if ($this->option('cancel-abandoned') && $isAbandoned) {
                    if ($statusService->cancelIfPending((string) $orderId)) {
                        $cancelled++;
                    } else {
                        $synced++;
                        $this->warn("{$orderId}: pembayaran ternyata sudah diterima dan dipulihkan sebagai lunas.");
                    }
                } else {
                    $statusService->sync((string) $orderId);
                    $synced++;
                }
            } catch (Throwable $exception) {
                $failed++;
                $this->error("{$orderId}: {$exception->getMessage()}");
            }
        }

        $this->info("Rekonsiliasi selesai: {$synced} disinkronkan, {$cancelled} dibatalkan aman, {$failed} gagal.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
