@extends('layouts.app')

@section('title', 'Manajemen Landing Page')

@section('page-title', 'Manajemen Landing Page')
@section('page-subtitle', 'Kelola konten halaman landing website')


@section('content')
<div class="min-w-0 w-full space-y-4" data-landing-page-index>
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div class="min-w-0">
                <h2 class="flex items-center gap-2 text-lg font-extrabold text-slate-950"><i class="fas fa-window-maximize text-brand-600" aria-hidden="true"></i>Halaman publik</h2>
                <p class="mt-1 text-sm text-slate-500">Pilih halaman yang ingin diperbarui. Perubahan konten tidak mengubah alamat publiknya.</p>
            </div>
            <span class="inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">{{ $pages->count() }} halaman</span>
        </header>

        @if($pages->isEmpty())
            <div class="px-5 py-16 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-folder-open" aria-hidden="true"></i></span><h3 class="mt-4 font-extrabold text-slate-900">Belum ada halaman</h3><p class="mt-1 text-sm text-slate-500">Data halaman publik belum tersedia.</p></div>
        @else
            <div class="divide-y divide-slate-100 lg:hidden">
                @foreach($pages as $page)
                    <article class="p-4">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><i class="fas fa-file-lines" aria-hidden="true"></i></span>
                            <div class="min-w-0 flex-1"><h3 class="truncate text-sm font-extrabold text-slate-950">{{ $page->title }}</h3><p class="mt-1 truncate font-mono text-xs text-brand-700">/{{ $page->slug === 'home' ? '' : $page->slug }}</p><p class="mt-2 text-xs text-slate-500" title="{{ $page->updated_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}">Diperbarui {{ $page->updated_at?->locale('id')->diffForHumans() ?? '-' }}</p></div>
                        </div>
                        <a href="{{ route('admin.landing-pages.edit', $page->slug) }}" class="mt-3 inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-pen" aria-hidden="true"></i>Edit konten</a>
                    </article>
                @endforeach
            </div>

            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full table-fixed text-left text-sm">
                    <colgroup><col><col class="w-60"><col class="w-64"><col class="w-44"></colgroup>
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Judul halaman</th><th class="px-4 py-3">Alamat publik</th><th class="px-4 py-3">Terakhir diperbarui</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pages as $page)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4"><strong class="block truncate text-slate-950" title="{{ $page->title }}">{{ $page->title }}</strong></td>
                                <td class="px-4 py-4"><code class="inline-flex max-w-full truncate rounded-lg bg-slate-100 px-2 py-1 text-xs font-bold text-brand-700">/{{ $page->slug === 'home' ? '' : $page->slug }}</code></td>
                                <td class="px-4 py-4 text-slate-600" title="{{ $page->updated_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}">{{ $page->updated_at?->locale('id')->diffForHumans() ?? '-' }}</td>
                                <td class="px-5 py-4"><div class="flex justify-end"><a href="{{ route('admin.landing-pages.edit', $page->slug) }}" class="inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-brand-600 px-3 text-xs font-bold text-white no-underline ring-1 ring-brand-600 hover:bg-brand-700"><i class="fas fa-pen" aria-hidden="true"></i>Edit konten</a></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
