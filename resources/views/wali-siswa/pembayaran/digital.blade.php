@extends('layouts.app')

@section('title', 'Pembayaran Tagihan')
@section('page-title', 'Pembayaran Tagihan')

@section('content')
@php
    $isPending = $pembayaran->status_validasi === 'pending';
    $isPaid = $pembayaran->status_validasi === 'disetujui';
    $gatewayTotal = (int) ($pembayaran->gateway_total ?: $totalBayar);
    $fee = max(0, $gatewayTotal - $totalBayar);
    $expired = $pembayaran->payment_expires_at && $pembayaran->payment_expires_at->isPast();
    $gatewayStatus = strtolower((string) $pembayaran->gateway_status);
    $paymentNotice = session('payment_notice');
    $paymentNoticeType = session('payment_notice_type');

    if (!$paymentNotice) {
        [$paymentNotice, $paymentNoticeType] = match (true) {
            $isPaid => ['Pembayaran telah dikonfirmasi otomatis dan tagihan siswa sudah diperbarui.', 'success'],
            $isPending && $expired => ['Batas waktu pembayaran telah berakhir. Silakan pilih kanal pembayaran baru.', 'warning'],
            $isPending && filled($pembayaran->payment_url) => ['Pembayaran masih menunggu penyelesaian.', 'info'],
            $isPending => [$pembayaran->gateway_error ?: 'Kanal pembayaran belum terbentuk. Silakan coba kembali atau pilih kanal lain.', 'warning'],
            $gatewayStatus === 'failed' => ['Pembayaran gagal diproses. Silakan kembali ke tagihan dan pilih kanal pembayaran lain.', 'danger'],
            $gatewayStatus === 'expired' => ['Transaksi pembayaran telah kedaluwarsa.', 'warning'],
            $gatewayStatus === 'cancelled' => ['Transaksi pembayaran telah dibatalkan.', 'danger'],
            default => ['Pembayaran sudah tidak aktif dan tidak dapat dilanjutkan.', 'danger'],
        };
    } else {
        $paymentNoticeType ??= 'info';
    }

    $alertColorMap = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'danger'  => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info'    => 'border-blue-200 bg-blue-50 text-blue-800',
    ];
    $alertIconMap = [
        'success' => 'fa-check-circle',
        'danger'  => 'fa-times-circle',
        'warning' => 'fa-exclamation-triangle',
        'info'    => 'fa-info-circle',
    ];
    $alertColors = $alertColorMap[$paymentNoticeType] ?? $alertColorMap['info'];
    $alertIcon = $alertIconMap[$paymentNoticeType] ?? $alertIconMap['info'];

    $heroBg = $isPaid ? 'from-emerald-600 to-emerald-700' : ($isPending && !$expired ? 'from-brand-600 to-brand-700' : 'from-slate-600 to-slate-700');
    $heroTitle = $isPaid ? 'Pembayaran Berhasil' : ($isPending && !$expired ? 'Selesaikan Pembayaran' : ($expired ? 'Pembayaran Kedaluwarsa' : 'Pembayaran Tidak Aktif'));
    $statusLabel = $isPaid ? 'Lunas' : ($isPending && !$expired ? 'Menunggu Pembayaran' : ($expired ? 'Kedaluwarsa' : 'Dibatalkan / Gagal'));
    $statusBg = $isPaid ? 'bg-emerald-100 text-emerald-700' : ($isPending ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
@endphp

<div class="min-w-0 w-full space-y-5">

    {{-- Back Button --}}
    <div>
        <a href="{{ route('wali-siswa.tagihan.anak', $pembayaran->siswa_id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Tagihan
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        {{-- Main Card --}}
        <div class="xl:col-span-2">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                @if($errors->any())
                    <div class="mx-5 mt-5 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                        <div>
                            <p class="font-bold">Kanal pembayaran belum dapat diterapkan.</p>
                            <ul class="mt-1 list-disc pl-4">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Hero --}}
                <div class="flex items-center justify-between bg-gradient-to-r {{ $heroBg }} px-6 py-5 text-white">
                    <div>
                        <span class="!text-white/80 text-[11px] font-bold uppercase tracking-widest">TAGIHAN SEKOLAH</span>
                        <h3 class="mt-1 !text-white text-xl font-bold">{{ $heroTitle }}</h3>
                        <p class="mt-0.5 !text-white/80 text-sm">Nomor transaksi {{ $pembayaran->order_id }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusBg }}">{{ $statusLabel }}</span>
                </div>

                <div class="p-5 space-y-5">
                    {{-- Rincian Tagihan --}}
                    <div>
                        <h6 class="mb-3 text-sm font-bold text-slate-700">Rincian Tagihan</h6>
                        <div class="divide-y divide-slate-100 rounded-lg border border-slate-200">
                            @foreach($allPayments as $item)
                                <div class="flex items-center justify-between px-4 py-3">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-800">{{ $item->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $item->tagihan->jenis_tagihan)) }}</div>
                                        <div class="mt-0.5 text-xs text-slate-400">{{ $item->kode_pembayaran }}</div>
                                    </div>
                                    <strong class="text-sm text-slate-800">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Total Box --}}
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Jumlah tagihan</span>
                            <strong class="text-slate-800">Rp {{ number_format($totalBayar, 0, ',', '.') }}</strong>
                        </div>
                        @if($fee > 0)
                            <div class="mt-2 flex items-center justify-between text-sm text-slate-500">
                                <span>Biaya kanal pembayaran</span>
                                <span>Rp {{ number_format($fee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                            <span class="text-sm font-bold text-slate-700">Total pembayaran</span>
                            <strong class="text-xl text-brand-600">Rp {{ number_format($gatewayTotal, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    {{-- Notice --}}
                    <div class="flex items-start gap-3 rounded-lg border {{ $alertColors }} p-4 text-sm">
                        <i class="fa-solid {{ $alertIcon }} mt-0.5 shrink-0"></i>
                        <span>{{ $paymentNotice }}</span>
                    </div>

                    {{-- Pay Button --}}
                    @if($isPending && $pembayaran->payment_url && !$expired)
                        <a href="{{ $pembayaran->payment_url }}" rel="noopener"
                           class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-600 px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                            <i class="fa-solid fa-lock"></i> Selesaikan Pembayaran
                        </a>
                        <p class="text-center text-xs text-slate-400">Anda dapat menutup halaman ini dan melanjutkan pembayaran kembali dari riwayat tagihan.</p>
                    @elseif($isPending)
                        <form action="{{ route('wali-siswa.pembayaran.continue', $pembayaran) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-700">
                                <i class="fa-solid fa-rotate"></i> Coba Buat Kanal Lagi
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Transaction Info --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h6 class="mb-3 text-sm font-bold text-slate-700">Informasi Transaksi</h6>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Siswa</dt>
                        <dd class="font-semibold text-slate-800">{{ $pembayaran->siswa->nama_lengkap }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Kanal</dt>
                        <dd class="font-semibold text-slate-800">{{ $pembayaran->payment_channel_label }}</dd>
                    </div>
                    @if($pembayaran->payment_expires_at)
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Berlaku hingga</dt>
                            <dd class="font-semibold text-slate-800">{{ $pembayaran->payment_expires_at->format('d M Y H:i') }} WIB</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Action Buttons --}}
            <div class="space-y-2">
                <form action="{{ route('wali-siswa.pembayaran.sync', $pembayaran) }}" method="POST">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-brand-300 px-4 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                        <i class="fa-solid fa-sync-alt"></i> Cek Status Pembayaran
                    </button>
                </form>
                <a href="{{ route('wali-siswa.pembayaran.invoice', $pembayaran) }}" target="_blank"
                   class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fa-solid fa-file-invoice"></i> Lihat Invoice
                </a>
            </div>

            {{-- Change Channel --}}
            @if($isPending && !$expired && count($paymentMethods) > 0)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h6 class="text-sm font-bold text-slate-700">Ganti Kanal Pembayaran</h6>
                    <p class="mt-0.5 mb-3 text-xs text-slate-500">Transaksi lama akan dibatalkan dengan aman sebelum kanal baru dibuat.</p>

                    <form action="{{ route('wali-siswa.pembayaran.change-method', $pembayaran) }}" method="POST" class="space-y-2">
                        @csrf
                        @foreach($paymentMethods as $method)
                            @php
                                $id = 'changeMethod' . preg_replace('/[^A-Za-z0-9]/', '', $method['code']);
                                $feeParts = [];
                                if ($method['fee_percent_bps'] > 0) {
                                    $feeParts[] = number_format($method['fee_percent_bps'] / 100, 2, ',', '.') . '%';
                                }
                                if ($method['fee_flat'] > 0) {
                                    $feeParts[] = 'Rp ' . number_format($method['fee_flat'], 0, ',', '.');
                                }
                            @endphp
                            <label class="relative block cursor-pointer">
                                <input type="radio" name="payment_method" value="{{ $method['code'] }}"
                                       @checked($pembayaran->payment_type === $method['code']) required class="peer sr-only">
                                <div class="flex items-center justify-between rounded-lg border-2 border-slate-200 pl-4 pr-12 py-3 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:ring-2 peer-checked:ring-brand-500/20 hover:border-slate-300">
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">{{ $method['name'] }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $feeParts ? 'Biaya ' . implode(' + ', $feeParts) : 'Biaya ditampilkan saat memilih bank' }}</div>
                                    </div>
                                </div>
                                    <span class="absolute right-4 top-1/2 flex h-5 w-5 -translate-y-1/2 items-center justify-center rounded-full bg-brand-600 text-white opacity-0 transition peer-checked:opacity-100" aria-hidden="true">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </span>
                            </label>
                        @endforeach
                        <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg border border-brand-300 px-4 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                            <i class="fa-solid fa-arrows-rotate"></i> Terapkan Kanal Pilihan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
