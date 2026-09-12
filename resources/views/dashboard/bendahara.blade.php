@extends('layouts.app')

@section('title', 'Dashboard Bendahara')
@section('page-title', 'Overview Keuangan')
@section('page-subtitle', 'Pantau aktivitas keuangan dan pembayaran siswa')

@section('content')
@php
    $stats = [
        [
            'label' => 'Total tagihan',
            'value' => 'Rp '.number_format($totalTagihan ?? 0, 0, ',', '.'),
            'note' => 'TA '.($tahunAjaran->nama_tahun_ajaran ?? '-'),
            'icon' => 'fa-file-invoice-dollar',
            'tone' => 'blue',
        ],
        [
            'label' => 'Total terbayar',
            'value' => 'Rp '.number_format($totalTerbayar ?? 0, 0, ',', '.'),
            'note' => 'Pembayaran tervalidasi',
            'icon' => 'fa-hand-holding-dollar',
            'tone' => 'emerald',
        ],
        [
            'label' => 'Kas masuk hari ini',
            'value' => 'Rp '.number_format($kasHariIni ?? 0, 0, ',', '.'),
            'note' => now()->translatedFormat('d F Y'),
            'icon' => 'fa-coins',
            'tone' => 'amber',
        ],
        [
            'label' => 'Menunggu validasi',
            'value' => number_format($pembayaranPending ?? 0),
            'note' => ($pembayaranPending ?? 0) > 0 ? 'Perlu ditindaklanjuti' : 'Semua sudah diperiksa',
            'icon' => 'fa-clock-rotate-left',
            'tone' => 'violet',
        ],
    ];

    $statTones = [
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-100',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-100',
    ];
@endphp

