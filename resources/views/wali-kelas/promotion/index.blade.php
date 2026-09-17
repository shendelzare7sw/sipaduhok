@extends('layouts.app')

@section('title', 'Prediksi Kenaikan Kelas')
@section('page-title', 'Prediksi Kenaikan Kelas')
@section('page-subtitle', 'Simulasi kelayakan akademik dan keuangan')

@section('content')
<div class="min-w-0 w-full space-y-4">
    <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><h1 class="text-lg font-extrabold text-slate-900">Prediksi kenaikan kelas</h1><p class="mt-1 text-xs text-slate-500">Simulasi berdasarkan ketuntasan nilai dan kondisi keuangan saat ini.</p></header>
    @isset($error)<p class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">{{ $error }}</p>@endisset
    @isset($kelas)
        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2"><h2 class="text-sm font-extrabold text-slate-900">Kelas {{ $kelas->nama_kelas }} · {{ $tahun->nama_tahun_ajaran }}</h2><span class="rounded-full bg-sky-50 px-3 py-1 text-[11px] font-bold text-sky-800">Simulasi</span></div>
            <p class="mt-3 rounded-lg border border-sky-200 bg-sky-50 p-3 text-xs leading-5 text-sky-900">Ini bukan keputusan kenaikan kelas. Status akhir ditentukan saat sistem menjalankan proses resmi.</p>
            <form method="GET" action="{{ route('wali.kenaikan-kelas.prediction') }}" class="mt-4 grid gap-3 sm:grid-cols-[minmax(160px,1fr)_minmax(150px,220px)_auto] sm:items-end">
                <label class="text-xs font-bold text-slate-700">Cari siswa<input type="search" name="search" value="{{ request('search') }}" placeholder="Nama siswa" class="mt-1 h-10 w-full rounded-lg border border-slate-300 px-3 text-sm font-normal text-slate-800"></label>
                <label class="text-xs font-bold text-slate-700">Status prediksi<select name="status_filter" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal text-slate-800"><option value="">Semua status</option><option value="aman" @selected(request('status_filter') === 'aman')>Aman / naik</option><option value="rawan" @selected(request('status_filter') === 'rawan')>Rawan / tertunda</option></select></label>
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-sky-700 px-4 text-xs font-bold text-white"><i class="fas fa-magnifying-glass" aria-hidden="true"></i>Terapkan</button>
            </form>
        </section>
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="prediksi-heading">
            <div class="border-b border-slate-100 px-4 py-3"><h2 id="prediksi-heading" class="text-sm font-extrabold text-slate-900">Daftar prediksi siswa</h2><p class="text-xs text-slate-500">{{ count($prediction) }} siswa cocok.</p></div>
            <div class="divide-y divide-slate-100">
                @forelse($prediction as $p)
                    @php
                        $financial = $p['result']['financial'];
                        $academic = $p['result']['academic'];
                        $eligible = $p['result']['eligible'];
                        $isLulus = preg_match('/(9|IX|12|XII)/', strtoupper($kelas->nama_kelas));
                    @endphp
                    <article class="grid gap-3 px-4 py-3 text-xs sm:grid-cols-[minmax(150px,1.4fr)_minmax(130px,1fr)_minmax(130px,1fr)_minmax(110px,0.8fr)] sm:items-center">
                        <h3 class="text-sm font-bold text-slate-900">{{ $p['siswa']->nama_lengkap }}</h3>
                        <div><span class="block text-[11px] font-semibold text-slate-500 sm:hidden">Keuangan</span><span class="inline-flex rounded-full px-2 py-1 font-bold {{ $financial['status'] === 'LUNAS' ? 'bg-emerald-50 text-emerald-800' : 'bg-rose-50 text-rose-800' }}">{{ $financial['status'] === 'LUNAS' ? 'Lunas' : 'Belum lunas' }}</span>@if($financial['is_dispensasi'])<span class="ml-1 inline-flex rounded-full bg-amber-50 px-2 py-1 font-bold text-amber-800">Dispensasi</span>@endif</div>
                        <div><span class="block text-[11px] font-semibold text-slate-500 sm:hidden">Akademik</span><span class="inline-flex rounded-full px-2 py-1 font-bold {{ $academic['is_tuntas'] ? 'bg-emerald-50 text-emerald-800' : 'bg-rose-50 text-rose-800' }}">{{ $academic['is_tuntas'] ? 'Aman' : 'Rawan' }} · {{ $academic['percentage'] }}%</span><span class="block text-[11px] text-slate-500">{{ $academic['tuntas_count'] }} mapel tuntas</span></div>
                        <div><span class="block text-[11px] font-semibold text-slate-500 sm:hidden">Prediksi</span><strong class="{{ $eligible ? 'text-emerald-700' : 'text-rose-700' }}">{{ $eligible ? ($isLulus ? 'Lulus' : 'Naik kelas') : 'Tertunda' }}</strong>@unless($eligible)<span class="block text-[11px] text-slate-500">{{ !$academic['is_tuntas'] ? 'Nilai kurang' : 'Tunggakan' }}</span>@endunless</div>
                    </article>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-slate-500">Tidak ada data prediksi yang cocok.</p>
                @endforelse
            </div>
        </section>
    @endisset
</div>
@endsection
