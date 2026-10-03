@extends('layouts.lms')

@section('title', $diskusi->judul)
@section('page-title', 'Diskusi Forum')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $dariGuru = $diskusi->isFromTeacher();
    // URL untuk partial balasan bersama (resources/views/lms/forum/reply-item.blade.php).
    $isClosed = (bool) $diskusi->is_closed;
    $forumUrls = [
        'reply' => route('siswa.lms.mapel.forum.reply', [$mataPelajaran->id, $diskusi->id]),
        'update' => fn ($replyId) => route('siswa.lms.mapel.forum.reply.update', [$mataPelajaran->id, $diskusi->id, $replyId]),
        'destroy' => fn ($replyId) => route('siswa.lms.mapel.forum.reply.destroy', [$mataPelajaran->id, $diskusi->id, $replyId]),
    ];
    $field = 'block h-10 w-full min-w-0 rounded-xl border border-slate-300 bg-white text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

<div class="min-w-0 w-full space-y-5"
     x-data="{
        q: '', peran: 'all', pengirim: 'all',
        cocok(el) {
            const q = this.q.trim().toLowerCase();
            const teks = (el.querySelector('[data-post-content]')?.textContent || '').toLowerCase();
            return (q === '' || teks.includes(q) || (el.dataset.author || '').toLowerCase().includes(q))
                && (this.peran === 'all' || el.dataset.role === this.peran)
                && (this.pengirim === 'all' || el.dataset.isMine === 'true');
        },
        get aktif() { return this.q !== '' || this.peran !== 'all' || this.pengirim !== 'all'; },
        hasil() {
            const posts = [...this.$root.querySelectorAll('[data-post]')];
            return `Menampilkan ${posts.filter((el) => this.cocok(el)).length} dari ${posts.length} pesan`;
        },
        reset() { this.q = ''; this.peran = 'all'; this.pengirim = 'all'; },
     }">
    <nav class="flex min-w-0 flex-wrap items-center gap-2 text-xs font-semibold text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('siswa.lms.dashboard') }}" class="inline-flex items-center gap-1.5 no-underline hover:text-indigo-700"><i class="fa-solid fa-house" aria-hidden="true"></i>Beranda LMS</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="no-underline hover:text-indigo-700">{{ $mataPelajaran->nama_mapel }}</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}" class="no-underline hover:text-indigo-700">Forum</a>
    </nav>

    <header class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:px-5">
        <div class="flex flex-wrap items-center gap-1.5">
            @if($diskusi->is_pinned)<span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-extrabold text-amber-800"><i class="fa-solid fa-thumbtack mr-1" aria-hidden="true"></i>Disematkan</span>@endif
            @if($diskusi->is_closed)<span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-extrabold text-slate-700"><i class="fa-solid fa-lock mr-1" aria-hidden="true"></i>Ditutup — balasan baru dinonaktifkan</span>@endif
        </div>
        <h2 class="mt-2 break-words text-lg font-extrabold text-slate-900 sm:text-xl">{{ $diskusi->judul }}</h2>

        <div class="mt-4 grid grid-cols-2 gap-2 lg:grid-cols-[minmax(0,1fr)_10rem_10rem_auto]">
            <label class="col-span-2 lg:col-span-1"><span class="sr-only">Cari pesan</span><input type="search" x-model.debounce.200ms="q" placeholder="Cari pesan atau nama..." class="{{ $field }} px-3"></label>
            <label><span class="sr-only">Filter peran</span><select x-model="peran" class="{{ $field }} pl-3 pr-8"><option value="all">Semua peran</option><option value="student">Siswa</option><option value="teacher">Guru</option></select></label>
            <label><span class="sr-only">Filter pengirim</span><select x-model="pengirim" class="{{ $field }} pl-3 pr-8"><option value="all">Semua pengirim</option><option value="my">Pesan saya</option></select></label>
            <button type="button" x-on:click="reset()" class="col-span-2 inline-flex h-10 items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 text-xs font-bold text-slate-600 hover:bg-slate-50 lg:col-span-1"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i>Reset</button>
        </div>
        <p x-cloak x-show="aktif" x-text="hasil()" class="mt-2 text-xs font-semibold text-indigo-700"></p>
    </header>

    {{-- Topik utama --}}
    <article data-post data-author="{{ $diskusi->user->name ?? 'User' }}" data-role="{{ $dariGuru ? 'teacher' : 'student' }}" data-is-mine="{{ $diskusi->user_id == auth()->id() ? 'true' : 'false' }}"
             x-show="cocok($el)" x-data="{ balas: false, lampir: false, berkas: [] }"
             class="min-w-0 rounded-2xl border border-indigo-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full text-sm font-extrabold text-white {{ $dariGuru ? 'bg-emerald-600' : 'bg-indigo-600' }}">
                @if($diskusi->user && $diskusi->user->foto_profil)
                    <img src="{{ asset('storage/' . $diskusi->user->foto_profil) }}" alt="{{ $diskusi->user->name }}" class="h-full w-full object-cover">
                @else
                    {{ substr($diskusi->user->name ?? '?', 0, 2) }}
                @endif
            </span>
            <div class="min-w-0">
                <p class="flex flex-wrap items-center gap-2 text-sm font-extrabold text-slate-900">{{ $diskusi->user->name ?? 'User' }}<span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $dariGuru ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700' }}">{{ $dariGuru ? 'Guru (penulis)' : 'Siswa' }}</span></p>
                <p class="text-[11px] text-slate-500">{{ $diskusi->created_at->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}</p>
            </div>
        </div>

        <div data-post-content class="mt-4 break-words text-sm leading-7 text-slate-700">
            <x-lms.rich-text :text="$diskusi->isi" />
            @if($diskusi->lampiran && is_array($diskusi->lampiran) && count($diskusi->lampiran) > 0)
                <div class="mt-3 flex flex-wrap gap-2 sm:gap-3">@foreach($diskusi->lampiran as $lampiran)<x-lms.media-display :file="$lampiran" group="topik-{{ $diskusi->id }}" />@endforeach</div>
            @endif
        </div>

        @unless($isClosed)
            <button type="button" x-on:click="balas = ! balas" class="mt-4 inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-indigo-50 px-3 text-xs font-bold text-indigo-700 hover:bg-indigo-100"><i class="fa-solid fa-reply" aria-hidden="true"></i>Balas</button>
            <form x-cloak x-show="balas" action="{{ $forumUrls['reply'] }}" method="POST" enctype="multipart/form-data" class="mt-3 space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                @csrf
                <textarea name="isi" rows="3" placeholder="Tulis balasan Anda..." required data-autogrow class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"></textarea>
                <button type="button" x-on:click="lampir = ! lampir" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-indigo-700"><i class="fa-solid fa-paperclip" aria-hidden="true"></i>Lampirkan file</button>
                <div x-show="lampir" class="rounded-xl border border-dashed border-slate-300 bg-white p-3">
                    <input type="file" name="attachment[]" multiple x-on:change="berkas = [...$event.target.files].map((f) => f.name)" class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-bold file:text-indigo-700">
                    <ul class="mt-2 space-y-0.5 text-[11px] text-slate-500"><template x-for="nama in berkas" :key="nama"><li class="truncate" x-text="nama"></li></template></ul>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex min-h-9 items-center rounded-lg bg-indigo-600 px-4 text-xs font-bold text-white hover:bg-indigo-700">Kirim balasan</button>
                    <button type="button" x-on:click="balas = false" class="inline-flex min-h-9 items-center rounded-lg border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                </div>
            </form>
        @endunless
    </article>

    <x-lms.lightbox />

    <div class="space-y-3">
        @foreach($diskusi->replies->whereNull('parent_id') as $reply)
            @include('lms.forum.reply-item', ['reply' => $reply, 'level' => 0])
        @endforeach
    </div>
</div>
@endsection
