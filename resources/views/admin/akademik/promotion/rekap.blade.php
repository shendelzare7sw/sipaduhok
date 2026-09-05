@extends('layouts.app')

@section('title', 'Rekap Kenaikan Kelas')
@section('page-title', 'Rekap Kenaikan Kelas')
@section('page-subtitle', 'Simulasikan, proses, dan telusuri kenaikan kelas dari satu alur')

@section('content')
@php
    $activeTab = in_array(request('tab'), ['history', 'scheduling'], true) ? request('tab') : 'simulation';
    $isAdminContext = str_contains(Route::currentRouteName(), 'admin.');
    $routeBase = $isAdminContext ? 'admin.akademik.kenaikan-kelas' : 'waka.kenaikan-kelas';
    $settingsRoute = $routeBase . '.settings.index';
    $printRoute = $routeBase . '.report.print';
    $executeRoute = $routeBase . '.execute';
    $promoteRoute = $routeBase . '.promote-selected';
    $cancelRoute = $routeBase . '.cancel-schedule';
    $tabUrl = fn (string $tab) => route(Route::currentRouteName(), array_merge(request()->except(['page', 'sim_page']), ['tab' => $tab]));
    $historyEligibleIds = collect($students->items())->where('bisa_dinaikkan_manual', true)->pluck('siswa_id')->map(fn ($id) => (string) $id)->values();
    $simulationEligibleIds = collect($simulationData)->reject(fn ($row) => $row['result']['eligible'])->pluck('siswa.id')->map(fn ($id) => (string) $id)->values();
    $historyFilters = collect([$isAdminContext ? $cabangId : null, $jenjangFilter, $kelasId, $filterStatus])->filter()->count();
    $simulationFilters = collect([$isAdminContext ? $cabangId : null, $jenjangFilter, $kelasId, ($showTanpaJadwal ?? false) ? 1 : null])->filter()->count();
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5 lg:gap-3">
        <article class="rounded-2xl border border-emerald-200 bg-white p-3 shadow-sm sm:p-4">
            <div class="flex items-start justify-between gap-2"><div><p class="text-[10px] font-bold uppercase text-slate-500">Naik Kelas</p><p class="mt-2 text-xl font-extrabold text-slate-950">{{ $stats['NAIK_KELAS'] ?? 0 }}</p></div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-arrow-up" aria-hidden="true"></i></span></div>
        </article>
        <article class="rounded-2xl border border-sky-200 bg-white p-3 shadow-sm sm:p-4">
            <div class="flex items-start justify-between gap-2"><div><p class="text-[10px] font-bold uppercase text-slate-500">Lulus</p><p class="mt-2 text-xl font-extrabold text-slate-950">{{ $stats['LULUS'] ?? 0 }}</p></div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-600"><i class="fas fa-graduation-cap" aria-hidden="true"></i></span></div>
        </article>
        <article class="rounded-2xl border border-amber-200 bg-white p-3 shadow-sm sm:p-4">
            <div class="flex items-start justify-between gap-2"><div><p class="text-[10px] font-bold uppercase text-slate-500">Naik Dispensasi</p><p class="mt-2 text-xl font-extrabold text-slate-950">{{ $stats['NAIK_KELAS_TUNGGAKAN'] ?? 0 }}</p></div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fas fa-hand-holding-dollar" aria-hidden="true"></i></span></div>
        </article>
        <article class="rounded-2xl border border-violet-200 bg-white p-3 shadow-sm sm:p-4">
            <div class="flex items-start justify-between gap-2"><div><p class="text-[10px] font-bold uppercase text-slate-500">Lulus Dispensasi</p><p class="mt-2 text-xl font-extrabold text-slate-950">{{ $stats['LULUS_TUNGGAKAN'] ?? 0 }}</p></div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fas fa-user-check" aria-hidden="true"></i></span></div>
        </article>
        <article class="col-span-2 rounded-2xl border border-red-200 bg-white p-3 shadow-sm sm:col-span-1 sm:p-4">
            <div class="flex items-start justify-between gap-2"><div><p class="text-[10px] font-bold uppercase text-slate-500">Tidak Naik</p><p class="mt-2 text-xl font-extrabold text-slate-950">{{ $stats['TIDAK_NAIK_KELAS'] ?? 0 }}</p></div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600"><i class="fas fa-circle-xmark" aria-hidden="true"></i></span></div>
        </article>
    </section>

    <nav class="grid grid-cols-3 gap-1 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" aria-label="Bagian kenaikan kelas">
        <a href="{{ $tabUrl('simulation') }}" class="flex min-w-0 items-center justify-center gap-2 rounded-xl px-2 py-2.5 text-center text-[11px] font-bold transition sm:text-xs {{ $activeTab === 'simulation' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fas fa-flask" aria-hidden="true"></i><span class="truncate">Simulasi</span></a>
        <a href="{{ $tabUrl('history') }}" class="flex min-w-0 items-center justify-center gap-2 rounded-xl px-2 py-2.5 text-center text-[11px] font-bold transition sm:text-xs {{ $activeTab === 'history' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fas fa-clock-rotate-left" aria-hidden="true"></i><span class="truncate">Riwayat ({{ $students->total() }})</span></a>
        <a href="{{ $tabUrl('scheduling') }}" class="flex min-w-0 items-center justify-center gap-2 rounded-xl px-2 py-2.5 text-center text-[11px] font-bold transition sm:text-xs {{ $activeTab === 'scheduling' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"><i class="fas fa-clock" aria-hidden="true"></i><span class="truncate">Jadwal</span></a>
    </nav>

    @if ($activeTab === 'history')
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div><h2 class="text-base font-extrabold text-slate-950">Riwayat hasil eksekusi</h2><p class="mt-1 text-xs text-slate-500">Tahun Ajaran {{ $tahun->nama_tahun_ajaran }}</p></div>
                    <a href="{{ route($printRoute, request()->only(['tahun_ajaran_id', 'status', 'cabang_id', 'jenjang', 'kelas_id'])) }}" target="_blank" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 hover:bg-slate-50"><i class="fas fa-print" aria-hidden="true"></i>Cetak laporan</a>
                </div>

                <form action="{{ route(Route::currentRouteName()) }}" method="GET" class="mt-4 grid gap-2 lg:grid-cols-[180px_minmax(180px,1fr)_auto]">
                    <input type="hidden" name="tab" value="history">
                    <select name="tahun_ajaran_id" data-auto-submit class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 outline-none focus:border-brand-500">
                        @foreach ($allTahunAjaran as $ta)<option value="{{ $ta->id }}" @selected($tahun->id == $ta->id)>{{ $ta->nama_tahun_ajaran }}</option>@endforeach
                    </select>
                    <label class="relative min-w-0"><i class="fas fa-search absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ $search }}" placeholder="Cari nama siswa..." class="h-10 w-full rounded-xl border border-slate-200 !pl-10 pr-3 text-xs outline-none focus:border-brand-500"></label>
                    <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white hover:bg-slate-900"><i class="fas fa-search" aria-hidden="true"></i>Cari</button>

                    <details class="lg:col-span-3 rounded-xl border border-slate-200 bg-slate-50/70" @if($historyFilters) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-2 px-3 py-2.5 text-xs font-bold text-slate-700"><span><i class="fas fa-filter mr-2 text-brand-600" aria-hidden="true"></i>Filter lanjutan @if($historyFilters)<span class="ml-1 rounded-full bg-brand-100 px-2 py-0.5 text-[10px] text-brand-700">{{ $historyFilters }} aktif</span>@endif</span><i class="fas fa-chevron-down text-[10px]" aria-hidden="true"></i></summary>
                        <div class="grid gap-2 border-t border-slate-200 p-3 sm:grid-cols-2 lg:grid-cols-4">
                            @if ($isAdminContext)
                                <select name="cabang_id" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs outline-none focus:border-brand-500"><option value="">Semua Cabang</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}" @selected($cabangId == $cabang->id)>{{ $cabang->nama_cabang }}</option>@endforeach</select>
                            @endif
                            <select name="jenjang" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs outline-none focus:border-brand-500"><option value="">Semua Jenjang</option>@foreach(['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'] as $option)<option value="{{ $option }}" @selected($jenjangFilter === $option)>{{ $option }}</option>@endforeach</select>
                            <select name="kelas_id" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs outline-none focus:border-brand-500"><option value="">Semua Kelas</option>@foreach($kelasList as $kelas)<option value="{{ $kelas->id }}" @selected($kelasId == $kelas->id)>{{ $kelas->nama_kelas }}</option>@endforeach</select>
                            <select name="status" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs outline-none focus:border-brand-500"><option value="">Semua Status</option><option value="NAIK_KELAS" @selected($filterStatus === 'NAIK_KELAS')>Naik Kelas</option><option value="LULUS" @selected($filterStatus === 'LULUS')>Lulus</option><option value="NAIK_KELAS_TUNGGAKAN" @selected($filterStatus === 'NAIK_KELAS_TUNGGAKAN')>Naik (Dispensasi)</option><option value="LULUS_TUNGGAKAN" @selected($filterStatus === 'LULUS_TUNGGAKAN')>Lulus (Dispensasi)</option><option value="TIDAK_NAIK_KELAS" @selected($filterStatus === 'TIDAK_NAIK_KELAS')>Tidak Naik</option></select>
                            <div class="flex gap-2 sm:col-span-2 lg:col-span-4"><button type="submit" class="inline-flex h-10 flex-1 items-center justify-center rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700">Terapkan filter</button><a href="{{ route(Route::currentRouteName(), ['tab' => 'history', 'tahun_ajaran_id' => $tahun->id]) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-600 hover:bg-slate-100">Reset</a></div>
                        </div>
                    </details>
                </form>
            </header>

            @if ($historyEligibleIds->isNotEmpty())
                <div class="mx-4 my-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-900 sm:mx-5"><i class="fas fa-triangle-exclamation mr-1" aria-hidden="true"></i>Siswa bertanda pilihan dapat dinaikkan manual karena keuangannya sudah aman, tetapi akademiknya belum terukur saat eksekusi.</div>
            @endif

            <form
                action="{{ route($promoteRoute) }}"
                method="POST"
                data-confirm
                data-confirm-title="Naikkan siswa terpilih?"
                data-confirm-message="Sistem akan mencari kelas lanjutan dan menandai proses ini sebagai override manual."
                data-confirm-text="Ya, naikkan"
                x-data="{ selected: [], pageIds: @js($historyEligibleIds), togglePage() { this.selected = this.selected.length === this.pageIds.length ? [] : [...this.pageIds] } }"
            >
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">

                @if ($historyEligibleIds->isNotEmpty())
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5"><button type="button" @click="togglePage()" class="text-xs font-bold text-brand-700 hover:text-brand-800" x-text="selected.length === pageIds.length ? 'Batalkan pilihan halaman' : 'Pilih semua yang dapat diproses'"></button><span class="text-[11px] text-slate-500"><strong x-text="selected.length">0</strong> dipilih</span></div>
                @endif

                <div class="hidden grid-cols-[minmax(180px,1.35fr)_minmax(90px,.7fr)_minmax(90px,.7fr)_minmax(110px,.8fr)_minmax(130px,1fr)_minmax(120px,.9fr)] bg-slate-50 px-5 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500 xl:grid"><span>Nama Siswa</span><span>Kelas Asal</span><span>Kelas Tujuan</span><span>Keuangan</span><span>Akademik</span><span>Hasil</span></div>
                <div class="divide-y divide-slate-100">
                    @forelse ($students as $data)
                        @php
                            $statusLabel = match ($data->status_kelulusan) {
                                'NAIK_KELAS' => 'Naik Kelas', 'LULUS' => 'Lulus', 'NAIK_KELAS_TUNGGAKAN' => 'Naik Dispensasi', 'LULUS_TUNGGAKAN' => 'Lulus Dispensasi', 'TIDAK_NAIK_KELAS' => 'Tidak Naik', default => str_replace('_', ' ', $data->status_kelulusan),
                            };
                            $statusClass = match ($data->status_kelulusan) {
                                'NAIK_KELAS' => 'bg-emerald-50 text-emerald-700', 'LULUS' => 'bg-sky-50 text-sky-700', 'NAIK_KELAS_TUNGGAKAN' => 'bg-amber-50 text-amber-700', 'LULUS_TUNGGAKAN' => 'bg-violet-50 text-violet-700', 'TIDAK_NAIK_KELAS' => 'bg-red-50 text-red-700', default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <article class="grid grid-cols-2 gap-3 p-4 hover:bg-slate-50/80 sm:p-5 xl:grid-cols-[minmax(180px,1.35fr)_minmax(90px,.7fr)_minmax(90px,.7fr)_minmax(110px,.8fr)_minmax(130px,1fr)_minmax(120px,.9fr)] xl:items-center xl:gap-0">
                            <div class="col-span-2 flex min-w-0 items-start gap-3 xl:col-span-1 xl:pr-3">@if($data->bisa_dinaikkan_manual)<input type="checkbox" name="siswa_ids[]" value="{{ $data->siswa_id }}" x-model="selected" class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500">@endif<div class="min-w-0"><p class="break-words text-sm font-extrabold text-slate-950">{{ $data->nama_lengkap }}</p>@if($data->bisa_dinaikkan_manual)<p class="mt-1 text-[10px] font-bold text-amber-700">Dapat diproses manual</p>@endif</div></div>
                            <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Kelas Asal</p><p class="mt-1 break-words text-xs text-slate-700 xl:mt-0">{{ $data->kelas_asal }}</p></div>
                            <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Kelas Tujuan</p><p class="mt-1 break-words text-xs text-slate-700 xl:mt-0">{{ $data->kelas_tujuan ?? '-' }}</p></div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Keuangan</p>
                                <p class="mt-1 text-xs font-bold {{ $data->status_pembayaran === 'LUNAS' ? 'text-emerald-700' : 'text-red-700' }} xl:mt-0">
                                    {{ $data->status_pembayaran === 'LUNAS' ? 'Lunas' : 'Belum Lunas' }}
                                    @if ($data->izin_khusus_ketua)
                                        · Dispensasi
                                    @endif
                                </p>
                            </div>
                            <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Akademik</p>@if($data->akademik_override_tanpa_jadwal ?? false)<p class="mt-1 text-xs font-bold text-amber-700 xl:mt-0">Aman · override tanpa jadwal</p>@else<p class="mt-1 text-xs text-slate-700 xl:mt-0">{{ $data->persentase_nilai_tuntas }}% · {{ $data->jumlah_mapel_tuntas }}/{{ $data->total_mapel }} mapel</p>@endif</div>
                            <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Hasil</p><span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold xl:mt-0 {{ $statusClass }}">{{ $statusLabel }}</span></div>
                        </article>
                    @empty
                        <div class="px-5 py-14 text-center"><i class="fas fa-circle-info text-2xl text-slate-300" aria-hidden="true"></i><p class="mt-3 text-sm font-bold text-slate-700">Belum ada riwayat eksekusi.</p><p class="mt-1 text-xs text-slate-500">Jalankan proses dari tab Simulasi saat semua persiapan sudah lengkap.</p></div>
                    @endforelse
                </div>

                <footer class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50/70 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">@if($historyEligibleIds->isNotEmpty())<button type="submit" :disabled="selected.length === 0" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 text-xs font-bold text-white hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-40"><i class="fas fa-arrow-up" aria-hidden="true"></i>Naikkan terpilih (<span x-text="selected.length">0</span>)</button>@else<span></span>@endif<div class="min-w-0">{{ $students->withQueryString()->links() }}</div></footer>
            </form>
        </section>
    @elseif ($activeTab === 'simulation')
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div><h2 class="text-base font-extrabold text-slate-950">Simulasi kenaikan kelas</h2><p class="mt-1 text-xs text-slate-500">Tahun Ajaran {{ $tahun->nama_tahun_ajaran }}</p></div>
                    <form action="{{ route($executeRoute) }}" method="POST" data-confirm data-confirm-title="Proses kenaikan kelas sekarang?" data-confirm-message="Semua siswa aktif akan diperiksa dan hasilnya dicatat. Pastikan tahun ajaran serta kelas tujuan sudah benar." data-confirm-text="Ya, proses sekarang">@csrf<input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}"><button type="submit" @disabled(!$promotionReadiness['isReady']) class="inline-flex h-10 items-center justify-center gap-2 rounded-xl px-4 text-xs font-bold text-white {{ $promotionReadiness['isReady'] ? 'bg-brand-600 hover:bg-brand-700' : 'cursor-not-allowed bg-slate-300' }}"><i class="fas {{ $promotionReadiness['isReady'] ? 'fa-gears' : 'fa-lock' }}" aria-hidden="true"></i>Proses kenaikan kelas</button></form>
                </div>

                <div class="mt-4 inline-flex rounded-xl bg-slate-100 p-1"><a href="{{ route(Route::currentRouteName(), array_merge(request()->except(['sim_mode', 'sim_page']), ['sim_mode' => 'current', 'tab' => 'simulation'])) }}" class="rounded-lg px-3 py-2 text-[11px] font-bold {{ ($simMode ?? 'current') === 'current' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500' }}">Keadaan saat ini</a><a href="{{ route(Route::currentRouteName(), array_merge(request()->except(['sim_mode', 'sim_page']), ['sim_mode' => 'historical', 'tab' => 'simulation'])) }}" class="rounded-lg px-3 py-2 text-[11px] font-bold {{ ($simMode ?? 'current') === 'historical' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500' }}">Saat eksekusi</a></div>

                <form action="{{ route(Route::currentRouteName()) }}" method="GET" class="mt-4 grid gap-2 lg:grid-cols-[180px_minmax(180px,1fr)_auto]">
                    <input type="hidden" name="tab" value="simulation"><input type="hidden" name="sim_mode" value="{{ $simMode ?? 'current' }}">
                    <select name="tahun_ajaran_id" data-auto-submit class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 outline-none focus:border-brand-500">@foreach($allTahunAjaran as $ta)<option value="{{ $ta->id }}" @selected($tahun->id == $ta->id)>{{ $ta->nama_tahun_ajaran }}</option>@endforeach</select>
                    <label class="relative min-w-0"><i class="fas fa-search absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ $search }}" placeholder="Cari nama siswa..." class="h-10 w-full rounded-xl border border-slate-200 !pl-10 pr-3 text-xs outline-none focus:border-brand-500"></label>
                    <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white hover:bg-slate-900"><i class="fas fa-search" aria-hidden="true"></i>Cari</button>
                    <details class="lg:col-span-3 rounded-xl border border-slate-200 bg-slate-50/70" @if($simulationFilters) open @endif><summary class="flex cursor-pointer list-none items-center justify-between gap-2 px-3 py-2.5 text-xs font-bold text-slate-700"><span><i class="fas fa-filter mr-2 text-brand-600" aria-hidden="true"></i>Filter lanjutan @if($simulationFilters)<span class="ml-1 rounded-full bg-brand-100 px-2 py-0.5 text-[10px] text-brand-700">{{ $simulationFilters }} aktif</span>@endif</span><i class="fas fa-chevron-down text-[10px]" aria-hidden="true"></i></summary><div class="grid gap-2 border-t border-slate-200 p-3 sm:grid-cols-2 lg:grid-cols-4">@if($isAdminContext)<select name="cabang_id" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs outline-none focus:border-brand-500"><option value="">Semua Cabang</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}" @selected($cabangId == $cabang->id)>{{ $cabang->nama_cabang }}</option>@endforeach</select>@endif<select name="jenjang" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs outline-none focus:border-brand-500"><option value="">Semua Jenjang</option>@foreach(['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'] as $option)<option value="{{ $option }}" @selected($jenjangFilter === $option)>{{ $option }}</option>@endforeach</select><select name="kelas_id" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs outline-none focus:border-brand-500"><option value="">Semua Kelas</option>@foreach($kelasList as $kelas)<option value="{{ $kelas->id }}" @selected($kelasId == $kelas->id)>{{ $kelas->nama_kelas }}</option>@endforeach</select>@if(($siswaTanpaJadwalCount ?? 0) > 0 || ($showTanpaJadwal ?? false))<label class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700"><input type="checkbox" name="tanpa_jadwal" value="1" @checked($showTanpaJadwal ?? false) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">Tanpa jadwal ({{ $siswaTanpaJadwalCount ?? 0 }})</label>@endif<div class="flex gap-2 sm:col-span-2 lg:col-span-4"><button type="submit" class="inline-flex h-10 flex-1 items-center justify-center rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700">Terapkan filter</button><a href="{{ route(Route::currentRouteName(), ['tab' => 'simulation', 'sim_mode' => $simMode ?? 'current', 'tahun_ajaran_id' => $tahun->id]) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-600 hover:bg-slate-100">Reset</a></div></div></details>
                </form>
            </header>

            <div class="space-y-3 p-4 sm:p-5">
                @if (!$promotionReadiness['hasNextTA'])
                    <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-xs leading-5 text-red-800"><strong>Tahun ajaran berikutnya belum dibuat.</strong> Siapkan periode baru sebelum memproses kenaikan. @if($isAdminContext)<a href="{{ route('admin.tahun-ajaran.create') }}" class="font-bold underline">Buat tahun ajaran</a>@endif</div>
                @elseif ($promotionReadiness['kelasBaruCount'] === 0)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-900"><strong>Kelas tujuan belum tersedia.</strong> Buat struktur kelas untuk {{ $promotionReadiness['nextTA']->nama_tahun_ajaran }}. @if($isAdminContext)<a href="{{ route('admin.kelas.index') }}" class="font-bold underline">Kelola kelas</a>@endif</div>
                @else
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs leading-5 text-emerald-800"><strong>Persiapan lengkap.</strong> {{ $promotionReadiness['kelasBaruCount'] }} kelas tujuan tersedia di {{ $promotionReadiness['nextTA']->nama_tahun_ajaran }}.</div>
                @endif

                @if (($simMode ?? 'current') === 'historical')
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs leading-5 text-slate-700">Snapshot ini hanya untuk membaca keadaan saat eksekusi terakhir. Gunakan <strong>Keadaan saat ini</strong> untuk tindakan manual.</div>
                @elseif ($showTanpaJadwal ?? false)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-900">Daftar ini khusus siswa yang kelasnya belum memiliki jadwal. Jika diproses manual, akademiknya dicatat sebagai override, bukan hasil pengukuran nilai.</div>
                @elseif (($siswaTanpaJadwalCount ?? 0) > 0)
                    <div class="rounded-xl border border-sky-200 bg-sky-50 p-3 text-xs leading-5 text-sky-800"><strong>{{ $siswaTanpaJadwalCount }} siswa tanpa jadwal disembunyikan</strong> agar daftar perhatian akademik tetap akurat. Tampilkan melalui filter jika perlu diproses manual.</div>
                @endif
            </div>

            @if (($simMode ?? 'current') === 'current')
                <form action="{{ route($promoteRoute) }}" method="POST" data-confirm data-confirm-title="Naikkan siswa terpilih?" data-confirm-message="Siswa terpilih akan diproses menuju kelas berikutnya. Periksa kembali pilihan Anda." data-confirm-text="Ya, naikkan" x-data="{ selected: [], pageIds: @js($simulationEligibleIds), allPages: false, togglePage() { this.allPages = false; this.selected = this.selected.length === this.pageIds.length ? [] : [...this.pageIds] } }">
                    @csrf<input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}"><input type="hidden" name="select_all" :value="allPages ? 1 : 0"><input type="hidden" name="cabang_id" value="{{ $cabangId }}"><input type="hidden" name="jenjang" value="{{ $jenjangFilter }}"><input type="hidden" name="kelas_id" value="{{ $kelasId }}"><input type="hidden" name="search" value="{{ $search }}">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-y border-slate-100 px-4 py-3 sm:px-5"><div class="flex flex-wrap gap-2"><button type="button" @click="togglePage()" class="rounded-lg bg-brand-50 px-3 py-2 text-[11px] font-bold text-brand-700 hover:bg-brand-100" x-text="selected.length === pageIds.length ? 'Batalkan halaman' : 'Pilih yang tertunda di halaman ini'"></button>@if($activeStudentsLinks->lastPage() > 1)<button type="button" @click="allPages = !allPages; selected = []" class="rounded-lg bg-amber-50 px-3 py-2 text-[11px] font-bold text-amber-700 hover:bg-amber-100" x-text="allPages ? 'Batalkan semua halaman' : 'Pilih semua {{ $totalActiveGlobal ?? $activeStudentsLinks->total() }} data'"></button>@endif</div><span class="text-[11px] text-slate-500" x-text="allPages ? '{{ $totalActiveGlobal ?? 0 }} data dipilih' : selected.length + ' dipilih'"></span></div>
            @endif

            <div class="hidden grid-cols-[minmax(180px,1.35fr)_minmax(90px,.7fr)_minmax(140px,1fr)_minmax(130px,.9fr)_minmax(110px,.8fr)] bg-slate-50 px-5 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500 xl:grid"><span>Nama Siswa</span><span>Kelas</span><span>Keuangan</span><span>Akademik</span><span>Prediksi</span></div>
            <div class="divide-y divide-slate-100">
                @forelse ($simulationData as $sim)
                    @php
                        $isFinalLevel = preg_match('/(9|IX|12|XII)/', strtoupper($sim['siswa']->kelas->nama_kelas ?? ''));
                    @endphp
                    <article class="grid grid-cols-2 gap-3 p-4 hover:bg-slate-50/80 sm:p-5 xl:grid-cols-[minmax(180px,1.35fr)_minmax(90px,.7fr)_minmax(140px,1fr)_minmax(130px,.9fr)_minmax(110px,.8fr)] xl:items-center xl:gap-0">
                        <div class="col-span-2 flex min-w-0 items-start gap-3 xl:col-span-1 xl:pr-3">@if(($simMode ?? 'current') === 'current' && !$sim['result']['eligible'])<input type="checkbox" name="siswa_ids[]" value="{{ $sim['siswa']->id }}" x-model="selected" @change="allPages = false" class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500">@endif<div class="min-w-0"><p class="break-words text-sm font-extrabold text-slate-950">{{ $sim['siswa']->nama_lengkap }}</p><p class="mt-1 text-[10px] text-slate-500">{{ $sim['siswa']->nis ?? 'NIS belum tersedia' }}</p></div></div>
                        <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Kelas</p><p class="mt-1 break-words text-xs text-slate-700 xl:mt-0">{{ $sim['siswa']->kelas->nama_kelas ?? '-' }}</p></div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Keuangan</p>
                            @if ($sim['result']['financial']['status'] === 'LUNAS')
                                <p class="mt-1 text-xs font-bold text-emerald-700 xl:mt-0">Lunas</p>
                            @else
                                <p class="mt-1 break-words text-xs font-bold text-red-700 xl:mt-0">Tunggakan Rp {{ number_format($sim['result']['financial']['unpaid_amount'], 0, ',', '.') }}</p>
                                @if ($sim['result']['financial']['is_dispensasi'])
                                    <p class="mt-1 text-[10px] font-bold text-amber-700">Dispensasi disetujui</p>
                                @endif
                            @endif
                        </div>
                        <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Akademik</p><p class="mt-1 text-xs font-bold {{ $sim['result']['academic']['is_tuntas'] ? 'text-emerald-700' : 'text-red-700' }} xl:mt-0">{{ $sim['result']['academic']['is_tuntas'] ? 'Aman' : 'Rawan' }} · {{ $sim['result']['academic']['percentage'] }}%</p><p class="mt-1 text-[10px] text-slate-500">{{ $sim['result']['academic']['tuntas_count'] }} mapel tuntas</p></div>
                        <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Prediksi</p><p class="mt-1 text-xs font-extrabold {{ $sim['result']['eligible'] ? ($isFinalLevel ? 'text-sky-700' : 'text-emerald-700') : 'text-red-700' }} xl:mt-0">{{ $sim['result']['eligible'] ? ($isFinalLevel ? 'Siap Lulus' : 'Siap Naik') : 'Tertunda' }}</p></div>
                    </article>
                @empty
                    <div class="px-5 py-14 text-center"><i class="fas fa-users text-2xl text-slate-300" aria-hidden="true"></i><p class="mt-3 text-sm font-bold text-slate-700">Tidak ada siswa pada pilihan ini.</p><p class="mt-1 text-xs text-slate-500">Ubah filter atau periksa kesiapan data kelas.</p></div>
                @endforelse
            </div>

            @if (($simMode ?? 'current') === 'current')
                    <footer class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50/70 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5"><button type="submit" :disabled="selected.length === 0 && !allPages" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40"><i class="fas fa-arrow-up" aria-hidden="true"></i>Naikkan terpilih</button><div class="min-w-0">{{ $activeStudentsLinks->appends(['tab' => 'simulation'])->withQueryString()->links() }}</div></footer>
                </form>
            @else
                <footer class="border-t border-slate-200 bg-slate-50/70 px-4 py-4 sm:px-5"><div class="min-w-0">{{ $activeStudentsLinks->appends(['tab' => 'simulation'])->withQueryString()->links() }}</div></footer>
            @endif
        </section>
    @else
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5"><div><h2 class="text-base font-extrabold text-slate-950">Jadwal eksekusi otomatis</h2><p class="mt-1 text-xs text-slate-500">Pantau proses yang dijadwalkan untuk Tahun Ajaran {{ $tahun->nama_tahun_ajaran }}.</p></div><a href="{{ route($settingsRoute) }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-sliders" aria-hidden="true"></i>Atur jadwal</a></header>
            <div class="mx-4 my-4 rounded-xl border border-sky-200 bg-sky-50 p-3 text-xs leading-5 text-sky-800 sm:mx-5">Tanggal dan waktu eksekusi dikelola melalui menu Pengaturan Kenaikan Kelas agar hanya ada satu sumber konfigurasi.</div>
            <div class="hidden grid-cols-[minmax(150px,1fr)_110px_minmax(130px,.9fr)_minmax(120px,.8fr)_minmax(120px,1fr)_80px] bg-slate-50 px-5 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500 xl:grid"><span>Jadwal</span><span>Status</span><span>Dibuat Oleh</span><span>Statistik</span><span>Log</span><span class="text-right">Aksi</span></div>
            <div class="divide-y divide-slate-100">
                @forelse ($schedules as $schedule)
                    @php
                        $scheduleLabel = match ($schedule->status) { 'PENDING' => 'Menunggu', 'RUNNING' => 'Berjalan', 'COMPLETED' => 'Selesai', 'FAILED' => 'Gagal', default => 'Batal' };
                        $scheduleClass = match ($schedule->status) { 'PENDING' => 'bg-amber-50 text-amber-700', 'RUNNING' => 'bg-sky-50 text-sky-700', 'COMPLETED' => 'bg-emerald-50 text-emerald-700', 'FAILED' => 'bg-red-50 text-red-700', default => 'bg-slate-100 text-slate-600' };
                    @endphp
                    <article class="grid grid-cols-2 gap-3 p-4 hover:bg-slate-50/80 sm:p-5 xl:grid-cols-[minmax(150px,1fr)_110px_minmax(130px,.9fr)_minmax(120px,.8fr)_minmax(120px,1fr)_80px] xl:items-center xl:gap-0">
                        <div class="col-span-2 xl:col-span-1"><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Jadwal</p><p class="mt-1 text-sm font-extrabold text-slate-950 xl:mt-0">{{ $schedule->scheduled_at->format('d/m/Y H:i') }}</p></div>
                        <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Status</p><span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold xl:mt-0 {{ $scheduleClass }}">{{ $scheduleLabel }}</span></div>
                        <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Dibuat Oleh</p><p class="mt-1 break-words text-xs font-bold text-slate-700 xl:mt-0">{{ $schedule->creator->name ?? '-' }}</p><p class="mt-1 text-[10px] text-slate-500">{{ $schedule->creator?->role === 'wakil_kepala_sekolah' ? 'Wakil Kepala Sekolah' : ucfirst($schedule->creator?->role ?? '') }}</p></div>
                        <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Statistik</p>@if($schedule->status === 'COMPLETED')<p class="mt-1 text-[11px] leading-5 text-slate-600 xl:mt-0">Proses {{ $schedule->students_processed }} · Naik {{ $schedule->students_promoted }} · Lulus {{ $schedule->students_graduated }} · Gagal {{ $schedule->students_failed }}</p>@else<p class="mt-1 text-xs text-slate-400 xl:mt-0">Belum tersedia</p>@endif</div>
                        <div><p class="text-[10px] font-bold uppercase text-slate-400 xl:hidden">Log</p><p class="mt-1 break-words text-[11px] text-slate-500 xl:mt-0" title="{{ $schedule->execution_log }}">{{ Str::limit($schedule->execution_log ?? '-', 50) }}</p></div>
                        <div class="col-span-2 flex justify-end xl:col-span-1">@if($schedule->status === 'PENDING')<form action="{{ route($cancelRoute, $schedule->id) }}" method="POST" data-confirm data-confirm-title="Batalkan jadwal eksekusi?" data-confirm-message="Jadwal {{ $schedule->scheduled_at->format('d/m/Y H:i') }} akan dibatalkan dan pengaturan tanggal eksekusi direset." data-confirm-text="Ya, batalkan">@csrf<button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-red-50 px-3 text-[11px] font-bold text-red-700 hover:bg-red-100">Batalkan</button></form>@else<span class="text-xs text-slate-300">—</span>@endif</div>
                    </article>
                @empty
                    <div class="px-5 py-14 text-center"><i class="fas fa-calendar-check text-2xl text-slate-300" aria-hidden="true"></i><p class="mt-3 text-sm font-bold text-slate-700">Belum ada jadwal otomatis.</p><p class="mt-1 text-xs text-slate-500">Atur tanggal eksekusi saat proses memang perlu dijalankan otomatis.</p></div>
                @endforelse
            </div>
        </section>
    @endif
</div>
@endsection
