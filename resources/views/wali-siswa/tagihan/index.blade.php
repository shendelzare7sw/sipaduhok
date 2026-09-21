@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran - ' . $siswa->nama_lengkap)
@section('page-title', 'Tagihan & Pembayaran')

@section('content')
<div class="min-w-0 w-full space-y-4 pb-24 sm:space-y-5"
     x-data="tagihanPage">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="text-lg font-bold text-slate-800">Tagihan & Pembayaran</h4>
            <p class="mt-0.5 text-sm text-slate-500">
                <i class="fa-solid fa-user-graduate mr-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-1">·</span>
                <i class="fa-solid fa-school mr-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <a href="{{ route('wali-siswa.dashboard') }}" class="inline-flex items-center gap-1.5 self-start rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" x-data="{ show: true }" x-show="show">
            <i class="fa-solid fa-exclamation-triangle mt-0.5 shrink-0"></i>
            <div class="flex-1">
                <strong>Terdapat kesalahan pada input Anda:</strong>
                <ul class="mt-1 list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="shrink-0 text-red-400 hover:text-red-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    @php
        $summaryCards = [
            ['label' => 'Tagihan Tahun Ini', 'value' => $totalTagihanCurrent, 'icon' => 'fa-calendar-day', 'color' => 'blue', 'valueColor' => 'text-slate-800'],
            ['label' => 'Total Tunggakan',   'value' => $totalTunggakan,      'icon' => 'fa-history',      'color' => 'red',  'valueColor' => 'text-red-600', 'highlight' => $totalTunggakan > 0],
            ['label' => 'Sudah Dibayar (Thn Ini)', 'value' => $totalBayarCurrent, 'icon' => 'fa-check-circle', 'color' => 'emerald', 'valueColor' => 'text-emerald-600'],
            ['label' => 'Total Kewajiban',   'value' => $grandTotalUnpaid,    'icon' => 'fa-wallet',       'color' => 'amber', 'valueColor' => 'text-slate-800'],
        ];
        $iconBgMap = ['blue' => 'bg-blue-50 text-blue-500', 'red' => 'bg-red-50 text-red-500', 'emerald' => 'bg-emerald-50 text-emerald-500', 'amber' => 'bg-amber-50 text-amber-500'];
    @endphp
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
        @foreach($summaryCards as $card)
            <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-5 {{ ($card['highlight'] ?? false) ? 'ring-1 ring-red-300' : '' }}">
                <div class="flex min-w-0 items-center gap-2.5 sm:gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg sm:h-10 sm:w-10 {{ $iconBgMap[$card['color']] }}">
                        <i class="fa-solid {{ $card['icon'] }}"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-[9px] font-semibold uppercase tracking-wide text-slate-500 sm:text-[11px]">{{ $card['label'] }}</div>
                        <div class="mt-0.5 truncate text-sm font-bold {{ $card['valueColor'] }} sm:text-lg">Rp {{ number_format($card['value'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Daftar Tagihan --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h5 class="flex items-center gap-2 text-base font-semibold text-slate-800">
                <i class="fa-solid fa-list text-brand-600"></i> Daftar Tagihan
            </h5>
            @if($sisaTagihanCurrent > 0)
                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-700">{{ $tagihan->where('status', 'belum_bayar')->where('tahun_ajaran_id', $activeYear->id ?? 0)->count() }} Belum Lunas</span>
            @else
                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700"><i class="fa-solid fa-check mr-0.5"></i> Lunas (Tahun Ini)</span>
            @endif
        </div>
        <div class="p-5">
            @if($tagihan->isEmpty())
                <div class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    <i class="fa-solid fa-info-circle mt-0.5 shrink-0"></i>
                    <div>Tidak ada tagihan untuk siswa ini.</div>
                </div>
            @else
                {{-- Section A: Tunggakan TA Lama BELUM DIALIHKAN --}}
                @if($arrearsBelumDialihkanGroup->isNotEmpty())
                    <div class="mb-6">
                        <div class="mb-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                            <i class="fa-solid fa-exclamation-triangle mt-0.5 shrink-0 text-lg"></i>
                            <div>
                                <strong>Perhatian:</strong> Terdapat tunggakan dari tahun ajaran sebelumnya yang
                                <strong>belum dialihkan</strong> ke TA aktif. Daftar di bawah hanya tampil sebagai informasi —
                                silakan <strong>hubungi bendahara sekolah</strong> agar tunggakan ini dialihkan ke tagihan TA aktif terlebih dahulu sebelum dapat dibayar.
                            </div>
                        </div>

                        @foreach($arrearsBelumDialihkanGroup as $tahunId => $tagihans)
                            @php $tahunLabel = $tagihans->first()->tahunAjaran->nama_tahun_ajaran ?? 'Tahun Lalu'; @endphp
                            <h6 class="mb-3 border-b border-red-200 pb-2 font-bold text-red-600">
                                <i class="fa-solid fa-history mr-1"></i> Tunggakan TA {{ $tahunLabel }} <span class="text-xs font-normal text-slate-500">(belum dialihkan)</span>
                            </h6>

                            <div class="mb-4 overflow-hidden rounded-lg border border-red-200">
                                <div class="hidden grid-cols-[minmax(0,1.5fr)_minmax(140px,.9fr)_minmax(170px,1fr)_minmax(145px,.85fr)] items-center gap-x-4 bg-red-50 px-4 py-3 text-[11px] font-extrabold uppercase tracking-wide text-slate-500 lg:grid">
                                    <span>Keterangan</span>
                                    <span>Jatuh Tempo</span>
                                    <span class="text-right">Tagihan</span>
                                    <span class="text-center whitespace-nowrap">Status</span>
                                </div>
                                @foreach($tagihans as $item)
                                    @php $isPartial = $item->sisa_tagihan < $item->jumlah; @endphp
                                    <article class="grid grid-cols-2 gap-x-4 gap-y-3 border-t border-red-100 bg-red-50/30 p-3 text-sm first:border-t-0 lg:grid-cols-[minmax(0,1.5fr)_minmax(140px,.9fr)_minmax(170px,1fr)_minmax(145px,.85fr)] lg:items-center lg:gap-x-4 lg:gap-y-0 lg:px-4 lg:py-3">
                                        <div class="col-span-2 min-w-0 lg:col-span-1">
                                            <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Keterangan</span>
                                            <div class="break-words font-bold text-red-700">{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</div>
                                            <div class="mt-0.5 text-xs text-slate-500">Status: {{ $item->status == 'cicilan' ? 'Cicilan' : 'Belum Lunas' }}</div>
                                        </div>
                                        <div class="min-w-0 text-slate-600 lg:col-span-1">
                                            <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Jatuh Tempo</span>
                                            <span class="whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}</span>
                                        </div>
                                        <div class="text-right lg:col-span-1">
                                            <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Tagihan</span>
                                            <span class="whitespace-nowrap font-bold text-red-600">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</span>
                                            @if($isPartial)
                                                <div class="text-xs text-slate-400 line-through">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-span-2 flex items-center justify-between gap-3 lg:col-span-1 lg:justify-center">
                                            <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Status</span>
                                            <span class="inline-flex whitespace-nowrap rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-700" title="Hubungi sekolah untuk pengalihan">
                                                <i class="fa-solid fa-lock mr-0.5"></i> Belum Bisa Dibayar
                                            </span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <p class="mt-1 text-xs text-slate-500"><i class="fa-solid fa-info-circle mr-1"></i>Tunggakan ini hanya tampil sebagai laporan. Hubungi bendahara sekolah agar dialihkan ke tagihan TA aktif sebelum dapat dibayar.</p>
                        @endforeach
                    </div>
                @endif

                {{-- Section B: Tunggakan TA Lama SUDAH DIALIHKAN --}}
                @if($arrearsDialihkanGroup->isNotEmpty())
                    <div class="mb-6">
                        <div class="mb-4 flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                            <i class="fa-solid fa-info-circle mt-0.5 shrink-0 text-lg"></i>
                            <div>
                                <strong>Riwayat:</strong> Tunggakan dari TA sebelumnya berikut sudah <strong>dialihkan</strong> menjadi tagihan baru di TA aktif (lihat di bagian "Tagihan Tahun Ajaran Ini" di bawah).
                            </div>
                        </div>

                        @foreach($arrearsDialihkanGroup as $tahunId => $tagihans)
                            @php $tahunLabel = $tagihans->first()->tahunAjaran->nama_tahun_ajaran ?? 'Tahun Lalu'; @endphp
                            <h6 class="mb-3 border-b border-slate-200 pb-2 font-bold text-slate-500">
                                <i class="fa-solid fa-history mr-1"></i> TA {{ $tahunLabel }} <span class="text-xs font-normal text-slate-400">(sudah dialihkan)</span>
                            </h6>
                            <div class="mb-4 overflow-hidden rounded-lg border border-slate-200">
                                <div class="hidden grid-cols-[minmax(0,1.6fr)_minmax(150px,1fr)_minmax(175px,1fr)] items-center gap-x-4 bg-slate-50 px-4 py-3 text-[11px] font-extrabold uppercase tracking-wide text-slate-500 lg:grid">
                                    <span>Keterangan</span>
                                    <span class="text-right">Jumlah Asli</span>
                                    <span class="text-center whitespace-nowrap">Status</span>
                                </div>
                                @foreach($tagihans as $item)
                                    <article class="grid grid-cols-2 gap-x-4 gap-y-3 border-t border-slate-100 p-3 text-sm text-slate-400 first:border-t-0 lg:grid-cols-[minmax(0,1.6fr)_minmax(150px,1fr)_minmax(175px,1fr)] lg:items-center lg:gap-x-4 lg:gap-y-0 lg:px-4 lg:py-3">
                                        <div class="col-span-2 min-w-0 lg:col-span-1">
                                            <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Keterangan</span>
                                            <div class="break-words font-semibold text-slate-600">{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</div>
                                            <div class="mt-0.5 text-xs">Dialihkan {{ $item->dialihkan_pada ? \Carbon\Carbon::parse($item->dialihkan_pada)->format('d M Y') : '-' }}</div>
                                        </div>
                                        <div class="text-right text-slate-600 lg:col-span-1">
                                            <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Jumlah Asli</span>
                                            <span class="whitespace-nowrap">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="col-span-2 flex items-center justify-between gap-3 lg:col-span-1 lg:justify-center">
                                            <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Status</span>
                                            @if($item->status === 'sudah_bayar')
                                                <span class="inline-flex whitespace-nowrap rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Lunas (via TA aktif)</span>
                                            @else
                                                <span class="inline-flex whitespace-nowrap rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-bold text-blue-700">Sudah dialihkan</span>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($arrearsBelumDialihkanGroup->isNotEmpty() || $arrearsDialihkanGroup->isNotEmpty())
                    <h5 class="mb-4 mt-6 flex items-center gap-2 text-base font-semibold text-slate-800">
                        <i class="fa-solid fa-calendar-check text-brand-600"></i> Tagihan Tahun Ajaran Ini
                    </h5>
                @endif

                {{-- Tagihan per Jenis --}}
                @foreach($tagihanGroup as $jenis => $items)
                    <div class="mb-5">
                        <h6 class="mb-3 border-b border-slate-200 pb-2 text-xs font-extrabold uppercase tracking-wide text-slate-500">
                            <i class="fa-solid fa-folder mr-1"></i>{{ \App\Models\Tagihan::getLabelJenis($jenis) ?? ucwords(str_replace('_', ' ', $jenis)) }}
                        </h6>
                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <div class="hidden grid-cols-[4.5rem_minmax(220px,1fr)_150px_190px_145px] items-center gap-x-4 bg-slate-50 px-4 py-3 text-[11px] font-extrabold uppercase tracking-wide text-slate-500 lg:grid">
                                <span class="text-center">Pilih</span>
                                <span>Keterangan</span>
                                <span>Jatuh Tempo</span>
                                <span class="text-right">Tagihan</span>
                                <span class="text-center whitespace-nowrap">Status</span>
                            </div>
                            @foreach($items as $item)
                                @php
                                    $isPaid = $item->status == 'sudah_bayar';
                                    $isPartial = (!$isPaid && $item->sisa_tagihan < $item->jumlah);
                                @endphp
                                <article class="{{ $isPaid ? 'bg-emerald-50/30' : 'hover:bg-slate-50' }} grid grid-cols-2 gap-x-4 gap-y-3 border-t border-slate-100 p-3 text-sm transition first:border-t-0 lg:grid-cols-[4.5rem_minmax(220px,1fr)_150px_190px_145px] lg:items-center lg:gap-x-4 lg:gap-y-0 lg:px-4 lg:py-3">
                                    <div class="col-span-2 flex items-center justify-between lg:col-span-1 lg:justify-center">
                                        <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Pilih</span>
                                        @if(!$isPaid)
                                            <input type="checkbox"
                                                   class="h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-2 focus:ring-brand-500/20"
                                                   x-model="selectedItems"
                                                   value="{{ $item->id }}"
                                                   data-amount="{{ $item->sisa_tagihan }}"
                                                   data-label="{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}"
                                                   @change="updateTotal()">
                                        @else
                                            <i class="fa-solid fa-check text-emerald-500"></i>
                                        @endif
                                    </div>
                                    <div class="col-span-2 min-w-0 lg:col-span-1">
                                        <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Keterangan</span>
                                        <div class="break-words font-semibold text-slate-800">{{ $item->keterangan ?: ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</div>
                                        @if($item->tagihan_asal_id && $item->tagihanAsal)
                                            <span class="mt-1 inline-flex max-w-full items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                                                <i class="fa-solid fa-exchange-alt mr-0.5 shrink-0"></i>
                                                <span class="truncate">Tunggakan dari TA {{ $item->tagihanAsal->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 text-slate-600 lg:col-span-1">
                                        <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Jatuh Tempo</span>
                                        <div class="whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d M Y') }}</div>
                                        @if(\Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->isPast() && !$isPaid)
                                            <span class="mt-1 inline-flex rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Terlambat</span>
                                        @endif
                                    </div>
                                    <div class="text-right lg:col-span-1">
                                        <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Tagihan</span>
                                        @if($isPaid)
                                            <span class="whitespace-nowrap font-bold text-emerald-600">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                        @else
                                            <span class="whitespace-nowrap font-bold text-red-600">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</span>
                                            @if($isPartial)
                                                <div class="text-xs text-slate-400 line-through">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-span-2 flex items-center justify-between gap-3 lg:col-span-1 lg:justify-center">
                                        <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Status</span>
                                        @if($isPaid)
                                            <span class="inline-flex whitespace-nowrap rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Lunas</span>
                                        @else
                                            <span class="inline-flex whitespace-nowrap rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-700">Belum Bayar</span>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Sticky Footer for Bulk Payment --}}
    <div x-show="selectedCount > 0" x-transition x-cloak
         class="fixed bottom-0 left-0 right-0 z-30 border-t border-slate-200 bg-white/95 px-3 py-3 shadow-2xl backdrop-blur-sm sm:px-5 lg:left-72">
        <div class="flex items-center justify-between gap-3">
            <button type="button" @click="openPaymentModal()"
                    class="inline-flex min-h-10 items-center gap-1.5 rounded-lg bg-brand-600 px-3.5 py-2.5 text-xs font-bold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 sm:gap-2 sm:px-5 sm:text-sm">
                <i class="fa-solid fa-wallet"></i><span>Bayar Sekarang</span>
            </button>
            <div class="text-right">
                <span class="block text-[10px] text-slate-500 sm:text-xs"><span x-text="selectedCount"></span> item terpilih</span>
                <span class="text-base font-bold text-brand-600 sm:text-xl" x-text="formatRupiah(totalBayar)"></span>
            </div>
        </div>
    </div>

    {{-- Bulk Payment Alpine Dialog --}}
    <template x-teleport="body">
        <div x-show="showPayModal" x-cloak
             class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-black/60 p-4"
             @keydown.escape.window="showPayModal = false">
            <div @click.outside="showPayModal = false"
                 x-show="showPayModal" x-transition
                 class="relative w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-2xl">
                <form action="{{ route('wali-siswa.tagihan.bulk-pay', $siswa->id) }}" method="POST" enctype="multipart/form-data"
                      @submit="handleSubmit($event)">
                    @csrf

                    {{-- Hidden inputs generated by Alpine --}}
                    <template x-for="(item, idx) in paymentItems" :key="idx">
                        <div>
                            <input type="hidden" :name="`items[${idx}][tagihan_id]`" :value="item.id">
                            <input type="hidden" :name="`items[${idx}][jumlah_bayar]`" :value="item.amount">
                        </div>
                    </template>
                    <input type="hidden" name="total_bayar" :value="totalBayar">

                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                        <h5 class="flex items-center gap-2 text-sm font-bold text-slate-800">
                            <i class="fa-solid fa-money-bill-wave text-brand-600"></i> Konfirmasi Pembayaran
                        </h5>
                        <button type="button" @click="showPayModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="max-h-[75vh] overflow-y-auto p-5 space-y-5">
                        {{-- Rincian --}}
                        <div>
                            <h6 class="mb-3 border-b border-slate-200 pb-2 text-sm font-bold text-slate-700">Rincian Pembayaran</h6>
                            <div class="space-y-2">
                                <template x-for="item in paymentItems" :key="item.id">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-600" x-text="item.label"></span>
                                        <span class="font-semibold text-slate-800" x-text="formatRupiah(item.amount)"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-3 flex items-center justify-between rounded-lg bg-slate-50 p-3 text-sm">
                                <span class="font-bold text-slate-700">Total Yang Harus Dibayar</span>
                                <span class="text-lg font-bold text-brand-600" x-text="formatRupiah(totalBayar)"></span>
                            </div>
                        </div>

                        {{-- Metode --}}
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">Pilihan Metode Pembayaran</label>
                            <div class="grid grid-cols-3 gap-2">
                                @if($infoPembayaran->isPaywuzEnabled() && count($paymentMethods) > 0)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="metode_pembayaran" value="paywuz" x-model="selectedMethod" class="peer sr-only" required>
                                        <div class="flex flex-col items-center justify-center rounded-lg border-2 border-slate-200 bg-white p-4 text-center transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:ring-2 peer-checked:ring-brand-500/20 hover:border-slate-300">
                                            <i class="fa-solid fa-credit-card mb-2 text-2xl text-slate-400 peer-checked:text-brand-600"></i>
                                            <span class="text-xs font-bold text-slate-700">Kanal Pembayaran</span>
                                        </div>
                                    </label>
                                @endif

                                @if($infoPembayaran->isDirectTransferEnabled())
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="metode_pembayaran" value="transfer" x-model="selectedMethod" class="peer sr-only" required>
                                        <div class="flex flex-col items-center justify-center rounded-lg border-2 border-slate-200 bg-white p-4 text-center transition peer-checked:border-cyan-500 peer-checked:bg-cyan-50 peer-checked:ring-2 peer-checked:ring-cyan-500/20 hover:border-slate-300">
                                            <i class="fa-solid fa-university mb-2 text-2xl text-slate-400"></i>
                                            <span class="text-xs font-bold text-slate-700">Direct Transfer</span>
                                        </div>
                                    </label>
                                @endif

                                <div class="relative cursor-not-allowed opacity-50">
                                    <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 p-4 text-center">
                                        <i class="fa-solid fa-money-bill-wave mb-2 text-2xl text-slate-300"></i>
                                        <span class="text-xs font-bold text-slate-400">Tunai (Sekolah)</span>
                                    </div>
                                </div>
                            </div>

                            @if((!$infoPembayaran->isPaywuzEnabled() || count($paymentMethods) === 0) && !$infoPembayaran->isDirectTransferEnabled())
                                <div class="mt-3 flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                                    <i class="fa-solid fa-exclamation-triangle mt-0.5 shrink-0"></i>
                                    <div><strong>Tidak ada metode pembayaran online yang tersedia.</strong> Silakan lakukan pembayaran tunai di sekolah.</div>
                                </div>
                            @endif
                        </div>

                        {{-- 1. Paywuz Info --}}
                        <div x-show="selectedMethod === 'paywuz'" x-transition x-cloak>
                            <div class="mb-3 flex items-start gap-2 rounded-lg border border-brand-200 bg-brand-50 p-3 text-xs text-brand-800">
                                <i class="fa-solid fa-shield-alt mt-0.5 shrink-0"></i>
                                <div>Setelah konfirmasi, Anda akan diarahkan ke halaman pembayaran aman. Status tagihan akan diperbarui <strong>otomatis</strong>.</div>
                            </div>

                            <label class="mb-2 block text-sm font-bold text-slate-700">Pilih kanal pembayaran <span class="text-red-500">*</span></label>
                            <div class="space-y-2">
                                @foreach($paymentMethods as $method)
                                    @php
                                        $methodId = 'ch_' . preg_replace('/[^A-Za-z0-9]/', '', $method['code']);
                                        $methodIcon = match ($method['type']) {
                                            'qris' => 'fa-qrcode',
                                            'retail' => 'fa-store',
                                            default => 'fa-university',
                                        };
                                        $feeParts = [];
                                        if ($method['fee_percent_bps'] > 0) {
                                            $feeParts[] = number_format($method['fee_percent_bps'] / 100, 2, ',', '.') . '%';
                                        }
                                        if ($method['fee_flat'] > 0) {
                                            $feeParts[] = 'Rp ' . number_format($method['fee_flat'], 0, ',', '.');
                                        }
                                    @endphp
                                    <label class="relative block cursor-pointer"
                                           data-payment-channel
                                           data-min-amount="{{ $method['min_amount'] }}"
                                           data-max-amount="{{ $method['max_amount'] }}"
                                           x-show="isChannelAvailable({{ $method['min_amount'] }}, {{ $method['max_amount'] }})">
                                        <input type="radio" name="payment_method" value="{{ $method['code'] }}" class="peer sr-only"
                                               :required="selectedMethod === 'paywuz'">
                                        <div class="flex items-center gap-3 rounded-lg border-2 border-slate-200 bg-white pl-4 pr-12 py-3 transition peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:ring-2 peer-checked:ring-brand-500/20 hover:border-slate-300">
                                            <i class="fa-solid {{ $methodIcon }} w-5 text-center text-slate-400"></i>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-bold text-slate-800">{{ $method['name'] }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $feeParts ? 'Biaya ' . implode(' + ', $feeParts) : 'Biaya mengikuti bank yang dipilih' }}</div>
                                                <div class="text-[11px] text-slate-400">Rp {{ number_format($method['min_amount'], 0, ',', '.') }}–Rp {{ number_format($method['max_amount'], 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                        <span class="absolute right-4 top-1/2 flex h-5 w-5 -translate-y-1/2 items-center justify-center rounded-full bg-brand-600 text-white opacity-0 transition peer-checked:opacity-100" aria-hidden="true">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p x-show="availableChannelCount === 0" class="mt-2 text-xs text-red-500">Tidak ada kanal yang mendukung total pembayaran ini.</p>
                        </div>

                        @if($infoPembayaran->isPaywuzEnabled() && count($paymentMethods) === 0)
                            <div class="flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                                <i class="fa-solid fa-exclamation-triangle mt-0.5 shrink-0"></i>
                                <div>Kanal pembayaran digital sedang tidak dapat dimuat.
                                    {{ $infoPembayaran->isDirectTransferEnabled() ? 'Silakan gunakan Direct Transfer atau coba kembali nanti.' : 'Silakan coba kembali nanti atau hubungi sekolah.' }}
                                </div>
                            </div>
                        @endif

                        {{-- 2. Direct Transfer Info --}}
                        @if($infoPembayaran->isDirectTransferEnabled())
                            <div x-show="selectedMethod === 'transfer'" x-transition x-cloak>
                                <div class="mb-3 rounded-lg border border-cyan-200 bg-cyan-50 p-4">
                                    <h6 class="mb-3 flex items-center gap-2 text-sm font-bold text-cyan-700">
                                        <i class="fa-solid fa-university"></i> Rekening Tujuan
                                    </h6>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center gap-3">
                                            <span class="w-28 text-xs font-bold uppercase text-slate-500">Bank</span>
                                            <span class="font-bold text-slate-800">{{ $infoPembayaran->nama_bank }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="w-28 text-xs font-bold uppercase text-slate-500">No. Rekening</span>
                                            <span class="font-mono text-lg font-bold text-brand-600">{{ $infoPembayaran->rekening_bank }}</span>
                                            <button type="button" @click="copyRekening('{{ $infoPembayaran->rekening_bank }}')" class="rounded-lg border border-brand-300 px-2.5 py-1 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                                <i class="fa-solid" :class="copySuccess ? 'fa-check' : 'fa-copy'"></i> <span x-text="copySuccess ? 'Tersalin' : 'Salin'"></span>
                                            </button>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="w-28 text-xs font-bold uppercase text-slate-500">Atas Nama</span>
                                            <span class="font-bold text-slate-800">{{ $infoPembayaran->atas_nama }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-bold text-slate-700">Bukti Direct Transfer <span class="text-red-500">*</span></label>
                                    <input type="file" name="bukti_bayar" accept="image/*"
                                           :required="selectedMethod === 'transfer'"
                                           @change="validateFileSize($event)"
                                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                                    <p class="mt-1 text-xs text-slate-400">Upload foto bukti Direct Transfer total nominal (Max: 10MB).</p>
                                </div>
                            </div>
                        @endif

                        {{-- 3. Tunai Info (always visible) --}}
                        <div class="rounded-lg border border-amber-200 bg-amber-50">
                            <div class="border-b border-amber-200 bg-amber-100/50 px-4 py-2.5 text-xs font-bold text-amber-700">
                                <i class="fa-solid fa-money-bill-wave mr-1"></i> Informasi Pembayaran Tunai
                            </div>
                            <div class="p-4 text-sm">
                                @php $tunai = $infoPembayaran->tunai_info ?? []; @endphp
                                <p class="mb-3 text-xs font-bold text-red-600">
                                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                    Pembayaran tunai <strong>tidak dapat dilakukan secara online</strong>. Silakan kunjungi lokasi di bawah ini.
                                </p>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="block text-[11px] font-bold uppercase text-slate-500">Lokasi</span>
                                        <span class="text-slate-700">{{ $tunai['lokasi'] ?? 'Loket Pembayaran Sekolah' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[11px] font-bold uppercase text-slate-500">Jam Operasional</span>
                                        <span class="text-slate-700">{{ $tunai['jam_operasional'] ?? 'Jam Kerja' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[11px] font-bold uppercase text-slate-500">Catatan</span>
                                        <span class="text-slate-700">{{ $tunai['deskripsi'] ?? 'Harap membawa kartu siswa saat melakukan pembayaran.' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
                        <button type="button" @click="showPayModal = false" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Batal</button>
                        <button type="submit"
                                :disabled="submitting || {{ ((!$infoPembayaran->isPaywuzEnabled() || count($paymentMethods) === 0) && !$infoPembayaran->isDirectTransferEnabled()) ? 'true' : 'false' }}"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2 text-sm font-bold text-white transition hover:bg-brand-700 disabled:opacity-50">
                            <template x-if="submitting">
                                <span class="flex items-center gap-2"><svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg> Memproses...</span>
                            </template>
                            <template x-if="!submitting">
                                <span><i class="fa-solid fa-check-circle mr-1"></i> Bayar</span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- Riwayat Pembayaran --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
            <i class="fa-solid fa-history text-brand-600"></i>
            <h5 class="text-base font-semibold text-slate-800">Riwayat Pembayaran</h5>
        </div>
        <div class="p-4">
            @if($riwayatPembayaran->isEmpty())
                <div class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    <i class="fa-solid fa-info-circle mt-0.5 shrink-0"></i>
                    <div>Belum ada riwayat pembayaran.</div>
                </div>
            @else
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <div class="hidden grid-cols-[120px_minmax(180px,1fr)_125px_135px_100px_150px] items-center gap-x-4 bg-slate-50 px-4 py-3 text-[11px] font-extrabold uppercase tracking-wide text-slate-500 lg:grid">
                        <span>Tanggal</span>
                        <span>Tagihan</span>
                        <span class="text-right">Jumlah</span>
                        <span>Metode</span>
                        <span class="text-center">Status</span>
                        <span class="text-center">Aksi</span>
                    </div>
                    @foreach($riwayatPembayaran as $bayar)
                        @php
                            $canContinue = $bayar->metode_pembayaran == 'paywuz'
                                && $bayar->status_validasi == 'pending'
                                && (!$bayar->payment_expires_at || $bayar->payment_expires_at->isFuture());
                        @endphp
                        <article class="grid grid-cols-2 gap-x-4 gap-y-3 border-t border-slate-100 p-3 text-sm transition first:border-t-0 hover:bg-slate-50 lg:grid-cols-[120px_minmax(180px,1fr)_125px_135px_100px_150px] lg:items-center lg:gap-x-4 lg:gap-y-0 lg:px-4 lg:py-3">
                            <div class="col-span-2 min-w-0 lg:col-span-1">
                                <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Tanggal</span>
                                <span class="whitespace-nowrap text-slate-600">{{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y H:i') }}</span>
                            </div>
                            <div class="col-span-2 min-w-0 lg:col-span-1">
                                <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Tagihan</span>
                                <div class="break-words font-semibold text-slate-800">{{ $bayar->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan ?? '-')) }}</div>
                                <div class="mt-0.5 text-xs text-slate-400">ID: #{{ $bayar->kode_pembayaran }}</div>
                            </div>
                            <div class="text-right lg:col-span-1">
                                <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Jumlah</span>
                                <span class="whitespace-nowrap font-bold text-slate-800">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="min-w-0 lg:col-span-1">
                                <span class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Metode</span>
                                @if($bayar->metode_pembayaran == 'tunai')
                                    <span class="inline-flex whitespace-nowrap rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600"><i class="fa-solid fa-money-bill-wave mr-0.5"></i> Tunai</span>
                                @elseif($bayar->metode_pembayaran == 'transfer')
                                    <span class="inline-flex whitespace-nowrap rounded-full bg-cyan-100 px-2.5 py-1 text-[11px] font-bold text-cyan-700"><i class="fa-solid fa-university mr-0.5"></i> Direct Transfer</span>
                                @elseif($bayar->metode_pembayaran == 'paywuz')
                                    <x-payment-method-badge :payment="$bayar" />
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-3 lg:col-span-1 lg:justify-center">
                                <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Status</span>
                                <span class="whitespace-nowrap"><x-payment-status-badge :payment="$bayar" /></span>
                            </div>
                            <div class="col-span-2 flex flex-wrap items-center justify-between gap-2 lg:col-span-1 lg:justify-center">
                                <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">Aksi</span>
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    @if($canContinue)
                                        <form action="{{ route('wali-siswa.pembayaran.continue', $bayar->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-brand-600 px-2.5 py-1.5 text-[11px] font-semibold text-white transition hover:bg-brand-700" title="Lanjutkan Pembayaran">
                                                <i class="fa-solid fa-credit-card"></i> Lanjut Bayar
                                            </button>
                                        </form>
                                    @endif
                                    @if($bayar->metode_pembayaran != 'tunai')
                                        <a href="{{ route('wali-siswa.pembayaran.invoice', $bayar->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[11px] font-semibold text-slate-600 transition hover:border-brand-400 hover:text-brand-600" title="Lihat Invoice">
                                            <i class="fa-solid fa-file-invoice"></i> Invoice
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
