@extends('layouts.app')

@section('title', 'Kelola Pembayaran')
@section('page-title', 'Kelola Pembayaran')
@section('page-subtitle', 'Periksa transaksi masuk dan validasi pembayaran siswa')

@section('content')
@php
    $hasFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();
    $inputClass = 'h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
@endphp

<div class="min-w-0 w-full space-y-5" data-payment-index x-data="{ query: @js($filters['search'] ?? '') }">
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-3">
        @foreach([
            ['Menunggu validasi', $stats['pending'], 'Perlu diperiksa', 'fa-clock-rotate-left', 'bg-amber-50 text-amber-600'],
            ['Disetujui', $stats['disetujui'], 'Pembayaran tervalidasi', 'fa-circle-check', 'bg-emerald-50 text-emerald-600'],
            ['Gagal / dibatalkan', $stats['ditolak'], 'Transaksi tidak dilanjutkan', 'fa-circle-xmark', 'bg-red-50 text-red-600'],
        ] as [$label, $value, $description, $icon, $tone])
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4 {{ $loop->last ? 'col-span-2 lg:col-span-1' : '' }}">
                <div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="text-xl font-extrabold text-slate-950 sm:text-2xl">{{ number_format($value) }}</p><p class="mt-1 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span></div>
                <p class="mt-3 truncate border-t border-slate-100 pt-3 text-[11px] text-slate-500">{{ $description }}</p>
            </article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-list-check text-brand-600" aria-hidden="true"></i>Rincian transaksi masuk</h2>
                <p class="mt-1 text-xs text-slate-500">{{ number_format($pembayaranList->total()) }} transaksi ditemukan. Buka detail untuk memeriksa dan memvalidasi.</p>
            </div>
            @if($stats['pending'] > 0)<span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-[10px] font-bold text-amber-700 ring-1 ring-amber-100"><i class="fas fa-circle-exclamation" aria-hidden="true"></i>{{ number_format($stats['pending']) }} perlu tindakan</span>@endif
        </header>

        <form action="{{ route('admin.keuangan.pembayaran.index') }}" method="GET" class="grid gap-2 border-b border-slate-200 p-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-[minmax(220px,1.2fr)_repeat(3,minmax(150px,0.55fr))_repeat(2,minmax(145px,0.5fr))_auto] sm:p-5">
            <label class="relative sm:col-span-2 lg:col-span-4 xl:col-span-1"><span class="sr-only">Cari transaksi</span><i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" x-model="query" value="{{ $filters['search'] ?? '' }}" placeholder="Cari siswa atau kode..." class="{{ $inputClass }} !pl-10 !pr-10"><button x-cloak x-show="query" type="button" @click="query = ''; $nextTick(() => $el.previousElementSibling.focus())" class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" aria-label="Hapus pencarian"><i class="fas fa-xmark" aria-hidden="true"></i></button></label>
            <select name="status" class="{{ $inputClass }}"><option value="">Semua status</option><option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Menunggu validasi</option><option value="disetujui" @selected(($filters['status'] ?? '') === 'disetujui')>Disetujui</option><option value="ditolak" @selected(($filters['status'] ?? '') === 'ditolak')>Gagal / dibatalkan</option></select>
            <select name="metode" class="{{ $inputClass }}"><option value="">Semua metode</option><option value="tunai" @selected(($filters['metode'] ?? '') === 'tunai')>Tunai</option><option value="transfer" @selected(($filters['metode'] ?? '') === 'transfer')>Direct Transfer</option><option value="paywuz" @selected(($filters['metode'] ?? '') === 'paywuz')>QRIS / VA / retail</option></select>
            <select name="kelas_id" class="{{ $inputClass }}"><option value="">Semua kelas</option>@foreach($kelasList as $kelas)<option value="{{ $kelas->id }}" @selected(($filters['kelas_id'] ?? '') == $kelas->id)>{{ $kelas->nama_kelas }} · {{ $kelas->jenjang }} · {{ $kelas->cabang->nama_cabang ?? 'Tanpa cabang' }}</option>@endforeach</select>
            <label><span class="sr-only">Dari tanggal</span><input type="date" name="tanggal_dari" value="{{ $filters['tanggal_dari'] ?? '' }}" class="{{ $inputClass }}" title="Dari tanggal"></label>
            <label><span class="sr-only">Sampai tanggal</span><input type="date" name="tanggal_sampai" value="{{ $filters['tanggal_sampai'] ?? '' }}" class="{{ $inputClass }}" title="Sampai tanggal"></label>
            <div class="flex gap-2"><button type="submit" class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white hover:bg-slate-900"><i class="fas fa-filter" aria-hidden="true"></i>Filter</button>@if($hasFilters)<a href="{{ route('admin.keuangan.pembayaran.index') }}" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-700 no-underline ring-1 ring-inset ring-red-100 hover:bg-red-100" aria-label="Reset filter" title="Reset filter"><i class="fas fa-xmark" aria-hidden="true"></i></a>@endif</div>
        </form>

        @if($pembayaranList->isEmpty())
            <div class="px-5 py-14 text-center text-sm text-slate-500"><i class="fas fa-inbox mb-3 block text-4xl text-slate-300" aria-hidden="true"></i>Tidak ada pembayaran yang ditemukan.<p class="mt-1 text-xs">Coba ubah pencarian atau filter yang digunakan.</p></div>
        @else
            <div class="divide-y divide-slate-100 lg:hidden">
                @foreach($pembayaranList as $pembayaran)
                    <article class="p-4">
                        <div class="flex min-w-0 items-start gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><i class="fas fa-receipt" aria-hidden="true"></i></span><div class="min-w-0 flex-1"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</h3><p class="mt-0.5 truncate font-mono text-[10px] font-bold text-brand-700">{{ $pembayaran->kode_pembayaran }}</p></div><x-payment-status-badge :payment="$pembayaran" class="shrink-0 text-[9px]" /></div>
                        <dl class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-xs"><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Tagihan</dt><dd class="mt-1 break-words font-bold text-slate-700">{{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}</dd></div><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Kelas</dt><dd class="mt-1 whitespace-nowrap font-bold text-slate-700">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</dd></div><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Jumlah</dt><dd class="mt-1 whitespace-nowrap font-extrabold text-slate-900">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</dd></div><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Tanggal</dt><dd class="mt-1 whitespace-nowrap font-bold text-slate-700">{{ $pembayaran->tanggal_bayar?->format('d/m/Y') ?? '-' }}</dd></div></dl>
                        <div class="mt-3 flex items-center justify-between gap-3"><x-payment-method-badge :payment="$pembayaran" />@if($pembayaran->order_id && $pembayaran->group_transactions_count > 1)<span class="text-[10px] font-bold text-violet-700" title="Transaksi gabungan"><i class="fas fa-layer-group mr-1" aria-hidden="true"></i>{{ $pembayaran->group_transactions_count }} tagihan</span>@endif<a href="{{ route('admin.keuangan.pembayaran.show', $pembayaran->id) }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-eye" aria-hidden="true"></i>Detail</a></div>
                    </article>
                @endforeach
            </div>

            <div class="hidden overflow-x-auto lg:block"><table class="w-full min-w-[1080px] table-fixed text-left text-xs">
                <colgroup><col class="w-12"><col class="w-40"><col><col class="w-[17%]"><col class="w-36"><col class="w-44"><col class="w-28"><col class="w-40"><col class="w-20"></colgroup>
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-3 py-3 text-center">No</th><th class="px-3 py-3">Kode</th><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">Tagihan</th><th class="px-3 py-3 text-right">Jumlah</th><th class="px-3 py-3">Metode</th><th class="px-3 py-3">Tanggal</th><th class="px-3 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">@foreach($pembayaranList as $index => $pembayaran)<tr class="hover:bg-slate-50/70">
                    <td class="px-3 py-4 text-center tabular-nums text-slate-400">{{ $pembayaranList->firstItem() + $index }}</td>
                    <td class="px-3 py-4"><code class="block truncate font-mono text-[11px] font-bold text-brand-700" title="{{ $pembayaran->kode_pembayaran }}">{{ $pembayaran->kode_pembayaran }}</code>@if($pembayaran->order_id && $pembayaran->group_transactions_count > 1)<span class="mt-1 inline-flex whitespace-nowrap rounded-full bg-violet-50 px-2 py-1 text-[9px] font-bold text-violet-700" title="Bagian dari transaksi gabungan"><i class="fas fa-layer-group mr-1" aria-hidden="true"></i>{{ $pembayaran->group_transactions_count }} tagihan</span>@endif</td>
                    <td class="min-w-0 px-3 py-4"><p class="truncate text-sm font-bold text-slate-900" title="{{ $pembayaran->siswa->nama_lengkap ?? '-' }}">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</p><p class="mt-0.5 truncate text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</p></td>
                    <td class="px-3 py-4"><p class="line-clamp-2 font-semibold leading-5 text-slate-600" title="{{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}">{{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}</p></td>
                    <td class="whitespace-nowrap px-3 py-4 text-right font-extrabold text-slate-900">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                    <td class="px-3 py-4"><x-payment-method-badge :payment="$pembayaran" /></td>
                    <td class="whitespace-nowrap px-3 py-4 font-semibold text-slate-600">{{ $pembayaran->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-3 py-4"><x-payment-status-badge :payment="$pembayaran" /></td>
                    <td class="px-4 py-4"><div class="flex justify-end"><x-cleanflow.table-action href="{{ route('admin.keuangan.pembayaran.show', $pembayaran->id) }}" tone="view" icon="fas fa-eye" label="Detail pembayaran" /></div></td>
                </tr>@endforeach</tbody>
            </table></div>

            <footer class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-5"><span>Menampilkan {{ $pembayaranList->firstItem() ?? 0 }}–{{ $pembayaranList->lastItem() ?? 0 }} dari {{ number_format($pembayaranList->total()) }} transaksi</span><div>{{ $pembayaranList->withQueryString()->links() }}</div></footer>
        @endif
    </section>
</div>
@endsection
