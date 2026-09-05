@extends('layouts.app')

@section('title', 'Kelola Flyer')
@section('page-title', 'Kelola Flyer')
@section('page-subtitle', 'Atur pop-up informasi berdasarkan target dan periode tayang')

@section('content')
@php
    $routeBase = request()->routeIs('sekretaris.*') ? 'sekretaris' : 'admin.akademik';
    $items = is_object($flyer) && method_exists($flyer, 'getCollection') ? $flyer->getCollection() : collect($flyer);
    $total = is_object($flyer) && method_exists($flyer, 'total') ? $flyer->total() : $items->count();
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="grid grid-cols-3 gap-2 sm:gap-3">
        @foreach([
            ['label' => 'Total', 'value' => $total, 'tone' => 'bg-brand-50 text-brand-700'],
            ['label' => 'Aktif', 'value' => $items->where('status', 'aktif')->count(), 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Belum Aktif', 'value' => $items->where('status', '!=', 'aktif')->count(), 'tone' => 'bg-slate-100 text-slate-700'],
        ] as $stat)
            <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4"><p class="truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p><p class="mt-2 text-xl font-extrabold text-slate-900">{{ number_format($stat['value']) }}</p><span class="mt-2 block h-1.5 w-10 rounded-full {{ $stat['tone'] }}"></span></article>
        @endforeach
    </section>

    <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div class="min-w-0"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-images text-brand-600" aria-hidden="true"></i>Daftar Flyer</h2><p class="mt-1 text-xs text-slate-500">Hanya flyer aktif dalam periode tayang yang muncul kepada pengguna.</p></div><a href="{{ route($routeBase . '.flyer.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i>Tambah Flyer</a></header>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
            @forelse($items as $item)
                @php
                    $active = ($item->status ?? '') === 'aktif';
                @endphp
                <article class="group min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100"><img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]"><div class="absolute inset-x-0 top-0 flex items-start justify-between p-3"><span class="rounded-full px-2.5 py-1 text-[10px] font-extrabold shadow-sm {{ $active ? 'bg-emerald-50 text-emerald-700' : 'bg-white text-slate-600' }}">{{ $item->status_badge['label'] ?? ucfirst($item->status) }}</span><span class="rounded-full bg-slate-900/75 px-2.5 py-1 text-[10px] font-bold text-white backdrop-blur">Urutan {{ $item->urutan_tampil }}</span></div></div>
                    <div class="p-4"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $item->judul }}</h3><p class="mt-1 min-h-10 text-xs leading-5 text-slate-500">{{ Str::limit($item->deskripsi ?? 'Tanpa deskripsi', 90) }}</p><dl class="mt-4 grid grid-cols-2 gap-2 rounded-xl bg-slate-50 p-3 text-[11px]"><div><dt class="font-bold uppercase tracking-wide text-slate-400">Target</dt><dd class="mt-1 font-bold text-slate-700">{{ $item->target_label }}</dd></div><div><dt class="font-bold uppercase tracking-wide text-slate-400">Periode</dt><dd class="mt-1 whitespace-nowrap font-bold text-slate-700">{{ $item->tanggal_mulai?->format('d M') ?? '-' }}–{{ $item->tanggal_selesai?->format('d M Y') ?? '-' }}</dd></div></dl>@if($item->link_url)<a href="{{ $item->link_url }}" target="_blank" rel="noopener" class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-50 px-3 py-2 text-xs font-bold text-brand-700 no-underline hover:bg-brand-100"><i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>Buka tautan</a>@endif<div class="mt-3 grid grid-cols-2 gap-2"><a href="{{ route($routeBase . '.flyer.edit', $item->id) }}" class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100"><i class="fas fa-edit" aria-hidden="true"></i>Edit</a><form action="{{ route($routeBase . '.flyer.destroy', $item->id) }}" method="POST" data-confirm data-confirm-title="Hapus flyer?" data-confirm-message="{{ $item->judul }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-1.5 rounded-lg bg-red-50 text-xs font-bold text-red-700 hover:bg-red-100"><i class="fas fa-trash" aria-hidden="true"></i>Hapus</button></form></div></div>
                </article>
            @empty
                <div class="py-14 text-center sm:col-span-2 xl:col-span-3 2xl:col-span-4"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-images" aria-hidden="true"></i></span><h3 class="mt-4 text-sm font-extrabold text-slate-800">Belum ada flyer</h3><p class="mt-1 text-xs text-slate-500">Tambahkan pop-up informasi pertama untuk pengguna.</p></div>
            @endforelse
        </div>

        @if(is_object($flyer) && method_exists($flyer, 'hasPages') && $flyer->hasPages())<footer class="mt-5 border-t border-slate-200 pt-4">{{ $flyer->withQueryString()->links() }}</footer>@endif
    </section>
</div>
@endsection
