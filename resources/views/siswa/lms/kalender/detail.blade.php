@extends('layouts.lms')

@section('title', 'Detail Kalender')
@section('page-title', 'Detail Kegiatan')
@section('page-subtitle', $tanggal->translatedFormat('d F Y'))

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $navBtn = 'inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline transition hover:border-indigo-200 hover:text-indigo-700';
@endphp

<div class="min-w-0 w-full space-y-4">
    <a href="{{ route('siswa.lms.kalender') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-1 text-sm font-bold text-slate-600 no-underline transition hover:text-indigo-700">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke kalender
    </a>

    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-5 text-white shadow-lg shadow-indigo-900/15 sm:p-6">
        <span class="pointer-events-none absolute -right-8 -top-10 h-36 w-36 rounded-full bg-white/10" aria-hidden="true"></span>
        <div class="relative flex items-center gap-4">
            <span class="text-5xl font-extrabold leading-none sm:text-6xl">{{ $tanggal->format('d') }}</span>
            <div class="min-w-0">
                <p class="text-lg font-extrabold sm:text-xl">{{ $tanggal->locale('id')->isoFormat('MMMM YYYY') }}</p>
                <p class="text-sm text-indigo-100">{{ $tanggal->locale('id')->isoFormat('dddd') }}</p>
            </div>
        </div>
    </section>

    <section class="space-y-3">
        <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-calendar-check text-indigo-500" aria-hidden="true"></i>Kegiatan hari ini <span class="text-sm font-bold text-slate-400">({{ $events->count() }})</span></h2>

        @forelse($events as $event)
            @php $tone = jenis_kegiatan_tone($event->jenis_kegiatan); @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 {{ in_array($event->jenis_kegiatan, ['pts', 'pas']) ? 'border-l-4 border-l-rose-500' : '' }}">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <h3 class="text-base font-extrabold text-slate-900">{{ $event->nama_kegiatan }}</h3>
                        <span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $tone['bg'] }}">{{ $event->jenis_label }}</span>
                    </div>
                    @if($event->waktu_mulai)
                        <span class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }}@if($event->waktu_selesai) – {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('H:i') }}@endif
                        </span>
                    @endif
                </div>

                @if($event->keterangan)
                    <div class="mt-3 rounded-xl bg-slate-50 p-3">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Keterangan</p>
                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $event->keterangan }}</p>
                    </div>
                @endif

                @if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai)
                    <p class="mt-3 text-xs text-slate-500"><i class="fa-regular fa-calendar mr-1" aria-hidden="true"></i>Berlangsung {{ $event->tanggal_mulai->translatedFormat('d M') }} – {{ $event->tanggal_selesai->translatedFormat('d M Y') }} ({{ $event->durasi }} hari)</p>
                @endif

                @if($event->lampiran_surat)
                    @php $isLink = filter_var($event->lampiran_surat, FILTER_VALIDATE_URL); @endphp
                    <a href="{{ $isLink ? $event->lampiran_surat : asset('storage/' . $event->lampiran_surat) }}" target="_blank" rel="noopener"
                       class="mt-3 inline-flex min-h-9 items-center gap-2 rounded-lg bg-sky-50 px-3 text-xs font-bold text-sky-700 no-underline ring-1 ring-inset ring-sky-200 transition hover:bg-sky-100">
                        <i class="fa-solid {{ $isLink ? 'fa-up-right-from-square' : 'fa-file-pdf' }}" aria-hidden="true"></i>{{ $isLink ? 'Lihat surat/tautan' : 'Unduh surat' }}
                    </a>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-10 text-center">
                <i class="fa-regular fa-calendar-xmark mb-2 block text-3xl text-slate-300" aria-hidden="true"></i>
                <p class="text-sm font-bold text-slate-800">Tidak ada kegiatan</p>
                <p class="mt-1 text-xs text-slate-500">Tidak ada kegiatan terjadwal pada tanggal ini.</p>
            </div>
        @endforelse
    </section>

    @if($jadwalPelajaran->count() > 0)
        <section class="space-y-3">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-book text-indigo-500" aria-hidden="true"></i>Jadwal pelajaran</h2>
            <div class="divide-y divide-slate-100 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @foreach($jadwalPelajaran as $jadwal)
                    <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="group flex items-center gap-3 px-4 py-3 no-underline transition hover:bg-indigo-50/60">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-extrabold text-slate-900 group-hover:text-indigo-700">{{ $jadwal->mataPelajaran->nama_mapel }}</span>
                            <span class="block truncate text-xs text-slate-500"><i class="fa-solid fa-user-tie mr-1" aria-hidden="true"></i>{{ $jadwal->guru->nama_lengkap }}</span>
                        </span>
                        <span class="shrink-0 rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $jadwal->jam_mulai->format('H:i') }} – {{ $jadwal->jam_selesai->format('H:i') }}</span>
                    </a>
                @endforeach
            </div>
            <p class="flex items-start gap-2 rounded-xl bg-sky-50 px-3 py-2.5 text-xs leading-5 text-sky-800"><i class="fa-solid fa-lightbulb mt-0.5" aria-hidden="true"></i><span><strong>Tips:</strong> klik mata pelajaran untuk melihat materi, tugas, dan ujian.</span></p>
        </section>
    @elseif($isWeekday)
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-8 text-center">
            <i class="fa-solid fa-chalkboard mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>
            <p class="text-sm font-bold text-slate-800">Tidak ada jadwal pelajaran</p>
            <p class="mt-1 text-xs text-slate-500">Tidak ada mata pelajaran terjadwal pada hari ini.</p>
        </div>
    @endif

    <nav class="flex items-center justify-between gap-2 pt-2" aria-label="Tanggal lain">
        @if($prevDate)
            <a href="{{ route('siswa.lms.kalender.detail', ['tanggal' => $prevDate->format('Y-m-d')]) }}" class="{{ $navBtn }}"><i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i>{{ $prevDate->translatedFormat('d M') }}</a>
        @else
            <span></span>
        @endif
        @if($nextDate)
            <a href="{{ route('siswa.lms.kalender.detail', ['tanggal' => $nextDate->format('Y-m-d')]) }}" class="{{ $navBtn }}">{{ $nextDate->translatedFormat('d M') }}<i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i></a>
        @endif
    </nav>
</div>
@endsection
