@extends('layouts.lms')

@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')
@section('page-subtitle', 'Lihat jadwal kegiatan dan agenda sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $route = fn (array $params) => route('siswa.lms.kalender', $params);
    $modes = [
        'minggu' => ['Minggu', ['mode' => 'minggu', 'date' => now()->format('Y-m-d')]],
        'bulan' => ['Bulan', ['mode' => 'bulan', 'month' => $month, 'year' => $year]],
        'tahun' => ['Tahun', ['mode' => 'tahun', 'year' => $year]],
    ];
    $prevParams = match ($viewMode) {
        'minggu' => ['mode' => 'minggu', 'date' => $prevWeekDate],
        'tahun' => ['mode' => 'tahun', 'year' => $prevYear],
        default => ['mode' => 'bulan', 'month' => $prevMonth['month'], 'year' => $prevMonth['year']],
    };
    $nextParams = match ($viewMode) {
        'minggu' => ['mode' => 'minggu', 'date' => $nextWeekDate],
        'tahun' => ['mode' => 'tahun', 'year' => $nextYear],
        default => ['mode' => 'bulan', 'month' => $nextMonth['month'], 'year' => $nextMonth['year']],
    };
    $judulPeriode = match ($viewMode) {
        'minggu' => \Carbon\Carbon::parse($startOfWeek)->translatedFormat('d M') . ' – ' . \Carbon\Carbon::parse($endOfWeek)->translatedFormat('d M Y'),
        'tahun' => 'Tahun ' . $year,
        default => \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y'),
    };
    $detailUrl = fn ($tanggal) => route('siswa.lms.kalender.detail', ['tanggal' => $tanggal]);
    $chip = 'block truncate rounded-md px-1.5 py-0.5 text-[11px] font-semibold no-underline transition hover:opacity-85';
    $iconBtn = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline transition hover:border-indigo-200 hover:text-indigo-700';
@endphp

<div class="min-w-0 w-full space-y-4" x-data="{ q: '' }">
    <section class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wide text-indigo-600">Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</p>
                <h2 class="mt-0.5 text-lg font-extrabold text-slate-900">{{ $judulPeriode }}</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <nav class="grid grid-cols-3 rounded-xl bg-slate-100 p-1" aria-label="Mode tampilan">
                    @foreach($modes as $key => [$label, $params])
                        <a href="{{ $route($params) }}" @if($viewMode === $key) aria-current="page" @endif
                           class="rounded-lg px-3 py-1.5 text-center text-xs font-bold no-underline transition {{ $viewMode === $key ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">{{ $label }}</a>
                    @endforeach
                </nav>
                <div class="flex items-center gap-2">
                    <a href="{{ $route($prevParams) }}" class="{{ $iconBtn }}" aria-label="Periode sebelumnya"><i class="fa-solid fa-chevron-left text-xs" aria-hidden="true"></i></a>
                    <a href="{{ $route($nextParams) }}" class="{{ $iconBtn }}" aria-label="Periode berikutnya"><i class="fa-solid fa-chevron-right text-xs" aria-hidden="true"></i></a>
                </div>
                <label class="relative w-full sm:w-56">
                    <span class="sr-only">Cari kegiatan</span>
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400" aria-hidden="true"></i>
                    <input type="search" x-model.debounce.150ms="q" placeholder="Cari kegiatan..." class="block h-10 w-full rounded-xl border border-slate-300 bg-white pl-10 pr-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                </label>
            </div>
        </div>
    </section>

    <div class="grid min-w-0 gap-4 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @if($viewMode === 'minggu')
                {{-- Desktop: grid jam × hari --}}
                <div class="hidden overflow-x-auto lg:block">
                    <table class="w-full min-w-[760px] table-fixed border-collapse text-xs">
                        <colgroup><col class="w-16">@foreach($weekDays as $day)<col>@endforeach</colgroup>
                        <thead>
                            <tr class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white">
                                <th class="px-2 py-3 font-bold">Waktu</th>
                                @foreach($weekDays as $day)
                                    <th class="px-2 py-3 {{ $day->isToday() ? 'bg-white/15' : '' }}">
                                        <span class="block text-[11px] font-bold uppercase">{{ $day->translatedFormat('D') }}</span>
                                        <span class="block text-sm font-extrabold">{{ $day->format('d/m') }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-slate-100 bg-slate-50 px-2 py-2 text-center text-[11px] font-bold text-slate-500">Sehari</td>
                                @foreach($weekDays as $day)
                                    <td class="border border-slate-100 p-1 align-top {{ $day->isToday() ? 'bg-indigo-50/60' : '' }}">
                                        @foreach($eventsMinggu as $event)
                                            @php
                                                $start = \Carbon\Carbon::parse($event->tanggal_mulai);
                                                $end = $event->tanggal_selesai ? \Carbon\Carbon::parse($event->tanggal_selesai) : $start;
                                            @endphp
                                            @if(!$event->waktu_mulai && $start->lte($day) && $end->gte($day))
                                                <a href="{{ $detailUrl($day->format('Y-m-d')) }}" title="{{ $event->nama_kegiatan }}" data-event="{{ Str::lower($event->nama_kegiatan) }}" x-show="!q || $el.dataset.event.includes(q.toLowerCase())"
                                                   class="{{ $chip }} mb-1 {{ jenis_kegiatan_tone($event->jenis_kegiatan)['bg'] }}">{{ $event->nama_kegiatan }}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                            @for($hour = 6; $hour <= 18; $hour++)
                                <tr>
                                    <td class="border border-slate-100 px-2 py-3 text-center text-[11px] font-bold text-slate-400">{{ sprintf('%02d:00', $hour) }}</td>
                                    @foreach($weekDays as $day)
                                        <td class="h-12 border border-slate-100 p-1 align-top {{ $day->isToday() ? 'bg-indigo-50/60' : '' }}">
                                            @foreach($eventsMinggu as $event)
                                                @continue(!$event->waktu_mulai)
                                                @php $startTime = \Carbon\Carbon::parse($event->waktu_mulai); @endphp
                                                @if(\Carbon\Carbon::parse($event->tanggal_mulai)->isSameDay($day) && $startTime->hour == $hour)
                                                    <a href="{{ $detailUrl($day->format('Y-m-d')) }}" title="{{ $event->nama_kegiatan }} ({{ $startTime->format('H:i') }})" data-event="{{ Str::lower($event->nama_kegiatan) }}" x-show="!q || $el.dataset.event.includes(q.toLowerCase())"
                                                       class="{{ $chip }} mb-1 {{ jenis_kegiatan_tone($event->jenis_kegiatan)['bg'] }}">{{ $startTime->format('H:i') }} · {{ $event->nama_kegiatan }}</a>
                                                @endif
                                            @endforeach
                                        </td>
                                    @endforeach
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                {{-- Mobile: daftar per hari --}}
                <ul class="divide-y divide-slate-100 lg:hidden">
                    @foreach($weekDays as $day)
                        @php
                            $hariIni = $eventsMinggu->filter(function ($event) use ($day) {
                                $start = \Carbon\Carbon::parse($event->tanggal_mulai);
                                $end = $event->tanggal_selesai ? \Carbon\Carbon::parse($event->tanggal_selesai) : $start;
                                return $event->waktu_mulai ? $start->isSameDay($day) : ($start->lte($day) && $end->gte($day));
                            });
                        @endphp
                        <li class="flex gap-3 px-3 py-3 {{ $day->isToday() ? 'bg-indigo-50/60' : '' }}">
                            <span class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl {{ $day->isToday() ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }}">
                                <span class="text-[10px] font-bold uppercase">{{ $day->translatedFormat('D') }}</span>
                                <span class="text-sm font-extrabold leading-none">{{ $day->format('d') }}</span>
                            </span>
                            <div class="min-w-0 flex-1 space-y-1 self-center">
                                @forelse($hariIni as $event)
                                    <a href="{{ $detailUrl($day->format('Y-m-d')) }}" data-event="{{ Str::lower($event->nama_kegiatan) }}" x-show="!q || $el.dataset.event.includes(q.toLowerCase())"
                                       class="{{ $chip }} py-1 text-xs {{ jenis_kegiatan_tone($event->jenis_kegiatan)['bg'] }}">@if($event->waktu_mulai){{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }} · @endif{{ $event->nama_kegiatan }}</a>
                                @empty
                                    <p class="text-xs text-slate-400">Tidak ada kegiatan</p>
                                @endforelse
                            </div>
                        </li>
                    @endforeach
                </ul>

            @elseif($viewMode === 'tahun')
                <div class="grid grid-cols-2 gap-2 p-3 sm:grid-cols-3 sm:gap-3 sm:p-4 lg:grid-cols-4">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $jumlahBulan = $eventsTahun->filter(function ($e) use ($m) {
                                $start = \Carbon\Carbon::parse($e->tanggal_mulai);
                                $end = $e->tanggal_selesai ? \Carbon\Carbon::parse($e->tanggal_selesai) : $start;
                                return $start->month == $m || $end->month == $m;
                            })->count();
                        @endphp
                        <a href="{{ $route(['mode' => 'bulan', 'month' => $m, 'year' => $year]) }}" class="group rounded-2xl border border-slate-200 p-3 text-center no-underline transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md sm:p-4">
                            <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                            <span class="mt-2 block text-sm font-extrabold text-slate-900 group-hover:text-indigo-700">{{ \Carbon\Carbon::createFromDate($year, $m, 1)->translatedFormat('F') }}</span>
                            <span class="block text-[11px] text-slate-400">{{ $year }}</span>
                            <span class="mt-2 inline-block rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $jumlahBulan > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $jumlahBulan > 0 ? $jumlahBulan . ' kegiatan' : 'Kosong' }}</span>
                        </a>
                    @endfor
                </div>

            @else
                <div class="grid grid-cols-7 bg-gradient-to-r from-indigo-600 to-violet-600 text-center text-[11px] font-bold uppercase tracking-wide text-white sm:text-xs">
                    @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $namaHari)
                        <div class="py-2.5">{{ $namaHari }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7">
                    @foreach($calendarDays as $day)
                        <div class="min-h-[64px] min-w-0 border-b border-r border-slate-100 p-1 sm:min-h-[96px] sm:p-1.5 {{ $day['isOtherMonth'] ? 'bg-slate-50 text-slate-300' : 'text-slate-800' }} {{ $day['isToday'] ? '!bg-amber-50' : '' }} [&:nth-child(7n)]:border-r-0">
                            <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full px-1 text-xs font-bold {{ $day['isToday'] ? 'bg-indigo-600 text-white' : '' }}">{{ $day['day'] }}</span>
                            @if($day['events']->isNotEmpty())
                                {{-- Mobile: titik warna; desktop: chip nama kegiatan --}}
                                <a href="{{ $detailUrl($day['fullDate']) }}" class="mt-1 flex flex-wrap gap-0.5 sm:hidden" aria-label="{{ $day['events']->count() }} kegiatan">
                                    @foreach($day['events']->take(4) as $event)
                                        <span class="h-1.5 w-1.5 rounded-full {{ jenis_kegiatan_tone($event->jenis_kegiatan)['dot'] }}"></span>
                                    @endforeach
                                </a>
                                <div class="mt-1 hidden space-y-0.5 sm:block">
                                    @foreach($day['events']->take(3) as $event)
                                        <a href="{{ $detailUrl($day['fullDate']) }}" title="{{ $event->nama_kegiatan }}" data-event="{{ Str::lower($event->nama_kegiatan) }}" x-show="!q || $el.dataset.event.includes(q.toLowerCase())"
                                           class="{{ $chip }} {{ jenis_kegiatan_tone($event->jenis_kegiatan)['bg'] }}">{{ $event->nama_kegiatan }}</a>
                                    @endforeach
                                    @if($day['events']->count() > 3)
                                        <a href="{{ $detailUrl($day['fullDate']) }}" class="{{ $chip }} bg-slate-100 text-slate-600">+{{ $day['events']->count() - 3 }} lainnya</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @for($i = count($calendarDays) % 7; $i > 0 && $i < 7; $i++)
                        <div class="min-h-[64px] border-b border-r border-slate-100 bg-slate-50 sm:min-h-[96px]"></div>
                    @endfor
                </div>
            @endif
        </section>

        <aside class="min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <h2 class="flex items-center gap-2 border-b border-slate-100 px-4 py-3 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-circle-info text-indigo-500" aria-hidden="true"></i>Keterangan</h2>
            <div class="max-h-[32rem] space-y-1 overflow-y-auto p-2">
                @forelse($events as $event)
                    @php $mulai = \Carbon\Carbon::parse($event->tanggal_mulai); @endphp
                    <a href="{{ $detailUrl($mulai->format('Y-m-d')) }}" data-event="{{ Str::lower($event->nama_kegiatan) }}" x-show="!q || $el.dataset.event.includes(q.toLowerCase())"
                       class="flex items-center gap-3 rounded-xl p-2 no-underline transition hover:bg-indigo-50/60">
                        <span class="flex w-12 shrink-0 flex-col items-center">
                            <span class="h-2.5 w-2.5 rounded-full {{ jenis_kegiatan_tone($event->jenis_kegiatan)['dot'] }}"></span>
                            <span class="mt-1 text-[10px] font-bold text-slate-500">{{ $mulai->translatedFormat('d M') }}</span>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-bold text-slate-900" title="{{ $event->nama_kegiatan }}">{{ $event->nama_kegiatan }}</span>
                            <span class="block text-[11px] text-slate-500">
                                <i class="fa-regular fa-calendar mr-0.5" aria-hidden="true"></i>{{ $mulai->locale('id')->isoFormat('dddd') }}
                                @if($event->waktu_mulai)<span class="ml-2"><i class="fa-regular fa-clock mr-0.5" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }}</span>@endif
                            </span>
                        </span>
                    </a>
                @empty
                    <p class="px-3 py-8 text-center text-sm text-slate-500"><i class="fa-regular fa-calendar-xmark mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>Tidak ada kegiatan bulan ini</p>
                @endforelse
            </div>
        </aside>
    </div>
</div>
@endsection
