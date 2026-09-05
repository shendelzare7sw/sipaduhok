@extends('layouts.app')

@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')
@section('page-subtitle', 'Temukan siswa lalu atur penempatan kelasnya')

@section('content')
@php
    $selectedKelasIds = collect((array) request('kelas_id'))->map(fn ($id) => (string) $id)->values();
    $selectedTahun = $tahunAjarans->firstWhere('id', (int) $taFilterId);
    $statusStyles = [
        'aktif' => 'bg-emerald-50 text-emerald-700',
        'lulus' => 'bg-blue-50 text-blue-700',
        'pindah' => 'bg-amber-50 text-amber-700',
        'keluar' => 'bg-slate-100 text-slate-600',
    ];
    $kelulusan = [
        'NAIK_KELAS' => ['bg-emerald-50 text-emerald-700', 'Naik Kelas'],
        'NAIK_KELAS_TUNGGAKAN' => ['bg-amber-50 text-amber-700', 'Naik (Dispensasi)'],
        'TIDAK_NAIK_KELAS' => ['bg-red-50 text-red-700', 'Tidak Naik'],
        'LULUS' => ['bg-blue-50 text-blue-700', 'Lulus'],
        'LULUS_TUNGGAKAN' => ['bg-amber-50 text-amber-700', 'Lulus (Dispensasi)'],
    ];
@endphp

