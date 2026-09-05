@extends('layouts.app')

@section('title', 'Kelola Berita')
@section('page-title', 'Kelola Berita')
@section('page-subtitle', 'Atur informasi publik, status tayang, dan berita unggulan')

@section('content')
@php
    $routeBase = request()->routeIs('sekretaris.*') ? 'sekretaris' : 'admin.akademik';
    $items = is_object($berita) && method_exists($berita, 'getCollection') ? $berita->getCollection() : collect($berita);
    $total = is_object($berita) && method_exists($berita, 'total') ? $berita->total() : $items->count();
    $statusTones = ['aktif' => 'bg-emerald-50 text-emerald-700', 'draft' => 'bg-amber-50 text-amber-700', 'arsip' => 'bg-slate-100 text-slate-600'];
@endphp

<div class="min-w-0 w-full space-y-5" x-data="{
    busy: null,
    async toggleFeatured(id, title, featured) {
        const result = await Swal.fire({
            title: featured ? 'Hapus dari berita unggulan?' : 'Jadikan berita unggulan?',
            text: title,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: featured ? 'Ya, hapus dari unggulan' : 'Ya, jadikan unggulan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb'
        });
        if (!result.isConfirmed) return;
        this.busy = id;
        try {
            const endpoint = @js(route($routeBase . '.berita.toggle-featured', ['id' => '__ID__'])).replace('__ID__', id);
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Status unggulan gagal diperbarui.');
            await Swal.fire({ title: 'Berhasil', text: data.message, icon: 'success', timer: 1100, showConfirmButton: false });
            window.location.reload();
        } catch (error) {
            Swal.fire({ title: 'Tidak dapat memproses', text: error.message, icon: 'error' });
        } finally {
            this.busy = null;
        }
    }
}">
    <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
        @foreach([
            ['label' => 'Total Berita', 'value' => $total, 'tone' => 'bg-brand-50 text-brand-700'],
            ['label' => 'Aktif', 'value' => $items->where('status', 'aktif')->count(), 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Unggulan', 'value' => $items->where('is_featured', true)->count(), 'tone' => 'bg-amber-50 text-amber-700'],
            ['label' => 'Kategori', 'value' => count($kategoriOptions ?? []), 'tone' => 'bg-violet-50 text-violet-700'],
        ] as $stat)
            <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p><p class="mt-2 text-xl font-extrabold text-slate-900">{{ number_format($stat['value']) }}</p><span class="mt-2 block h-1.5 w-10 rounded-full {{ $stat['tone'] }}"></span></article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div class="min-w-0"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-newspaper text-brand-600" aria-hidden="true"></i>Daftar Berita</h2><p class="mt-1 text-xs text-slate-500">Konten yang ditampilkan pada kanal publik sekolah.</p></div><a href="{{ route($routeBase . '.berita.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i>Tambah Berita</a></div>
            <form method="GET" action="{{ route($routeBase . '.berita.index') }}" class="mt-4 grid gap-2 sm:grid-cols-[minmax(220px,1fr)_180px_160px_auto]">
                <label class="relative min-w-0"><span class="sr-only">Cari berita</span><i class="fas fa-search pointer-events-none absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..." class="h-10 w-full rounded-xl border border-slate-200 bg-white !pl-10 pr-3 text-xs outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"></label>
                <select name="kategori" data-auto-submit class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 outline-none focus:border-brand-500"><option value="all">Semua Kategori</option>@foreach(($kategoriOptions ?? []) as $value => $label)<option value="{{ $value }}" {{ request('kategori') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select>
                <select name="status" data-auto-submit class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 outline-none focus:border-brand-500"><option value="all">Semua Status</option>@foreach(($statusOptions ?? []) as $value => $label)<option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select>
                <div class="flex gap-2"><button type="submit" class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white hover:bg-slate-900"><i class="fas fa-search" aria-hidden="true"></i>Cari</button>@if(request()->hasAny(['search', 'kategori', 'status']))<a href="{{ route($routeBase . '.berita.index') }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-red-50 px-3 text-red-700 no-underline hover:bg-red-100" aria-label="Reset filter"><i class="fas fa-times" aria-hidden="true"></i></a>@endif</div>
            </form>
        </header>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($items as $item)
                @php
                    $status = strtolower($item->status ?? 'draft');
                @endphp
                <article class="p-4"><div class="flex gap-3"><img src="{{ $item->gambar_url }}" alt="" class="h-20 w-24 shrink-0 rounded-xl object-cover"><div class="min-w-0"><div class="flex flex-wrap items-center gap-1.5"><span class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700">{{ $item->kategori_label }}</span>@if($item->is_featured)<span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-700"><i class="fas fa-star mr-1" aria-hidden="true"></i>Unggulan</span>@endif</div><h3 class="mt-2 break-words text-sm font-extrabold text-slate-900">{{ $item->judul }}</h3><p class="mt-1 text-[11px] text-slate-500">{{ $item->tanggal_berita?->format('d M Y') ?? '-' }} · Urutan {{ $item->urutan_tampil }}</p></div></div><p class="mt-3 text-xs leading-5 text-slate-600">{{ Str::limit($item->deskripsi_singkat, 135) }}</p><div class="mt-3 flex items-center justify-between"><span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusTones[$status] ?? $statusTones['arsip'] }}">{{ ucfirst($status) }}</span><a href="{{ $item->url_berita }}" target="_blank" rel="noopener" class="text-xs font-bold text-brand-700 no-underline hover:text-brand-800">Buka berita <i class="fas fa-arrow-up-right-from-square ml-1" aria-hidden="true"></i></a></div><div class="mt-4 grid grid-cols-3 gap-2"><button type="button" @click="toggleFeatured({{ $item->id }}, @js($item->judul), {{ $item->is_featured ? 'true' : 'false' }})" :disabled="busy === {{ $item->id }}" class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 hover:bg-amber-100 disabled:opacity-50"><i class="{{ $item->is_featured ? 'fas' : 'far' }} fa-star" aria-hidden="true"></i>Unggulan</button><a href="{{ route($routeBase . '.berita.edit', $item->id) }}" class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg bg-blue-50 text-xs font-bold text-blue-700 no-underline hover:bg-blue-100"><i class="fas fa-edit" aria-hidden="true"></i>Edit</a><form action="{{ route($routeBase . '.berita.destroy', $item->id) }}" method="POST" data-confirm data-confirm-title="Hapus berita?" data-confirm-message="{{ $item->judul }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-1 rounded-lg bg-red-50 text-xs font-bold text-red-700 hover:bg-red-100"><i class="fas fa-trash" aria-hidden="true"></i>Hapus</button></form></div></article>
            @empty
                <div class="px-5 py-14 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-newspaper" aria-hidden="true"></i></span><h3 class="mt-4 text-sm font-extrabold text-slate-800">Berita tidak ditemukan</h3><p class="mt-1 text-xs text-slate-500">Tambah berita pertama atau ubah filter pencarian.</p></div>
            @endforelse
        </div>

        @if($items->isNotEmpty())
            <div class="hidden overflow-x-auto lg:block"><table class="w-full min-w-[1060px] table-fixed border-collapse text-left text-sm"><colgroup><col class="w-24"><col><col class="w-32"><col class="w-36"><col class="w-24"><col class="w-40"></colgroup><thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Gambar</th><th class="px-3 py-3">Berita</th><th class="px-3 py-3">Kategori</th><th class="px-3 py-3">Tanggal</th><th class="px-3 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100">
            @foreach($items as $item)
                @php
                    $status = strtolower($item->status ?? 'draft');
                @endphp
                <tr class="hover:bg-slate-50/80"><td class="px-4 py-4"><img src="{{ $item->gambar_url }}" alt="" class="h-14 w-16 rounded-lg object-cover"></td><td class="min-w-0 px-3 py-4"><div class="flex min-w-0 items-center gap-2"><p class="truncate font-bold text-slate-900" title="{{ $item->judul }}">{{ $item->judul }}</p>@if($item->is_featured)<i class="fas fa-star shrink-0 text-amber-500" title="Berita unggulan" aria-hidden="true"></i>@endif</div><p class="mt-1 truncate text-xs text-slate-500" title="{{ $item->deskripsi_singkat }}">{{ $item->deskripsi_singkat }}</p><a href="{{ $item->url_berita }}" target="_blank" rel="noopener" class="mt-1 block truncate text-[11px] font-semibold text-brand-700 no-underline">{{ $item->url_berita }}</a></td><td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-700">{{ $item->kategori_label }}</span></td><td class="whitespace-nowrap px-3 py-4 text-xs text-slate-600">{{ $item->tanggal_berita?->format('d M Y') ?? '-' }}<span class="mt-1 block text-[10px] text-slate-400">Urutan {{ $item->urutan_tampil }}</span></td><td class="px-3 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusTones[$status] ?? $statusTones['arsip'] }}">{{ ucfirst($status) }}</span></td><td class="px-4 py-4"><div class="flex items-center justify-end gap-2"><x-cleanflow.table-action type="button" tone="featured" icon="{{ $item->is_featured ? 'fas' : 'far' }} fa-star" label="Ubah status unggulan" data-title="{{ $item->judul }}" x-on:click="toggleFeatured({{ $item->id }}, $el.dataset.title, {{ $item->is_featured ? 'true' : 'false' }})" x-bind:disabled="busy === {{ $item->id }}" /><x-cleanflow.table-action href="{{ route($routeBase . '.berita.edit', $item->id) }}" tone="edit" icon="fas fa-edit" label="Edit berita" /><form action="{{ route($routeBase . '.berita.destroy', $item->id) }}" method="POST" data-confirm data-confirm-title="Hapus berita?" data-confirm-message="{{ $item->judul }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus berita" /></form></div></td></tr>
            @endforeach
            </tbody></table></div>
        @endif

        @if(is_object($berita) && method_exists($berita, 'hasPages') && $berita->hasPages())<footer class="border-t border-slate-200 px-4 py-3">{{ $berita->withQueryString()->links() }}</footer>@endif
    </section>
</div>
@endsection
