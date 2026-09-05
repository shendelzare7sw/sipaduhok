@extends('layouts.app')

@section('title', 'Kelola Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')
@section('page-subtitle', 'Siapkan daftar pelajaran sebelum menyusun guru dan jadwal')

@section('content')
@php
    $jenjangStats = [
        ['label' => 'Total', 'value' => $stats['total'], 'tone' => 'bg-brand-50 text-brand-700'],
        ['label' => 'KB', 'value' => $stats['kb'], 'tone' => 'bg-pink-50 text-pink-700'],
        ['label' => 'TKA', 'value' => $stats['tka'], 'tone' => 'bg-fuchsia-50 text-fuchsia-700'],
        ['label' => 'TKB', 'value' => $stats['tkb'], 'tone' => 'bg-violet-50 text-violet-700'],
        ['label' => 'SD', 'value' => $stats['sd'], 'tone' => 'bg-blue-50 text-blue-700'],
        ['label' => 'SMP', 'value' => $stats['smp'], 'tone' => 'bg-emerald-50 text-emerald-700'],
        ['label' => 'SMA', 'value' => $stats['sma'], 'tone' => 'bg-amber-50 text-amber-700'],
    ];
    $jenjangTone = collect($jenjangStats)->skip(1)->mapWithKeys(fn ($item) => [$item['label'] => $item['tone']])->all();
@endphp

