@extends('layouts.lms')

@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')
@section('page-subtitle', 'Jadwal mingguan kelas ' . ($kelas->nama_kelas ?? '-'))

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    // Versi mobile: daftar per hari yang diturunkan dari grid yang sama (sel 'taken'/'empty' dilewati).
    $perHari = collect($scheduleGrid['days'])->mapWithKeys(fn ($day) => [$day => collect($scheduleGrid['rows'])
        ->map(fn ($row) => $row['days'][$day])
        ->reject(fn ($cell) => in_array($cell['type'], ['taken', 'empty'], true))
        ->values()]);
    $card = 'min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm';
@endphp

<div class="min-w-0 w-full space-y-4">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-5 text-white shadow-lg shadow-indigo-900/15 sm:p-6">
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h2 class="flex items-center gap-2 text-lg font-extrabold text-white sm:text-xl"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i>Jadwal Pelajaran {{ $kelas->nama_kelas }}</h2>
                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-indigo-100">
                    <span><i class="fa-solid fa-graduation-cap mr-1" aria-hidden="true"></i>{{ strtoupper($kelas->jenjang) }}</span>
                    <span><i class="fa-solid fa-calendar mr-1" aria-hidden="true"></i>{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                    <span><i class="fa-solid fa-user-tie mr-1" aria-hidden="true"></i>Wali: {{ $kelas->waliKelas->nama_lengkap ?? '-' }}</span>
                </div>
            </div>
            <a href="{{ route('siswa.lms.jadwal.print') }}" target="_blank" rel="noopener" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 self-start rounded-xl bg-white px-4 text-xs font-extrabold text-indigo-700 no-underline shadow-sm transition hover:bg-indigo-50 sm:self-auto">
                <i class="fa-solid fa-print" aria-hidden="true"></i>Cetak jadwal
            </a>
        </div>
    </section>

    <section class="{{ $card }} overflow-hidden">
        @if(empty($scheduleGrid['rows']))
            <p class="px-4 py-10 text-center text-sm text-slate-500"><i class="fa-regular fa-calendar-xmark mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>Belum ada jadwal pelajaran</p>
        @else
            {{-- Desktop: tabel mingguan --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full min-w-[820px] table-fixed border-collapse text-sm">
                    <colgroup><col class="w-20">@foreach($scheduleGrid['days'] as $day)<col>@endforeach</colgroup>
                    <thead>
                        <tr class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white">
                            <th class="px-2 py-3 text-xs font-bold">Jam</th>
                            @foreach($scheduleGrid['days'] as $day)
                                <th class="px-2 py-3 text-xs font-bold uppercase tracking-wide">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scheduleGrid['rows'] as $row)
                            <tr>
                                <td class="border border-slate-100 bg-slate-50 px-2 py-2 text-center text-xs font-bold text-slate-500">{{ $row['time_start'] }}</td>
                                @for($i = 0; $i < count($scheduleGrid['days']); $i++)
                                    @php
                                        $day = $scheduleGrid['days'][$i];
                                        $cell = $row['days'][$day];
                                    @endphp
                                    @if($cell['type'] == 'taken')
                                        {{-- Tergabung rowspan --}}
                                    @elseif($cell['type'] == 'empty')
                                        <td class="border border-slate-100"></td>
                                    @else
                                        @php
                                            // Gabungkan istirahat yang sama secara horizontal (colspan).
                                            $colspan = 1;
                                            if ($cell['type'] == 'break') {
                                                $breakName = $cell['data']->nama_istirahat ?? 'Istirahat';
                                                for ($j = $i + 1; $j < count($scheduleGrid['days']); $j++) {
                                                    $nextCell = $row['days'][$scheduleGrid['days'][$j]];
                                                    if ($nextCell['type'] == 'break' && ($nextCell['data']->nama_istirahat ?? '') == $breakName && $nextCell['data']->jam_mulai == $cell['data']->jam_mulai) {
                                                        $colspan++;
                                                    } else {
                                                        break;
                                                    }
                                                }
                                            }
                                            $i += ($colspan - 1);
                                            $isBreak = $cell['type'] == 'break';
                                        @endphp
                                        <td rowspan="{{ $cell['rowspan'] ?? 1 }}" colspan="{{ $colspan }}" class="border border-slate-100 p-2 align-top {{ $isBreak ? 'bg-amber-50 text-center align-middle text-xs font-bold text-amber-800' : 'bg-white' }}">
                                            @if($isBreak)
                                                <i class="fa-solid fa-mug-hot mr-1" aria-hidden="true"></i>{{ $cell['data']->nama_istirahat ?? 'Istirahat' }}
                                            @else
                                                <div class="space-y-2">
                                                    @foreach($cell['data'] as $jadwal)
                                                        <div class="rounded-lg border-l-4 border-indigo-500 bg-indigo-50/60 px-2 py-1.5">
                                                            @if($siswa->canAccessMapel($jadwal->mataPelajaran))
                                                                <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="block truncate text-xs font-extrabold text-slate-900 no-underline hover:text-indigo-700">{{ $jadwal->mataPelajaran->nama_mapel }}</a>
                                                            @else
                                                                <span class="block truncate text-xs font-extrabold text-slate-900">{{ $jadwal->mataPelajaran->nama_mapel }}</span>
                                                            @endif
                                                            <span class="block truncate text-[11px] text-slate-500">{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '(-)' }}</span>
                                                            <span class="mt-1 inline-block rounded-md bg-white px-1.5 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile: daftar per hari --}}
            <div class="divide-y divide-slate-100 lg:hidden" x-data="{ buka: @js($hariIni) }">
                @foreach($perHari as $day => $cells)
                    <div>
                        <button type="button" x-on:click="buka = buka === @js($day) ? null : @js($day)" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left">
                            <span class="text-sm font-extrabold text-slate-900">{{ $day }} @if($day === $hariIni)<span class="ml-1 rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] font-bold text-white">Hari ini</span>@endif</span>
                            <span class="flex items-center gap-2 text-xs text-slate-500">{{ $cells->where('type', '!=', 'break')->count() }} sesi <i class="fa-solid fa-chevron-down text-[10px] transition" x-bind:class="buka === @js($day) && 'rotate-180'" aria-hidden="true"></i></span>
                        </button>
                        <div x-show="buka === @js($day)" x-cloak class="space-y-2 px-4 pb-4">
                            @forelse($cells as $cell)
                                @if($cell['type'] == 'break')
                                    <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-bold text-amber-800"><i class="fa-solid fa-mug-hot mr-1" aria-hidden="true"></i>{{ $cell['data']->nama_istirahat ?? 'Istirahat' }} · {{ \Carbon\Carbon::parse($cell['data']->jam_mulai)->format('H:i') }}</p>
                                @else
                                    @foreach($cell['data'] as $jadwal)
                                        @php $bisaBuka = $siswa->canAccessMapel($jadwal->mataPelajaran); @endphp
                                        <a @if($bisaBuka) href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" @endif class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5 no-underline {{ $bisaBuka ? 'transition hover:border-indigo-200' : '' }}">
                                            <span class="w-12 shrink-0 text-center text-xs font-extrabold text-indigo-700">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}<span class="block text-[10px] font-semibold text-slate-400">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span></span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block truncate text-sm font-bold text-slate-900">{{ $jadwal->mataPelajaran->nama_mapel }}</span>
                                                <span class="block truncate text-xs text-slate-500">{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '(-)' }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                @endif
                            @empty
                                <p class="text-xs text-slate-400">Tidak ada jadwal.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <div class="grid min-w-0 gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
        <section class="{{ $card }}">
            <h2 class="flex items-center gap-2 border-b border-slate-100 px-4 py-3 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-sun text-amber-500" aria-hidden="true"></i>Hari ini ({{ $hariIni }})</h2>
            <div class="space-y-1 p-2">
                @forelse($jadwalHariIni as $jadwal)
                    @if($siswa->canAccessMapel($jadwal->mataPelajaran))
                        <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="group flex items-center justify-between gap-2 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-800 no-underline transition hover:bg-indigo-50/60 hover:text-indigo-700">
                            <span class="truncate">{{ $jadwal->mataPelajaran->nama_mapel }}</span>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-indigo-500" aria-hidden="true"></i>
                        </a>
                    @else
                        <p class="truncate rounded-xl px-3 py-2.5 text-sm font-bold text-slate-400">{{ $jadwal->mataPelajaran->nama_mapel }}</p>
                    @endif
                @empty
                    <p class="px-3 py-6 text-center text-sm text-slate-500"><i class="fa-regular fa-calendar-xmark mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>Tidak ada jadwal hari ini</p>
                @endforelse
            </div>
        </section>

        <section class="{{ $card }}" x-data="{ q: '' }">
            <h2 class="flex items-center gap-2 border-b border-slate-100 px-4 py-3 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-book text-indigo-500" aria-hidden="true"></i>Mata pelajaran lengkap</h2>
            <div class="space-y-3 p-3 sm:p-4">
                <label class="relative block">
                    <span class="sr-only">Cari mata pelajaran</span>
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400" aria-hidden="true"></i>
                    <input type="search" x-model="q" placeholder="Cari mata pelajaran..." class="block h-10 w-full rounded-xl border border-slate-300 bg-white pl-10 pr-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                </label>
                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse($mataPelajaranList as $mapel)
                        @php $bisaBuka = $siswa->canAccessMapel($mapel); @endphp
                        <a @if($bisaBuka) href="{{ route('siswa.lms.mapel.show', $mapel->id) }}" @endif data-name="{{ Str::lower($mapel->nama_mapel) }}" x-show="$el.dataset.name.includes(q.toLowerCase().trim())"
                           class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-bold no-underline {{ $bisaBuka ? 'text-slate-800 transition hover:border-indigo-200 hover:text-indigo-700' : 'text-slate-400' }}">
                            <span class="truncate">{{ $mapel->nama_mapel }}</span>
                            @if($bisaBuka)<i class="fa-solid fa-chevron-right text-xs text-slate-300" aria-hidden="true"></i>@endif
                        </a>
                    @empty
                        <p class="text-sm text-slate-500 sm:col-span-2 xl:col-span-3">Belum ada mata pelajaran</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <section class="rounded-2xl border border-slate-200 border-l-4 border-l-sky-500 bg-white p-4 shadow-sm">
        <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-circle-info text-sky-500" aria-hidden="true"></i>Informasi penting</h2>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-xs leading-5 text-slate-500">
            <li>Klik nama mata pelajaran untuk melihat materi dan mengumpulkan tugas.</li>
            <li>Jadwal ini merupakan jadwal rutin mingguan Anda.</li>
        </ul>
    </section>
</div>
@endsection
