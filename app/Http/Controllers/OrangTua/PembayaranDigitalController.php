<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Services\NotificationService;
use App\Services\PaywuzPaymentStatusService;
use App\Services\PaywuzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PembayaranDigitalController extends Controller
{
    public function prosesBayar(Request $request, int $siswaId, PaywuzService $paywuz): RedirectResponse
    {
        $user = Auth::user();
        $siswa = $user->children()->find($siswaId);

        if (! $siswa) {
            return redirect()->route('wali-siswa.dashboard')->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        if ($request->input('metode_pembayaran') === 'tunai') {
            return back()->with('error', 'Pembayaran tunai hanya dilayani di loket sekolah.')->withInput();
        }

        $validated = $request->validate([
            'tagihan_id' => ['required', 'integer', 'exists:tagihan,id'],
            'jumlah_bayar' => ['required', 'integer', 'min:1000'],
            'metode_pembayaran' => ['required', 'in:transfer,paywuz'],
            'payment_method' => ['required_if:metode_pembayaran,paywuz', 'nullable', 'string', 'max:50'],
            'bukti_bayar' => ['required_if:metode_pembayaran,transfer', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $tagihan = Tagihan::query()->findOrFail($validated['tagihan_id']);
        $this->guardTagihan($tagihan, $siswa->id);

        $amount = (int) $validated['jumlah_bayar'];
        $this->guardAmount($tagihan, $amount);

        if ($validated['metode_pembayaran'] === 'transfer') {
            $proofPath = $request->file('bukti_bayar')->store('pembayaran/bukti', 'public');
            $payment = DB::transaction(function () use ($tagihan, $siswa, $user, $amount, $proofPath, $validated): Pembayaran {
                $lockedTagihan = Tagihan::query()->lockForUpdate()->findOrFail($tagihan->id);
                $this->guardTagihan($lockedTagihan, $siswa->id);
                $this->guardAmount($lockedTagihan, $amount);

                return Pembayaran::create([
                    'tagihan_id' => $lockedTagihan->id,
                    'siswa_id' => $siswa->id,
                    'paid_by_parent_id' => $user->id,
                    'kode_pembayaran' => $this->generatePaymentCode(),
                    'jumlah_bayar' => $amount,
                    'tanggal_bayar' => now(),
                    'metode_pembayaran' => 'transfer',
                    'bukti_pembayaran' => $proofPath,
                    'status_validasi' => 'pending',
                    'catatan' => $validated['catatan'] ?? null,
                ]);
            });

            app(NotificationService::class)->notifyPembayaranBaru($payment);

            return redirect()->route('wali-siswa.tagihan.anak', $siswa->id)
                ->with('success', 'Pembayaran berhasil diajukan dan menunggu validasi bendahara.');
        }

        if (! $paywuz->isConfigured()) {
            return back()->with('error', 'Pembayaran digital belum dikonfigurasi. Silakan hubungi admin atau bendahara.')->withInput();
        }

        $pending = Pembayaran::query()
            ->where('tagihan_id', $tagihan->id)
            ->where('siswa_id', $siswa->id)
            ->where('payment_gateway', 'paywuz')
            ->where('status_validasi', 'pending')
            ->latest('id')
            ->first();

        if ($pending) {
            app(PaywuzPaymentStatusService::class)->sync((string) $pending->order_id);
            $pending->refresh();

            if ($pending->status_validasi === 'pending') {
                return redirect()->route('wali-siswa.pembayaran.digital', $pending)
                    ->with('info', 'Anda masih memiliki pembayaran yang menunggu penyelesaian.');
            }
        }

        try {
            $paywuz->assertPaymentMethodAvailable((string) $validated['payment_method'], $amount);
        } catch (Throwable $exception) {
            return back()->withErrors([
                'payment_method' => $exception->getMessage(),
            ])->withInput();
        }

        $payment = DB::transaction(function () use ($tagihan, $siswa, $user, $amount, $validated, $paywuz): Pembayaran {
            $lockedTagihan = Tagihan::query()->lockForUpdate()->findOrFail($tagihan->id);
            $this->guardTagihan($lockedTagihan, $siswa->id);
            $this->guardAmount($lockedTagihan, $amount);

            $existing = Pembayaran::query()
                ->where('tagihan_id', $lockedTagihan->id)
                ->where('siswa_id', $siswa->id)
                ->where('payment_gateway', 'paywuz')
                ->where('status_validasi', 'pending')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            return $existing ?: Pembayaran::create([
                'tagihan_id' => $lockedTagihan->id,
                'siswa_id' => $siswa->id,
                'paid_by_parent_id' => $user->id,
                'kode_pembayaran' => $this->generatePaymentCode(),
                'jumlah_bayar' => $amount,
                'tanggal_bayar' => now(),
                'metode_pembayaran' => 'paywuz',
                'payment_gateway' => 'paywuz',
                'payment_type' => $validated['payment_method'],
                'payment_environment' => $paywuz->environment(),
                'order_id' => $this->generateGatewayOrderId(),
                'status_validasi' => 'pending',
                'catatan' => $validated['catatan'] ?? null,
            ]);
        });

        if (! $payment->wasRecentlyCreated) {
            return redirect()->route('wali-siswa.pembayaran.digital', $payment)
                ->with('info', 'Pembayaran yang sama sedang menunggu penyelesaian.');
        }

        try {
            $this->openGatewayTransaction($payment, $paywuz);
        } catch (Throwable $exception) {
            Log::warning('Pembuatan kanal pembayaran digital tertunda.', [
                'pembayaran_id' => $payment->id,
                'order_id' => $payment->order_id,
                'message' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('wali-siswa.pembayaran.digital', $payment);
    }

    public function processBulkPay(Request $request, int $siswaId, PaywuzService $paywuz): RedirectResponse
    {
        $user = Auth::user();
        $siswa = $user->children()->find($siswaId);

        if (! $siswa) {
            return redirect()->route('wali-siswa.dashboard')->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.tagihan_id' => ['required', 'integer', 'distinct', 'exists:tagihan,id'],
            'items.*.jumlah_bayar' => ['required', 'integer', 'min:1'],
            'total_bayar' => ['required', 'integer', 'min:1'],
            'metode_pembayaran' => ['required', 'in:transfer,paywuz'],
            'payment_method' => ['required_if:metode_pembayaran,paywuz', 'nullable', 'string', 'max:50'],
            'bukti_bayar' => ['required_if:metode_pembayaran,transfer', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ]);

        $tagihan = Tagihan::query()
            ->whereIn('id', collect($validated['items'])->pluck('tagihan_id'))
            ->get()
            ->keyBy('id');

        $items = collect($validated['items'])->map(function (array $item) use ($tagihan, $siswa): array {
            $bill = $tagihan->get((int) $item['tagihan_id']);
            if (! $bill) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu tagihan tidak valid untuk siswa ini.',
                ]);
            }
            $this->guardTagihan($bill, $siswa->id);
            $amount = (int) $item['jumlah_bayar'];
            $this->guardAmount($bill, $amount);

            return ['tagihan' => $bill, 'amount' => $amount];
        });

        $total = (int) $items->sum('amount');
        if ($total !== (int) $validated['total_bayar']) {
            return back()->with('error', 'Total pembayaran berubah. Silakan pilih ulang tagihan.')->withInput();
        }

        if ($validated['metode_pembayaran'] === 'paywuz') {
            if (! $paywuz->isConfigured()) {
                return back()->with('error', 'Pembayaran digital belum dikonfigurasi.')->withInput();
            }

            $pending = Pembayaran::query()
                ->whereIn('tagihan_id', $items->map(fn (array $item) => $item['tagihan']->id))
                ->where('siswa_id', $siswa->id)
                ->where('payment_gateway', 'paywuz')
                ->where('status_validasi', 'pending')
                ->first();
            if ($pending) {
                return redirect()->route('wali-siswa.pembayaran.digital', $pending)
                    ->with('info', 'Selesaikan pembayaran yang masih menunggu sebelum membuat transaksi baru.');
            }

            try {
                $paywuz->assertPaymentMethodAvailable((string) $validated['payment_method'], $total);
            } catch (Throwable $exception) {
                return back()->withErrors([
                    'payment_method' => $exception->getMessage(),
                ])->withInput();
            }
        }

        $buktiPath = $request->hasFile('bukti_bayar')
            ? $request->file('bukti_bayar')->store('pembayaran/bukti', 'public')
            : null;
        $orderId = $validated['metode_pembayaran'] === 'paywuz' ? $this->generateGatewayOrderId() : null;
        $baseCode = $this->generatePaymentCode();

        $creation = DB::transaction(function () use ($items, $validated, $siswa, $user, $buktiPath, $orderId, $baseCode, $paywuz): array {
            $billIds = $items->map(fn (array $item) => $item['tagihan']->id)->sort()->values();
            $lockedBills = Tagihan::query()
                ->whereIn('id', $billIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $lockedBill = $lockedBills->get($item['tagihan']->id);
                if (! $lockedBill) {
                    throw ValidationException::withMessages(['items' => 'Salah satu tagihan tidak lagi tersedia.']);
                }
                $this->guardTagihan($lockedBill, $siswa->id);
                $this->guardAmount($lockedBill, $item['amount']);
            }

            if ($validated['metode_pembayaran'] === 'paywuz') {
                $pending = Pembayaran::query()
                    ->whereIn('tagihan_id', $billIds)
                    ->where('siswa_id', $siswa->id)
                    ->where('payment_gateway', 'paywuz')
                    ->where('status_validasi', 'pending')
                    ->lockForUpdate()
                    ->first();

                if ($pending) {
                    return ['payment_ids' => [], 'pending_id' => $pending->id];
                }
            }

            $ids = [];
            foreach ($items->values() as $index => $item) {
                $payment = Pembayaran::create([
                    'tagihan_id' => $item['tagihan']->id,
                    'siswa_id' => $siswa->id,
                    'paid_by_parent_id' => $user->id,
                    'kode_pembayaran' => $baseCode.'-'.($index + 1),
                    'jumlah_bayar' => $item['amount'],
                    'tanggal_bayar' => now(),
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'bukti_pembayaran' => $buktiPath,
                    'status_validasi' => 'pending',
                    'payment_gateway' => $validated['metode_pembayaran'] === 'paywuz' ? 'paywuz' : null,
                    'payment_type' => $validated['metode_pembayaran'] === 'paywuz' ? $validated['payment_method'] : null,
                    'payment_environment' => $validated['metode_pembayaran'] === 'paywuz' ? $paywuz->environment() : null,
                    'order_id' => $orderId,
                ]);
                $ids[] = $payment->id;
            }

            return ['payment_ids' => $ids, 'pending_id' => null];
        });

        if ($creation['pending_id']) {
            return redirect()->route('wali-siswa.pembayaran.digital', $creation['pending_id'])
                ->with('info', 'Selesaikan pembayaran yang masih menunggu sebelum membuat transaksi baru.');
        }

        $paymentIds = $creation['payment_ids'];

        $firstPayment = Pembayaran::query()->findOrFail($paymentIds[0]);

        if ($validated['metode_pembayaran'] === 'paywuz') {
            try {
                $this->openGatewayTransaction($firstPayment, $paywuz);
            } catch (Throwable $exception) {
                Log::warning('Pembuatan kanal pembayaran digital gabungan tertunda.', [
                    'pembayaran_id' => $firstPayment->id,
                    'order_id' => $firstPayment->order_id,
                    'message' => $exception->getMessage(),
                ]);
            }

            return redirect()->route('wali-siswa.pembayaran.digital', $firstPayment);
        }

        try {
            app(NotificationService::class)->notifyNewPayment($paymentIds, $user);
        } catch (Throwable $exception) {
            Log::error('Gagal mengirim notifikasi pembayaran.', ['message' => $exception->getMessage()]);
        }

        return redirect()->route('wali-siswa.tagihan.anak', $siswa->id)
            ->with('success', 'Pembayaran berhasil diajukan dan menunggu validasi bendahara.');
    }

    public function digitalPayment(int $pembayaranId, PaywuzPaymentStatusService $statusService, PaywuzService $paywuz): View|RedirectResponse
    {
        $payment = Pembayaran::with(['siswa', 'tagihan'])->findOrFail($pembayaranId);
        $this->guardParentAccess($payment);

        if ($payment->payment_gateway !== 'paywuz') {
            return redirect()->route('wali-siswa.tagihan.anak', $payment->siswa_id)
                ->with('error', 'Pembayaran ini bukan pembayaran digital.');
        }

        $statusService->sync((string) $payment->order_id);
        $payment->refresh();

        if ($payment->status_validasi === 'pending' && blank($payment->payment_url)) {
            try {
                $this->openGatewayTransaction($payment, $paywuz);
                $statusService->sync((string) $payment->order_id);
                $payment->refresh();
            } catch (Throwable $exception) {
                Log::warning('Kanal pembayaran belum dapat dibuka.', [
                    'pembayaran_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $allPayments = Pembayaran::with('tagihan')
            ->where('payment_gateway', 'paywuz')
            ->where('order_id', $payment->order_id)
            ->get();

        return view('wali-siswa.pembayaran.digital', [
            'pembayaran' => $payment,
            'allPayments' => $allPayments,
            'totalBayar' => (int) $allPayments->sum('jumlah_bayar'),
        ]);
    }

    public function syncDigitalPayment(int $pembayaranId, PaywuzPaymentStatusService $statusService): RedirectResponse
    {
        $payment = Pembayaran::findOrFail($pembayaranId);
        $this->guardParentAccess($payment);
        $statusService->sync((string) $payment->order_id);

        return back()->with('success', 'Status pembayaran telah diperbarui.');
    }

    public function continuePayment(int $pembayaranId): RedirectResponse
    {
        $payment = Pembayaran::findOrFail($pembayaranId);
        $this->guardParentAccess($payment);

        if ($payment->payment_gateway !== 'paywuz' || $payment->status_validasi !== 'pending') {
            return redirect()->route('wali-siswa.tagihan.anak', $payment->siswa_id)
                ->with('error', 'Pembayaran ini sudah tidak dapat dilanjutkan.');
        }

        return redirect()->route('wali-siswa.pembayaran.digital', $payment);
    }

    private function openGatewayTransaction(Pembayaran $payment, PaywuzService $paywuz): void
    {
        $group = Pembayaran::query()
            ->where('payment_gateway', 'paywuz')
            ->where('order_id', $payment->order_id)
            ->get();
        $amount = (int) $group->sum('jumlah_bayar');
        $transaction = $paywuz->createTransaction(
            (string) $payment->order_id,
            $amount,
            (string) $payment->payment_type,
            $payment->id,
            $payment->siswa_id,
            route('wali-siswa.pembayaran.digital', $payment),
            $payment->payment_environment,
        );

        Pembayaran::query()
            ->where('payment_gateway', 'paywuz')
            ->where('order_id', $payment->order_id)
            ->update([
                'transaction_id' => (string) $transaction['id'],
                'payment_type' => (string) ($transaction['paymentMethod'] ?? $payment->payment_type),
                'payment_url' => (string) $transaction['paymentUrl'],
                'gateway_total' => (int) ($transaction['totalPayment'] ?? $amount),
                'gateway_status' => (string) $transaction['status'],
                'payment_expires_at' => $transaction['expiresAt'] ?? null,
                'gateway_response' => json_encode($transaction),
            ]);
    }

    private function guardParentAccess(Pembayaran $payment): void
    {
        abort_unless(
            Auth::user()?->children()->where('siswa.id', $payment->siswa_id)->exists(),
            403,
            'Anda tidak memiliki akses ke transaksi ini.',
        );
    }

    private function guardTagihan(Tagihan $tagihan, int $siswaId): void
    {
        if ((int) $tagihan->siswa_id !== $siswaId) {
            throw ValidationException::withMessages([
                'tagihan_id' => 'Tagihan tidak valid untuk siswa ini.',
            ]);
        }

        $activeYearId = TahunAjaran::query()->where('is_active', true)->value('id');
        if ($activeYearId
            && (int) $tagihan->tahun_ajaran_id !== (int) $activeYearId
            && blank($tagihan->dialihkan_ke_id)
            && blank($tagihan->tagihan_asal_id)) {
            throw ValidationException::withMessages([
                'tagihan_id' => 'Tagihan tahun ajaran lama harus dialihkan oleh bendahara sebelum dibayar.',
            ]);
        }
    }

    private function guardAmount(Tagihan $tagihan, int $amount): void
    {
        $approved = (int) Pembayaran::query()
            ->where('tagihan_id', $tagihan->id)
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');
        $remaining = max(0, (int) $tagihan->jumlah - $approved);

        if ($remaining < 1 || $amount > $remaining) {
            throw ValidationException::withMessages([
                'jumlah_bayar' => 'Nominal pembayaran melebihi sisa tagihan.',
            ]);
        }
    }

    private function generatePaymentCode(): string
    {
        do {
            $code = 'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (Pembayaran::query()->where('kode_pembayaran', $code)->exists());

        return $code;
    }

    private function generateGatewayOrderId(): string
    {
        do {
            $orderId = 'SPH-'.now()->format('Ymd').'-'.Str::upper(Str::random(12));
        } while (Pembayaran::query()->where('order_id', $orderId)->exists());

        return $orderId;
    }
}
