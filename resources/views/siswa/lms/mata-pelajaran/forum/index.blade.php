@extends('layouts.lms')

@section('title', 'Forum Diskusi - ' . $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Forum Diskusi')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $topikTone = [
        'materi' => 'bg-sky-50 text-sky-700',
        'tugas' => 'bg-amber-50 text-amber-700',
        'ujian' => 'bg-rose-50 text-rose-700',
        'umum' => 'bg-slate-100 text-slate-600',
    ];
@endphp

<div class="min-w-0 w-full space-y-4">
    <nav class="flex min-w-0 flex-wrap items-center gap-2 text-xs font-semibold text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('siswa.lms.dashboard') }}" class="inline-flex items-center gap-1.5 no-underline hover:text-indigo-700"><i class="fa-solid fa-house" aria-hidden="true"></i>Beranda LMS</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="no-underline hover:text-indigo-700">{{ $mataPelajaran->nama_mapel }}</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <span class="text-slate-800">Forum diskusi</span>
    </nav>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <h2 class="flex items-center gap-2 border-b border-slate-100 px-4 py-3 text-base font-extrabold text-slate-900 sm:px-5"><i class="fa-solid fa-comments text-indigo-500" aria-hidden="true"></i>Forum diskusi</h2>

        @if($diskusi->count() > 0)
            <ul class="divide-y divide-slate-100">
                @foreach($diskusi as $item)
                    <li>
                        <a href="{{ route('siswa.lms.mapel.forum.show', [$mataPelajaran->id, $item->id]) }}" class="group flex gap-3 px-4 py-4 no-underline transition hover:bg-indigo-50/50 sm:px-5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-violet-600 text-sm font-extrabold text-white">{{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="flex flex-wrap items-center gap-1.5">
                                    @if($item->is_pinned)<i class="fa-solid fa-thumbtack text-xs text-amber-500" title="Disematkan" aria-label="Disematkan"></i>@endif
                                    <span class="text-sm font-extrabold text-slate-900 group-hover:text-indigo-700">{{ $item->judul }}</span>
                                    @if($item->is_closed)<span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-600">Ditutup</span>@endif
                                </span>
                                <span class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-slate-500">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $topikTone[$item->topik] ?? $topikTone['umum'] }}">{{ ucfirst($item->topik) }}</span>
                                    <span>oleh <strong class="text-slate-700">{{ $item->user->name ?? 'Unknown' }}</strong></span>
                                    <span>· {{ $item->created_at->copy()->locale('id')->diffForHumans() }}</span>
                                </span>
                                <span class="mt-1.5 line-clamp-2 block text-xs leading-5 text-slate-600">{{ Str::limit($item->isi, 150) }}</span>
                                <span class="mt-2 flex flex-wrap gap-3 text-[11px] font-bold">
                                    <span class="text-indigo-600"><i class="fa-solid fa-reply mr-1" aria-hidden="true"></i>{{ $item->replies_count ?? $item->replies->count() }} balasan</span>
                                    @if($item->replies->where('is_answer', true)->count() > 0)
                                        <span class="text-emerald-600"><i class="fa-solid fa-circle-check mr-1" aria-hidden="true"></i>Terjawab</span>
                                    @endif
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="px-4 py-12 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-500"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
                <h3 class="mt-3 text-sm font-extrabold text-slate-900">Belum ada diskusi</h3>
                <p class="mt-1 text-xs text-slate-500">Guru belum memulai diskusi untuk mata pelajaran ini.</p>
            </div>
        @endif
    </section>

    @if($diskusi->hasPages())
        <div>{{ $diskusi->links() }}</div>
    @endif
</div>
@endsection
