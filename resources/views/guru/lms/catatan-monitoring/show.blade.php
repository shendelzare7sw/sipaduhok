@extends('layouts.app')

@section('title', 'Detail Catatan Monitoring')
@section('page-title', 'Detail Catatan Monitoring')
@section('page-subtitle', 'Catatan dari ' . ($catatan->pengirim->name ?? 'Pimpinan'))

@section('content')
<div class="min-w-0 w-full space-y-5">
    <a href="{{ route('guru.lms.catatan-monitoring.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar catatan</a>

    <section class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fa-solid fa-user-shield" aria-hidden="true"></i></span>
        <div class="min-w-0">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Catatan dari</p>
            <h2 class="truncate text-base font-extrabold text-slate-900">{{ $catatan->pengirim->name ?? 'Pimpinan' }}</h2>
            <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-slate-600">{{ str_replace('_', ' ', $catatan->pengirim_role) }}</span>
                <span><i class="fa-solid fa-clock mr-1" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($catatan->created_at)->translatedFormat('d F Y, H:i') }}</span>
            </p>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Terkait konten</p>
        <p class="mt-2 flex flex-wrap items-center gap-2">
            <span class="rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-extrabold uppercase text-amber-700">{{ $catatan->kontenLabel() }}</span>
            <strong class="min-w-0 break-words text-base text-slate-900">{{ $catatan->kontenJudul() }}</strong>
        </p>
        <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
            @if($catatan->mataPelajaran)<span><i class="fa-solid fa-book mr-1" aria-hidden="true"></i>{{ $catatan->mataPelajaran->nama_mapel }}</span>@endif
            @if($catatan->kelas)<span><i class="fa-solid fa-school mr-1" aria-hidden="true"></i>{{ $catatan->kelas->nama_kelas }}</span>@endif
        </p>

        @if($konten)
            @php
                $editRoute = null;
                if ($catatan->konten_type === 'materi') {
                    $editRoute = route('guru.lms.materi.edit', [$catatan->kelas_id, $catatan->mata_pelajaran_id, $catatan->konten_id]);
                } elseif ($catatan->konten_type === 'tugas') {
                    $editRoute = route('guru.lms.tugas.edit', [$catatan->kelas_id, $catatan->mata_pelajaran_id, $catatan->konten_id]);
                } elseif ($catatan->konten_type === 'ujian') {
                    $editRoute = route('guru.lms.ujian.edit', [$catatan->kelas_id, $catatan->mata_pelajaran_id, $catatan->konten_id]);
                }
            @endphp
            @if($editRoute)
                <a href="{{ $editRoute }}" class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-4 text-xs font-bold text-brand-700 no-underline hover:bg-brand-100"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Buka &amp; revisi konten</a>
            @endif
        @else
            <p class="mt-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                <i class="fa-solid fa-triangle-exclamation mt-0.5" aria-hidden="true"></i>
                <span>Konten yang dimaksud sudah tidak tersedia (mungkin telah dihapus).</span>
            </p>
        @endif
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Isi catatan</p>
        <div class="mt-3 break-words text-sm leading-7 text-slate-800">{!! nl2br(e($catatan->isi_catatan)) !!}</div>
    </section>

    @if($catatan->dibaca_pada)
        <p class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>Anda telah membaca catatan ini pada {{ \Carbon\Carbon::parse($catatan->dibaca_pada)->translatedFormat('d F Y, H:i') }}
        </p>
    @endif
</div>
@endsection
