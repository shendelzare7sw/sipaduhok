@extends('layouts.lms')

@section('title', 'Beranda LMS')
@section('page-title', 'Beranda')
@section('page-subtitle', 'Selamat datang di HOK Learning Management System')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $stats = [
        ['label' => 'Kehadiran', 'value' => $persenKehadiran . '%', 'icon' => 'fa-user-check', 'tone' => 'bg-indigo-50 text-indigo-600'],
        ['label' => 'Tugas pending', 'value' => $tugasPending, 'icon' => 'fa-clipboard-list', 'tone' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Agenda bulan ini', 'value' => $agendaBulanIni, 'icon' => 'fa-calendar-day', 'tone' => 'bg-sky-50 text-sky-600'],
        ['label' => 'Ujian mendatang', 'value' => $ujianMendatang->count(), 'icon' => 'fa-file-lines', 'tone' => 'bg-violet-50 text-violet-600'],
    ];
    $card = 'min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm';
    $cardHead = 'flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5';
    $cardTitle = 'flex min-w-0 items-center gap-2 text-sm font-extrabold text-slate-900 sm:text-base';
    $link = 'shrink-0 text-xs font-bold text-indigo-600 no-underline hover:text-indigo-800';
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 p-5 text-white shadow-lg shadow-indigo-900/15 sm:p-6">
        <span class="pointer-events-none absolute -right-10 -top-12 h-44 w-44 rounded-full bg-white/10" aria-hidden="true"></span>
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-bold text-indigo-100">HOK Learning · {{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <h2 class="mt-1 text-xl font-extrabold text-white sm:text-2xl">Halo, {{ $siswa->nama_lengkap }}!</h2>
                <p class="mt-2 text-xs leading-5 text-indigo-50/90 sm:text-sm">Siap untuk belajar hari ini? Cek jadwal dan tugas terbarumu.</p>
            </div>
            <a href="{{ route('siswa.sia.dashboard') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 self-start rounded-xl bg-white/15 px-4 text-xs font-extrabold text-white no-underline ring-1 ring-inset ring-white/30 transition hover:bg-white/25 sm:self-auto">
                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>Akses SIA
            </a>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4" aria-label="Ringkasan belajar">
        @foreach($stats as $stat)
            <article class="{{ $card }} p-3 sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                </div>
            </article>
        @endforeach
    </section>

    <div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="min-w-0 space-y-5">
            {{-- Jadwal hari ini --}}
            <section class="{{ $card }}">
                <div class="{{ $cardHead }}">
                    <h2 class="{{ $cardTitle }}">
                        <i class="fa-solid fa-clock text-indigo-500" aria-hidden="true"></i>Jadwal hari ini
                        <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-700">{{ now()->locale('id')->dayName }}</span>
                    </h2>
                    <a href="{{ route('siswa.lms.jadwal') }}" class="{{ $link }}">Lihat semua</a>
                </div>
                <div class="p-3 sm:p-4">
                    @forelse($jadwalHariIni as $jadwal)
                        <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="group relative flex items-center gap-3 rounded-xl p-2 no-underline transition hover:bg-indigo-50/60 sm:gap-4 sm:p-3">
                            <span class="w-14 shrink-0 text-right">
                                <span class="block text-sm font-extrabold text-slate-900">{{ date('H:i', strtotime($jadwal->jam_mulai)) }}</span>
                                <span class="block text-[11px] text-slate-400">{{ date('H:i', strtotime($jadwal->jam_selesai)) }}</span>
                            </span>
                            <span class="h-10 w-1 shrink-0 rounded-full bg-gradient-to-b from-indigo-500 to-violet-500" aria-hidden="true"></span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold text-slate-900 group-hover:text-indigo-700">{{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</span>
                                <span class="block truncate text-xs text-slate-500"><i class="fa-solid fa-chalkboard-user mr-1" aria-hidden="true"></i>{{ $jadwal->guru->nama_lengkap ?? 'N/A' }}</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-500" aria-hidden="true"></i>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-500">
                            <i class="fa-solid fa-mug-hot mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>
                            Tidak ada jadwal pelajaran hari ini. Selamat beristirahat!
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Tugas tenggat terdekat --}}
            <section class="{{ $card }}">
                <div class="{{ $cardHead }}">
                    <h2 class="{{ $cardTitle }}"><i class="fa-solid fa-list-check text-amber-500" aria-hidden="true"></i>Tugas dengan tenggat terdekat</h2>
                    <a href="{{ route('siswa.lms.tugas.index') }}" class="{{ $link }}">Lihat semua</a>
                </div>
                <div class="grid gap-3 p-3 sm:grid-cols-2 sm:p-4">
                    @forelse($tugasDeadline as $tugas)
                        @php $mendesak = $tugas->tanggal_deadline->diffInDays(now()) <= 1; @endphp
                        <article class="flex min-w-0 flex-col rounded-xl border border-slate-200 border-l-4 bg-white p-4 {{ $mendesak ? 'border-l-rose-500' : 'border-l-amber-400' }}">
                            <span class="self-start rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $tugas->jenis_tugas === 'latihan' ? 'bg-sky-50 text-sky-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($tugas->jenis_tugas) }}</span>
                            <h3 class="mt-2 line-clamp-2 text-sm font-extrabold text-slate-900">{{ $tugas->judul_tugas }}</h3>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $tugas->mataPelajaran->nama_mapel ?? '-' }}</p>
                            <div class="mt-auto flex items-center justify-between gap-2 border-t border-slate-100 pt-3">
                                <span class="min-w-0 truncate text-xs font-semibold {{ $mendesak ? 'text-rose-600' : 'text-slate-500' }}"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $tugas->tanggal_deadline->copy()->locale('id')->diffForHumans() }}</span>
                                <a href="{{ route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]) }}" class="inline-flex min-h-9 shrink-0 items-center rounded-lg bg-indigo-600 px-3 text-xs font-bold text-white no-underline transition hover:bg-indigo-700">Kerjakan</a>
                            </div>
                        </article>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-500 sm:col-span-2">
                            <i class="fa-solid fa-circle-check mb-2 block text-2xl text-emerald-500" aria-hidden="true"></i>
                            Semua tugas aman! Tidak ada deadline mendesak.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Mata pelajaran --}}
            <section class="{{ $card }}">
                <div class="{{ $cardHead }}">
                    <h2 class="{{ $cardTitle }}"><i class="fa-solid fa-book text-indigo-500" aria-hidden="true"></i>Mata pelajaran</h2>
                </div>
                <div class="grid gap-3 p-3 sm:grid-cols-2 sm:p-4 2xl:grid-cols-3">
                    @forelse($mataPelajaranList as $jadwal)
                        <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="group flex min-w-0 items-center gap-3 rounded-xl border border-slate-200 p-3 no-underline transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-base font-extrabold text-white">{{ substr($jadwal->mataPelajaran->nama_mapel ?? '?', 0, 1) }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-extrabold text-slate-900 group-hover:text-indigo-700">{{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</span>
                                <span class="block truncate text-xs text-slate-500">{{ $jadwal->guru->nama_lengkap ?? 'N/A' }}</span>
                            </span>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-slate-500 sm:col-span-2 2xl:col-span-3">
                            <i class="fa-solid fa-book-open mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>
                            Belum ada mata pelajaran.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="min-w-0 space-y-5">
            {{-- Pengumuman --}}
            <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 text-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-white/15 px-4 py-3 sm:px-5">
                    <h2 class="flex items-center gap-2 text-sm font-extrabold text-white sm:text-base"><i class="fa-solid fa-bullhorn" aria-hidden="true"></i>Pengumuman</h2>
                    <a href="{{ route('siswa.lms.pengumuman.index') }}" class="shrink-0 text-xs font-bold text-indigo-100 no-underline hover:text-white">Lihat semua <i class="fa-solid fa-chevron-right text-[10px]" aria-hidden="true"></i></a>
                </div>
                <div class="divide-y divide-white/10">
                    @forelse($pengumumanList as $ann)
                        <article class="px-4 py-3 sm:px-5">
                            @if(in_array($ann->prioritas, ['penting', 'mendesak'], true))
                                <span class="mb-1.5 inline-block rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $ann->prioritas === 'mendesak' ? 'bg-rose-500 text-white' : 'bg-amber-300 text-amber-950' }}">{{ ucfirst($ann->prioritas) }}</span>
                            @endif
                            <h3 class="line-clamp-2 text-sm font-bold text-white">{{ $ann->judul }}</h3>
                            <div class="mt-2 flex items-center justify-between gap-2">
                                <span class="text-[11px] text-indigo-100">{{ $ann->created_at->copy()->locale('id')->diffForHumans() }}</span>
                                <a href="{{ route('siswa.lms.pengumuman.show', $ann->id) }}" class="inline-flex min-h-8 items-center rounded-lg bg-white px-3 text-xs font-bold text-indigo-700 no-underline transition hover:bg-indigo-50">Lihat</a>
                            </div>
                        </article>
                    @empty
                        <p class="px-4 py-6 text-center text-sm text-indigo-100"><i class="fa-solid fa-inbox mb-2 block text-xl" aria-hidden="true"></i>Tidak ada pengumuman baru</p>
                    @endforelse
                </div>
            </section>

            {{-- Minggu ini --}}
            <section class="{{ $card }}">
                <div class="{{ $cardHead }}">
                    <h2 class="{{ $cardTitle }}"><i class="fa-solid fa-calendar-week text-sky-500" aria-hidden="true"></i>Minggu ini</h2>
                    <a href="{{ route('siswa.lms.kalender') }}" class="{{ $link }}">Kalender</a>
                </div>
                <div class="space-y-2 p-3 sm:p-4">
                    @forelse($kalenderMingguIni as $event)
                        @php $tgl = \Carbon\Carbon::parse($event->tanggal_mulai); @endphp
                        <div class="flex items-center gap-3 rounded-xl p-2">
                            <span class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                                <span class="text-sm font-extrabold leading-none">{{ $tgl->format('d') }}</span>
                                <span class="mt-0.5 text-[9px] font-bold uppercase">{{ $tgl->locale('id')->translatedFormat('M') }}</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold text-slate-900">{{ $event->nama_kegiatan }}</span>
                                <span class="mt-0.5 inline-block rounded-full px-2 py-0.5 text-[10px] font-bold {{ $event->jenis_kegiatan === 'libur' ? 'bg-rose-50 text-rose-700' : 'bg-sky-50 text-sky-700' }}">{{ format_jenis_kegiatan($event->jenis_kegiatan) }}</span>
                            </span>
                        </div>
                    @empty
                        <p class="px-2 py-6 text-center text-sm text-slate-500">Tidak ada agenda minggu ini</p>
                    @endforelse
                </div>
            </section>

            {{-- Pengajar --}}
            <section class="{{ $card }}">
                <div class="{{ $cardHead }}">
                    <h2 class="{{ $cardTitle }}"><i class="fa-solid fa-users text-emerald-500" aria-hidden="true"></i>Pengajar</h2>
                    <a href="{{ route('siswa.lms.guru') }}" class="{{ $link }}">Semua</a>
                </div>
                <div class="space-y-1 p-3 sm:p-4">
                    @forelse($guruPengajar as $guru)
                        <div class="flex items-center gap-3 rounded-xl p-2">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-indigo-600 text-sm font-bold text-white">
                                @if($guru->foto)
                                    <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->nama_lengkap }}" class="h-full w-full object-cover">
                                @else
                                    {{ substr($guru->nama_lengkap, 0, 1) }}
                                @endif
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold text-slate-900">{{ $guru->nama_lengkap }}</span>
                                <span class="block truncate text-xs text-slate-500">{{ $guru->guruKelas->first()->mataPelajaran->nama_mapel ?? 'Pengajar' }}</span>
                            </span>
                        </div>
                    @empty
                        <p class="px-2 py-6 text-center text-sm text-slate-500">Data guru belum tersedia</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
