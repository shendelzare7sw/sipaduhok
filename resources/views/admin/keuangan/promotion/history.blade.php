@extends('layouts.app')

@section('title', 'Riwayat Dispensasi')
@section('page-title', 'Riwayat Dispensasi')
@section('page-subtitle', 'Telusuri keputusan pengajuan dispensasi kenaikan kelas')

@section('content')
@php
    $approvedCount = $history->where('status', 'DISETUJUI')->count();
    $rejectedCount = $history->where('status', 'DITOLAK')->count();
    $filterActive = collect($filters)->only(['q', 'cabang', 'kelas', 'status'])->filter(fn ($value) => filled($value))->isNotEmpty();
    $isKetua = request()->routeIs('ketua.*');
    $isBendahara = request()->routeIs('bendahara.*');
    $routePrefix = $isKetua
        ? 'ketua.kenaikan-kelas.approval'
        : ($isBendahara ? 'bendahara.kenaikan-kelas.validation' : 'admin.keuangan.kenaikan-kelas.validation');
    $indexRoute = $routePrefix.'.index';
    $historyRoute = $routePrefix.'.history';
    $deleteRoute = $routePrefix.'.history.bulk-delete';
@endphp

<div
    class="min-w-0 w-full space-y-5"
    x-data="{
        selected: [],
        available: @js($history->pluck('id')->map(fn ($id) => (string) $id)->values()),
        toggleAll() { this.selected = this.selected.length === this.available.length ? [] : [...this.available]; },
        removeSelected() { if (this.selected.length) this.$refs.deleteDialog.showModal(); }
    }"
