@extends('layouts.app')

@section('title', 'Monitoring LMS - '.$kelas->nama_kelas)
@section('page-title', 'Monitoring LMS')
@section('page-subtitle', 'Detail konten kelas '.$kelas->nama_kelas.($kelas->tahunAjaran ? ' - '.($kelas->tahunAjaran->nama_tahun_ajaran ?? $kelas->tahunAjaran->tahun_ajaran) : ''))

@section('content')
@php
    $activeTab = in_array($filters['tab'] ?? 'materi', ['materi', 'tugas', 'latihan', 'ujian'], true) ? $filters['tab'] : 'materi';
    $stats = [
        ['materi', 'Materi', $konten['materi']->count(), 'fa-book-open', 'bg-cyan-50 text-cyan-700'],
        ['tugas', 'Tugas', $konten['tugas']->count(), 'fa-list-check', 'bg-amber-50 text-amber-700'],
        ['latihan', 'Latihan', $konten['latihan']->count(), 'fa-pencil-ruler', 'bg-violet-50 text-violet-700'],
        ['ujian', 'Ujian', $konten['ujian']->count(), 'fa-file-lines', 'bg-red-50 text-red-700'],
    ];
@endphp

<div
    class="min-w-0 w-full space-y-5"
    data-monitoring-lms-detail
    x-data="{
        tab: @js($activeTab),
        note: { type: '', id: '', label: '', title: '' },
        noteText: '',
        submittingNote: false,
        setTab(value) {
            this.tab = value;
            this.$refs.tabInput.value = value;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', value);
            window.history.replaceState({}, '', url);
        },
        openNote(detail) {
            this.note = detail;
            this.noteText = '';
            this.$nextTick(() => this.$refs.noteDialog.showModal());
        }
    }"
    @monitoring-note.window="openNote($event.detail)"
