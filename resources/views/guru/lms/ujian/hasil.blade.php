@extends('layouts.lms-guru')

@php
    $isLatihan = request()->routeIs('guru.lms.latihan.*');
    $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $prefix = $isLatihan ? 'guru.lms.latihan' : 'guru.lms.ujian';
    $selesai = $hasilUjian->where('status', 'selesai');
    $statusMap = [
        'belum_mulai' => ['Belum mulai', 'bg-slate-100 text-slate-600'],
        'sedang_mengerjakan' => ['Sedang mengerjakan', 'bg-amber-50 text-amber-700'],
        'selesai' => ['Selesai', 'bg-emerald-50 text-emerald-700'],
        'dinilai' => ['Sudah dinilai', 'bg-indigo-50 text-indigo-700'],
    ];
    $stats = [
        ['label' => 'Total peserta', 'value' => $hasilUjian->count(), 'tone' => 'text-indigo-700', 'icon' => 'fa-users'],
        ['label' => 'Selesai', 'value' => $selesai->count(), 'tone' => 'text-emerald-700', 'icon' => 'fa-circle-check'],
        ['label' => 'Nilai rata-rata', 'value' => number_format($selesai->avg('nilai') ?? 0, 1), 'tone' => 'text-amber-700', 'icon' => 'fa-chart-simple'],
        ['label' => 'Nilai tertinggi', 'value' => number_format($selesai->max('nilai') ?? 0, 1), 'tone' => 'text-rose-700', 'icon' => 'fa-trophy'],
    ];
    $peringkat = [0 => 'fa-trophy text-amber-500', 1 => 'fa-medal text-slate-400', 2 => 'fa-award text-orange-500'];
@endphp

@section('title', 'Hasil ' . $tipeLabel)
@section('page-title', 'Hasil ' . $tipeLabel . ': ' . $ujian->judul_ujian)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route($prefix.'.index', [$kelas->id, $mapel->id]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali</a>
        @unless($isLatihan)
            <a href="{{ route('guru.lms.ujian.pengawasan', [$kelas->id, $mapel->id, $ujian->id]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 text-xs font-bold text-indigo-700 no-underline hover:bg-indigo-100"><i class="fa-solid fa-desktop" aria-hidden="true"></i>Pengawasan</a>
        @endunless
    </div>

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4">
        @foreach($stats as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold tabular-nums sm:text-2xl {{ $stat['tone'] }}">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                    <i class="fa-solid {{ $stat['icon'] }} text-slate-300" aria-hidden="true"></i>
                </div>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-3 sm:px-5"><h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-chart-column text-indigo-600" aria-hidden="true"></i>Hasil {{ strtolower($tipeLabel) }} siswa</h2></header>

        <div class="hidden grid-cols-[4rem_minmax(0,1fr)_9rem_9rem_9rem_6rem_7rem_7rem] gap-3 bg-slate-50 px-5 py-2 text-[11px] font-bold uppercase tracking-wide text-slate-500 xl:grid">
            <span class="text-center">Rank</span><span>Nama siswa</span><span class="text-center">Mulai</span><span class="text-center">Selesai</span><span class="text-center">Status</span><span class="text-center">Terakhir</span><span class="text-center">Terbaik</span><span class="text-center">Aksi</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($hasilUjian as $index => $hasil)
                @php [$statusLabel, $statusTone] = $statusMap[$hasil->status] ?? [ucfirst((string) $hasil->status), 'bg-slate-100 text-slate-600']; @endphp
                <article class="grid min-w-0 grid-cols-[2.5rem_minmax(0,1fr)_auto] items-center gap-x-3 gap-y-2 px-4 py-3 sm:px-5 xl:grid-cols-[4rem_minmax(0,1fr)_9rem_9rem_9rem_6rem_7rem_7rem]">
                    <span class="flex justify-center text-sm font-extrabold text-slate-600">
                        @if(isset($peringkat[$index]) && $hasil->nilai)<i class="fa-solid {{ $peringkat[$index] }} text-lg" aria-label="Peringkat {{ $index + 1 }}"></i>@else{{ $index + 1 }}@endif
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-extrabold text-slate-900">{{ $hasil->siswa->nama_lengkap ?? 'Siswa tidak ditemukan (ID: '.$hasil->siswa_id.')' }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-500 xl:hidden">{{ $hasil->waktu_mulai ? $hasil->waktu_mulai->format('d M Y H:i') : 'Belum mulai' }}@if($hasil->waktu_selesai) → {{ $hasil->waktu_selesai->format('H:i') }}@endif</p>
                    </div>
                    <span class="hidden whitespace-nowrap text-center text-xs text-slate-600 xl:block">{{ $hasil->waktu_mulai ? $hasil->waktu_mulai->format('d M Y H:i') : '-' }}</span>
                    <span class="hidden whitespace-nowrap text-center text-xs text-slate-600 xl:block">{{ $hasil->waktu_selesai ? $hasil->waktu_selesai->format('d M Y H:i') : '-' }}</span>
                    <span class="justify-self-end xl:justify-self-center"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $statusTone }}">{{ $statusLabel }}</span></span>
                    <div class="col-span-3 flex flex-wrap items-center justify-between gap-2 xl:contents">
                        <span class="text-xs text-slate-500 xl:text-center xl:text-sm"><span class="xl:hidden">Terakhir: </span><strong class="font-extrabold tabular-nums text-slate-700">{{ $hasil->nilai !== null ? number_format($hasil->nilai, 1) : '-' }}</strong></span>
                        <span class="text-xs text-slate-500 xl:text-center xl:text-sm"><span class="xl:hidden">Terbaik: </span><strong class="font-extrabold tabular-nums text-indigo-700">{{ $hasil->nilai_terbaik !== null ? number_format($hasil->nilai_terbaik, 1).'/100' : '-' }}</strong></span>
                        <span class="xl:text-center">
                            @if(in_array($hasil->status, ['selesai', 'dinilai']))
                                <a href="{{ route($prefix.'.koreksi.show', [$kelas->id, $mapel->id, $ujian->id, $hasil->id]) }}" class="inline-flex min-h-9 items-center gap-1.5 whitespace-nowrap rounded-lg bg-indigo-600 px-3 text-xs font-bold text-white no-underline hover:bg-indigo-700"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Koreksi</a>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </span>
                    </div>
                </article>
            @empty
                <p class="px-5 py-10 text-center text-sm text-slate-500">Belum ada siswa yang mengerjakan {{ strtolower($tipeLabel) }}.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
