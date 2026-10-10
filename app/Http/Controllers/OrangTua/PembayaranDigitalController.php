<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\InfoPembayaran;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Services\MidtransPaymentStatusService;
use App\Services\MidtransService;
use App\Services\NotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PembayaranDigitalController extends Controller
{
    public function prosesBayar(Request $request, int $siswaId, MidtransService $midtrans): RedirectResponse
    {
        $user = Auth::user();
        $siswa = $user->financialChildren()->find($siswaId);

        if (! $siswa) {
            return redirect()->route('wali-siswa.dashboard')->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        if ($request->input('metode_pembayaran') === 'tunai') {
            return back()->with('error', 'Pembayaran tunai hanya dilayani di loket sekolah.')->withInput();
        }

        $validated = $request->validate([
            'tagihan_id' => ['required', 'integer', 'exists:tagihan,id'],
            'jumlah_bayar' => ['required', 'integer', 'min:1000'],
            'metode_pembayaran' => ['required', 'in:transfer,midtrans'],
            'payment_method' => ['exclude_unless:metode_pembayaran,midtrans', 'required', 'string', 'max:50'],
            'bukti_bayar' => ['required_if:metode_pembayaran,transfer', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'payment_method.required' => 'Silakan pilih kanal pembayaran.',
        ]);

        $tagihan = Tagihan::query()->findOrFail($validated['tagihan_id']);
        $this->guardTagihan($tagihan, $siswa->id);

        if ($validated['metode_pembayaran'] === 'transfer' && ! InfoPembayaran::getInstance()->isDirectTransferEnabled()) {
            return back()->with('error', 'Direct Transfer sedang dinonaktifkan. Silakan pilih pembayaran digital.')->withInput();
        }

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

        if (! $midtrans->isConfigured()) {
            return back()->with('error', 'Pembayaran digital belum dikonfigurasi. Silakan hubungi admin atau bendahara.')->withInput();
        }

        $paymentMethod = (string) $validated['payment_method'];

        try {
            $midtrans->assertPaymentMethodAvailable($paymentMethod, $amount);
        } catch (Throwable $exception) {
            return back()->withErrors([
                'payment_method' => $exception->getMessage(),
            ])->withInput();
        }

        $pending = Pembayaran::query()
            ->where('tagihan_id', $tagihan->id)
            ->where('siswa_id', $siswa->id)
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('status_validasi', 'pending')
            ->latest('id')
            ->first();

        if ($pending) {
            $statusService = app(MidtransPaymentStatusService::class);
            $statusService->sync((string) $pending->order_id);
            $pending->refresh();

            if ($pending->status_validasi === 'pending') {
                if (! $this->matchesRequestedPaymentMethod($pending, $paymentMethod)) {
                    return $this->replacePendingPaymentMethod($pending, $paymentMethod, $statusService, $midtrans);
                }

                return redirect()->route('wali-siswa.pembayaran.digital', $pending)
                    ->with('payment_notice', 'Anda masih memiliki pembayaran yang menunggu penyelesaian.');
            }
        }

        $payment = DB::transaction(function () use ($tagihan, $siswa, $user, $amount, $validated, $paymentMethod, $midtrans): Pembayaran {
            $lockedTagihan = Tagihan::query()->lockForUpdate()->findOrFail($tagihan->id);
            $this->guardTagihan($lockedTagihan, $siswa->id);
            $this->guardAmount($lockedTagihan, $amount);

            $existing = Pembayaran::query()
                ->where('tagihan_id', $lockedTagihan->id)
                ->where('siswa_id', $siswa->id)
                ->where('payment_gateway', MidtransService::GATEWAY)
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
                'metode_pembayaran' => MidtransService::GATEWAY,
                'payment_gateway' => MidtransService::GATEWAY,
                'payment_type' => $paymentMethod,
                'payment_environment' => $midtrans->environment(),
                'order_id' => $this->generateGatewayOrderId(),
                'status_validasi' => 'pending',
                'catatan' => $validated['catatan'] ?? null,
            ]);
        });

        if (! $payment->wasRecentlyCreated) {
            return redirect()->route('wali-siswa.pembayaran.digital', $payment)
                ->with('payment_notice', 'Pembayaran yang sama sedang menunggu penyelesaian.');
        }

        try {
            $this->openGatewayTransaction($payment, $midtrans);
        } catch (Throwable $exception) {
            Log::warning('Pembuatan kanal pembayaran digital tertunda.', [
                'pembayaran_id' => $payment->id,
                'order_id' => $payment->order_id,
                'message' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('wali-siswa.pembayaran.digital', $payment);
    }

    public function processBulkPay(Request $request, int $siswaId, MidtransService $midtrans): RedirectResponse
    {
        $user = Auth::user();
        $siswa = $user->financialChildren()->find($siswaId);

        if (! $siswa) {
            return redirect()->route('wali-siswa.dashboard')->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.tagihan_id' => ['required', 'integer', 'distinct', 'exists:tagihan,id'],
            'items.*.jumlah_bayar' => ['required', 'integer', 'min:1'],
            'total_bayar' => ['required', 'integer', 'min:1'],
            'metode_pembayaran' => ['required', 'in:transfer,midtrans'],
            'payment_method' => ['exclude_unless:metode_pembayaran,midtrans', 'required', 'string', 'max:50'],
            'bukti_bayar' => ['required_if:metode_pembayaran,transfer', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ], [
            'payment_method.required' => 'Silakan pilih kanal pembayaran.',
        ]);

        if ($validated['metode_pembayaran'] === 'transfer' && ! InfoPembayaran::getInstance()->isDirectTransferEnabled()) {
            return back()->with('error', 'Direct Transfer sedang dinonaktifkan. Silakan pilih pembayaran digital.')->withInput();
        }

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

        if ($validated['metode_pembayaran'] === MidtransService::GATEWAY) {
            if (! $midtrans->isConfigured()) {
                return back()->with('error', 'Pembayaran digital belum dikonfigurasi.')->withInput();
            }

            try {
                $midtrans->assertPaymentMethodAvailable((string) $validated['payment_method'], $total);
            } catch (Throwable $exception) {
                return back()->withErrors([
                    'payment_method' => $exception->getMessage(),
                ])->withInput();
            }

            $pending = Pembayaran::query()
                ->whereIn('tagihan_id', $items->map(fn (array $item) => $item['tagihan']->id))
                ->where('siswa_id', $siswa->id)
                ->where('payment_gateway', MidtransService::GATEWAY)
                ->where('status_validasi', 'pending')
                ->first();
            if ($pending) {
                $statusService = app(MidtransPaymentStatusService::class);
                $statusService->sync((string) $pending->order_id);
                $pending->refresh();

                if ($pending->status_validasi !== 'pending') {
                    $pending = null;
                }
            }

            if ($pending) {
                $requestedTagihanIds = $items->map(fn (array $item) => (int) $item['tagihan']->id)->all();

                if ($this->pendingMatchesTagihanIds($pending, $requestedTagihanIds)
                    && ! $this->matchesRequestedPaymentMethod($pending, (string) $validated['payment_method'])) {
                    return $this->replacePendingPaymentMethod(
                        $pending,
                        (string) $validated['payment_method'],
                        $statusService,
                        $midtrans,
                    );
                }

                return redirect()->route('wali-siswa.pembayaran.digital', $pending)
                    ->with('payment_notice', $this->matchesRequestedPaymentMethod($pending, (string) $validated['payment_method'])
                        ? 'Selesaikan pembayaran yang masih menunggu sebelum membuat transaksi baru.'
                        : 'Transaksi pending memuat pilihan tagihan yang berbeda. Selesaikan atau batalkan transaksi tersebut terlebih dahulu.');
            }
        }

        $buktiPath = $request->hasFile('bukti_bayar')
            ? $request->file('bukti_bayar')->store('pembayaran/bukti', 'public')
            : null;
        $orderId = $validated['metode_pembayaran'] === MidtransService::GATEWAY ? $this->generateGatewayOrderId() : null;
        $baseCode = $this->generatePaymentCode();

        $creation = DB::transaction(function () use ($items, $validated, $siswa, $user, $buktiPath, $orderId, $baseCode, $midtrans): array {
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

            if ($validated['metode_pembayaran'] === MidtransService::GATEWAY) {
                $pending = Pembayaran::query()
                    ->whereIn('tagihan_id', $billIds)
                    ->where('siswa_id', $siswa->id)
                    ->where('payment_gateway', MidtransService::GATEWAY)
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
                    'payment_gateway' => $validated['metode_pembayaran'] === MidtransService::GATEWAY ? MidtransService::GATEWAY : null,
                    'payment_type' => $validated['metode_pembayaran'] === MidtransService::GATEWAY ? $validated['payment_method'] : null,
                    'payment_environment' => $validated['metode_pembayaran'] === MidtransService::GATEWAY ? $midtrans->environment() : null,
                    'order_id' => $orderId,
                ]);
                $ids[] = $payment->id;
            }

            return ['payment_ids' => $ids, 'pending_id' => null];
        });

        if ($creation['pending_id']) {
            $pending = Pembayaran::query()->findOrFail($creation['pending_id']);
            $requestedTagihanIds = $items->map(fn (array $item) => (int) $item['tagihan']->id)->all();

            if ($validated['metode_pembayaran'] === MidtransService::GATEWAY
                && $this->pendingMatchesTagihanIds($pending, $requestedTagihanIds)
                && ! $this->matchesRequestedPaymentMethod($pending, (string) $validated['payment_method'])) {
                return $this->replacePendingPaymentMethod(
                    $pending,
                    (string) $validated['payment_method'],
                    app(MidtransPaymentStatusService::class),
                    $midtrans,
                );
            }

            return redirect()->route('wali-siswa.pembayaran.digital', $pending)
                ->with('payment_notice', 'Selesaikan pembayaran yang masih menunggu sebelum membuat transaksi baru.');
        }

        $paymentIds = $creation['payment_ids'];

        $firstPayment = Pembayaran::query()->findOrFail($paymentIds[0]);

        if ($validated['metode_pembayaran'] === MidtransService::GATEWAY) {
            try {
                $this->openGatewayTransaction($firstPayment, $midtrans);
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

    public function digitalPayment(int $pembayaranId, MidtransPaymentStatusService $statusService, MidtransService $midtrans): View|RedirectResponse
    {
        $payment = Pembayaran::with(['siswa', 'tagihan'])->findOrFail($pembayaranId);
        $this->guardParentAccess($payment);

        if ($payment->payment_gateway !== MidtransService::GATEWAY) {
            return redirect()->route('wali-siswa.tagihan.anak', $payment->siswa_id)
                ->with('error', 'Pembayaran ini bukan pembayaran digital.');
        }

        if (filled($payment->transaction_id) || filled($payment->payment_url)) {
            $statusService->sync((string) $payment->order_id);
            $payment->refresh();
        }

        if ($payment->status_validasi === 'pending' && blank($payment->payment_url)) {
            try {
                // Order dapat berganti ID jika percobaan sebelumnya gagal,
                // sehingga data selalu dimuat ulang setelah kanal dibuka.
                $this->openGatewayTransaction($payment, $midtrans);
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
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $payment->order_id)
            ->where('siswa_id', $payment->siswa_id)
            ->get();
        $totalBayar = (int) $allPayments->sum('jumlah_bayar');
        $paymentMethods = [];

        if ($payment->status_validasi === 'pending') {
            try {
                $paymentMethods = $midtrans->availablePaymentMethods($totalBayar);
            } catch (Throwable $exception) {
                Log::warning('Pilihan kanal pembayaran tidak dapat dimuat.', [
                    'pembayaran_id' => $payment->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return view('wali-siswa.pembayaran.digital', [
            'pembayaran' => $payment,
            'allPayments' => $allPayments,
            'totalBayar' => $totalBayar,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function syncDigitalPayment(
        int $pembayaranId,
        MidtransPaymentStatusService $statusService,
        MidtransService $midtrans,
    ): RedirectResponse {
        $payment = Pembayaran::findOrFail($pembayaranId);
        $this->guardParentAccess($payment);

        if ($payment->status_validasi === 'pending' && blank($payment->payment_url)) {
            try {
                $this->openGatewayTransaction($payment, $midtrans);
            } catch (Throwable $exception) {
                return back()
                    ->with('payment_notice', $this->gatewayUserMessage($exception))
                    ->with('payment_notice_type', 'danger');
            }
        }

        if (filled($payment->fresh()->order_id)) {
            $statusService->sync((string) $payment->fresh()->order_id);
        }

        return back();
    }

    public function continuePayment(int $pembayaranId, MidtransService $midtrans): RedirectResponse
    {
        $payment = Pembayaran::findOrFail($pembayaranId);
        $this->guardParentAccess($payment);

        if ($payment->payment_gateway !== MidtransService::GATEWAY || $payment->status_validasi !== 'pending') {
            return redirect()->route('wali-siswa.tagihan.anak', $payment->siswa_id)
                ->with('error', 'Pembayaran ini sudah tidak dapat dilanjutkan.');
        }

        if (blank($payment->payment_url)) {
            try {
                $this->openGatewayTransaction($payment, $midtrans);
            } catch (Throwable $exception) {
                return redirect()->route('wali-siswa.pembayaran.digital', $payment)
                    ->with('payment_notice', $this->gatewayUserMessage($exception))
                    ->with('payment_notice_type', 'danger');
            }
        }

        return redirect()->route('wali-siswa.pembayaran.digital', $payment);
    }

    public function changePaymentMethod(
        Request $request,
        int $pembayaranId,
        MidtransPaymentStatusService $statusService,
        MidtransService $midtrans,
    ): RedirectResponse {
        $payment = Pembayaran::findOrFail($pembayaranId);
        $this->guardParentAccess($payment);

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
        ], [
            'payment_method.required' => 'Silakan pilih kanal pembayaran.',
        ]);

        return $this->replacePendingPaymentMethod(
            $payment,
            (string) $validated['payment_method'],
            $statusService,
            $midtrans,
        );
    }

    private function replacePendingPaymentMethod(
        Pembayaran $payment,
        string $newMethod,
        MidtransPaymentStatusService $statusService,
        MidtransService $midtrans,
    ): RedirectResponse {
        try {
            return Cache::lock('midtrans:replace:'.sha1((string) ($payment->order_id ?: $payment->id)), 60)
                ->block(5, fn (): RedirectResponse => $this->replacePendingPaymentMethodLocked(
                    $payment->fresh(),
                    $newMethod,
                    $statusService,
                    $midtrans,
                ));
        } catch (LockTimeoutException) {
            return back()->with('error', 'Perubahan kanal yang sama sedang diproses. Silakan tunggu beberapa saat lalu muat ulang halaman.');
        }
    }

    private function replacePendingPaymentMethodLocked(
        Pembayaran $payment,
        string $newMethod,
        MidtransPaymentStatusService $statusService,
        MidtransService $midtrans,
    ): RedirectResponse {

        if ($payment->payment_gateway !== MidtransService::GATEWAY || $payment->status_validasi !== 'pending') {
            return back()->with('error', 'Metode pembayaran ini sudah tidak dapat diubah.');
        }

        $oldOrderId = (string) $payment->order_id;
        if (blank($oldOrderId)) {
            return back()->with('error', 'Transaksi digital belum memiliki nomor pesanan yang valid.');
        }

        $amount = (int) Pembayaran::query()
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $oldOrderId)
            ->sum('jumlah_bayar');

        try {
            $midtrans->assertPaymentMethodAvailable($newMethod, $amount);
        } catch (Throwable $exception) {
            return back()->withErrors(['payment_method' => $exception->getMessage()]);
        }

        if ($this->matchesRequestedPaymentMethod($payment, $newMethod)) {
            return $this->continuePayment($payment->id, $midtrans);
        }

        try {
            // order_id tetap wajib diperiksa meskipun transaction_id/payment_url
            // lokal kosong. API bisa sudah membuat transaksi sebelum proses lokal
            // gagal menyimpan responsnya.
            if (! $statusService->cancelIfPending($oldOrderId)) {
                return back()->with('info', 'Pembayaran telah diterima. Kanal tidak dapat diganti dan status tagihan sudah diperbarui.');
            }
        } catch (Throwable $exception) {
            Log::warning('Penggantian kanal ditunda karena transaksi lama tidak dapat dibatalkan dengan aman.', [
                'pembayaran_id' => $payment->id,
                'order_id' => $oldOrderId,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Transaksi lama belum dapat dibatalkan dan tetap berstatus menunggu. Coba lagi beberapa saat agar tidak terjadi pembayaran ganda.');
        }

        $newPayment = DB::transaction(function () use ($oldOrderId, $newMethod, $midtrans): Pembayaran {
            $oldPayments = Pembayaran::query()
                ->where('payment_gateway', MidtransService::GATEWAY)
                ->where('order_id', $oldOrderId)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($oldPayments->isEmpty() || $oldPayments->contains(fn (Pembayaran $item) => $item->status_validasi === 'disetujui')) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Status transaksi telah berubah. Muat ulang halaman sebelum melanjutkan.',
                ]);
            }

            $oldPayments->each(function (Pembayaran $item): void {
                if ($item->status_validasi === 'pending') {
                    $item->update([
                        'status_validasi' => 'ditolak',
                        'tanggal_validasi' => now(),
                        'gateway_status' => 'cancelled',
                        'catatan' => trim(($item->catatan ? $item->catatan."\n" : '').'Kanal pembayaran diganti oleh wali siswa.'),
                    ]);
                }
            });

            $newOrderId = $this->generateGatewayOrderId();
            $baseCode = $this->generatePaymentCode();
            $newPayments = collect();

            foreach ($oldPayments->values() as $index => $oldPayment) {
                $newPayments->push(Pembayaran::create([
                    'tagihan_id' => $oldPayment->tagihan_id,
                    'siswa_id' => $oldPayment->siswa_id,
                    'paid_by_parent_id' => Auth::id(),
                    'kode_pembayaran' => $baseCode.'-'.($index + 1),
                    'jumlah_bayar' => $oldPayment->jumlah_bayar,
                    'tanggal_bayar' => now(),
                    'metode_pembayaran' => MidtransService::GATEWAY,
                    'payment_gateway' => MidtransService::GATEWAY,
                    'payment_type' => $newMethod,
                    'payment_environment' => $midtrans->environment(),
                    'order_id' => $newOrderId,
                    'status_validasi' => 'pending',
                ]));
            }

            return $newPayments->first();
        });

        try {
            $this->openGatewayTransaction($newPayment, $midtrans);
        } catch (Throwable $exception) {
            Log::warning('Kanal pengganti belum dapat dibuat.', [
                'pembayaran_id' => $newPayment->id,
                'order_id' => $newPayment->order_id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('wali-siswa.pembayaran.digital', $newPayment)
                ->with('payment_notice', $this->gatewayUserMessage($exception))
                ->with('payment_notice_type', 'danger');
        }

        return redirect()->route('wali-siswa.pembayaran.digital', $newPayment)
            ->with('payment_notice', 'Kanal pembayaran berhasil diganti.');
    }

    private function matchesRequestedPaymentMethod(Pembayaran $payment, string $requestedMethod): bool
    {
        $requestedMethod = strtoupper(trim($requestedMethod));

        if ($requestedMethod === 'VA') {
            return $payment->payment_channel_group === 'va';
        }

        return hash_equals(strtoupper(trim((string) $payment->payment_type)), $requestedMethod);
    }

    /** @param list<int> $requestedTagihanIds */
    private function pendingMatchesTagihanIds(Pembayaran $payment, array $requestedTagihanIds): bool
    {
        $pendingTagihanIds = Pembayaran::query()
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $payment->order_id)
            ->pluck('tagihan_id')
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values()
            ->all();

        sort($requestedTagihanIds);

        return $pendingTagihanIds === array_values($requestedTagihanIds);
    }

    private function openGatewayTransaction(Pembayaran $payment, MidtransService $midtrans): void
    {
        try {
            Cache::lock('midtrans:open:'.sha1((string) ($payment->order_id ?: $payment->id)), 30)
                ->block(10, fn () => $this->openGatewayTransactionLocked($payment, $midtrans));
        } catch (LockTimeoutException) {
            throw new \RuntimeException('Kanal pembayaran sedang dibuat. Silakan muat ulang halaman beberapa saat lagi.');
        }
    }

    private function openGatewayTransactionLocked(Pembayaran $payment, MidtransService $midtrans): void
    {
        $payment->refresh();

        if ($payment->payment_gateway !== MidtransService::GATEWAY
            || $payment->status_validasi !== 'pending'
            || filled($payment->payment_url)) {
            // Token Snap yang sudah terbit tetap dipakai sampai kedaluwarsa;
            // order Midtrans tidak dapat diterbitkan ulang dengan ID yang sama.
            return;
        }

        $group = Pembayaran::query()
            ->with('tagihan')
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $payment->order_id)
            ->orderBy('id')
            ->get();
        $amount = (int) $group->sum('jumlah_bayar');
        $paymentMethod = filled($payment->payment_type)
            ? strtoupper((string) $payment->payment_type)
            : $midtrans->defaultPaymentMethod($amount);

        if ((int) $payment->gateway_attempts > 0) {
            // Percobaan sebelumnya mungkin sudah tercatat di Midtrans meskipun
            // responsnya tidak diterima. Pastikan tidak ada transaksi aktif,
            // lalu gunakan order ID baru agar Snap tidak menolak duplikasi.
            $existing = $midtrans->findTransactionStatus((string) $payment->order_id, $amount, $payment->payment_environment);
            if ($existing !== null) {
                app(MidtransPaymentStatusService::class)->apply((string) $payment->order_id, $existing);

                throw new \RuntimeException('Transaksi sebelumnya sudah tercatat di penyedia pembayaran. Silakan cek status pembayaran.');
            }

            $newOrderId = $this->generateGatewayOrderId();
            Pembayaran::query()
                ->where('payment_gateway', MidtransService::GATEWAY)
                ->where('order_id', $payment->order_id)
                ->where('status_validasi', 'pending')
                ->update(['order_id' => $newOrderId, 'gateway_error' => null]);
            $payment->order_id = $newOrderId;
        }

        Pembayaran::query()
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $payment->order_id)
            ->update([
                'payment_type' => $paymentMethod,
                'gateway_attempts' => DB::raw('gateway_attempts + 1'),
            ]);

        try {
            $transaction = $midtrans->createTransaction(
                (string) $payment->order_id,
                $amount,
                $paymentMethod,
                $group->map(fn (Pembayaran $item): array => [
                    'id' => 'TAG-'.$item->tagihan_id,
                    'name' => $item->tagihan?->keterangan
                        ?: ucwords(str_replace('_', ' ', (string) ($item->tagihan?->jenis_tagihan ?? 'Tagihan sekolah'))),
                    'price' => (int) $item->jumlah_bayar,
                ])->all(),
                $this->customerDetails(),
                route('wali-siswa.pembayaran.digital', $payment),
                $payment->payment_environment,
            );
        } catch (Throwable $exception) {
            $this->recordGatewayFailure($payment, $exception);
            throw $exception;
        }

        Pembayaran::query()
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $payment->order_id)
            ->update([
                'payment_type' => (string) $transaction['paymentMethod'],
                'payment_url' => (string) $transaction['paymentUrl'],
                'gateway_total' => (int) $transaction['totalPayment'],
                'gateway_status' => (string) $transaction['status'],
                'payment_expires_at' => $this->parseGatewayDate($transaction['expiresAt'] ?? null),
                'gateway_response' => json_encode(collect($transaction)->except('paymentUrl')->all()),
                'gateway_error' => null,
            ]);
    }

    /** @return array<string, string> */
    private function customerDetails(): array
    {
        // Email akun wali umumnya alamat buatan sistem, sehingga hanya nama
        // yang dikirim agar Midtrans tidak mengirim struk ke alamat fiktif.
        return array_filter([
            'first_name' => Str::limit(trim((string) (Auth::user()?->name ?? '')), 50, ''),
        ]);
    }

    private function recordGatewayFailure(Pembayaran $payment, Throwable $exception): void
    {
        Pembayaran::query()
            ->where('payment_gateway', MidtransService::GATEWAY)
            ->where('order_id', $payment->order_id)
            ->update([
                'gateway_error' => $this->gatewayUserMessage($exception),
            ]);
    }

    private function parseGatewayDate(mixed $value): ?CarbonImmutable
    {
        return filled($value) ? CarbonImmutable::parse((string) $value) : null;
    }

    private function gatewayUserMessage(Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'sedang dibuat') || str_contains($message, 'sudah tercatat')) {
            return $exception->getMessage();
        }

        if (str_contains($message, 'nominal') || str_contains($message, 'batas') || str_contains($message, 'gross_amount')) {
            return 'Nominal tagihan tidak sesuai dengan batas kanal yang dipilih. Silakan pilih kanal lain.';
        }

        if (str_contains($message, 'metode') || str_contains($message, 'kanal') || str_contains($message, 'enabled_payments')) {
            return 'Kanal pembayaran sedang tidak tersedia. Silakan pilih kanal lain.';
        }

        return 'Kanal pembayaran belum dapat dibuat. Silakan coba lagi beberapa saat atau pilih kanal lain.';
    }

    private function guardParentAccess(Pembayaran $payment): void
    {
        abort_unless(
            Auth::user()?->financialChildren()->where('siswa.id', $payment->siswa_id)->exists(),
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
