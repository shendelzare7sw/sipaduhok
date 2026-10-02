@extends('layouts.lms-guru')

@section('title', 'Forum Diskusi')
@section('page-title', 'Diskusi Kelas')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php $args = [$kelas->id, $mapel->id]; @endphp

<div class="min-w-0 w-full space-y-5"
     x-data="{
        aksi: { mode: '', url: '', judul: '', label: '' },
        buka(el) {
            this.aksi = { mode: el.dataset.mode, url: el.dataset.url, judul: el.dataset.judul, label: el.dataset.label };
            this.$refs.aksiDialog.showModal();
        },
     }">
    <header class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="min-w-0">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-comments text-indigo-600" aria-hidden="true"></i>Daftar diskusi kelas</h2>
            <p class="mt-0.5 text-xs text-slate-500">Buka topik untuk membalas, atau sematkan/tutup diskusi dari sini.</p>
        </div>
        <a href="{{ route('guru.lms.forum.create', $args) }}" class="inline-flex min-h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white no-underline shadow-sm hover:bg-indigo-700"><i class="fa-solid fa-plus" aria-hidden="true"></i>Buat diskusi</a>
    </header>

    @if($forums->count() > 0)
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @foreach($forums as $forum)
                @php $dariGuru = $forum->isFromTeacher(); @endphp
                <article class="min-w-0 border-b border-slate-100 p-4 last:border-b-0 sm:p-5 {{ $forum->is_pinned ? 'bg-amber-50/40' : '' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if($forum->is_pinned)<span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-extrabold text-amber-800"><i class="fa-solid fa-thumbtack mr-1" aria-hidden="true"></i>Disematkan</span>@endif
                            @if($forum->is_closed)<span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-[10px] font-extrabold text-slate-700"><i class="fa-solid fa-lock mr-1" aria-hidden="true"></i>Ditutup</span>@endif
                            <span class="rounded-full bg-sky-50 px-2.5 py-0.5 text-[10px] font-extrabold text-sky-700 ring-1 ring-inset ring-sky-200">{{ ucfirst($forum->topik) }}</span>
                            <span class="text-[11px] text-slate-400">{{ $forum->created_at->copy()->locale('id')->diffForHumans() }}</span>
                        </div>
                        <div class="flex gap-1.5">
                            <x-cleanflow.table-action tone="featured" icon="fa-solid fa-thumbtack" :label="$forum->is_pinned ? 'Lepas pin '.$forum->judul : 'Sematkan '.$forum->judul" data-mode="sync" data-url="{{ route('guru.lms.forum.togglePin', [...$args, $forum->id]) }}" data-judul="{{ $forum->judul }}" data-label="{{ $forum->is_pinned ? 'Lepas pin diskusi' : 'Sematkan diskusi' }}" x-on:click="buka($el)" />
                            <x-cleanflow.table-action tone="neutral" :icon="$forum->is_closed ? 'fa-solid fa-lock-open' : 'fa-solid fa-lock'" :label="$forum->is_closed ? 'Buka kembali '.$forum->judul : 'Tutup '.$forum->judul" data-mode="sync" data-url="{{ route('guru.lms.forum.toggleClose', [...$args, $forum->id]) }}" data-judul="{{ $forum->judul }}" data-label="{{ $forum->is_closed ? 'Buka kembali diskusi' : 'Tutup diskusi' }}" x-on:click="buka($el)" />
                            <x-cleanflow.table-action tone="delete" icon="fa-solid fa-trash" label="Hapus {{ $forum->judul }}" data-mode="delete" data-url="{{ route('guru.lms.forum.destroy', [...$args, $forum->id]) }}" data-judul="{{ $forum->judul }}" data-label="Hapus diskusi" x-on:click="buka($el)" />
                        </div>
                    </div>

                    <a href="{{ route('guru.lms.forum.show', [...$args, $forum->id]) }}" class="mt-2 block no-underline">
                        <h3 class="break-words text-base font-extrabold text-slate-900 hover:text-indigo-700">{{ $forum->judul }}</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ Str::limit(strip_tags($forum->isi), 130) }}</p>
                    </a>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="flex items-center gap-2 font-semibold text-slate-600">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full text-[11px] font-extrabold text-white {{ $dariGuru ? 'bg-emerald-600' : 'bg-indigo-600' }}">{{ substr($forum->user->name ?? 'U', 0, 1) }}</span>
                                {{ $forum->user->name ?? 'Tidak diketahui' }}
                                @if($dariGuru)<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Guru</span>@endif
                            </span>
                            <span class="text-slate-500"><i class="fa-regular fa-comment mr-1" aria-hidden="true"></i>{{ $forum->replies_count ?? $forum->replies()->count() }} balasan</span>
                        </div>
                        <a href="{{ route('guru.lms.forum.show', [...$args, $forum->id]) }}" class="inline-flex items-center gap-1.5 font-bold text-indigo-700 no-underline hover:text-indigo-800">Buka diskusi<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </article>
            @endforeach
        </section>
        <div>{{ $forums->links() }}</div>
    @else
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
            <h3 class="mt-4 font-extrabold text-slate-900">Belum ada diskusi di kelas ini</h3>
        </section>
    @endif

    <dialog x-ref="aksiDialog" @click.self="$el.close()" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <form method="POST" :action="aksi.url" data-confirmed="true" class="px-5 py-5">
            @csrf
            <input type="hidden" name="_method" :value="aksi.mode === 'delete' ? 'DELETE' : 'PATCH'">
            <h3 class="flex items-center gap-2 text-base font-extrabold text-slate-900">
                <i class="fa-solid" :class="aksi.mode === 'delete' ? 'fa-triangle-exclamation text-rose-600' : 'fa-circle-question text-indigo-600'" aria-hidden="true"></i>
                <span x-text="aksi.label"></span>
            </h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Diskusi <strong x-text="aksi.judul"></strong><span x-text="aksi.mode === 'delete' ? ' akan dihapus beserta seluruh balasannya.' : ' akan diperbarui.'"></span></p>

            <template x-if="aksi.mode === 'delete'">
                <label class="mt-4 flex cursor-pointer items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-800">
                    <input type="checkbox" name="hapus_terkait" value="1" class="mt-0.5 h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                    Hapus juga diskusi ini dari kelas lain (jika ada duplikat).
                </label>
            </template>
            <template x-if="aksi.mode === 'sync'">
                <label class="mt-4 flex cursor-pointer items-start gap-2.5 rounded-xl border border-indigo-200 bg-indigo-50 p-3 text-xs text-indigo-900">
                    <input type="hidden" name="sync_kelas" value="0">
                    <input type="checkbox" name="sync_kelas" value="1" checked class="mt-0.5 h-4 w-4 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                    <span><strong class="block">Terapkan juga ke kelas lain</strong>Aksi diterapkan pada diskusi berjudul sama di kelas yang Anda ampu (jika ada).</span>
                </label>
            </template>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$refs.aksiDialog.close()" class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="min-h-10 rounded-xl px-4 text-xs font-bold text-white" :class="aksi.mode === 'delete' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-indigo-600 hover:bg-indigo-700'" x-text="aksi.mode === 'delete' ? 'Ya, hapus' : 'Ya, lanjutkan'">Ya, lanjutkan</button>
            </div>
        </form>
    </dialog>
</div>
@endsection
