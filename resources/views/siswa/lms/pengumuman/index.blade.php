@extends('layouts.lms')

@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Daftar pengumuman sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    $prioritasInfo = [
        'mendesak' => ['label' => 'Mendesak', 'badge' => 'bg-rose-50 text-rose-700 ring-rose-200', 'bar' => 'bg-rose-500'],
        'penting' => ['label' => 'Penting', 'badge' => 'bg-amber-50 text-amber-800 ring-amber-200', 'bar' => 'bg-amber-400'],
        'biasa' => ['label' => 'Biasa', 'badge' => 'bg-slate-100 text-slate-600 ring-slate-200', 'bar' => 'bg-indigo-400'],
    ];
    $totalSemua = $jumlahPrioritas->sum();
    $tabs = ['' => ['Semua', $totalSemua]] + collect($prioritasInfo)->map(fn ($p, $key) => [$p['label'], $jumlahPrioritas[$key] ?? 0])->all();
    $tabUrl = fn ($prioritas) => route('siswa.lms.pengumuman.index', array_filter(array_merge($filters, ['prioritas' => $prioritas]), fn ($v) => $v !== null && $v !== ''));
    $field = 'block h-11 w-full min-w-0 rounded-xl border border-slate-300 bg-white text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
    $adaFilter = $filters['q'] !== '' || $filters['from'] || $filters['to'] || $filters['sort'] !== 'prioritas';
@endphp

<div class="min-w-0 w-full space-y-4">
    <form method="GET" action="{{ route('siswa.lms.pengumuman.index') }}" class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
        @if($filters['prioritas'] !== '')
            <input type="hidden" name="prioritas" value="{{ $filters['prioritas'] }}">
        @endif
        <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-[minmax(0,1.6fr)_repeat(3,minmax(0,1fr))_auto]">
            <label class="relative sm:col-span-2 xl:col-span-1">
                <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Cari</span>
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute bottom-3.5 left-3.5 text-sm text-slate-400" aria-hidden="true"></i>
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari judul atau isi pengumuman..." class="{{ $field }} pl-10 pr-3">
            </label>
            <label>
                <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Dari tanggal</span>
                <input type="date" name="from" value="{{ $filters['from'] }}" title="Dari tanggal" class="{{ $field }} px-3">
            </label>
            <label>
                <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Sampai tanggal</span>
                <input type="date" name="to" value="{{ $filters['to'] }}" title="Sampai tanggal" class="{{ $field }} px-3">
            </label>
            <label>
                <span class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Urutkan</span>
                <select name="sort" class="{{ $field }} pl-3 pr-8" data-auto-submit>
                    <option value="prioritas" @selected($filters['sort'] === 'prioritas')>Urut: prioritas</option>
                    <option value="terbaru" @selected($filters['sort'] === 'terbaru')>Urut: terbaru</option>
                    <option value="terlama" @selected($filters['sort'] === 'terlama')>Urut: terlama</option>
                </select>
            </label>
            <div class="grid grid-cols-2 gap-2 self-end sm:col-span-2 xl:col-span-1 xl:flex">
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-indigo-600 px-4 text-sm font-bold text-white transition hover:bg-indigo-700"><i class="fa-solid fa-filter" aria-hidden="true"></i>Terapkan</button>
                <a href="{{ route('siswa.lms.pengumuman.index') }}" class="inline-flex h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline transition hover:bg-slate-50 {{ $adaFilter || $filters['prioritas'] !== '' ? '' : 'pointer-events-none opacity-50' }}"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i>Reset</a>
            </div>
        </div>
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <nav class="flex gap-1 overflow-x-auto border-b border-slate-100 px-2 pt-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Filter prioritas">
            @foreach($tabs as $key => [$label, $jumlah])
                @php $aktif = $filters['prioritas'] === $key; @endphp
                <a href="{{ $tabUrl($key) }}" @if($aktif) aria-current="page" @endif
                   class="inline-flex shrink-0 items-center gap-2 whitespace-nowrap border-b-2 px-3 py-2.5 text-sm font-bold no-underline transition {{ $aktif ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    {{ $label }}
                    <span class="rounded-full px-2 py-0.5 text-[11px] {{ $aktif ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $jumlah }}</span>
                </a>
            @endforeach
        </nav>

        <p class="px-4 py-3 text-xs text-slate-500 sm:px-5"><strong class="text-slate-800">{{ $pengumumanList->total() }}</strong> pengumuman ditemukan</p>

        @if($pengumumanList->count() > 0)
            <ul class="divide-y divide-slate-100 border-t border-slate-100">
                @foreach($pengumumanList as $item)
                    @php $info = $prioritasInfo[$item->prioritas] ?? $prioritasInfo['biasa']; @endphp
                    <li>
                        <a href="{{ route('siswa.lms.pengumuman.show', $item->id) }}" class="group relative flex gap-3 px-4 py-4 no-underline transition hover:bg-indigo-50/50 sm:px-5">
                            <span class="mt-1 h-10 w-1 shrink-0 rounded-full {{ $info['bar'] }}" aria-hidden="true"></span>
                            <span class="min-w-0 flex-1">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $info['badge'] }}">{{ $info['label'] }}</span>
                                    <span class="text-[11px] font-semibold text-slate-400">SIPADUHOK</span>
                                    <span class="ml-auto flex items-center gap-2 text-[11px] text-slate-500">
                                        @if($item->lampiran_surat)
                                            <i class="fa-solid fa-paperclip" title="Ada lampiran" aria-label="Ada lampiran"></i>
                                        @endif
                                        {{ $item->created_at->copy()->locale('id')->diffForHumans(null, true) }}
                                    </span>
                                </span>
                                <span class="mt-1.5 block truncate text-sm font-extrabold text-slate-900 group-hover:text-indigo-700">{{ $item->judul }}</span>
                                <span class="mt-0.5 line-clamp-2 block text-xs leading-5 text-slate-500 sm:line-clamp-1">{{ Str::limit(strip_tags($item->isi_pengumuman), 160) }}</span>
                            </span>
                            <i class="fa-solid fa-chevron-right mt-1 hidden self-center text-xs text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-500 sm:block" aria-hidden="true"></i>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if($pengumumanList->hasPages())
                <div class="border-t border-slate-100 px-4 py-3 sm:px-5">{{ $pengumumanList->links() }}</div>
            @endif
        @else
            <div class="border-t border-slate-100 px-4 py-12 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-2xl text-indigo-500"><i class="fa-regular fa-envelope-open" aria-hidden="true"></i></span>
                <h2 class="mt-3 text-sm font-extrabold text-slate-900">Tidak ada pengumuman</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $adaFilter || $filters['prioritas'] !== '' ? 'Tidak ada pengumuman yang cocok dengan filter.' : 'Belum ada pengumuman untuk saat ini.' }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
