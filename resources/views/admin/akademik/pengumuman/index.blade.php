@extends('layouts.app')

@section('title', 'Kelola Pengumuman')
@section('page-title', 'Kelola Pengumuman')
@section('page-subtitle', 'Sampaikan informasi sekolah dengan jelas dan tepat waktu')

@section('content')
@php
    $routeBase = request()->routeIs('sekretaris.*') ? 'sekretaris' : 'admin.akademik';
    $items = is_object($pengumuman) && method_exists($pengumuman, 'getCollection') ? $pengumuman->getCollection() : collect($pengumuman);
    $total = is_object($pengumuman) && method_exists($pengumuman, 'total') ? $pengumuman->total() : $items->count();
    $autoCount = $items->where('is_from_kalender', true)->count();
    $urgentCount = $items->where('prioritas', 'mendesak')->count();
    $priorityTones = [
        'biasa' => 'bg-blue-50 text-blue-700',
        'penting' => 'bg-amber-50 text-amber-700',
        'mendesak' => 'bg-red-50 text-red-700',
    ];
    $statusTones = [
        'aktif' => 'bg-emerald-50 text-emerald-700',
        'draft' => 'bg-amber-50 text-amber-700',
        'arsip' => 'bg-slate-100 text-slate-600',
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="grid grid-cols-3 gap-2 sm:gap-3">
        @foreach([
            ['label' => 'Total', 'value' => $total, 'icon' => 'fas fa-bullhorn', 'tone' => 'bg-brand-50 text-brand-700'],
            ['label' => 'Dari Kalender', 'value' => $autoCount, 'icon' => 'fas fa-calendar-check', 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Mendesak', 'value' => $urgentCount, 'icon' => 'fas fa-exclamation-circle', 'tone' => 'bg-red-50 text-red-700'],
        ] as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-center justify-between gap-2"><div class="min-w-0"><p class="truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p><p class="mt-1 text-xl font-extrabold text-slate-900 sm:text-2xl">{{ number_format($stat['value']) }}</p></div><span class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl sm:flex {{ $stat['tone'] }}"><i class="{{ $stat['icon'] }}" aria-hidden="true"></i></span></div>
            </article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div class="min-w-0"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-list text-brand-600" aria-hidden="true"></i>Daftar Pengumuman</h2><p class="mt-1 text-xs text-slate-500">Pengumuman manual dan informasi yang terhubung ke kalender akademik.</p></div>
            <a href="{{ route($routeBase . '.pengumuman.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i>Tambah Pengumuman</a>
        </header>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($items as $item)
                @php
                    $priority = strtolower($item->prioritas ?? 'biasa');
                    $status = strtolower($item->status ?? 'aktif');
                @endphp
                <article class="min-w-0 p-4">
                    <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $item->judul }}</h3><p class="mt-1 text-xs leading-5 text-slate-500">{{ Str::limit($item->isi_pengumuman, 120) }}</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusTones[$status] ?? $statusTones['arsip'] }}">{{ $item->status_badge['label'] ?? ucfirst($status) }}</span></div>
                    <div class="mt-3 flex flex-wrap gap-2 text-[10px] font-bold"><span class="rounded-full px-2.5 py-1 {{ $priorityTones[$priority] ?? $priorityTones['biasa'] }}">{{ $item->prioritas_badge['label'] ?? ucfirst($priority) }}</span><span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-600"><i class="far fa-calendar mr-1" aria-hidden="true"></i>{{ $item->tanggal_pengumuman?->format('d M Y') ?? '-' }}</span><span class="rounded-full {{ $item->is_from_kalender ? 'bg-emerald-50 text-emerald-700' : 'bg-violet-50 text-violet-700' }} px-2.5 py-1">{{ $item->is_from_kalender ? 'Dari kalender' : 'Manual' }}</span></div>
                    @if($item->lampiran_surat)<a href="{{ asset('storage/' . $item->lampiran_surat) }}" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-brand-700 no-underline hover:text-brand-800"><i class="fas fa-paperclip" aria-hidden="true"></i>Lihat lampiran PDF</a>@endif
                    <div class="mt-4 grid grid-cols-2 gap-2"><a href="{{ route($routeBase . '.pengumuman.edit', $item->id) }}" class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100"><i class="fas fa-edit" aria-hidden="true"></i>Edit</a><form action="{{ route($routeBase . '.pengumuman.destroy', $item->id) }}" method="POST" data-confirm data-confirm-title="Hapus pengumuman?" data-confirm-message="{{ $item->judul }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-1.5 rounded-lg bg-red-50 text-xs font-bold text-red-700 hover:bg-red-100"><i class="fas fa-trash" aria-hidden="true"></i>Hapus</button></form></div>
                </article>
            @empty
                <div class="px-5 py-14 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-bullhorn" aria-hidden="true"></i></span><h3 class="mt-4 text-sm font-extrabold text-slate-800">Belum ada pengumuman</h3><p class="mt-1 text-xs text-slate-500">Tambahkan pengumuman pertama untuk warga sekolah.</p></div>
            @endforelse
        </div>

        @if($items->isNotEmpty())
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full min-w-[1040px] table-fixed border-collapse text-left text-sm">
                    <colgroup><col><col class="w-36"><col class="w-28"><col class="w-32"><col class="w-24"><col class="w-28"></colgroup>
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Pengumuman</th><th class="px-3 py-3">Tanggal</th><th class="px-3 py-3">Prioritas</th><th class="px-3 py-3">Sumber</th><th class="px-3 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($items as $item)
                            @php
                                $priority = strtolower($item->prioritas ?? 'biasa');
                                $status = strtolower($item->status ?? 'aktif');
                            @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="min-w-0 px-4 py-4"><p class="truncate font-bold text-slate-900" title="{{ $item->judul }}">{{ $item->judul }}</p><p class="mt-1 truncate text-xs text-slate-500" title="{{ $item->isi_pengumuman }}">{{ Str::limit($item->isi_pengumuman, 110) }}</p>@if($item->lampiran_surat)<a href="{{ asset('storage/' . $item->lampiran_surat) }}" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-brand-700 no-underline"><i class="fas fa-paperclip" aria-hidden="true"></i>Lampiran</a>@endif</td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs text-slate-600">{{ $item->tanggal_pengumuman?->format('d M Y') ?? '-' }}</td>
                                <td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $priorityTones[$priority] ?? $priorityTones['biasa'] }}">{{ $item->prioritas_badge['label'] ?? ucfirst($priority) }}</span></td>
                                <td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $item->is_from_kalender ? 'bg-emerald-50 text-emerald-700' : 'bg-violet-50 text-violet-700' }}">{{ $item->is_from_kalender ? 'Kalender' : 'Manual' }}</span></td>
                                <td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusTones[$status] ?? $statusTones['arsip'] }}">{{ $item->status_badge['label'] ?? ucfirst($status) }}</span></td>
                                <td class="px-4 py-4"><div class="flex items-center justify-end gap-2"><x-cleanflow.table-action href="{{ route($routeBase . '.pengumuman.edit', $item->id) }}" tone="edit" icon="fas fa-edit" label="Edit pengumuman" /><form action="{{ route($routeBase . '.pengumuman.destroy', $item->id) }}" method="POST" data-confirm data-confirm-title="Hapus pengumuman?" data-confirm-message="{{ $item->judul }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus pengumuman" /></form></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(is_object($pengumuman) && method_exists($pengumuman, 'hasPages') && $pengumuman->hasPages())<footer class="border-t border-slate-200 px-4 py-3">{{ $pengumuman->withQueryString()->links() }}</footer>@endif
    </section>
</div>
@endsection
