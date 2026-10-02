@extends('layouts.lms-guru')

@section('title', 'Daftar Materi')
@section('page-title', 'Materi Pembelajaran')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php
    $args = [$kelas->id, $mapel->id];
    $fileIcons = [
        'pdf' => 'fa-file-pdf bg-rose-50 text-rose-600',
        'ppt' => 'fa-file-powerpoint bg-amber-50 text-amber-600',
        'doc' => 'fa-file-word bg-blue-50 text-blue-600',
        'video' => 'fa-file-video bg-cyan-50 text-cyan-600',
        'link' => 'fa-link bg-slate-100 text-slate-600',
    ];
@endphp

<div class="min-w-0 w-full space-y-5" x-data="{ hapusUrl: '', hapusJudul: '', bukaHapus(el) { this.hapusUrl = el.dataset.url; this.hapusJudul = el.dataset.judul; this.$refs.terkait.checked = false; this.$refs.hapusDialog.showModal(); } }">
    <header class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:px-5 md:flex-row md:items-center md:justify-between">
        <div class="min-w-0">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-book text-indigo-600" aria-hidden="true"></i>Daftar materi</h2>
            <p class="mt-0.5 text-xs text-slate-500">Kelola materi pembelajaran untuk kelas ini.</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <form method="GET" x-data class="flex items-center gap-2">
                <label class="sr-only" for="filter-tanggal">Filter tanggal upload</label>
                <input id="filter-tanggal" type="date" name="tanggal" value="{{ request('tanggal') }}" @change="$el.form.submit()" class="h-10 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                @if(request('tanggal'))
                    <a href="{{ url()->current() }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-500 no-underline hover:bg-slate-50" title="Reset filter" aria-label="Reset filter"><i class="fa-solid fa-xmark" aria-hidden="true"></i></a>
                @endif
            </form>
            <a href="{{ route('guru.lms.materi.create', $args) }}" class="inline-flex min-h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white no-underline shadow-sm hover:bg-indigo-700"><i class="fa-solid fa-circle-plus" aria-hidden="true"></i>Tambah materi</a>
        </div>
    </header>

    @if($materiList->count() > 0)
        @foreach($materiList->groupBy(fn ($item) => $item->tanggal_upload->format('Y-m-d')) as $date => $materis)
            <section class="min-w-0">
                <div class="mb-3 flex items-center gap-3">
                    <span class="shrink-0 rounded-full bg-indigo-600 px-3 py-1 text-[11px] font-bold text-white shadow-sm">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    <span class="hidden h-px flex-1 bg-slate-200 sm:block" aria-hidden="true"></span>
                </div>

                <div class="grid min-w-0 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($materis as $materi)
                        @php [$iconName, $iconTone] = explode(' ', $fileIcons[$materi->tipe_file] ?? 'fa-file bg-slate-100 text-slate-600', 2); @endphp
                        <article class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $iconTone }}"><i class="fa-solid {{ $iconName }}" aria-hidden="true"></i></span>
                                <div class="min-w-0">
                                    <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $materi->judul_materi }}">{{ $materi->judul_materi }}</h3>
                                    <p class="mt-1 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                                        @if($materi->tipe_file)<span class="rounded-md bg-slate-100 px-2 py-0.5 font-bold uppercase text-slate-600">{{ $materi->tipe_file }}</span>@endif
                                        <span><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $materi->created_at->format('H:i') }}</span>
                                        @if($materi->kategori === 'modul_ajar')<span class="rounded-md bg-violet-50 px-2 py-0.5 font-bold text-violet-700">Modul ajar</span>@endif
                                    </p>
                                </div>
                            </div>
                            <p class="mt-3 line-clamp-3 flex-1 text-xs leading-5 text-slate-500">{{ $materi->deskripsi ?? '' }}</p>
                            <div class="mt-4 flex justify-end gap-2 border-t border-slate-100 pt-3">
                                <a href="{{ route('guru.lms.materi.edit', [...$args, $materi->id]) }}" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-amber-50 px-3 text-xs font-bold text-amber-700 no-underline ring-1 ring-inset ring-amber-100 hover:bg-amber-100"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Edit</a>
                                <button type="button" data-url="{{ route('guru.lms.materi.destroy', [...$args, $materi->id]) }}" data-judul="{{ $materi->judul_materi }}" @click="bukaHapus($el)" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-rose-50 px-3 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-100 hover:bg-rose-100"><i class="fa-solid fa-trash" aria-hidden="true"></i>Hapus</button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div>{{ $materiList->appends(request()->query())->links() }}</div>
    @else
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-folder-open" aria-hidden="true"></i></span>
            <h3 class="mt-4 font-extrabold text-slate-900">Belum ada materi</h3>
            <p class="mt-1 text-sm text-slate-500">Mulai dengan menambahkan materi baru untuk kelas ini.</p>
        </section>
    @endif

    <dialog x-ref="hapusDialog" @click.self="$el.close()" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <form method="POST" :action="hapusUrl" data-confirmed="true" class="px-5 py-5">
            @csrf
            @method('DELETE')
            <h3 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-triangle-exclamation text-rose-600" aria-hidden="true"></i>Hapus materi?</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Materi <strong x-text="hapusJudul"></strong> akan dihapus dan tidak dapat dikembalikan.</p>
            <label class="mt-4 flex cursor-pointer items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-800">
                <input type="checkbox" name="hapus_terkait" value="1" x-ref="terkait" class="mt-0.5 h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                Hapus juga materi ini dari kelas lain (jika ada duplikat).
            </label>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$refs.hapusDialog.close()" class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="min-h-10 rounded-xl bg-rose-600 px-4 text-xs font-bold text-white hover:bg-rose-700">Ya, hapus</button>
            </div>
        </form>
    </dialog>
</div>
@endsection
