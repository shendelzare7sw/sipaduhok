@extends('layouts.app')

@section('title', 'Dashboard Sekretaris')
@section('page-title', 'Dashboard Sekretaris')
@section('page-subtitle', 'Kelola agenda dan publikasi sekolah')

@section('content')
@php
    $workflows = [
        ['route' => 'sekretaris.kalender.index', 'icon' => 'fa-calendar-days', 'title' => 'Kalender Akademik', 'description' => 'Susun agenda utama dan atur visibilitasnya.', 'tone' => 'bg-blue-50 text-blue-700'],
        ['route' => 'sekretaris.pengumuman.index', 'icon' => 'fa-bullhorn', 'title' => 'Pengumuman', 'description' => 'Sampaikan informasi penting kepada warga sekolah.', 'tone' => 'bg-cyan-50 text-cyan-700'],
        ['route' => 'sekretaris.flyer.index', 'icon' => 'fa-images', 'title' => 'Flyer / Iklan', 'description' => 'Atur materi visual beserta periode tayangnya.', 'tone' => 'bg-amber-50 text-amber-700'],
        ['route' => 'sekretaris.berita.index', 'icon' => 'fa-newspaper', 'title' => 'Berita', 'description' => 'Kelola tautan berita dan konten unggulan.', 'tone' => 'bg-emerald-50 text-emerald-700'],
    ];
    $statCards = [
        ['label' => 'Total agenda', 'value' => $stats['totalKalender'] ?? 0, 'icon' => 'fa-calendar', 'tone' => 'bg-blue-50 text-blue-600'],
        ['label' => 'Agenda aktif', 'value' => $stats['kegiatanAktif'] ?? 0, 'icon' => 'fa-circle-check', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Pengumuman aktif', 'value' => $stats['pengumumanAktif'] ?? 0, 'icon' => 'fa-bullhorn', 'tone' => 'bg-cyan-50 text-cyan-600'],
        ['label' => 'Flyer & berita', 'value' => ($stats['flyerAktif'] ?? 0) + ($stats['beritaAktif'] ?? 0), 'icon' => 'fa-photo-film', 'tone' => 'bg-violet-50 text-violet-600'],
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-brand-700 via-blue-600 to-cyan-500 text-white shadow-lg shadow-brand-900/15">
        <div class="flex flex-col gap-5 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-bold text-blue-100">Selamat datang, {{ auth()->user()->name }}</p>
                <h2 class="mt-1 text-xl font-extrabold !text-white sm:text-2xl">Apa yang perlu dipublikasikan hari ini?</h2>
                <p class="mt-2 max-w-2xl text-xs leading-5 text-blue-50/90 sm:text-sm">Mulai dari agenda sekolah, lalu teruskan informasi penting melalui pengumuman, flyer, atau berita.</p>
                <div class="mt-4 flex flex-wrap gap-2 text-[10px] font-bold">
                    <span class="rounded-full bg-white/15 px-3 py-1.5 ring-1 ring-inset ring-white/20"><i class="fas fa-graduation-cap mr-1.5" aria-hidden="true"></i>{{ $tahunAjaranAktif->nama_tahun_ajaran ?? 'Belum ada tahun ajaran aktif' }}</span>
                    <span class="rounded-full bg-white/15 px-3 py-1.5 ring-1 ring-inset ring-white/20"><i class="fas fa-calendar-day mr-1.5" aria-hidden="true"></i>{{ now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>
            <a href="{{ route('sekretaris.kalender.create') }}" class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 text-xs font-extrabold text-brand-700 no-underline shadow-sm transition hover:bg-blue-50">
                <i class="fas fa-plus" aria-hidden="true"></i>Tambah agenda
            </a>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4">
        @foreach($statCards as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ number_format($stat['value']) }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="fas {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                </div>
            </article>
        @endforeach
    </section>

    <div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_380px]">
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5">
                <div class="min-w-0">
                    <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-clock text-brand-600" aria-hidden="true"></i>Kegiatan hari ini</h2>
                    <p class="mt-1 text-xs text-slate-500">Agenda aktif yang perlu dipantau sekarang.</p>
                </div>
                <a href="{{ route('sekretaris.kalender.index') }}" class="shrink-0 text-xs font-bold text-brand-700 no-underline hover:text-brand-800">Lihat kalender</a>
            </header>

            <div class="divide-y divide-slate-100">
                @forelse($kegiatanHariIni as $kegiatan)
                    <article class="flex items-center gap-3 p-4 sm:px-5">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-brand-600"><i class="fas fa-calendar-check" aria-hidden="true"></i></span>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $kegiatan->nama_kegiatan }}">{{ $kegiatan->nama_kegiatan }}</h3>
                            <p class="mt-1 text-[11px] text-slate-500"><i class="far fa-clock mr-1" aria-hidden="true"></i>{{ $kegiatan->waktu_mulai ? IlluminateSupportStr::substr($kegiatan->waktu_mulai, 0, 5) : 'Seharian' }}</p>
                        </div>
                        <a href="{{ route('sekretaris.kalender.edit', $kegiatan->id) }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 no-underline hover:bg-amber-100" aria-label="Edit {{ $kegiatan->nama_kegiatan }}"><i class="fas fa-edit" aria-hidden="true"></i></a>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-calendar-day" aria-hidden="true"></i></span>
                        <h3 class="mt-4 text-sm font-extrabold text-slate-800">Tidak ada kegiatan hari ini</h3>
                        <p class="mt-1 text-xs text-slate-500">Anda dapat menyiapkan agenda untuk hari berikutnya.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5">
                <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-bolt text-amber-500" aria-hidden="true"></i>Pekerjaan utama</h2>
                <p class="mt-1 text-xs text-slate-500">Pilih sesuai informasi yang ingin dikelola.</p>
            </header>
            <div class="grid gap-2 p-3 sm:grid-cols-2 xl:grid-cols-1">
                @foreach($workflows as $item)
                    <a href="{{ route($item['route']) }}" class="group flex min-w-0 items-center gap-3 rounded-xl border border-slate-200 p-3 no-underline transition hover:border-brand-200 hover:bg-brand-50/40">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $item['tone'] }}"><i class="fas {{ $item['icon'] }}" aria-hidden="true"></i></span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-xs font-extrabold text-slate-900">{{ $item['title'] }}</span>
                            <span class="mt-0.5 block truncate text-[10px] text-slate-500" title="{{ $item['description'] }}">{{ $item['description'] }}</span>
                        </span>
                        <i class="fas fa-chevron-right text-[10px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500" aria-hidden="true"></i>
                    </a>
                @endforeach
            </div>
        </section>
    </div>

    <section class="flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-xs leading-5 text-blue-800">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-brand-600 shadow-sm"><i class="fas fa-circle-info" aria-hidden="true"></i></span>
        <p><strong>Alur yang disarankan:</strong> catat kegiatan pada Kalender Akademik terlebih dahulu. Jika informasi perlu menjangkau warga sekolah, lanjutkan ke Pengumuman atau materi publikasi yang sesuai.</p>
    </section>
</div>
@endsection
