@extends('layouts.app')

@section('title', 'Rekap Harian Presensi')
@section('page-title', 'Rekap Harian')
@section('page-subtitle', 'Telusuri presensi per tanggal')

@section('content')
<div class="min-w-0 w-full space-y-4" x-data="{ semester: @js($semester ?? '') }">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm"><div><h1 class="text-lg font-extrabold text-slate-900">Rekap harian</h1><p class="mt-0.5 text-xs text-slate-500">Pilih tanggal untuk melihat dan mengoreksi presensi siswa.</p></div><a href="{{ route('wali.presensi.index') }}" class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a></header>

    @if($error ?? false)
        <p class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">{{ $error }}</p>
    @else
        <form action="{{ route('wali.presensi.rekap-harian') }}" method="GET" class="grid grid-cols-2 gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:grid-cols-[repeat(3,minmax(0,1fr))_auto] sm:items-end">
            <label class="min-w-0 text-xs font-bold text-slate-600">Semester<select name="semester" x-model="semester" class="mt-1 block h-10 w-full rounded-lg border border-slate-300 bg-white px-2.5 text-sm font-medium text-slate-800"><option value="">Per bulan</option><option value="ganjil">Ganjil</option><option value="genap">Genap</option></select></label>
            <label class="min-w-0 text-xs font-bold text-slate-600">Bulan<select name="bulan" :disabled="!!semester" class="mt-1 block h-10 w-full rounded-lg border border-slate-300 bg-white px-2.5 text-sm font-medium text-slate-800 disabled:bg-slate-100 disabled:text-slate-400">@foreach(range(1, 12) as $month)<option value="{{ $month }}" @selected($bulan == $month)>{{ \Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F') }}</option>@endforeach</select></label>
            <label class="min-w-0 text-xs font-bold text-slate-600">Tahun<select name="tahun" :disabled="!!semester" class="mt-1 block h-10 w-full rounded-lg border border-slate-300 bg-white px-2.5 text-sm font-medium text-slate-800 disabled:bg-slate-100 disabled:text-slate-400">@foreach(range(now()->year - 2, now()->year + 1) as $year)<option value="{{ $year }}" @selected($tahun == $year)>{{ $year }}</option>@endforeach</select></label>
            <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 self-end rounded-lg bg-sky-700 px-4 text-xs font-bold text-white hover:bg-sky-800"><i class="fas fa-filter" aria-hidden="true"></i>Terapkan</button>
        </form>

        @if($dates->isEmpty())
            <section class="rounded-xl border border-slate-200 bg-white px-4 py-9 text-center shadow-sm"><i class="fas fa-calendar-xmark text-2xl text-slate-300" aria-hidden="true"></i><h2 class="mt-3 text-sm font-bold text-slate-900">Belum ada presensi pada periode ini</h2><p class="mt-1 text-xs text-slate-500">Mulai dari input presensi kelas.</p><a href="{{ route('wali.presensi.index') }}" class="mt-4 inline-flex min-h-9 items-center rounded-lg bg-sky-700 px-4 text-xs font-bold text-white">Input presensi</a></section>
        @else
            <section class="grid min-w-0 gap-3 sm:grid-cols-2 xl:grid-cols-3" aria-label="Daftar tanggal presensi">
                @foreach($dates as $date)
                    @php($day = \Carbon\Carbon::parse($date->tanggal))
                    @php($percent = $date->total_siswa > 0 ? round($date->hadir / $date->total_siswa * 100) : 0)
                    <a href="{{ route('wali.presensi.show-harian', ['tanggal' => $day->toDateString()]) }}" class="block min-w-0 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm transition hover:border-sky-300 hover:bg-sky-50">
                        <div class="flex items-start justify-between gap-2"><div><h2 class="text-sm font-extrabold text-slate-900">{{ $day->locale('id')->translatedFormat('l') }}</h2><p class="mt-0.5 text-xs font-bold text-sky-700">{{ $day->locale('id')->translatedFormat('d F Y') }}</p></div><span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">{{ $date->total_siswa }} siswa</span></div>
                        <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] font-bold"><span class="rounded-md bg-emerald-50 px-2 py-1 text-emerald-800">H {{ $date->hadir }}</span><span class="rounded-md bg-amber-50 px-2 py-1 text-amber-800">S {{ $date->sakit }}</span><span class="rounded-md bg-sky-50 px-2 py-1 text-sky-800">I {{ $date->izin }}</span><span class="rounded-md bg-rose-50 px-2 py-1 text-rose-800">A {{ $date->alpha }}</span></div>
                        <p class="mt-3 border-t border-slate-100 pt-2 text-xs text-slate-500">Kehadiran <strong class="text-slate-800">{{ $percent }}%</strong><i class="fas fa-arrow-right ml-2 text-sky-600" aria-hidden="true"></i></p>
                    </a>
                @endforeach
            </section>
        @endif
    @endif
</div>
@endsection