<div class="min-w-0 w-full space-y-5">
    @if(session('import_warnings'))
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs leading-5 text-amber-900"><p class="font-extrabold"><i class="fas fa-exclamation-triangle mr-1.5" aria-hidden="true"></i>Beberapa baris import dilewati</p><ul class="mt-2 list-disc space-y-1 pl-5">@foreach(session('import_warnings') as $warning)<li>{{ $warning }}</li>@endforeach</ul></section>
    @endif

    <section class="grid grid-cols-4 gap-2 sm:grid-cols-7">
        @foreach($jenjangStats as $item)
            <article class="min-w-0 rounded-xl border border-slate-200 bg-white p-3 text-center shadow-sm"><p class="text-[10px] font-bold uppercase tracking-wide {{ $item['tone'] }} -mx-1 -mt-1 rounded-lg py-1">{{ $item['label'] }}</p><p class="mt-2 text-lg font-extrabold leading-none text-slate-900">{{ number_format($item['value']) }}</p></article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-book text-brand-600" aria-hidden="true"></i>Daftar Mata Pelajaran</h2><p class="mt-1 text-xs text-slate-500">{{ $mataPelajaranList->total() }} pelajaran ditemukan{{ $jenjang ? ' untuk jenjang ' . $jenjang : '' }}.</p></div>
                <div class="grid grid-cols-3 gap-2 sm:flex"><a href="{{ route('admin.mata-pelajaran.print', request()->only(['jenjang', 'search'])) }}" target="_blank" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200"><i class="fas fa-print" aria-hidden="true"></i><span class="hidden sm:inline">Cetak</span></a><a href="{{ route('admin.mata-pelajaran.import') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700 no-underline hover:bg-emerald-100"><i class="fas fa-file-import" aria-hidden="true"></i><span class="hidden sm:inline">Import</span></a><a href="{{ route('admin.mata-pelajaran.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i><span class="hidden sm:inline">Tambah</span></a></div>
            </div>
            <form method="GET" class="mt-4 grid gap-2 sm:grid-cols-[minmax(240px,1fr)_180px_auto]">
                <label class="relative min-w-0"><span class="sr-only">Cari mata pelajaran</span><i class="fas fa-search pointer-events-none absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ $search }}" placeholder="Cari nama, kode, deskripsi, atau agama..." class="h-10 w-full rounded-xl border border-slate-200 bg-white !pl-10 pr-3 text-xs outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"></label>
                <select name="jenjang" data-auto-submit class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 outline-none focus:border-brand-500"><option value="">Semua Jenjang</option>@foreach(['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'] as $option)<option value="{{ $option }}" {{ $jenjang === $option ? 'selected' : '' }}>{{ $option }}</option>@endforeach</select>
                <div class="flex gap-2"><button type="submit" class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white hover:bg-slate-900"><i class="fas fa-search" aria-hidden="true"></i>Cari</button>@if($jenjang || $search !== '')<a href="{{ route('admin.mata-pelajaran.index') }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-red-50 px-3 text-red-700 no-underline hover:bg-red-100" title="Reset filter"><i class="fas fa-times" aria-hidden="true"></i></a>@endif</div>
            </form>
        </header>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($mataPelajaranList as $mapel)
                <article class="min-w-0 p-4">
                    <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $mapel->nama_mapel }}</h3><p class="mt-1 text-[11px] font-semibold text-slate-500">{{ $mapel->kode_mapel ?: 'Tanpa kode' }}</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $jenjangTone[$mapel->jenjang] ?? 'bg-slate-100 text-slate-700' }}">{{ $mapel->jenjang }}</span></div>
                    <div class="mt-3 grid grid-cols-2 gap-2 rounded-xl bg-slate-50 p-3 text-xs"><div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Filter agama</p><p class="mt-1 font-semibold text-slate-700">{{ $mapel->filter_agama ?: '-' }}</p></div><div><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Deskripsi</p><p class="mt-1 break-words text-slate-600">{{ $mapel->deskripsi ? Str::limit($mapel->deskripsi, 55) : '-' }}</p></div></div>
                    <div class="mt-3 grid grid-cols-3 gap-2"><a href="{{ route('admin.mata-pelajaran.show', $mapel) }}" class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg bg-blue-50 text-xs font-bold text-blue-700 no-underline hover:bg-blue-100"><i class="fas fa-eye" aria-hidden="true"></i>Detail</a><a href="{{ route('admin.mata-pelajaran.edit', $mapel) }}" class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100"><i class="fas fa-edit" aria-hidden="true"></i>Edit</a><form action="{{ route('admin.mata-pelajaran.destroy', $mapel) }}" method="POST" data-confirm data-confirm-title="Hapus mata pelajaran?" data-confirm-message="{{ $mapel->nama_mapel }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-1 rounded-lg bg-red-50 text-xs font-bold text-red-700 hover:bg-red-100"><i class="fas fa-trash" aria-hidden="true"></i>Hapus</button></form></div>
                </article>
            @empty
                <div class="px-5 py-14 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-book" aria-hidden="true"></i></span><h3 class="mt-4 text-sm font-extrabold text-slate-800">Mata pelajaran tidak ditemukan</h3><p class="mt-1 text-xs text-slate-500">Tambah data pertama atau ubah kata pencarian.</p><a href="{{ route('admin.mata-pelajaran.create') }}" class="mt-4 inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i>Tambah Mata Pelajaran</a></div>
            @endforelse
        </div>

        @if($mataPelajaranList->isNotEmpty())
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full min-w-[900px] table-fixed border-collapse text-left text-sm">
                    <colgroup>
                        <col class="w-14">
                        <col class="w-28">
                        <col class="w-72">
                        <col class="w-24">
                        <col class="w-32">
                        <col>
                        <col class="w-40">
                    </colgroup>
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-3 py-3 text-center">No</th>
                            <th class="px-3 py-3">Kode</th>
                            <th class="px-3 py-3">Mata Pelajaran</th>
                            <th class="px-3 py-3">Jenjang</th>
                            <th class="px-3 py-3">Agama</th>
                            <th class="px-3 py-3">Deskripsi</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($mataPelajaranList as $index => $mapel)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-3 py-4 text-center text-xs text-slate-500">{{ $mataPelajaranList->firstItem() + $index }}</td>
                            <td class="px-3 py-4">
                                <span class="inline-flex min-w-16 whitespace-nowrap rounded-lg bg-slate-100 px-2 py-1 font-mono text-[11px] font-bold text-slate-600">{{ $mapel->kode_mapel ?: '-' }}</span>
                            </td>
                            <td class="px-3 py-4">
                                <p class="truncate font-bold text-slate-900" title="{{ $mapel->nama_mapel }}">{{ $mapel->nama_mapel }}</p>
                            </td>
                            <td class="px-3 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $jenjangTone[$mapel->jenjang] ?? 'bg-slate-100 text-slate-700' }}">{{ $mapel->jenjang }}</span>
                            </td>
                            <td class="px-3 py-4 text-xs text-slate-600">
                                <span class="block truncate" title="{{ $mapel->filter_agama ?: '-' }}">{{ $mapel->filter_agama ?: '-' }}</span>
                            </td>
                            <td class="px-3 py-4 text-xs text-slate-500">
                                <p class="truncate" title="{{ $mapel->deskripsi ?: '-' }}">{{ $mapel->deskripsi ?: '-' }}</p>
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <x-cleanflow.table-action href="{{ route('admin.mata-pelajaran.show', $mapel) }}" tone="view" icon="fas fa-eye" label="Detail mata pelajaran" />
                                    <x-cleanflow.table-action href="{{ route('admin.mata-pelajaran.edit', $mapel) }}" tone="edit" icon="fas fa-edit" label="Edit mata pelajaran" />
                                    <form action="{{ route('admin.mata-pelajaran.destroy', $mapel) }}" method="POST" data-confirm data-confirm-title="Hapus mata pelajaran?" data-confirm-message="{{ $mapel->nama_mapel }} akan dihapus permanen." data-confirm-text="Ya, hapus">
                                        @csrf
                                        @method('DELETE')
                                        <x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus mata pelajaran" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($mataPelajaranList->hasPages())<footer class="flex flex-col gap-2 border-t border-slate-200 px-4 py-3 text-[11px] text-slate-500 sm:flex-row sm:items-center sm:justify-between"><span>Menampilkan {{ $mataPelajaranList->firstItem() ?? 0 }}-{{ $mataPelajaranList->lastItem() ?? 0 }} dari {{ $mataPelajaranList->total() }}</span>{{ $mataPelajaranList->withQueryString()->links() }}</footer>@endif
    </section>
</div>
@endsection
