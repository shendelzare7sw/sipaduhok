@extends('layouts.app')

@section('title', 'Pilih Mata Pelajaran')
@section('page-title', 'Kelas ' . $kelas->nama_kelas)
@section('page-subtitle', 'Pilih mata pelajaran untuk masuk LMS')

@section('content')
<div class="min-w-0 w-full space-y-5">
    <a href="{{ route('guru.kelas.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar kelas</a>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-4 sm:px-5">
            <h1 class="flex items-center gap-2 text-lg font-extrabold text-brand-700"><i class="fa-solid fa-chalkboard" aria-hidden="true"></i>Kelas {{ $kelas->nama_kelas }}</h1>
            <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
                <span class="whitespace-nowrap"><i class="fa-solid fa-users mr-1" aria-hidden="true"></i>{{ $jumlahSiswa }} siswa</span>
                <span class="whitespace-nowrap"><i class="fa-solid fa-book mr-1" aria-hidden="true"></i>{{ $mapelYangDiajar->count() }} mata pelajaran</span>
            </p>
        </header>

        <div class="p-4 sm:p-5">
            <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-800"><i class="fa-solid fa-list text-slate-400" aria-hidden="true"></i>Pilih mata pelajaran</h2>

            <div class="mt-4 grid min-w-0 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($mapelYangDiajar as $pengajaran)
                    <article class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fa-solid fa-book-open" aria-hidden="true"></i></span>
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $pengajaran->mataPelajaran->nama_mapel }}">{{ $pengajaran->mataPelajaran->nama_mapel }}</h3>
                                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $pengajaran->mataPelajaran->kode_mapel }}</p>
                            </div>
                        </div>
                        <a href="{{ route('guru.lms.dashboard', [$kelas->id, $pengajaran->mata_pelajaran_id]) }}" class="mt-4 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-extrabold text-white no-underline shadow-sm transition hover:bg-brand-700"><i class="fa-solid fa-door-open" aria-hidden="true"></i>Masuk LMS</a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="flex items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4 sm:p-5">
        <i class="fa-solid fa-circle-info mt-0.5 text-sky-600" aria-hidden="true"></i>
        <p class="text-xs leading-5 text-slate-600">Klik tombol <strong class="text-slate-800">"Masuk LMS"</strong> untuk mengakses sistem pembelajaran bagi mata pelajaran yang Anda pilih. Di dalam LMS, Anda dapat mengelola materi, tugas, ujian, dan forum diskusi.</p>
    </section>
</div>
@endsection
