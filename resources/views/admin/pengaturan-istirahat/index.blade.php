@extends('layouts.app')

@section('title', 'Pengaturan Waktu Istirahat')
@section('page-title', 'Pengaturan Istirahat')
@section('page-subtitle', 'Blokir waktu jeda agar tidak terisi saat jadwal disusun')

@section('content')
@php
    $totalPengaturan = collect($pengaturanPerJenjang)->sum(fn ($items) => $items->count());
    $totalAktif = collect($pengaturanPerJenjang)->sum(fn ($items) => $items->where('is_active', true)->count());
    $jenjangTones = [
        'KB' => 'bg-pink-50 text-pink-700 ring-pink-100',
        'TKA' => 'bg-fuchsia-50 text-fuchsia-700 ring-fuchsia-100',
        'TKB' => 'bg-violet-50 text-violet-700 ring-violet-100',
        'SD' => 'bg-blue-50 text-blue-700 ring-blue-100',
        'SMP' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'SMA' => 'bg-amber-50 text-amber-700 ring-amber-100',
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-start gap-3">
            <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline hover:border-brand-300 hover:text-brand-700" aria-label="Kembali ke jadwal pelajaran"><i class="fas fa-arrow-left" aria-hidden="true"></i></a>
            <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-brand-600">Pengaturan jadwal</p><h2 class="text-xl font-extrabold text-slate-950 sm:text-2xl">Waktu istirahat per jenjang</h2><p class="mt-1 text-sm text-slate-500">Atur maksimal dua jeda untuk setiap jenjang.</p></div>
        </div>
        <a href="{{ route('admin.pengaturan-istirahat.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white no-underline shadow-sm hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i>Tambah istirahat</a>
    </header>

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="flex items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-mug-hot" aria-hidden="true"></i></span><div><p class="text-2xl font-extrabold leading-none text-slate-950">{{ $totalPengaturan }}</p><p class="mt-1 text-[10px] font-bold uppercase tracking-wide text-slate-500">Total jeda</p></div></div></article>
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="flex items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-circle-check" aria-hidden="true"></i></span><div><p class="text-2xl font-extrabold leading-none text-slate-950">{{ $totalAktif }}</p><p class="mt-1 text-[10px] font-bold uppercase tracking-wide text-slate-500">Jeda aktif</p></div></div></article>
        <article class="col-span-2 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-1"><div class="flex items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fas fa-layer-group" aria-hidden="true"></i></span><div><p class="text-2xl font-extrabold leading-none text-slate-950">{{ count($jenjangList) }}</p><p class="mt-1 text-[10px] font-bold uppercase tracking-wide text-slate-500">Jenjang tersedia</p></div></div></article>
    </section>

    <aside class="flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-blue-950">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-blue-600"><i class="fas fa-circle-info" aria-hidden="true"></i></span>
        <div class="min-w-0"><h3 class="text-sm font-extrabold">Cara kerja waktu istirahat</h3><p class="mt-1 text-xs leading-5 text-blue-800">Jeda aktif otomatis memblokir slot ketika jadwal dibuat dan ditandai pada hasil cetak. Hari yang tidak dipilih tetap dapat digunakan.</p></div>
    </aside>

    <section class="grid min-w-0 gap-4 lg:grid-cols-2 2xl:grid-cols-3">
        @foreach($jenjangList as $jenjang)
            @php $items = $pengaturanPerJenjang[$jenjang]; @endphp
            <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3.5">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <span class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-xl px-2 text-xs font-extrabold ring-1 ring-inset {{ $jenjangTones[$jenjang] }}">{{ $jenjang }}</span>
                        <div class="min-w-0"><h3 class="truncate text-sm font-extrabold text-slate-950">Jenjang {{ $jenjang }}</h3><p class="text-[11px] text-slate-500">{{ $items->count() }} dari 2 jeda digunakan</p></div>
                    </div>
                    @if($items->count() < 2)
                        <a href="{{ route('admin.pengaturan-istirahat.create', ['jenjang' => $jenjang]) }}" class="inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-xl bg-brand-50 px-3 text-xs font-bold text-brand-700 no-underline hover:bg-brand-100"><i class="fas fa-plus" aria-hidden="true"></i>Tambah</a>
                    @endif
                </header>

                <div class="divide-y divide-slate-100">
                    @forelse($items as $pengaturan)
                        <div class="p-4">
                            <div class="flex min-w-0 items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2"><h4 class="truncate text-sm font-extrabold text-slate-900" title="{{ $pengaturan->nama_istirahat }}">{{ $pengaturan->nama_istirahat }}</h4><span class="inline-flex whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold {{ $pengaturan->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $pengaturan->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
                                    <p class="mt-1.5 whitespace-nowrap text-sm font-bold text-slate-700"><i class="far fa-clock mr-1 text-brand-600" aria-hidden="true"></i>{{ substr($pengaturan->jam_mulai, 0, 5) }} - {{ substr($pengaturan->jam_selesai, 0, 5) }}</p>
                                </div>
                                <span class="inline-flex h-8 min-w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 px-2 text-[10px] font-extrabold text-slate-600">Jeda {{ $pengaturan->urutan }}</span>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-1.5">@foreach($pengaturan->hari_aktif ?? [] as $hari)<span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600">{{ $hari }}</span>@endforeach</div>

                            <div class="mt-4 flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                                <form action="{{ route('admin.pengaturan-istirahat.toggle-status', $pengaturan->id) }}" method="POST">@csrf @method('PATCH')<x-cleanflow.table-action type="submit" :tone="$pengaturan->is_active ? 'neutral' : 'success'" :icon="$pengaturan->is_active ? 'fas fa-pause' : 'fas fa-play'" :label="$pengaturan->is_active ? 'Nonaktifkan istirahat' : 'Aktifkan istirahat'" /></form>
                                <x-cleanflow.table-action href="{{ route('admin.pengaturan-istirahat.edit', $pengaturan->id) }}" tone="edit" icon="fas fa-edit" label="Edit waktu istirahat" />
                                <form action="{{ route('admin.pengaturan-istirahat.destroy', $pengaturan->id) }}" method="POST" data-confirm data-confirm-title="Hapus waktu istirahat?" data-confirm-message="{{ $pengaturan->nama_istirahat }} untuk jenjang {{ $jenjang }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus waktu istirahat" /></form>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-9 text-center"><span class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400"><i class="fas fa-mug-hot" aria-hidden="true"></i></span><p class="mt-3 text-sm font-bold text-slate-700">Belum ada waktu istirahat</p><p class="mt-1 text-xs text-slate-500">Tambahkan jeda pertama untuk {{ $jenjang }}.</p></div>
                    @endforelse
                </div>
            </article>
        @endforeach
    </section>
</div>
@endsection