>
    <nav class="flex min-w-0 items-center gap-2 text-xs" aria-label="Breadcrumb"><a href="{{ route($baseRoute.'.index') }}" class="inline-flex min-h-9 shrink-0 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left" aria-hidden="true"></i>Daftar kelas</a><i class="fas fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i><span class="truncate font-semibold text-slate-500">{{ $kelas->nama_kelas }}</span></nav>

    <section class="overflow-hidden rounded-2xl border border-brand-700/20 bg-gradient-to-r from-brand-900 via-brand-800 to-brand-600 text-white shadow-sm">
        <div class="flex min-w-0 flex-col gap-4 p-4 sm:flex-row sm:items-center sm:p-6">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-lg font-extrabold ring-1 ring-white/20">{{ $kelas->jenjang }}</span>
            <div class="min-w-0 flex-1"><p class="text-[10px] font-bold uppercase tracking-[.16em] text-blue-100">Kelas yang dipantau</p><h2 class="mt-1 truncate text-2xl font-extrabold !text-white" title="{{ $kelas->nama_kelas }}">{{ $kelas->nama_kelas }}</h2><div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-blue-50/90">@if($kelas->cabang)<span><i class="fas fa-location-dot mr-1" aria-hidden="true"></i>{{ $kelas->cabang->nama_cabang }}</span>@endif @if($kelas->waliKelas)<span><i class="fas fa-user-tie mr-1" aria-hidden="true"></i>Wali: {{ $kelas->waliKelas->nama_lengkap }}</span>@endif</div></div>
            @if($kelas->tahunAjaran)<span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-2 text-xs font-bold ring-1 ring-white/20"><i class="fas fa-calendar-days" aria-hidden="true"></i>{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? $kelas->tahunAjaran->tahun_ajaran }}</span>@endif
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-magnifying-glass text-brand-600" aria-hidden="true"></i>Saring konten kelas</h2><p class="mt-1 text-xs text-slate-500">Gunakan judul, mata pelajaran, atau rentang tanggal untuk mempersempit hasil.</p></header>
        <form method="GET" class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5 xl:grid-cols-[minmax(220px,1fr)_minmax(180px,.7fr)_repeat(2,minmax(150px,.55fr))_auto] xl:items-end">
            <input x-ref="tabInput" type="hidden" name="tab" value="{{ $activeTab }}">
            <label class="min-w-0"><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Judul konten</span><span class="relative block"><i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Ketik kata kunci..." class="h-11 w-full rounded-xl border border-slate-300 bg-white !pl-10 pr-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></span></label>
            <label class="min-w-0"><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Mata pelajaran</span><select name="mapel_id" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua mata pelajaran</option>@foreach($konten['mapelOptions'] as $mp)<option value="{{ $mp->id }}" @selected($filters['mapel_id'] === $mp->id)>{{ $mp->nama_mapel }}</option>@endforeach</select></label>
            <label><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Dari tanggal</span><input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></label>
            <label><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Sampai tanggal</span><input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></label>
            <div class="grid grid-cols-[1fr_2.75rem] gap-2 sm:col-span-2 xl:col-span-1"><button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-xs font-bold text-white hover:bg-slate-800"><i class="fas fa-filter" aria-hidden="true"></i>Filter</button><a href="{{ route($baseRoute.'.kelas', $kelas->id) }}" class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-600 no-underline hover:bg-slate-100" aria-label="Reset filter" title="Reset filter"><i class="fas fa-rotate-left" aria-hidden="true"></i></a></div>
        </form>
    </section>

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Ringkasan konten kelas">
        @foreach($stats as [$key, $label, $value, $icon, $tone])
            <button type="button" @click="setTab('{{ $key }}')" :class="tab === '{{ $key }}' ? 'border-brand-300 ring-2 ring-brand-100' : 'border-slate-200 hover:border-brand-200'" class="flex min-w-0 items-center gap-3 rounded-2xl border bg-white p-3 text-left shadow-sm transition sm:p-4"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span><span class="min-w-0"><strong class="block text-xl font-extrabold leading-none text-slate-950">{{ $value }}</strong><span class="mt-1.5 block truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</span></span></button>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <nav class="grid grid-cols-4 border-b border-slate-200 bg-slate-50 p-1.5" aria-label="Jenis konten">
            @foreach($stats as [$key, $label, $value, $icon])
                <button type="button" @click="setTab('{{ $key }}')" :class="tab === '{{ $key }}' ? 'bg-white text-brand-700 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-800'" class="flex min-w-0 items-center justify-center gap-1.5 rounded-xl px-2 py-2.5 text-[10px] font-bold transition sm:text-xs"><i class="fas {{ $icon }} shrink-0" aria-hidden="true"></i><span class="hidden truncate min-[360px]:inline">{{ $label }}</span><span class="rounded-full bg-slate-100 px-1.5 py-0.5 text-[9px] text-slate-600">{{ $value }}</span></button>
            @endforeach
        </nav>

        <div x-cloak x-show="tab === 'materi'" class="p-4 sm:p-5">@include('monitoring-lms.partials.konten-grouped', ['items' => $konten['materi'], 'type' => 'materi', 'titleField' => 'judul_materi', 'dateField' => 'tanggal_upload', 'dateLabel' => 'Diupload', 'iconClass' => 'fa-book-open', 'iconTone' => 'bg-cyan-50 text-cyan-700', 'badgeTone' => 'bg-cyan-50 text-cyan-700', 'badgeText' => fn($item) => 'Materi', 'emptyText' => 'Belum ada materi.'])</div>
        <div x-cloak x-show="tab === 'tugas'" class="p-4 sm:p-5">@include('monitoring-lms.partials.konten-grouped', ['items' => $konten['tugas'], 'type' => 'tugas', 'titleField' => 'judul_tugas', 'dateField' => 'tanggal_deadline', 'dateLabel' => 'Tenggat', 'iconClass' => 'fa-list-check', 'iconTone' => 'bg-amber-50 text-amber-700', 'badgeTone' => 'bg-amber-50 text-amber-700', 'badgeText' => fn($item) => 'Tugas', 'emptyText' => 'Belum ada tugas.'])</div>
        <div x-cloak x-show="tab === 'latihan'" class="p-4 sm:p-5">@include('monitoring-lms.partials.konten-grouped', ['items' => $konten['latihan'], 'type' => 'latihan', 'titleField' => 'judul_ujian', 'dateField' => 'tanggal_mulai', 'dateLabel' => 'Mulai', 'iconClass' => 'fa-pencil-ruler', 'iconTone' => 'bg-violet-50 text-violet-700', 'badgeTone' => 'bg-violet-50 text-violet-700', 'badgeText' => fn($item) => 'Latihan', 'emptyText' => 'Belum ada latihan.'])</div>
        <div x-cloak x-show="tab === 'ujian'" class="p-4 sm:p-5">@include('monitoring-lms.partials.konten-grouped', ['items' => $konten['ujian'], 'type' => 'ujian', 'titleField' => 'judul_ujian', 'dateField' => 'tanggal_mulai', 'dateLabel' => 'Mulai', 'iconClass' => 'fa-file-lines', 'iconTone' => 'bg-red-50 text-red-700', 'badgeTone' => 'bg-red-50 text-red-700', 'badgeText' => fn($item) => method_exists($item, 'getTipeLabelAttribute') ? $item->tipe_label : 'Ujian', 'emptyText' => 'Belum ada ujian.'])</div>
    </section>

    @include('monitoring-lms.partials.modal-catatan')
</div>
@endsection
