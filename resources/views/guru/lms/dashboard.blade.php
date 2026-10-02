@extends('layouts.lms-guru')

@section('title', 'Beranda LMS')
@section('page-title', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)
@section('page-subtitle', 'Kelola pembelajaran untuk ' . $jumlahSiswa . ' siswa')

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php
    $args = [$kelas->id, $mapel->id];
    $stats = [
        ['label' => 'Total siswa', 'value' => $jumlahSiswa, 'icon' => 'fa-users', 'tone' => 'bg-sky-50 text-sky-600'],
        ['label' => 'Materi', 'value' => $jumlahMateri, 'icon' => 'fa-book', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Tugas', 'value' => $jumlahTugas, 'icon' => 'fa-list-check', 'tone' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Perlu koreksi', 'value' => $tugasBelumDikoreksi, 'icon' => 'fa-circle-exclamation', 'tone' => 'bg-rose-50 text-rose-600'],
    ];
    $actions = [
        ['route' => route('guru.lms.materi.index', $args), 'icon' => 'fa-book', 'title' => 'Materi', 'desc' => 'Kelola bahan ajar', 'tone' => 'bg-sky-50 text-sky-600'],
        ['route' => route('guru.lms.tugas.index', $args), 'icon' => 'fa-list-check', 'title' => 'Tugas', 'desc' => 'Buat & koreksi tugas', 'tone' => 'bg-amber-50 text-amber-600'],
        ['route' => route('guru.lms.latihan.index', $args), 'icon' => 'fa-pencil-ruler', 'title' => 'Latihan', 'desc' => 'Kelola soal latihan', 'tone' => 'bg-violet-50 text-violet-600'],
        ['route' => route('guru.lms.ujian.index', $args), 'icon' => 'fa-file-lines', 'title' => 'Ujian', 'desc' => 'Ujian & pengawasan', 'tone' => 'bg-rose-50 text-rose-600'],
        ['route' => route('guru.lms.forum.index', $args), 'icon' => 'fa-comments', 'title' => 'Forum', 'desc' => 'Ruang diskusi siswa', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['route' => route('guru.lms.meeting.index', $args), 'icon' => 'fa-video', 'title' => 'Kelas virtual', 'desc' => 'Jadwalkan meeting', 'tone' => 'bg-slate-100 text-slate-600'],
        ['route' => route('guru.lms.nilai.index', $args), 'icon' => 'fa-chart-line', 'title' => 'Nilai siswa', 'desc' => 'Rekap penilaian', 'tone' => 'bg-pink-50 text-pink-600'],
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 p-5 text-white shadow-lg shadow-indigo-900/15 sm:p-6">
        <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-bold text-indigo-100">Ruang kelas · Kelas {{ $kelas->nama_kelas }}</p>
                <h2 class="mt-1 truncate text-xl font-extrabold !text-white sm:text-2xl">{{ $mapel->nama_mapel }}</h2>
                <p class="mt-2 text-xs leading-5 text-indigo-50/90 sm:text-sm">
                    @if($tugasBelumDikoreksi > 0)
                        Ada {{ $tugasBelumDikoreksi }} pengumpulan yang menunggu koreksi Anda.
                    @else
                        Semua pengumpulan sudah dikoreksi. Lanjutkan dengan materi atau tugas baru.
                    @endif
                </p>
            </div>
            <div class="grid shrink-0 grid-cols-2 gap-2 sm:flex">
                <a href="{{ route('guru.lms.materi.create', $args) }}" class="inline-flex min-h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 text-xs font-extrabold text-indigo-700 no-underline shadow-sm transition hover:bg-indigo-50"><i class="fa-solid fa-plus" aria-hidden="true"></i>Materi</a>
                <a href="{{ route('guru.lms.tugas.create', $args) }}" class="inline-flex min-h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-white/15 px-4 text-xs font-extrabold text-white no-underline ring-1 ring-inset ring-white/30 transition hover:bg-white/25"><i class="fa-solid fa-plus" aria-hidden="true"></i>Tugas</a>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4" aria-label="Ringkasan kelas">
        @foreach($stats as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ number_format($stat['value']) }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                </div>
            </article>
        @endforeach
    </section>

    <section aria-labelledby="aksi-cepat">
        <h2 id="aksi-cepat" class="mb-3 flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-bolt text-amber-500" aria-hidden="true"></i>Aksi cepat</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($actions as $action)
                <a href="{{ $action['route'] }}" class="group flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 no-underline shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $action['tone'] }}"><i class="fa-solid {{ $action['icon'] }}" aria-hidden="true"></i></span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-extrabold text-slate-900 group-hover:text-indigo-700">{{ $action['title'] }}</span>
                        <span class="block truncate text-xs text-slate-500">{{ $action['desc'] }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-xs text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-500" aria-hidden="true"></i>
                </a>
            @endforeach
        </div>
    </section>

    <div class="grid min-w-0 gap-5 xl:grid-cols-2">
        <section class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-book text-sky-600" aria-hidden="true"></i>Materi terbaru</h2>
                <a href="{{ route('guru.lms.materi.index', $args) }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold text-indigo-700 no-underline hover:text-indigo-800">Semua<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </header>
            @forelse($materiTerbaru as $materi)
                <article class="flex min-w-0 items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-b-0 hover:bg-slate-50 sm:px-5">
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $materi->judul_materi }}">{{ $materi->judul_materi }}</h3>
                        <p class="mt-0.5 text-xs text-slate-500"><i class="fa-solid fa-calendar mr-1" aria-hidden="true"></i>{{ $materi->tanggal_upload->format('d M Y') }}</p>
                    </div>
                    @if($materi->tipe_file)
                        <span class="shrink-0 rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-extrabold uppercase text-sky-700">{{ $materi->tipe_file }}</span>
                    @endif
                </article>
            @empty
                <div class="flex flex-1 flex-col items-center justify-center px-5 py-12 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
                    <p class="mt-3 text-sm font-bold text-slate-700">Belum ada materi</p>
                    <a href="{{ route('guru.lms.materi.create', $args) }}" class="mt-3 inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-indigo-50 px-3 text-xs font-bold text-indigo-700 no-underline hover:bg-indigo-100"><i class="fa-solid fa-plus" aria-hidden="true"></i>Unggah materi pertama</a>
                </div>
            @endforelse
        </section>

        <section class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-list-check text-amber-500" aria-hidden="true"></i>Tugas terbaru</h2>
                <a href="{{ route('guru.lms.tugas.index', $args) }}" class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-xs font-bold text-indigo-700 no-underline hover:text-indigo-800">Semua<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </header>
            @forelse($tugasTerbaru as $tugas)
                <article class="flex min-w-0 items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-b-0 hover:bg-slate-50 sm:px-5">
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $tugas->judul_tugas }}">{{ $tugas->judul_tugas }}</h3>
                        <p class="mt-0.5 text-xs text-slate-500"><i class="fa-solid fa-clock mr-1" aria-hidden="true"></i>Deadline {{ $tugas->tanggal_deadline->format('d M Y') }}</p>
                    </div>
                    @if($tugas->isOverdue())
                        <span class="shrink-0 rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-extrabold text-rose-700">Lewat</span>
                    @else
                        <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold text-emerald-700">Aktif</span>
                    @endif
                </article>
            @empty
                <div class="flex flex-1 flex-col items-center justify-center px-5 py-12 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
                    <p class="mt-3 text-sm font-bold text-slate-700">Belum ada tugas</p>
                    <a href="{{ route('guru.lms.tugas.create', $args) }}" class="mt-3 inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-indigo-50 px-3 text-xs font-bold text-indigo-700 no-underline hover:bg-indigo-100"><i class="fa-solid fa-plus" aria-hidden="true"></i>Buat tugas pertama</a>
                </div>
            @endforelse
        </section>
    </div>
</div>
@endsection
