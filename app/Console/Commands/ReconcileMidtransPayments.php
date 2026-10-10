<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use App\Services\MidtransPaymentStatusService;
use App\Services\MidtransService;
use Illuminate\Console\Command;
use Throwable;

class ReconcileMidtransPayments extends Command
{
    protected $signature = 'midtrans:reconcile
        {--all : Sinkronkan juga order yang sudah final, bukan hanya yang masih pending}
        {--cancel-abandoned : Tutup order Midtrans yang telanjur tidak aktif di database lokal}
        {--order= : Batasi rekonsiliasi ke satu order ID}
        {--limit=200 : Batas order per eksekusi}';

    protected $description = 'Rekonsiliasi status transaksi Midtrans dengan data pembayaran sekolah';

    public function handle(MidtransPaymentStatusService $statusService): int
    {
        $limit = max(1, min((int) $this->option('limit'), 1000));
        $includeFinal = $this->option('all') || $this->option('cancel-abandoned') || filled($this->option('order'));
        $orders = Pembayaran::query()
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->whereNotNull('order_id')
            ->when(! $includeFinal, fn ($query) => $query->where('status_validasi', 'pending'))
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
                    ->where('payment_gateway', MidtransService::GATEWAY)
                    ->where('order_id', $orderId)
                    ->get();

                $isAbandoned = $payments->isNotEmpty()
                    && $payments->every(fn (Pembayaran $payment): bool => $payment->status_validasi === 'ditolak')
                    && $payments->contains(fn (Pembayaran $payment): bool => in_array(
                        strtolower((string) $payment->gateway_status),
                        ['', 'pending'],
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
                    $statusService->sync((string) $orderId, throw: true);
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
