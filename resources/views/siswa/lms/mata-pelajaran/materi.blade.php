@extends('layouts.lms')

@section('title', 'Materi - ' . $materi->judul_materi)
@section('page-title', $materi->mataPelajaran->nama_mapel)
@section('page-subtitle', 'Detail Materi Pembelajaran')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-4">
    <nav class="flex min-w-0 flex-wrap items-center gap-2 text-xs font-semibold text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('siswa.lms.dashboard') }}" class="inline-flex items-center gap-1.5 no-underline hover:text-indigo-700"><i class="fa-solid fa-house" aria-hidden="true"></i>Beranda LMS</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <a href="{{ route('siswa.lms.mapel.show', $materi->mata_pelajaran_id) }}" class="no-underline hover:text-indigo-700">{{ $materi->mataPelajaran->nama_mapel }}</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <span class="max-w-[16rem] truncate text-slate-800">{{ $materi->judul_materi }}</span>
    </nav>

    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-100 bg-gradient-to-br from-indigo-50 to-violet-50 p-4 sm:p-6">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-0.5 text-[11px] font-bold text-indigo-700 ring-1 ring-inset ring-indigo-200"><i class="fa-solid fa-book-open" aria-hidden="true"></i>Materi</span>
            <h2 class="mt-2 text-lg font-extrabold leading-snug text-slate-900 sm:text-2xl">{{ $materi->judul_materi }}</h2>
            <dl class="mt-3 flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-slate-600">
                <div class="flex items-center gap-1.5"><dt><i class="fa-solid fa-user-tie text-indigo-400" aria-hidden="true"></i><span class="sr-only">Guru</span></dt><dd class="font-semibold">{{ $materi->guru->nama_lengkap }}</dd></div>
                <div class="flex items-center gap-1.5"><dt><i class="fa-regular fa-calendar text-indigo-400" aria-hidden="true"></i><span class="sr-only">Tanggal</span></dt><dd class="font-semibold">{{ $materi->tanggal_upload->translatedFormat('d F Y') }}</dd></div>
                @if($materi->tipe_file)
                    <div class="flex items-center gap-1.5"><dt><i class="fa-solid fa-file text-indigo-400" aria-hidden="true"></i><span class="sr-only">Tipe</span></dt><dd class="font-semibold">{{ strtoupper($materi->tipe_file) }}</dd></div>
                @endif
            </dl>
        </header>

        <div class="space-y-5 p-4 sm:p-6">
            @if($materi->deskripsi)
                <section>
                    <h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-align-left text-indigo-500" aria-hidden="true"></i>Deskripsi materi</h3>
                    <p class="mt-2 whitespace-pre-line break-words text-sm leading-7 text-slate-700">{{ $materi->deskripsi }}</p>
                </section>
            @endif

            @if($materi->file_materi)
                <section class="flex flex-col items-center gap-3 rounded-2xl border border-dashed border-indigo-200 bg-indigo-50/40 px-4 py-6 text-center">
                    @if($materi->tipe_file === 'link')
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-xl text-indigo-600 shadow-sm"><i class="fa-solid fa-link" aria-hidden="true"></i></span>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Link materi</h3>
                            <p class="text-xs text-slate-500">Link eksternal ke materi pembelajaran</p>
                        </div>
                        <a href="{{ $materi->file_materi }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-bold text-white no-underline transition hover:bg-indigo-700"><i class="fa-solid fa-up-right-from-square" aria-hidden="true"></i>Buka link</a>
                    @else
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-xl text-indigo-600 shadow-sm"><i class="fa-solid fa-file-lines" aria-hidden="true"></i></span>
                        <x-file-preview :path="$materi->file_materi" :title="$materi->judul_materi" label="Lihat Materi" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-bold text-white hover:bg-indigo-700" />
                    @endif
                </section>
            @else
                <p class="flex items-center gap-2 rounded-xl bg-amber-50 px-3 py-2.5 text-sm text-amber-800"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>Belum ada file materi yang diunggah</p>
            @endif

            <a href="{{ route('siswa.lms.mapel.show', $materi->mata_pelajaran_id) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline transition hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke mata pelajaran
            </a>
        </div>
    </article>

    <section class="rounded-2xl border border-slate-200 border-l-4 border-l-sky-500 bg-white p-4 shadow-sm">
        <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-lightbulb text-amber-500" aria-hidden="true"></i>Tips belajar</h2>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-xs leading-5 text-slate-600">
            <li>Baca materi dengan seksama sebelum mengerjakan tugas.</li>
            <li>Catat hal-hal penting untuk memudahkan belajar.</li>
            <li>Jika ada yang tidak dipahami, tanyakan di forum diskusi.</li>
            <li>Unduh materi untuk dipelajari secara offline.</li>
        </ul>
    </section>
</div>
@endsection
