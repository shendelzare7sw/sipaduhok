@extends('layouts.app')

@section('title', 'Detail Tagihan - '.$siswa->nama_lengkap)
@section('page-title', 'Detail Tagihan Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('content')
@php
    $isAdminContext = request()->routeIs('admin.*');
    $tagihanRoute = $isAdminContext ? 'admin.keuangan.tagihan' : 'bendahara.tagihan';
    $pembayaranRoute = $isAdminContext ? 'admin.keuangan.pembayaran' : 'bendahara.pembayaran';
@endphp
<div data-tagihan-detail class="min-w-0 w-full space-y-5">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-brand-800 to-brand-600 p-5 text-white shadow-lg sm:p-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl ring-1 ring-white/20"><i class="fas fa-user-graduate" aria-hidden="true"></i></span>
                <div class="min-w-0">
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-blue-100">Tagihan siswa</p>
                    <h1 class="truncate text-xl font-black text-white sm:text-2xl">{{ $siswa->nama_lengkap }}</h1>
                    <p class="mt-1 truncate text-xs text-blue-100 sm:text-sm">NISN {{ $siswa->nisn ?: '-' }} · {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }} · {{ $siswa->cabang->nama_cabang ?? 'Cabang belum diatur' }}</p>
                </div>
            </div>
            <a href="{{ route($tagihanRoute.'.index') }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 text-sm font-bold text-white no-underline hover:bg-white/20"><i class="fas fa-arrow-left"></i>Kembali ke daftar</a>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-3">
        <article class="rounded-2xl border border-blue-200 bg-white p-4 shadow-sm sm:p-5"><div class="flex items-start justify-between gap-3"><div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Total tagihan</p><p class="mt-2 text-lg font-black tabular-nums text-slate-950 sm:text-xl">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fas fa-file-invoice-dollar"></i></span></div></article>
        <article class="rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm sm:p-5"><div class="flex items-start justify-between gap-3"><div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Sudah dibayar</p><p class="mt-2 text-lg font-black tabular-nums text-emerald-700 sm:text-xl">Rp {{ number_format($tagihanLunas, 0, ',', '.') }}</p></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="fas fa-circle-check"></i></span></div></article>
        <article class="col-span-2 rounded-2xl border {{ $sisaTagihan > 0 ? 'border-red-200' : 'border-emerald-200' }} bg-white p-4 shadow-sm lg:col-span-1 sm:p-5"><div class="flex items-start justify-between gap-3"><div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Sisa tagihan</p><p class="mt-2 text-lg font-black tabular-nums {{ $sisaTagihan > 0 ? 'text-red-700' : 'text-emerald-700' }} sm:text-xl">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</p></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $sisaTagihan > 0 ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}"><i class="fas {{ $sisaTagihan > 0 ? 'fa-clock' : 'fa-check-double' }}"></i></span></div></article>
    </section>

    <section class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
        <a href="{{ route($tagihanRoute.'.edit', $siswa->id) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-amber-50 px-4 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100 sm:text-sm"><i class="fas fa-pen"></i>Edit tagihan</a>
        <a href="{{ route($pembayaranRoute.'.create', $siswa->id) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white no-underline hover:bg-emerald-700 sm:text-sm"><i class="fas fa-plus"></i>Input pembayaran</a>
        <a href="{{ route($pembayaranRoute.'.riwayat-siswa', $siswa->id) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-cyan-50 px-4 text-xs font-bold text-cyan-700 no-underline hover:bg-cyan-100 sm:text-sm"><i class="fas fa-clock-rotate-left"></i>Riwayat bayar</a>
        <a href="{{ route($tagihanRoute.'.cetak', $siswa->id) }}" target="_blank" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200 sm:text-sm"><i class="fas fa-print"></i>Cetak tagihan</a>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5"><h2 class="flex items-center gap-2 text-lg font-extrabold text-slate-950"><i class="fas fa-receipt text-amber-600"></i>Rincian tagihan</h2><p class="mt-1 text-sm text-slate-500">Kewajiban, jatuh tempo, dan status pembayaran siswa.</p></header>

        @if($tagihan->isEmpty())
            <div class="px-5 py-14 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-inbox"></i></span><h3 class="mt-4 font-extrabold text-slate-900">Belum ada tagihan</h3><p class="mt-1 text-sm text-slate-500">Tambahkan kewajiban pertama untuk siswa ini.</p><a href="{{ route($tagihanRoute.'.edit', $siswa->id) }}" class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white no-underline"><i class="fas fa-plus"></i>Tambah tagihan</a></div>
        @else
            <div class="divide-y divide-slate-100 md:hidden">
                @foreach($tagihan as $item)
                    @php
                        $statusClasses = match ($item->status) {
                            'sudah_bayar' => 'bg-emerald-50 text-emerald-700',
                            'terlambat' => 'bg-red-50 text-red-700',
                            default => 'bg-amber-50 text-amber-700',
                        };
                        $statusLabel = match ($item->status) { 'sudah_bayar' => 'Lunas', 'terlambat' => 'Terlambat', default => 'Belum bayar' };
                    @endphp
                    <article class="p-4">
                        <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="font-extrabold text-slate-950">{{ $jenisTagihan[$item->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</h3><p class="mt-1 text-xs text-slate-500">Jatuh tempo {{ $item->tanggal_jatuh_tempo ? $item->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</p></div><span class="inline-flex shrink-0 rounded-full px-2 py-1 text-[10px] font-extrabold {{ $statusClasses }}">{{ $statusLabel }}</span></div>
                        @if($item->tagihan_asal_id && $item->tagihanAsal)<p class="mt-2 text-xs font-semibold text-orange-700"><i class="fas fa-arrow-right mr-1"></i>Carryover dari {{ $item->tagihanAsal->tahunAjaran->nama_tahun_ajaran ?? 'TA lama' }}</p>@endif
                        @if($item->dialihkan_ke_id && $item->tagihanAlihan)<p class="mt-2 text-xs font-semibold text-blue-700"><i class="fas fa-share mr-1"></i>Dialihkan ke {{ $item->tagihanAlihan->tahunAjaran->nama_tahun_ajaran ?? 'TA aktif' }}</p>@endif
                        @if($item->keterangan)<p class="mt-2 text-xs leading-5 text-slate-600">{{ $item->keterangan }}</p>@endif
                        <p class="mt-3 border-t border-slate-100 pt-3 text-right text-base font-black tabular-nums text-slate-950">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</p>
                    </article>
                @endforeach
                <div class="flex items-center justify-between bg-slate-900 px-4 py-4 text-white"><span class="text-xs font-extrabold uppercase tracking-wide">Total semua tagihan</span><strong class="text-base font-black tabular-nums">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong></div>
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full table-fixed text-left text-sm">
                    <colgroup><col class="w-14"><col><col class="w-40"><col class="w-40"><col class="w-32"></colgroup>
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3 text-center">No</th><th class="px-4 py-3">Jenis tagihan</th><th class="px-4 py-3 text-right">Jumlah</th><th class="px-4 py-3 text-center">Jatuh tempo</th><th class="px-4 py-3 text-center">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tagihan as $item)
                            @php
                                $statusClasses = match ($item->status) { 'sudah_bayar' => 'bg-emerald-50 text-emerald-700', 'terlambat' => 'bg-red-50 text-red-700', default => 'bg-amber-50 text-amber-700' };
                                $statusLabel = match ($item->status) { 'sudah_bayar' => 'Lunas', 'terlambat' => 'Terlambat', default => 'Belum bayar' };
                            @endphp
                            <tr class="hover:bg-slate-50"><td class="px-4 py-4 text-center text-xs tabular-nums text-slate-400">{{ $loop->iteration }}</td><td class="px-4 py-4"><strong class="text-slate-950">{{ $jenisTagihan[$item->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</strong>@if($item->tagihan_asal_id && $item->tagihanAsal)<span class="ml-2 inline-flex rounded-lg bg-orange-50 px-2 py-1 text-[10px] font-bold text-orange-700">Carryover · {{ $item->tagihanAsal->tahunAjaran->nama_tahun_ajaran ?? 'TA lama' }}</span>@endif @if($item->dialihkan_ke_id && $item->tagihanAlihan)<span class="ml-2 inline-flex rounded-lg bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700">Dialihkan · {{ $item->tagihanAlihan->tahunAjaran->nama_tahun_ajaran ?? 'TA aktif' }}</span>@endif @if($item->keterangan)<p class="mt-1 truncate text-xs text-slate-500" title="{{ $item->keterangan }}">{{ $item->keterangan }}</p>@endif</td><td class="whitespace-nowrap px-4 py-4 text-right font-extrabold tabular-nums text-slate-950">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td><td class="whitespace-nowrap px-4 py-4 text-center text-slate-600">{{ $item->tanggal_jatuh_tempo ? $item->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</td><td class="px-4 py-4 text-center"><span class="inline-flex whitespace-nowrap rounded-full px-2 py-1 text-[10px] font-extrabold {{ $statusClasses }}">{{ $statusLabel }}</span></td></tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-900 text-white"><tr><th colspan="2" class="px-4 py-4 text-right text-xs font-extrabold uppercase tracking-wide">Total semua tagihan</th><td class="whitespace-nowrap px-4 py-4 text-right text-base font-black tabular-nums">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td><td colspan="2"></td></tr></tfoot>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