<div data-bendahara-dashboard class="min-w-0 w-full space-y-5">
    <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach($stats as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex min-w-0 items-start justify-between gap-3">
                    <p class="min-w-0 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-500 sm:text-xs">{{ $stat['label'] }}</p>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm ring-1 ring-inset sm:h-11 sm:w-11 {{ $statTones[$stat['tone']] }}">
                        <i class="fas {{ $stat['icon'] }}" aria-hidden="true"></i>
                    </span>
                </div>
                <p class="mt-3 truncate text-base font-extrabold tracking-tight tabular-nums text-slate-950 sm:text-xl" title="{{ $stat['value'] }}">{{ $stat['value'] }}</p>
                <p class="mt-4 truncate border-t border-slate-100 pt-3 text-[10px] font-semibold text-slate-500 sm:text-xs" title="{{ $stat['note'] }}">{{ $stat['note'] }}</p>
            </article>
        @endforeach
    </section>

    <div class="grid min-w-0 items-start gap-5 xl:grid-cols-[minmax(0,2fr)_minmax(19rem,1fr)]">
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" x-data="{ tab: 'validasi' }">
            <header class="border-b border-slate-200 p-4 sm:p-5">
                <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950 sm:text-lg">
                    <i class="fas fa-clipboard-check text-brand-600" aria-hidden="true"></i>Pusat tindakan
                </h2>
                <p class="mt-1 text-xs leading-5 text-slate-500 sm:text-sm">Dahulukan transaksi dan permintaan yang membutuhkan keputusan Anda.</p>
            </header>

            <div class="grid grid-cols-3 gap-1 border-b border-slate-200 bg-slate-50 p-1.5" role="tablist" aria-label="Kategori tindakan">
                <button type="button" @click="tab = 'validasi'" :aria-selected="tab === 'validasi'" :class="tab === 'validasi' ? 'bg-white text-brand-700 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-800'" class="flex min-h-11 min-w-0 items-center justify-center gap-1.5 rounded-xl px-2 text-[10px] font-extrabold transition sm:text-xs" role="tab">
                    <i class="fas fa-receipt hidden sm:inline" aria-hidden="true"></i><span class="truncate">Validasi</span>
                    @if(($pembayaranPending ?? 0) > 0)<span class="rounded-full bg-red-50 px-1.5 py-0.5 text-[9px] text-red-700">{{ $pembayaranPending > 99 ? '99+' : $pembayaranPending }}</span>@endif
                </button>
                <button type="button" @click="tab = 'transaksi'" :aria-selected="tab === 'transaksi'" :class="tab === 'transaksi' ? 'bg-white text-brand-700 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-800'" class="flex min-h-11 min-w-0 items-center justify-center gap-1.5 rounded-xl px-2 text-[10px] font-extrabold transition sm:text-xs" role="tab">
                    <i class="fas fa-arrow-right-arrow-left hidden sm:inline" aria-hidden="true"></i><span class="truncate">Transaksi</span>
                    @if(($transaksiTerbaru ?? collect())->isNotEmpty())<span class="rounded-full bg-emerald-50 px-1.5 py-0.5 text-[9px] text-emerald-700">{{ $transaksiTerbaru->count() }}</span>@endif
                </button>
                <button type="button" @click="tab = 'dispensasi'" :aria-selected="tab === 'dispensasi'" :class="tab === 'dispensasi' ? 'bg-white text-brand-700 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-800'" class="flex min-h-11 min-w-0 items-center justify-center gap-1.5 rounded-xl px-2 text-[10px] font-extrabold transition sm:text-xs" role="tab">
                    <i class="fas fa-handshake hidden sm:inline" aria-hidden="true"></i><span class="truncate">Dispensasi</span>
                    @if(($dispensasiPending ?? collect())->isNotEmpty())<span class="rounded-full bg-amber-50 px-1.5 py-0.5 text-[9px] text-amber-700">{{ $dispensasiPending->count() }}</span>@endif
                </button>
            </div>

            <div x-show="tab === 'validasi'" role="tabpanel">
                @forelse($pendingPembayaran ?? [] as $item)
                    <article class="flex min-w-0 flex-col gap-3 border-b border-slate-100 p-4 last:border-b-0 hover:bg-slate-50 sm:flex-row sm:items-center">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-amber-100"><i class="fas fa-receipt" aria-hidden="true"></i></span>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-sm font-extrabold text-slate-950">{{ $item->siswa->nama_lengkap ?? 'Siswa' }}</h3>
                            <p class="mt-1 flex flex-wrap gap-x-2 gap-y-1 text-[11px] text-slate-500"><span>{{ $item->kode_pembayaran }}</span><span>• {{ $item->siswa->kelas->nama_kelas ?? '-' }}</span><span>• {{ $item->payment_channel_label }}</span></p>
                        </div>
                        <div class="flex shrink-0 items-center justify-between gap-3 sm:block sm:text-right">
                            <strong class="block whitespace-nowrap text-sm tabular-nums text-amber-700">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                            <a href="{{ route('bendahara.pembayaran.show', $item->id) }}" class="mt-0 inline-flex min-h-9 items-center gap-2 rounded-xl bg-blue-50 px-3 text-xs font-bold text-blue-700 no-underline ring-1 ring-blue-100 hover:bg-blue-100 sm:mt-2"><i class="fas fa-eye" aria-hidden="true"></i>Review</a>
                        </div>
                    </article>
                @empty
                    <div class="px-5 py-14 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700"><i class="fas fa-check" aria-hidden="true"></i></span><h3 class="mt-4 font-extrabold text-slate-900">Semua tervalidasi</h3><p class="mt-1 text-sm text-slate-500">Tidak ada pembayaran yang menunggu pemeriksaan.</p></div>
                @endforelse
                @if(($pembayaranPending ?? 0) > 7)<a href="{{ route('bendahara.pembayaran.index', ['status' => 'pending']) }}" class="flex min-h-12 items-center justify-center gap-2 border-t border-slate-200 bg-slate-50 px-4 text-xs font-bold text-brand-700 no-underline hover:bg-blue-50">Lihat {{ $pembayaranPending }} pembayaran pending<i class="fas fa-arrow-right" aria-hidden="true"></i></a>@endif
            </div>

            <div x-cloak x-show="tab === 'transaksi'" role="tabpanel">
                @forelse($transaksiTerbaru ?? [] as $item)
                    <article class="flex min-w-0 items-center gap-3 border-b border-slate-100 p-4 last:border-b-0 hover:bg-slate-50">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100"><i class="fas fa-check" aria-hidden="true"></i></span>
                        <div class="min-w-0 flex-1"><h3 class="truncate text-sm font-extrabold text-slate-950">{{ $item->siswa->nama_lengkap ?? 'Siswa' }}</h3><p class="mt-1 truncate text-[11px] text-slate-500">{{ ucwords(str_replace('_', ' ', $item->tagihan->jenis_tagihan ?? '-')) }} • {{ $item->tanggal_validasi ? $item->tanggal_validasi->copy()->locale('id')->diffForHumans() : '-' }}</p></div>
                        <div class="shrink-0 text-right"><strong class="block whitespace-nowrap text-xs tabular-nums text-emerald-700 sm:text-sm">+Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong><span class="mt-1 inline-flex rounded-full bg-emerald-50 px-2 py-1 text-[9px] font-extrabold text-emerald-700">LUNAS</span></div>
                    </article>
                @empty
                    <div class="px-5 py-14 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fas fa-inbox" aria-hidden="true"></i></span><h3 class="mt-4 font-extrabold text-slate-900">Belum ada transaksi</h3><p class="mt-1 text-sm text-slate-500">Pembayaran tervalidasi akan muncul di sini.</p></div>
                @endforelse
                <a href="{{ route('bendahara.pembayaran.index', ['status' => 'disetujui']) }}" class="flex min-h-12 items-center justify-center gap-2 border-t border-slate-200 bg-slate-50 px-4 text-xs font-bold text-brand-700 no-underline hover:bg-blue-50">Lihat seluruh transaksi<i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div x-cloak x-show="tab === 'dispensasi'" role="tabpanel">
                @forelse($dispensasiPending ?? [] as $item)
                    <article class="flex min-w-0 items-center gap-3 border-b border-slate-100 p-4 last:border-b-0 hover:bg-slate-50">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-amber-100"><i class="fas fa-user-clock" aria-hidden="true"></i></span>
                        <div class="min-w-0 flex-1"><h3 class="truncate text-sm font-extrabold text-slate-950">{{ $item->nama_lengkap }}</h3><p class="mt-1 truncate text-[11px] text-slate-500">{{ $item->nama_kelas ?? '-' }} • NISN {{ $item->nisn ?? '-' }} • {{ \Carbon\Carbon::parse($item->created_at)->locale('id')->diffForHumans() }}</p></div>
                        <span class="shrink-0 rounded-full bg-amber-50 px-2 py-1 text-[9px] font-extrabold text-amber-700">MENUNGGU</span>
                    </article>
                @empty
                    <div class="px-5 py-14 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fas fa-handshake" aria-hidden="true"></i></span><h3 class="mt-4 font-extrabold text-slate-900">Tidak ada permintaan</h3><p class="mt-1 text-sm text-slate-500">Belum ada dispensasi yang menunggu keputusan.</p></div>
                @endforelse
                <a href="{{ route('bendahara.kenaikan-kelas.validation.index') }}" class="flex min-h-12 items-center justify-center gap-2 border-t border-slate-200 bg-slate-50 px-4 text-xs font-bold text-brand-700 no-underline hover:bg-blue-50">Kelola dispensasi<i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </section>

        <aside class="min-w-0 space-y-5">
            <section class="grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="flex min-w-0 items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900 no-underline transition hover:-translate-y-0.5 hover:shadow-sm"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/70 text-amber-700"><i class="fas fa-circle-exclamation"></i></span><span class="min-w-0 flex-1"><small class="block truncate text-[9px] font-extrabold uppercase tracking-wider text-amber-700">Belum lunas</small><strong class="mt-1 block truncate text-sm">{{ number_format($tagihanBelumLunas ?? 0) }} siswa</strong></span><i class="fas fa-chevron-right text-xs text-amber-500"></i></a>
                <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="flex min-w-0 items-center gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-900 no-underline transition hover:-translate-y-0.5 hover:shadow-sm"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/70 text-red-700"><i class="fas fa-clock"></i></span><span class="min-w-0 flex-1"><small class="block truncate text-[9px] font-extrabold uppercase tracking-wider text-red-700">Terlambat</small><strong class="mt-1 block truncate text-sm">{{ number_format($tagihanTerlambat ?? 0) }} siswa</strong></span><i class="fas fa-chevron-right text-xs text-red-500"></i></a>
                <a href="{{ route('bendahara.pembayaran.index') }}" class="flex min-w-0 items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 no-underline transition hover:-translate-y-0.5 hover:shadow-sm"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/70 text-emerald-700"><i class="fas fa-chart-line"></i></span><span class="min-w-0 flex-1"><small class="block truncate text-[9px] font-extrabold uppercase tracking-wider text-emerald-700">Terbayar bulan ini</small><strong class="mt-1 block truncate text-sm" title="Rp {{ number_format($pembayaranBulanIni ?? 0, 0, ',', '.') }}">Rp {{ number_format($pembayaranBulanIni ?? 0, 0, ',', '.') }}</strong></span><i class="fas fa-chevron-right text-xs text-emerald-500"></i></a>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h2 class="flex items-center gap-2 font-extrabold text-slate-950"><i class="fas fa-bolt text-amber-500"></i>Akses cepat</h2></header>
                <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 xl:grid-cols-2">
                    @foreach([
                        ['route' => route('bendahara.pembayaran.index', ['status' => 'pending']), 'icon' => 'fa-check-double', 'label' => 'Validasi pembayaran', 'tone' => 'text-blue-700 bg-blue-50'],
                        ['route' => route('bendahara.tagihan.bulk-create'), 'icon' => 'fa-plus', 'label' => 'Tagihan massal', 'tone' => 'text-emerald-700 bg-emerald-50'],
                        ['route' => route('bendahara.validasi-akses.index'), 'icon' => 'fa-id-card', 'label' => 'Validasi akses', 'tone' => 'text-amber-700 bg-amber-50'],
                        ['route' => route('bendahara.kenaikan-kelas.validation.index'), 'icon' => 'fa-handshake', 'label' => 'Validasi dispensasi', 'tone' => 'text-violet-700 bg-violet-50'],
                        ['route' => route('bendahara.tagihan.index'), 'icon' => 'fa-file-invoice-dollar', 'label' => 'Kelola tagihan', 'tone' => 'text-cyan-700 bg-cyan-50'],
                        ['route' => route('bendahara.laporan.index'), 'icon' => 'fa-print', 'label' => 'Cetak laporan', 'tone' => 'text-slate-700 bg-slate-100'],
                    ] as $link)
                        <a href="{{ $link['route'] }}" class="group flex min-h-24 min-w-0 flex-col items-center justify-center gap-2 rounded-xl border border-slate-200 p-3 text-center no-underline transition hover:border-brand-300 hover:bg-blue-50/50">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $link['tone'] }}"><i class="fas {{ $link['icon'] }}" aria-hidden="true"></i></span>
                            <span class="text-xs font-bold leading-5 text-slate-700 group-hover:text-brand-700">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
