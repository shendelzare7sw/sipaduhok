@extends('layouts.app')

@section('title', 'Daftar Kelas')
@section('page-title', 'Daftar Kelas')
@section('page-subtitle', 'Pilih kelas untuk mengelola pembelajaran')

@section('content')
<div class="min-w-0 w-full space-y-5">
    <header class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:px-5">
        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700">Tugas mengajar</p>
        <h1 class="mt-1 flex items-center gap-2 text-lg font-extrabold text-slate-900"><i class="fa-solid fa-chalkboard-user text-brand-600" aria-hidden="true"></i>Kelas yang Anda ajar</h1>
    </header>

    @if($dataKelas && count($dataKelas) > 0)
        <div class="grid min-w-0 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($dataKelas as $item)
                <article class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-extrabold text-brand-700" title="{{ $item['kelas']->nama_kelas }}">{{ $item['kelas']->nama_kelas }}</h2>
                            <p class="mt-0.5 text-[11px] font-bold uppercase tracking-wide text-slate-500">Jenjang {{ $item['kelas']->jenjang }}</p>
                        </div>
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fa-solid fa-school" aria-hidden="true"></i></span>
                    </div>

                    <dl class="mt-4 grid grid-cols-2 divide-x divide-slate-200 rounded-xl border border-slate-200 bg-slate-50 py-3 text-center">
                        <div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Siswa</dt><dd class="mt-0.5 text-lg font-extrabold text-slate-900">{{ $item['jumlah_siswa'] }}</dd></div>
                        <div><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Mapel</dt><dd class="mt-0.5 text-lg font-extrabold text-slate-900">{{ $item['jumlah_mapel'] }}</dd></div>
                    </dl>

                    <div class="mt-4 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Mata pelajaran</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach($item['mapel'] as $mapel)
                                <span class="rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-bold text-brand-700 ring-1 ring-inset ring-brand-100">{{ $mapel->nama_mapel }}</span>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('guru.kelas.mapel', $item['kelas']->id) }}" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-extrabold text-white no-underline shadow-sm transition hover:bg-brand-700">Kelola kelas<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            @endforeach
        </div>
    @else
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fa-solid fa-folder-open" aria-hidden="true"></i></span>
            <h2 class="mt-4 text-lg font-extrabold text-slate-900">Belum ada kelas</h2>
            <p class="mt-1 text-sm text-slate-500">Anda belum memiliki tugas mengajar di tahun ajaran aktif.</p>
            <p class="mx-auto mt-4 flex max-w-xl items-start gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-left text-xs leading-5 text-sky-900">
                <i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>
                <span>Mencari konten kelas tahun ajaran lalu? Buka menu <a href="{{ route('guru.lms.arsip.index') }}" class="font-bold text-brand-700 underline">Arsip LMS</a>. Jika Anda merasa ini kesalahan untuk TA aktif, silakan hubungi bagian Akademik atau Admin.</span>
            </p>
        </section>
    @endif
</div>
@endsection
