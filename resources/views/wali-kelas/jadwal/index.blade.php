@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')
@section('page-subtitle', $kelas ? 'Jadwal kelas '.$kelas->nama_kelas : 'Jadwal kelas yang Anda walikan')

@section('content')
<div class="min-w-0 space-y-5">
    @if($error ?? false)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800" role="alert"><i class="fas fa-triangle-exclamation mr-2" aria-hidden="true"></i>{{ $error }}</div>
    @endif

    @if($kelas)
        <header class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-700">Akademik · {{ strtoupper($kelas->jenjang) }}</p>
                <h1 class="mt-1 text-xl font-extrabold text-slate-900 sm:text-2xl">Jadwal Kelas {{ $kelas->nama_kelas }}</h1>
                <p class="mt-1 text-sm leading-6 text-slate-500">Jadwal dikelola oleh Admin. Wali kelas dapat melihat dan mencetaknya.</p>
            </div>
            <a href="{{ route('wali.jadwal.print') }}" target="_blank" rel="noopener" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-800"><i class="fas fa-print" aria-hidden="true"></i>Cetak jadwal</a>
        </header>

        <section class="grid min-w-0 gap-4 lg:grid-cols-2" aria-label="Jadwal per hari">
            @foreach($hariList as $hari)
                <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                        <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-clock text-sky-700" aria-hidden="true"></i>{{ $hari }}</h2>
                        <span class="shrink-0 rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-800">{{ $jadwalPerHari[$hari]->count() }} pelajaran</span>
                    </div>
                    @if($jadwalPerHari[$hari]->isNotEmpty())
                        <ul class="divide-y divide-slate-100">
                            @foreach($jadwalPerHari[$hari] as $jadwal)
                                <li class="grid min-w-0 gap-2 px-5 py-4 sm:grid-cols-[110px_minmax(0,1fr)] sm:gap-4">
                                    <span class="inline-flex h-fit w-fit rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold tabular-nums text-slate-700">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                    <div class="min-w-0"><p class="font-bold text-slate-900">{{ $jadwal->mataPelajaran->nama_mapel ?? 'Mata pelajaran belum tersedia' }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $jadwal->mataPelajaran->kode_mapel ?? '-' }} · {{ $jadwal->guru->nama_lengkap ?? 'Pengajar belum ditentukan' }}</p></div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="px-5 py-10 text-center text-sm text-slate-500"><i class="fas fa-calendar-xmark mb-3 block text-2xl text-slate-300" aria-hidden="true"></i>Belum ada jadwal hari ini.</div>
                    @endif
                </article>
            @endforeach
        </section>
    @endif
</div>
@endsection
