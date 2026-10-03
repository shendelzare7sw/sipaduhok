@extends('layouts.lms')

@section('title', 'Kelas Virtual')
@section('page-title', 'Kelas Virtual (Meeting)')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-4">
    <nav class="flex min-w-0 flex-wrap items-center gap-2 text-xs font-semibold text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('siswa.lms.dashboard') }}" class="inline-flex items-center gap-1.5 no-underline hover:text-indigo-700"><i class="fa-solid fa-house" aria-hidden="true"></i>Beranda LMS</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="no-underline hover:text-indigo-700">{{ $mataPelajaran->nama_mapel }}</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <span class="text-slate-800">Kelas virtual</span>
    </nav>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <h2 class="flex items-center gap-2 border-b border-slate-100 px-4 py-3 text-base font-extrabold text-slate-900 sm:px-5"><i class="fa-solid fa-video text-teal-500" aria-hidden="true"></i>Daftar kelas virtual</h2>

        <div class="space-y-3 p-3 sm:p-4">
            @forelse($meetings as $meeting)
                @php
                    [$statusTone, $statusIcon, $statusText] = match (true) {
                        $meeting->waktu_mulai->isFuture() => ['bg-amber-50 text-amber-700', 'fa-clock', 'Akan datang'],
                        (bool) $meeting->is_active => ['bg-emerald-50 text-emerald-700', 'fa-circle-check', 'Sedang berlangsung'],
                        default => ['bg-slate-100 text-slate-600', 'fa-flag-checkered', 'Selesai'],
                    };
                @endphp
                <article class="flex flex-col gap-4 rounded-2xl border p-4 lg:flex-row lg:items-center lg:justify-between {{ $meeting->is_active ? 'border-teal-200 bg-teal-50/30' : 'border-slate-200' }}">
                    <div class="min-w-0">
                        <div class="flex flex-wrap gap-1.5">
                            <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700"><i class="fa-solid fa-video mr-1" aria-hidden="true"></i>{{ ucfirst(str_replace('_', ' ', $meeting->platform)) }}</span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusTone }}"><i class="fa-solid {{ $statusIcon }} mr-1" aria-hidden="true"></i>{{ $statusText }}</span>
                        </div>
                        <h3 class="mt-2 text-sm font-extrabold text-slate-900 sm:text-base">{{ $meeting->judul }}</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            <i class="fa-regular fa-calendar mr-1" aria-hidden="true"></i>{{ $meeting->waktu_mulai->translatedFormat('l, d F Y') }}
                            · <i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $meeting->waktu_mulai->format('H:i') }} – {{ $meeting->waktu_selesai ? $meeting->waktu_selesai->format('H:i') : 'Selesai' }}
                        </p>
                        @if($meeting->deskripsi)
                            <p class="mt-2 whitespace-pre-line text-xs leading-5 text-slate-600">{{ $meeting->deskripsi }}</p>
                        @endif
                    </div>
                    <div class="shrink-0">
                        @if($meeting->is_active)
                            <a href="{{ $meeting->link_meeting }}" target="_blank" rel="noopener" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 text-sm font-bold text-white no-underline shadow-sm transition hover:bg-teal-700 lg:w-auto"><i class="fa-solid fa-video" aria-hidden="true"></i>Masuk meeting</a>
                        @else
                            <button type="button" disabled class="inline-flex min-h-11 w-full cursor-not-allowed items-center justify-center rounded-xl bg-slate-100 px-5 text-sm font-bold text-slate-400 lg:w-auto">Link kedaluwarsa</button>
                        @endif
                    </div>
                </article>
            @empty
                <div class="px-4 py-12 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-50 text-2xl text-teal-500"><i class="fa-solid fa-video" aria-hidden="true"></i></span>
                    <p class="mt-3 text-sm text-slate-500">Belum ada jadwal meeting yang tersedia.</p>
                </div>
            @endforelse
        </div>

        @if($meetings->hasPages())
            <div class="border-t border-slate-100 px-4 py-3 sm:px-5">{{ $meetings->links() }}</div>
        @endif
    </section>
</div>
@endsection
