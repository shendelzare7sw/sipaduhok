@extends('layouts.app')

@section('title', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')
@section('page-subtitle', 'Jadwal mengajar mingguan Anda')

@section('content')
@php
    $allJadwal = $jadwal->flatten(1);
    $totalJadwal = $allJadwal->count();
    $totalHari = $jadwal->count();
    $totalMapel = $allJadwal->pluck('mataPelajaran.nama_mapel')->filter()->unique()->count();
    $totalKelas = $allJadwal
        ->flatMap(fn ($item) => $item->kelas->pluck('nama_kelas'))
        ->filter()
        ->unique()
        ->count();

    $statCards = [
        ['label' => 'Total jadwal', 'value' => $totalJadwal, 'icon' => 'fa-calendar-check', 'tone' => 'bg-blue-50 text-blue-600'],
        ['label' => 'Hari mengajar', 'value' => $totalHari, 'icon' => 'fa-calendar-day', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Kelas diampu', 'value' => $totalKelas, 'icon' => 'fa-chalkboard', 'tone' => 'bg-sky-50 text-sky-600'],
        ['label' => 'Mata pelajaran', 'value' => $totalMapel, 'icon' => 'fa-book-open', 'tone' => 'bg-amber-50 text-amber-600'],
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    @if(session('error'))
        <p class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">{{ session('error') }}</p>
    @endif

    <form method="GET" action="{{ route('guru.jadwal.index') }}" x-data class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:px-5">
        <label for="filterTahunAjaran" class="block text-xs font-bold text-slate-600 sm:max-w-xs">
            Tahun ajaran
            <select name="tahun_ajaran_id" id="filterTahunAjaran" @change="$el.form.submit()" class="mt-1 block h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100">
                <option value="0" @selected($taFilterId == 0)>Semua TA</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected($taFilterId == $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>
                @endforeach
            </select>
        </label>
        <noscript><button type="submit" class="mt-2 inline-flex min-h-10 items-center gap-2 rounded-lg bg-brand-600 px-4 text-xs font-bold text-white"><i class="fa-solid fa-filter" aria-hidden="true"></i>Filter</button></noscript>
    </form>

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4" aria-label="Ringkasan jadwal">
        @foreach($statCards as $stat)
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

    @if($jadwal->isEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-calendar-xmark" aria-hidden="true"></i></span>
            <h2 class="mt-4 font-extrabold text-slate-900">Belum ada jadwal mengajar</h2>
            <p class="mt-1 text-sm text-slate-500">
                @if($taFilterId)
                    Tidak ada jadwal untuk tahun ajaran yang dipilih. Coba pilih <strong>Semua TA</strong> di filter di atas.
                @else
                    Jadwal Anda akan tampil di sini setelah ditentukan oleh admin atau waka.
                @endif
            </p>
        </section>
    @else
        <div class="grid min-w-0 gap-5 lg:grid-cols-2 2xl:grid-cols-3">
            @foreach($jadwal as $hari => $items)
                <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-5">
                        <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-calendar-day text-brand-600" aria-hidden="true"></i>{{ $hari }}</h2>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">{{ $items->count() }} jadwal</span>
                    </header>

                    <div class="divide-y divide-slate-100">
                        @foreach($items as $item)
                            <article class="flex min-w-0 gap-3 p-4 hover:bg-slate-50 sm:gap-4">
                                <div class="w-16 shrink-0 text-sm font-extrabold tabular-nums text-brand-700">
                                    {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                                    <span class="block text-xs font-semibold text-slate-400">- {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $item->mataPelajaran->nama_mapel ?? '-' }}">{{ $item->mataPelajaran->nama_mapel ?? '-' }}</h3>
                                    <p class="mt-0.5 truncate text-[11px] text-slate-500">{{ $item->tahunAjaran->nama_tahun_ajaran ?? 'Tahun ajaran tidak tersedia' }}</p>
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @forelse($item->kelas as $kelas)
                                            <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-[11px] font-bold text-brand-700 ring-1 ring-inset ring-brand-100">{{ $kelas->nama_kelas }}</span>
                                        @empty
                                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-500">Kelas belum diatur</span>
                                        @endforelse
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
@endsection
