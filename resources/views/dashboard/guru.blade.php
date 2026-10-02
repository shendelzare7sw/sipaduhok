@extends('layouts.app')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Guru')
@section('page-subtitle', 'Ringkasan aktivitas mengajar hari ini')

@section('content')
@php
    $kelasYangDiajar = $kelasYangDiajar ?? collect();
    $jadwalHariIni = $jadwalHariIni ?? collect();

    $statCards = [
        ['label' => 'Kelas diampu', 'value' => $kelasYangDiajar->count(), 'note' => 'Kelas aktif', 'icon' => 'fa-school', 'tone' => 'bg-blue-50 text-blue-600'],
        ['label' => 'Total siswa', 'value' => $totalSiswa ?? 0, 'note' => 'Siswa yang diajar', 'icon' => 'fa-graduation-cap', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Jadwal hari ini', 'value' => $jadwalHariIni->count(), 'note' => now()->locale('id')->translatedFormat('l, d M Y'), 'icon' => 'fa-calendar-day', 'tone' => 'bg-amber-50 text-amber-600'],
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-brand-700 via-blue-600 to-cyan-500 text-white shadow-lg shadow-brand-900/15">
        <div class="flex flex-col gap-4 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-bold text-blue-100">Selamat datang, {{ auth()->user()->name }}</p>
                <h2 class="mt-1 text-xl font-extrabold !text-white sm:text-2xl">
                    @if($jadwalHariIni->count())
                        Anda mengajar {{ $jadwalHariIni->count() }} sesi hari ini
                    @else
                        Tidak ada sesi mengajar hari ini
                    @endif
                </h2>
                <p class="mt-2 text-xs leading-5 text-blue-50/90 sm:text-sm">Masuk ke LMS dari jadwal, atau pilih kelas untuk mengelola materi, tugas, dan ujian.</p>
            </div>
            <a href="{{ route('guru.jadwal.index') }}" class="inline-flex h-11 shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-5 text-xs font-extrabold text-brand-700 no-underline shadow-sm transition hover:bg-blue-50">
                <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>Jadwal lengkap
            </a>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-3" aria-label="Ringkasan mengajar">
        @foreach($statCards as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4 {{ $loop->last ? 'col-span-2 xl:col-span-1' : '' }}">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ number_format($stat['value']) }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                </div>
                <p class="mt-3 truncate border-t border-slate-100 pt-3 text-[11px] font-semibold text-slate-500">{{ $stat['note'] }}</p>
            </article>
        @endforeach
    </section>

    <div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,2fr)_minmax(19rem,1fr)]">
        <div class="min-w-0 space-y-5">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5">
                    <div class="min-w-0">
                        <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-calendar-day text-amber-500" aria-hidden="true"></i>Jadwal mengajar</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <a href="{{ route('guru.jadwal.index') }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold text-brand-700 no-underline hover:text-brand-800">Jadwal lengkap<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </header>

                @forelse($jadwalHariIni as $jadwal)
                    <article class="flex min-w-0 flex-col gap-3 border-b border-slate-100 p-4 last:border-b-0 hover:bg-slate-50 sm:flex-row sm:items-center sm:gap-4">
                        <span class="shrink-0 whitespace-nowrap text-sm font-extrabold tabular-nums text-brand-700 sm:w-28">{{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}</span>
                        <span class="hidden h-10 w-1 shrink-0 rounded-full bg-brand-200 sm:block" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $jadwal->mataPelajaran->nama_mapel }}">{{ $jadwal->mataPelajaran->nama_mapel }}</h3>
                            <p class="mt-0.5 truncate text-xs text-slate-500">Kelas {{ $jadwal->kelas->nama_kelas }}</p>
                        </div>
                        <a href="{{ route('guru.lms.dashboard', [$jadwal->kelas->id, $jadwal->link_mapel_id]) }}" class="inline-flex min-h-9 shrink-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-brand-50 px-3 text-xs font-bold text-brand-700 no-underline ring-1 ring-inset ring-brand-100 hover:bg-brand-100"><i class="fa-solid fa-door-open" aria-hidden="true"></i>Masuk LMS</a>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-mug-hot" aria-hidden="true"></i></span>
                        <h3 class="mt-4 font-extrabold text-slate-900">Tidak ada jadwal</h3>
                        <p class="mt-1 text-sm text-slate-500">Tidak ada jadwal mengajar hari ini. Istirahat sejenak!</p>
                    </div>
                @endforelse
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5">
                    <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-chalkboard-user text-cyan-600" aria-hidden="true"></i>Kelas yang diampu</h2>
                    <a href="{{ route('guru.kelas.index') }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold text-brand-700 no-underline hover:text-brand-800">Lihat semua<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </header>

                @forelse($kelasYangDiajar as $item)
                    <article class="flex min-w-0 items-center gap-3 border-b border-slate-100 p-4 last:border-b-0 hover:bg-slate-50 sm:gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 px-1 text-center text-xs font-extrabold text-brand-700">{{ $item['kelas']->nama_kelas }}</span>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-sm font-extrabold text-slate-900">Kelas {{ $item['kelas']->nama_kelas }}</h3>
                            <p class="mt-0.5 flex flex-wrap items-center gap-x-2 text-[11px] text-slate-500">
                                <span class="whitespace-nowrap"><i class="fa-solid fa-users mr-1" aria-hidden="true"></i>{{ $item['jumlah_siswa'] }} siswa</span>
                                <span aria-hidden="true">·</span>
                                <span class="whitespace-nowrap"><i class="fa-solid fa-book mr-1" aria-hidden="true"></i>{{ $item['jumlah_mapel'] }} mapel</span>
                            </p>
                        </div>
                        <a href="{{ route('guru.kelas.mapel', $item['kelas']->id) }}" class="inline-flex min-h-9 shrink-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-cyan-50 px-3 text-xs font-bold text-cyan-800 no-underline ring-1 ring-inset ring-cyan-100 hover:bg-cyan-100"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i>Kelola</a>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
                        <h3 class="mt-4 font-extrabold text-slate-900">Belum ada kelas</h3>
                        <p class="mt-1 text-sm text-slate-500">Belum ada kelas yang diampu saat ini.</p>
                    </div>
                @endforelse
            </section>
        </div>

        <aside class="min-w-0">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h2 class="flex items-center gap-2 font-extrabold text-slate-950"><i class="fa-solid fa-bolt text-amber-500" aria-hidden="true"></i>Akses cepat</h2></header>
                <div class="grid grid-cols-2 gap-3 p-4">
                    @php $firstKelas = $kelasYangDiajar->first(); @endphp
                    @foreach([
                        ['route' => route('guru.kelas.index'), 'icon' => 'fa-list', 'label' => 'Semua kelas', 'tone' => 'text-blue-700 bg-blue-50'],
                        ['route' => route('guru.jadwal.index'), 'icon' => 'fa-calendar-days', 'label' => 'Jadwal mengajar', 'tone' => 'text-emerald-700 bg-emerald-50'],
                        ['route' => $firstKelas ? route('guru.kelas.mapel', $firstKelas['kelas']->id) : route('guru.kelas.index'), 'icon' => 'fa-chalkboard', 'label' => $firstKelas ? 'Kelas pertama' : 'Kelola kelas', 'tone' => 'text-amber-700 bg-amber-50'],
                        ['route' => route('guru.lms.arsip.index'), 'icon' => 'fa-box-archive', 'label' => 'Arsip LMS', 'tone' => 'text-violet-700 bg-violet-50'],
                    ] as $link)
                        <a href="{{ $link['route'] }}" class="group flex min-h-24 min-w-0 flex-col items-center justify-center gap-2 rounded-xl border border-slate-200 p-3 text-center no-underline transition hover:border-brand-300 hover:bg-blue-50/50">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $link['tone'] }}"><i class="fa-solid {{ $link['icon'] }}" aria-hidden="true"></i></span>
                            <span class="text-xs font-bold leading-5 text-slate-700 group-hover:text-brand-700">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
