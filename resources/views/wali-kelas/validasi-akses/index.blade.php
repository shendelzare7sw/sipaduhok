@extends('layouts.app')

@section('title', 'Status Akses Siswa')
@section('page-title', 'Status Akses')
@section('page-subtitle', isset($kelas) && $kelas ? 'Akses ujian dan rapor kelas '.$kelas->nama_kelas : 'Status akses siswa')

@section('content')
<div class="min-w-0 w-full space-y-4">
    @if($error ?? false)
        <p class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">{{ $error }}</p>
    @else
        <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><h1 class="text-lg font-extrabold text-slate-900">Status akses siswa</h1><p class="mt-1 text-xs text-slate-500">Pemantauan read-only untuk kelas {{ $kelas->nama_kelas }}.</p></header>
        <section class="grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-4" aria-label="Ringkasan akses">
            @foreach(['Ujian terbuka' => $stats['ujianValid'] ?? 0, 'Rapor terbuka' => $stats['raporValid'] ?? 0, 'Ujian menunggu' => $stats['ujianPending'] ?? 0, 'Rapor menunggu' => $stats['raporPending'] ?? 0] as $label => $value)
                <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">{{ $label }}</p><p class="mt-1 text-xl font-extrabold text-slate-900">{{ $value }}</p><p class="text-[11px] text-slate-500">dari {{ $siswaList->count() }} siswa</p></div>
            @endforeach
        </section>
        <form action="{{ route('wali.validasi-akses.index') }}" method="GET" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[minmax(160px,1fr)_minmax(160px,220px)_auto] sm:items-end">
            <label class="text-xs font-bold text-slate-700">Cari siswa<input type="search" name="search" value="{{ request('search') }}" placeholder="Nama atau NIS" class="mt-1 h-10 w-full rounded-lg border border-slate-300 px-3 text-sm font-normal text-slate-800"></label>
            <label class="text-xs font-bold text-slate-700">Status<select name="filter" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal text-slate-800"><option value="">Semua siswa</option><option value="ujian_pending" @selected($filterStatus === 'ujian_pending')>Ujian: belum akses</option><option value="ujian_selesai" @selected($filterStatus === 'ujian_selesai')>Ujian: terbuka</option><option value="rapor_pending" @selected($filterStatus === 'rapor_pending')>Rapor: belum akses</option><option value="rapor_selesai" @selected($filterStatus === 'rapor_selesai')>Rapor: terbuka</option></select></label>
            <div class="flex gap-2"><button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-lg bg-sky-700 px-4 text-xs font-bold text-white">Filter</button><a href="{{ route('wali.validasi-akses.index') }}" aria-label="Reset filter" class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-rotate-left" aria-hidden="true"></i></a></div>
        </form>
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="akses-heading">
            <div class="border-b border-slate-100 px-4 py-3"><h2 id="akses-heading" class="text-sm font-extrabold text-slate-900">Akses per siswa</h2><p class="text-xs text-slate-500">{{ $siswaList->count() }} siswa cocok.</p></div>
            <div class="divide-y divide-slate-100">
                @forelse($siswaList as $siswa)
                    @php
                        $examOpen = $aksesUjian[$siswa->id] ?? false;
                        $reportLabel = $siswa->validasi_rapor_bendahara ? 'Terbuka' : ($siswa->validasi_rapor_ketua ? 'Tunggu Bendahara' : ($siswa->validasi_rapor_wali ? 'Tunggu Ketua' : 'Belum akses'));
                    @endphp
                    <article class="grid gap-2 px-4 py-3 text-xs sm:grid-cols-[minmax(160px,1.4fr)_minmax(130px,1fr)_minmax(130px,1fr)] sm:items-center"><div><h3 class="text-sm font-bold text-slate-900">{{ $siswa->nama_lengkap }}</h3><p class="text-[11px] text-slate-500">NIS {{ $siswa->nis ?: '—' }}</p></div><div><span class="mr-2 text-[11px] text-slate-500 sm:hidden">Ujian</span><span class="inline-flex rounded-full px-2 py-1 font-bold {{ $examOpen ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ $examOpen ? 'Akses terbuka' : 'Belum akses' }}</span></div><div><span class="mr-2 text-[11px] text-slate-500 sm:hidden">Rapor</span><span class="inline-flex rounded-full px-2 py-1 font-bold {{ $siswa->validasi_rapor_bendahara ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-900' }}">{{ $reportLabel }}</span></div></article>
                @empty
                    <p class="px-4 py-9 text-center text-sm text-slate-500">Tidak ada siswa yang cocok.</p>
                @endforelse
            </div>
        </section>
        <p class="rounded-xl border border-sky-200 bg-sky-50 p-4 text-xs leading-5 text-sky-900">Akses ujian mengikuti pelunasan atau dispensasi Bendahara. Akses rapor mengikuti alur Wali Kirim → Ketua Setujui → Keuangan Periksa → Terbuka.</p>
    @endif
</div>
@endsection