>
    <section class="grid grid-cols-2 gap-3 sm:grid-cols-3">
        @foreach([
            ['Total keputusan', $history->count(), 'fa-clock-rotate-left', 'bg-brand-50 text-brand-600'],
            ['Disetujui', $approvedCount, 'fa-circle-check', 'bg-emerald-50 text-emerald-600'],
            ['Ditolak', $rejectedCount, 'fa-circle-xmark', 'bg-red-50 text-red-600'],
        ] as [$label, $value, $icon, $tone])
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $loop->first ? 'col-span-2 sm:col-span-1' : '' }}"><div class="flex min-w-0 items-start justify-between gap-3"><div class="min-w-0"><p class="text-xl font-extrabold text-slate-950">{{ number_format($value) }}</p><p class="mt-1 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500" title="{{ $label }}">{{ $label }}</p></div><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span></div></article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-4 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5"><div><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-clock-rotate-left text-brand-600" aria-hidden="true"></i>Riwayat keputusan</h2><p class="mt-1 text-xs leading-5 text-slate-500">Keputusan pada tahun ajaran {{ $tahun->nama_tahun_ajaran }}.</p></div><a href="{{ route($indexRoute) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200"><i class="fas fa-arrow-left" aria-hidden="true"></i>{{ $isKetua ? 'Kembali ke persetujuan' : 'Kembali ke kandidat' }}</a></header>

        <form action="{{ route($historyRoute) }}" method="GET" class="grid gap-3 border-b border-slate-200 bg-slate-50/70 p-4 sm:grid-cols-2 xl:grid-cols-[minmax(220px,1.4fr)_repeat(3,minmax(150px,.7fr))_auto] sm:p-5">
            <label class="block"><span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Cari siswa</span><span class="relative mt-1.5 block"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama atau NIS..." class="h-11 w-full rounded-xl border border-slate-300 bg-white !pl-10 pr-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></span></label>
            <label class="block"><span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Cabang</span><select name="cabang" class="mt-1.5 h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua cabang</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}" @selected(($filters['cabang'] ?? '') == $cabang->id)>{{ $cabang->nama_cabang }}</option>@endforeach</select></label>
            <label class="block"><span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Kelas</span><select name="kelas" class="mt-1.5 h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua kelas</option>@foreach($kelasList as $kelas)<option value="{{ $kelas->id }}" @selected(($filters['kelas'] ?? '') == $kelas->id)>{{ $kelas->nama_kelas }} · {{ $kelas->jenjang }} · {{ $kelas->cabang->nama_cabang ?? '-' }}</option>@endforeach</select></label>
            <label class="block"><span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Status</span><select name="status" class="mt-1.5 h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua status</option><option value="DISETUJUI" @selected(($filters['status'] ?? '') === 'DISETUJUI')>Disetujui</option><option value="DITOLAK" @selected(($filters['status'] ?? '') === 'DITOLAK')>Ditolak</option></select></label>
            <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-1"><button type="submit" class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-xs font-bold text-white hover:bg-slate-800"><i class="fas fa-filter" aria-hidden="true"></i>Filter</button>@if($filterActive)<a href="{{ route($historyRoute) }}" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-600 no-underline hover:bg-slate-100" aria-label="Reset filter"><i class="fas fa-rotate-left" aria-hidden="true"></i></a>@endif</div>
        </form>

        <form x-ref="deleteForm" method="POST" action="{{ route($deleteRoute) }}">
            @csrf
            <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
            @if($history->isNotEmpty())
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-5"><label class="inline-flex cursor-pointer items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" @change="toggleAll()" :checked="available.length > 0 && selected.length === available.length" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">Pilih semua</label><button type="button" @click="removeSelected()" :disabled="!selected.length" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-red-50 px-3 text-xs font-bold text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"><i class="fas fa-trash" aria-hidden="true"></i>Hapus <span x-show="selected.length">(<span x-text="selected.length"></span>)</span></button></div>
            @endif

            <div class="hidden lg:block">
                <table class="w-full table-fixed text-left text-xs">
                    <colgroup><col class="w-12"><col><col class="w-48"><col class="w-52"><col class="w-64"></colgroup>
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3"></th><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">Kelas & cabang</th><th class="px-3 py-3">Pengajuan</th><th class="px-4 py-3">Keputusan</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($history as $item)
                            @php $approved = $item->status === 'DISETUJUI'; @endphp
                            <tr class="hover:bg-slate-50/70"><td class="px-4 py-4"><input type="checkbox" value="{{ $item->id }}" x-model="selected" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"></td><td class="px-3 py-4"><p class="truncate text-sm font-bold text-slate-900" title="{{ $item->nama_siswa }}">{{ $item->nama_siswa }}</p><p class="mt-0.5 truncate text-[11px] text-slate-500">NIS {{ $item->nis ?: '-' }}</p></td><td class="px-3 py-4"><p class="truncate font-bold text-slate-800">{{ $item->nama_kelas }}</p><p class="mt-0.5 truncate text-[11px] text-slate-500" title="{{ $item->nama_cabang ?? '-' }}">{{ $item->nama_cabang ?? '-' }}</p></td><td class="px-3 py-4"><p class="whitespace-nowrap font-semibold text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->locale('id')->translatedFormat('d M Y') }}</p><p class="mt-0.5 truncate text-[11px] text-slate-500" title="{{ $item->pengaju }}">oleh {{ $item->pengaju }}</p></td><td class="px-4 py-4"><div class="flex items-start gap-3"><span class="inline-flex shrink-0 whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $approved ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}"><i class="fas {{ $approved ? 'fa-circle-check' : 'fa-circle-xmark' }} mr-1" aria-hidden="true"></i>{{ $approved ? 'Disetujui' : 'Ditolak' }}</span><div class="min-w-0"><p class="truncate text-[11px] font-semibold text-slate-700" title="{{ $item->penyetuju ?? '-' }}">{{ $item->penyetuju ?? '-' }}</p><p class="mt-0.5 truncate text-[10px] text-slate-500" title="{{ $item->catatan_ketua ?? '-' }}">{{ $item->catatan_ketua ?? 'Tanpa catatan' }}</p>@if($item->tanggal_persetujuan)<time class="mt-0.5 block text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($item->tanggal_persetujuan)->locale('id')->translatedFormat('d M Y') }}</time>@endif</div></div></td></tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-14 text-center text-sm text-slate-500"><i class="fas fa-inbox mb-3 block text-4xl text-slate-300" aria-hidden="true"></i>Belum ada riwayat yang sesuai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse($history as $item)
                    @php $approved = $item->status === 'DISETUJUI'; @endphp
                    <article class="p-4"><div class="flex min-w-0 items-start gap-3"><input type="checkbox" value="{{ $item->id }}" x-model="selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><div class="min-w-0 flex-1"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $item->nama_siswa }}</h3><p class="mt-0.5 text-[11px] text-slate-500">NIS {{ $item->nis ?: '-' }} · {{ $item->nama_kelas }}</p></div><span class="shrink-0 rounded-full px-2 py-1 text-[9px] font-bold {{ $approved ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $approved ? 'Disetujui' : 'Ditolak' }}</span></div><dl class="mt-3 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-xs"><div><dt class="text-[9px] font-bold uppercase text-slate-400">Diajukan</dt><dd class="mt-1 font-semibold text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->locale('id')->translatedFormat('d M Y') }}</dd><dd class="mt-0.5 break-words text-[10px] text-slate-500">{{ $item->pengaju }}</dd></div><div><dt class="text-[9px] font-bold uppercase text-slate-400">Diputuskan oleh</dt><dd class="mt-1 break-words font-semibold text-slate-700">{{ $item->penyetuju ?? '-' }}</dd>@if($item->tanggal_persetujuan)<dd class="mt-0.5 text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($item->tanggal_persetujuan)->locale('id')->translatedFormat('d M Y') }}</dd>@endif</div></dl><div class="mt-3 rounded-xl border border-slate-200 p-3"><p class="text-[9px] font-bold uppercase text-slate-400">Catatan keputusan</p><p class="mt-1 break-words text-xs leading-5 text-slate-600">{{ $item->catatan_ketua ?? 'Tanpa catatan' }}</p></div></article>
                @empty
                    <div class="p-12 text-center text-sm text-slate-500"><i class="fas fa-inbox mb-3 block text-4xl text-slate-300" aria-hidden="true"></i>Belum ada riwayat yang sesuai.</div>
                @endforelse
            </div>
        </form>
    </section>

    <dialog x-ref="deleteDialog" class="m-auto w-[calc(100%-2rem)] max-w-md overflow-hidden rounded-2xl bg-white p-0 shadow-2xl backdrop:bg-slate-950/60" @click.self="$el.close()"><div class="p-5 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-700"><i class="fas fa-trash" aria-hidden="true"></i></span><h2 class="mt-3 text-base font-extrabold text-slate-950">Hapus riwayat terpilih?</h2><p class="mt-2 text-xs leading-5 text-slate-500"><strong x-text="selected.length"></strong> riwayat akan dihapus permanen.</p><div class="mt-5 grid grid-cols-2 gap-2"><button type="button" @click="$refs.deleteDialog.close()" class="h-10 rounded-xl bg-slate-100 text-xs font-bold text-slate-700">Batal</button><button type="button" @click="$refs.deleteForm.requestSubmit()" class="h-10 rounded-xl bg-red-600 text-xs font-bold text-white">Ya, hapus</button></div></div></dialog>
</div>
@endsection
