@extends('layouts.lms-guru')

@section('title', 'Koreksi Tugas')
@section('page-title', 'Koreksi Tugas: ' . $tugas->judul_tugas)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php
    $args = [$kelas->id, $mapel->id, $tugas->id];
    $statusMap = [
        'belum_dikerjakan' => ['Belum dikerjakan', 'bg-slate-100 text-slate-600'],
        'dikerjakan' => ['Perlu koreksi', 'bg-amber-50 text-amber-700'],
        'dinilai' => ['Sudah dinilai', 'bg-emerald-50 text-emerald-700'],
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar tugas</a>

    <section class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="min-w-0">
            <h2 class="break-words text-base font-extrabold text-slate-900">{{ $tugas->judul_tugas }}</h2>
            <p class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <span><i class="fa-regular fa-calendar mr-1" aria-hidden="true"></i>Deadline {{ $tugas->tanggal_deadline->format('d M Y') }}</span>
                @if($tugas->isOverdue())<span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-extrabold text-rose-700">Lewat deadline</span>@endif
            </p>
        </div>
        <dl class="grid grid-cols-2 gap-2">
            <div class="rounded-xl bg-emerald-50 px-4 py-2 text-center"><dt class="text-[10px] font-bold uppercase text-emerald-700">Dikumpulkan</dt><dd class="text-xl font-extrabold tabular-nums text-emerald-800">{{ $submitted }}</dd></div>
            <div class="rounded-xl bg-amber-50 px-4 py-2 text-center"><dt class="text-[10px] font-bold uppercase text-amber-700">Belum dinilai</dt><dd class="text-xl font-extrabold tabular-nums text-amber-800">{{ $belumDinilai }}</dd></div>
        </dl>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-3 sm:px-5"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-users text-indigo-600" aria-hidden="true"></i>Daftar siswa &amp; jawaban</h3></header>

        <div class="hidden grid-cols-[3rem_minmax(0,1fr)_10rem_9rem_5rem_9.5rem] gap-3 bg-slate-50 px-5 py-2 text-[11px] font-bold uppercase tracking-wide text-slate-500 lg:grid">
            <span>No</span><span>Nama siswa</span><span class="text-center">Tanggal kumpul</span><span class="text-center">Status</span><span class="text-center">Nilai</span><span class="text-center">Aksi</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($daftarSiswa as $index => $ts)
                @php [$statusLabel, $statusTone] = $statusMap[$ts->status] ?? [ucfirst($ts->status), 'bg-slate-100 text-slate-600']; @endphp
                <article class="grid min-w-0 grid-cols-[minmax(0,1fr)_auto] items-center gap-x-3 gap-y-2 px-4 py-3 sm:px-5 lg:grid-cols-[3rem_minmax(0,1fr)_10rem_9rem_5rem_9.5rem]">
                    <span class="hidden text-xs text-slate-500 lg:block">{{ $index + 1 }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-extrabold text-slate-900" title="{{ $ts->siswa->nama_lengkap }}">{{ $ts->siswa->nama_lengkap }}</p>
                        <p class="mt-0.5 flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500 lg:hidden">{{ $ts->tanggal_submit ? $ts->tanggal_submit->format('d M Y H:i') : 'Belum mengumpulkan' }}</p>
                        @if($ts->isLate())<span class="mt-1 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">Terlambat</span>@endif
                    </div>
                    <span class="hidden whitespace-nowrap text-center text-xs text-slate-600 lg:block">{{ $ts->tanggal_submit ? $ts->tanggal_submit->format('d M Y H:i') : '-' }}</span>
                    <span class="justify-self-end lg:justify-self-center"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $statusTone }}">{{ $statusLabel }}</span></span>
                    <span class="text-sm font-extrabold tabular-nums text-indigo-700 lg:text-center">{{ ! is_null($ts->nilai) && $ts->nilai !== '' ? number_format($ts->nilai, 0) : '-' }}</span>
                    <div class="justify-self-end lg:justify-self-center">
                        @if($ts->status != 'belum_dikerjakan')
                            <a href="{{ route('guru.lms.tugas.koreksi.show', [...$args, $ts->id]) }}" class="inline-flex min-h-9 items-center gap-1.5 whitespace-nowrap rounded-lg bg-indigo-600 px-3 text-xs font-bold text-white no-underline hover:bg-indigo-700"><i class="fa-solid fa-eye" aria-hidden="true"></i>Lihat &amp; nilai</a>
                        @else
                            <span class="text-xs text-slate-400">Belum submit</span>
                        @endif
                    </div>
                </article>
            @empty
                <p class="px-5 py-10 text-center text-sm text-slate-500">Belum ada siswa yang mengumpulkan tugas.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