<div class="min-w-0 w-full space-y-4">
    <header class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-brand-600">Penempatan siswa</p>
            <h2 class="text-xl font-extrabold text-slate-950 sm:text-2xl">Cari siswa, lalu tentukan kelas</h2>
            <p class="mt-1 text-sm text-slate-500">Gunakan filter periode untuk melihat riwayat penempatan tanpa mengubah data saat ini.</p>
        </div>
        <a href="{{ route('admin.manajemen-siswa.print', request()->query()) }}" target="_blank" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white no-underline hover:bg-slate-700">
            <i class="fas fa-print" aria-hidden="true"></i>Cetak daftar
        </a>
    </header>

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        @foreach([
            ['value' => $stats['totalSiswa'], 'label' => 'Total siswa', 'icon' => 'fas fa-user-graduate', 'tone' => 'bg-blue-50 text-blue-700'],
            ['value' => $stats['siswaWithKelas'], 'label' => 'Sudah ada kelas', 'icon' => 'fas fa-circle-check', 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['value' => $stats['siswaNoKelas'], 'label' => 'Belum ada kelas', 'icon' => 'fas fa-hourglass-half', 'tone' => 'bg-amber-50 text-amber-700'],
            ['value' => $stats['siswaLaki'], 'label' => 'Laki-laki', 'icon' => 'fas fa-mars', 'tone' => 'bg-cyan-50 text-cyan-700'],
            ['value' => $stats['siswaPerempuan'], 'label' => 'Perempuan', 'icon' => 'fas fa-venus', 'tone' => 'bg-fuchsia-50 text-fuchsia-700'],
        ] as $stat)
            <article class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="{{ $stat['icon'] }}" aria-hidden="true"></i></span>
                <div class="min-w-0"><strong class="block text-xl font-extrabold text-slate-950">{{ $stat['value'] }}</strong><span class="block truncate text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</span></div>
            </article>
        @endforeach
    </section>

    @if($isHistorical)
        <div class="flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            <i class="fas fa-clock-rotate-left mt-0.5" aria-hidden="true"></i>
            <p><strong>Mode riwayat {{ $selectedTahun?->nama_tahun_ajaran }}.</strong> Kelas dan status di bawah berasal dari snapshot periode tersebut.</p>
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <div><h3 class="text-lg font-extrabold text-slate-950"><i class="fas fa-list mr-2 text-brand-600"></i>Daftar siswa</h3><p class="mt-1 text-xs text-slate-500">{{ $siswaList->total() }} siswa sesuai filter.</p></div>
            @if(request()->hasAny(['search', 'cabang_id', 'jenjang', 'kelas_id', 'no_kelas', 'tahun_ajaran_id']) || request('status', 'aktif') !== 'aktif')
                <a href="{{ route('admin.manajemen-siswa.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-red-50 px-3 text-xs font-bold text-red-700 no-underline hover:bg-red-100"><i class="fas fa-rotate-left"></i>Reset filter</a>
            @endif
        </header>

        <form action="{{ route('admin.manajemen-siswa.index') }}" method="GET" class="grid gap-2 border-b border-slate-200 px-4 py-4 sm:grid-cols-2 xl:grid-cols-[minmax(260px,1fr)_180px_130px_180px_160px_auto] sm:px-5">
            <label class="relative sm:col-span-2 xl:col-span-1"><span class="sr-only">Cari siswa</span><i class="fas fa-search absolute left-3.5 top-3.5 text-xs text-slate-400"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Nama, NIS, atau NISN..." class="h-11 w-full rounded-xl border border-slate-300 !pl-10 pr-3 text-sm outline-none focus:border-brand-500"></label>
            <select name="cabang_id" @change="$el.form.submit()" class="h-11 min-w-0 rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Semua cabang</option>@foreach($cabangs as $c)<option value="{{ $c->id }}" @selected((string)request('cabang_id') === (string)$c->id)>{{ $c->nama_cabang }}</option>@endforeach</select>
            <select name="jenjang" @change="$el.form.submit()" @disabled($isHistorical) class="h-11 min-w-0 rounded-xl border border-slate-300 bg-white px-3 text-sm disabled:bg-slate-100"><option value="">Semua jenjang</option>@foreach($jenjangs as $j)<option value="{{ $j }}" @selected(request('jenjang') === $j)>{{ $j }}</option>@endforeach</select>
            <select name="status" @change="$el.form.submit()" class="h-11 min-w-0 rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="aktif" @selected(request('status','aktif')==='aktif')>Status: Aktif</option><option value="lulus" @selected(request('status')==='lulus')>Lulus / Alumni</option><option value="pindah" @selected(request('status')==='pindah')>Pindah</option><option value="keluar" @selected(request('status')==='keluar')>Keluar</option></select>
            <select name="tahun_ajaran_id" @change="$el.form.submit()" class="h-11 min-w-0 rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Periode aktif</option>@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @selected((string)request('tahun_ajaran_id') === (string)$ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>@endforeach</select>
            <button type="submit" class="h-11 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white"><i class="fas fa-filter mr-2"></i>Terapkan</button>

            <div x-data="{ open: false, search: '', selected: @js($selectedKelasIds) }" class="relative sm:col-span-2 xl:col-span-6">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="open = !open" :aria-expanded="open" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700"><i class="fas fa-school text-brand-600"></i><span x-text="selected.length ? selected.length + ' kelas dipilih' : 'Pilih beberapa kelas'"></span><i class="fas fa-chevron-down text-[10px] transition" :class="open && 'rotate-180'"></i></button>
                    <label class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 text-xs font-bold text-slate-700"><input type="checkbox" name="no_kelas" value="1" @checked(request('no_kelas')==='1') @disabled($isHistorical) @change="$el.form.submit()" class="h-4 w-4 rounded border-slate-300 text-brand-600">Belum ada kelas</label>
                </div>
                <div x-cloak x-show="open" @click.outside="open = false" class="absolute left-0 top-12 z-30 w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                    <input type="search" x-model="search" placeholder="Cari kelas atau cabang..." class="mb-2 h-10 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-brand-500">
                    <div class="max-h-64 space-y-1 overflow-y-auto">
                        @forelse($kelasList as $k)
                            <label x-show="search === '' || @js(strtolower($k->nama_kelas.' '.($k->cabang->nama_cabang ?? '').' '.$k->jenjang)).includes(search.toLowerCase())" class="flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2 hover:bg-slate-50">
                                <input type="checkbox" name="kelas_id[]" value="{{ $k->id }}" x-model="selected" @disabled($isHistorical) class="h-4 w-4 rounded border-slate-300 text-brand-600">
                                <span class="min-w-0"><strong class="block truncate text-sm text-slate-900">{{ $k->nama_kelas }} <span class="text-xs text-slate-400">{{ $k->jenjang }}</span></strong><small class="block truncate text-slate-500">{{ $k->cabang->nama_cabang ?? 'Tanpa cabang' }}</small></span>
                            </label>
                        @empty
                            <p class="p-4 text-center text-sm text-slate-500">Belum ada kelas pada periode ini.</p>
                        @endforelse
                    </div>
                    <div class="mt-3 flex justify-end gap-2 border-t border-slate-100 pt-3"><button type="button" @click="selected = []" class="min-h-9 rounded-lg px-3 text-xs font-bold text-slate-600">Kosongkan</button><button type="submit" class="min-h-9 rounded-lg bg-brand-600 px-3 text-xs font-bold text-white">Terapkan kelas</button></div>
                </div>
            </div>
        </form>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($siswaList as $siswa)
                @php $snapshot = $isHistorical ? $siswa->statusNaikKelas->first() : null; @endphp
                <article class="p-4">
                    <div class="flex items-start gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $siswa->jenis_kelamin === 'P' ? 'bg-fuchsia-50 text-fuchsia-700' : 'bg-blue-50 text-blue-700' }} text-sm font-extrabold">{{ strtoupper(substr($siswa->nama_lengkap,0,1)) }}</span><div class="min-w-0 flex-1"><h4 class="truncate text-sm font-extrabold text-slate-950">{{ $siswa->nama_lengkap }}</h4><p class="truncate text-xs text-slate-500">NISN {{ $siswa->nisn ?: '-' }} &middot; {{ $siswa->cabang->nama_cabang ?? 'Tanpa cabang' }}</p></div><span class="rounded-full px-2 py-1 text-[10px] font-bold {{ $statusStyles[$siswa->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($siswa->status) }}</span></div>
                    <div class="mt-3 flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2"><div class="min-w-0"><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Kelas</span><strong class="block truncate text-sm text-slate-800">{{ $snapshot?->kelas_asal ?? $siswa->kelas?->nama_kelas ?? 'Belum ditempatkan' }}</strong>@if($snapshot?->kelas_tujuan)<small class="block truncate text-slate-500">Tujuan: {{ $snapshot->kelas_tujuan }}</small>@endif</div><div class="flex shrink-0 gap-2"><x-cleanflow.table-action :href="route('admin.manajemen-siswa.show',$siswa)" tone="view" icon="fas fa-eye" label="Lihat siswa" /><x-cleanflow.table-action :href="route('admin.manajemen-siswa.print-kartu',$siswa)" tone="success" icon="fas fa-id-card" label="Cetak kartu" target="_blank" /></div></div>
                </article>
            @empty
                <div class="p-10 text-center"><i class="fas fa-user-graduate text-3xl text-slate-300"></i><h4 class="mt-3 font-extrabold text-slate-900">Siswa tidak ditemukan</h4><p class="mt-1 text-sm text-slate-500">Ubah atau kosongkan filter pencarian.</p></div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto lg:block">
            <table class="w-full table-fixed text-left text-sm">
                <colgroup><col class="w-[30%]"><col class="w-[13%]"><col><col class="w-[15%]"><col class="w-[10%]"><col class="w-28"></colgroup>
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Siswa</th><th class="px-3 py-3">Jenis kelamin</th><th class="px-3 py-3">Cabang</th><th class="px-3 py-3">Kelas</th><th class="px-3 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($siswaList as $siswa)
                        @php
                            $snapshot = $isHistorical ? $siswa->statusNaikKelas->first() : null;
                            [$snapshotStyle, $snapshotLabel] = $snapshot ? ($kelulusan[$snapshot->status_kelulusan] ?? ['bg-slate-100 text-slate-600', str_replace('_',' ',$snapshot->status_kelulusan)]) : [null, null];
                        @endphp
                        <tr class="hover:bg-slate-50/70"><td class="px-5 py-3"><div class="flex min-w-0 items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $siswa->jenis_kelamin === 'P' ? 'bg-fuchsia-50 text-fuchsia-700' : 'bg-blue-50 text-blue-700' }} text-xs font-extrabold">{{ strtoupper(substr($siswa->nama_lengkap,0,1)) }}</span><div class="min-w-0"><strong class="block truncate text-slate-950" title="{{ $siswa->nama_lengkap }}">{{ $siswa->nama_lengkap }}</strong><span class="block truncate text-xs text-slate-500">NISN {{ $siswa->nisn ?: '-' }}{{ $siswa->nis ? ' · NIS '.$siswa->nis : '' }}</span></div></div></td><td class="whitespace-nowrap px-3 py-3 text-xs text-slate-600">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td><td class="px-3 py-3"><span class="block truncate text-xs text-slate-600" title="{{ $siswa->cabang->nama_cabang ?? '-' }}">{{ $siswa->cabang->nama_cabang ?? '-' }}</span></td><td class="px-3 py-3"><span class="block truncate whitespace-nowrap text-xs font-bold text-slate-800">{{ $snapshot?->kelas_asal ?? $siswa->kelas?->nama_kelas ?? 'Belum ada kelas' }}</span>@if($snapshot?->kelas_tujuan)<small class="block truncate text-slate-400">Tujuan {{ $snapshot->kelas_tujuan }}</small>@endif</td><td class="px-3 py-3">@if($snapshot)<span class="inline-flex whitespace-nowrap rounded-full px-2 py-1 text-[10px] font-bold {{ $snapshotStyle }}">{{ $snapshotLabel }}</span>@else<span class="inline-flex whitespace-nowrap rounded-full px-2 py-1 text-[10px] font-bold {{ $statusStyles[$siswa->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($siswa->status) }}</span>@endif</td><td class="px-5 py-3"><div class="flex justify-end gap-2"><x-cleanflow.table-action :href="route('admin.manajemen-siswa.show',$siswa)" tone="view" icon="fas fa-eye" label="Lihat siswa" /><x-cleanflow.table-action :href="route('admin.manajemen-siswa.print-kartu',$siswa)" tone="success" icon="fas fa-id-card" label="Cetak kartu" target="_blank" /></div></td></tr>
                    @empty
                        <tr><td colspan="6" class="p-10 text-center text-sm text-slate-500">Tidak ada siswa yang sesuai dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($siswaList->hasPages())
            <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-4 py-3 text-xs text-slate-500 sm:px-5"><span>Menampilkan {{ $siswaList->firstItem() }}-{{ $siswaList->lastItem() }} dari {{ $siswaList->total() }}</span><div>{{ $siswaList->links() }}</div></footer>
        @endif
    </section>
</div>
@endsection
