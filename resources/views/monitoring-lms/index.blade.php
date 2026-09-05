@extends('layouts.app')

@section('title', 'Monitoring LMS')
@section('page-title', 'Monitoring LMS')
@section('page-subtitle', 'Tinjau materi, tugas, latihan, dan ujian yang diunggah oleh guru')

@section('content')
@php
    $totalMateri = $kelas->sum('materi_count');
    $totalTugas = $kelas->sum('tugas_count');
    $totalLatihan = $kelas->sum('latihan_count');
    $totalUjian = $kelas->sum('ujian_count');
    $kelasDenganKonten = $kelas->filter(fn ($item) => ($item->materi_count + $item->tugas_count + $item->latihan_count + $item->ujian_count) > 0)->count();
@endphp

<div class="min-w-0 w-full space-y-5" data-monitoring-lms-index>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex items-start gap-3 border-b border-slate-200 p-4 sm:p-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-cyan-500 text-white shadow-sm"><i class="fas fa-filter" aria-hidden="true"></i></span>
            <div class="min-w-0"><h2 class="text-base font-extrabold text-slate-950 sm:text-lg">Temukan kelas yang perlu ditinjau</h2><p class="mt-1 text-xs leading-5 text-slate-500">Cari kelas, pilih periode, atau fokuskan daftar pada kelas yang sudah memiliki konten.</p></div>
        </header>
        <form method="GET" class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5 xl:grid-cols-[minmax(260px,1fr)_minmax(220px,.55fr)_auto_minmax(250px,.7fr)] xl:items-end">
            <label class="block min-w-0"><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Cari kelas</span><span class="relative block"><i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Nama atau kode kelas..." class="h-11 w-full rounded-xl border border-slate-300 bg-white !pl-10 pr-3 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></span></label>
            <label class="block min-w-0"><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Tahun ajaran</span><select name="tahun_ajaran_id" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="0" @selected(($taFilter ?? 0) === 0)>Semua tahun ajaran</option>@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @selected(($taFilter ?? 0) === $ta->id)>{{ $ta->nama_tahun_ajaran ?? $ta->tahun_ajaran ?? ('TA #'.$ta->id) }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>@endforeach</select></label>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-xs font-bold text-white shadow-sm hover:bg-slate-800"><i class="fas fa-filter" aria-hidden="true"></i>Terapkan</button>
            <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs font-semibold leading-5 text-slate-700 hover:border-brand-200 hover:bg-brand-50"><input type="checkbox" name="only_with_content" value="1" class="h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @change="$el.form.requestSubmit()" @checked($onlyWithContent ?? false)><span>Hanya kelas yang memiliki konten LMS</span></label>
        </form>
    </section>

    <section class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-5" aria-label="Ringkasan konten LMS">
        @foreach([
            ['Total kelas', $kelas->count(), $kelasDenganKonten.' berisi konten', 'fa-school', 'bg-blue-50 text-blue-700', 'border-t-blue-500'],
            ['Materi', $totalMateri, 'Bahan pembelajaran', 'fa-book-open', 'bg-cyan-50 text-cyan-700', 'border-t-cyan-500'],
            ['Tugas', $totalTugas, 'Aktivitas terstruktur', 'fa-list-check', 'bg-amber-50 text-amber-700', 'border-t-amber-500'],
            ['Latihan', $totalLatihan, 'Latihan mandiri', 'fa-pencil-ruler', 'bg-violet-50 text-violet-700', 'border-t-violet-500'],
            ['Ujian', $totalUjian, 'Evaluasi terjadwal', 'fa-file-lines', 'bg-red-50 text-red-700', 'border-t-red-500'],
        ] as [$label, $value, $help, $icon, $tone, $border])
            <article class="min-w-0 rounded-2xl border border-t-4 border-slate-200 {{ $border }} bg-white p-4 shadow-sm {{ $loop->first ? 'col-span-2 sm:col-span-1' : '' }}"><div class="flex min-w-0 items-start justify-between gap-3"><div class="min-w-0"><p class="text-2xl font-extrabold leading-none text-slate-950">{{ number_format($value) }}</p><p class="mt-2 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span></div><p class="mt-3 truncate border-t border-slate-100 pt-3 text-[11px] text-slate-500" title="{{ $help }}">{{ $help }}</p></article>
        @endforeach
    </section>

    <section class="min-w-0">
        <header class="mb-3 flex flex-wrap items-end justify-between gap-2 px-1"><div><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-laptop-file text-brand-600" aria-hidden="true"></i>Daftar kelas</h2><p class="mt-1 text-xs text-slate-500">Buka kelas untuk memeriksa rincian konten dan aktivitasnya.</p></div><span class="rounded-full bg-brand-50 px-3 py-1.5 text-[10px] font-bold text-brand-700">{{ $kelas->count() }} kelas ditampilkan</span></header>

        @if($kelas->isEmpty())
            <div class="flex min-h-64 flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm"><span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-inbox" aria-hidden="true"></i></span><h3 class="mt-4 text-sm font-extrabold text-slate-900">Kelas tidak ditemukan</h3><p class="mt-1 max-w-lg text-xs leading-5 text-slate-500">Ubah kata pencarian, periode tahun ajaran, atau nonaktifkan filter kelas yang memiliki konten.</p></div>
        @else
            <div class="grid min-w-0 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                @foreach($kelas as $k)
                    @php
                        $totalKonten = $k->materi_count + $k->tugas_count + $k->latihan_count + $k->ujian_count;
                        $isEmpty = $totalKonten === 0;
                        $isActiveTa = $tahunAjaranAktif && (int) $k->tahun_ajaran_id === (int) $tahunAjaranAktif->id;
                    @endphp
                    <a href="{{ route($baseRoute.'.kelas', $k->id) }}" class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 no-underline shadow-sm transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md">
                        <div class="h-1 {{ $isEmpty ? 'bg-slate-300' : 'bg-gradient-to-r from-brand-600 to-cyan-500' }}"></div>
                        <div class="flex min-w-0 flex-1 flex-col p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-3"><span class="rounded-lg bg-brand-50 px-2.5 py-1 text-[10px] font-extrabold text-brand-700 ring-1 ring-brand-100">{{ $k->jenjang }}</span>@if($k->tahunAjaran)<span class="inline-flex min-w-0 items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-bold {{ $isActiveTa ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-slate-100 text-slate-600' }}"><i class="fas fa-calendar-days shrink-0" aria-hidden="true"></i><span class="truncate">{{ $k->tahunAjaran->nama_tahun_ajaran ?? $k->tahunAjaran->tahun_ajaran ?? '-' }}</span></span>@endif</div>
                            <h3 class="mt-4 truncate text-lg font-extrabold text-slate-950" title="{{ $k->nama_kelas }}">{{ $k->nama_kelas }}</h3>
                            <div class="mt-2 min-w-0 space-y-1.5 text-[11px] text-slate-500"><p class="flex min-w-0 items-center gap-2"><i class="fas fa-location-dot w-3 shrink-0 text-slate-400" aria-hidden="true"></i><span class="truncate" title="{{ $k->cabang->nama_cabang ?? 'Cabang belum diatur' }}">{{ $k->cabang->nama_cabang ?? 'Cabang belum diatur' }}</span></p><p class="flex min-w-0 items-center gap-2"><i class="fas fa-user-tie w-3 shrink-0 text-slate-400" aria-hidden="true"></i><span class="truncate" title="{{ $k->waliKelas->nama_lengkap ?? 'Wali kelas belum ditetapkan' }}">{{ $k->waliKelas->nama_lengkap ?? 'Wali kelas belum ditetapkan' }}</span></p></div>

                            @if($isEmpty)
                                <div class="mt-4 flex min-h-20 items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 px-3 text-center text-xs font-semibold text-slate-500"><i class="fas fa-circle-minus mr-2 text-slate-400" aria-hidden="true"></i>Belum ada konten LMS</div>
                            @else
                                <dl class="mt-4 grid grid-cols-4 gap-2 text-center"><div class="rounded-xl bg-cyan-50 p-2"><dt class="text-[9px] font-bold uppercase text-cyan-700">Materi</dt><dd class="mt-1 text-sm font-extrabold text-cyan-900">{{ $k->materi_count }}</dd></div><div class="rounded-xl bg-amber-50 p-2"><dt class="text-[9px] font-bold uppercase text-amber-700">Tugas</dt><dd class="mt-1 text-sm font-extrabold text-amber-900">{{ $k->tugas_count }}</dd></div><div class="rounded-xl bg-violet-50 p-2"><dt class="text-[9px] font-bold uppercase text-violet-700">Latihan</dt><dd class="mt-1 text-sm font-extrabold text-violet-900">{{ $k->latihan_count }}</dd></div><div class="rounded-xl bg-red-50 p-2"><dt class="text-[9px] font-bold uppercase text-red-700">Ujian</dt><dd class="mt-1 text-sm font-extrabold text-red-900">{{ $k->ujian_count }}</dd></div></dl>
                            @endif

                            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 text-xs font-bold text-brand-700"><span>{{ $totalKonten }} konten tersedia</span><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 transition group-hover:bg-brand-600 group-hover:text-white"><i class="fas fa-arrow-right" aria-hidden="true"></i></span></div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
