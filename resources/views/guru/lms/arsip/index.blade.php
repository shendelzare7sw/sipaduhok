@extends('layouts.app')

@section('title', 'Arsip LMS')
@section('page-title', 'Arsip LMS')
@section('page-subtitle', 'Materi, tugas, latihan, dan ujian Anda lintas tahun ajaran')

@section('content')
@php
    $sections = [
        'materi' => ['icon' => 'fa-book-open', 'label' => 'Materi', 'tone' => 'bg-blue-50 text-blue-700', 'ring' => 'ring-blue-100'],
        'tugas' => ['icon' => 'fa-list-check', 'label' => 'Tugas', 'tone' => 'bg-amber-50 text-amber-700', 'ring' => 'ring-amber-100'],
        'latihan' => ['icon' => 'fa-pencil-ruler', 'label' => 'Latihan', 'tone' => 'bg-violet-50 text-violet-700', 'ring' => 'ring-violet-100'],
        'ujian' => ['icon' => 'fa-file-lines', 'label' => 'Ujian', 'tone' => 'bg-rose-50 text-rose-700', 'ring' => 'ring-rose-100'],
    ];
    $bisaSalin = $kelasMapelTujuan->isNotEmpty();
    $sectionValues = collect($sections)->map(fn ($cfg, $key) => $arsip[$key]->map(fn ($item) => $key.':'.$item->id)->values())->all();
    $firstTujuan = $bisaSalin ? $kelasMapelTujuan->first()['kelas_id'].'|'.$kelasMapelTujuan->first()['mata_pelajaran_id'] : '';
    $inputClass = 'mt-1 block h-10 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 text-sm font-medium text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100';
@endphp

<div class="min-w-0 w-full space-y-5">
    <header class="rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:px-5">
        <h1 class="flex items-center gap-2 text-lg font-extrabold text-slate-900"><i class="fa-solid fa-box-archive text-brand-600" aria-hidden="true"></i>Arsip LMS</h1>
        <p class="mt-1 text-xs leading-5 text-slate-500 sm:text-sm">Lihat semua materi, tugas, latihan, dan ujian yang pernah Anda buat lintas tahun ajaran. Salin ke kelas aktif untuk dipakai ulang.</p>
    </header>

    @if(! $bisaSalin)
        <p class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" role="alert">
            <i class="fa-solid fa-triangle-exclamation mt-0.5" aria-hidden="true"></i>
            <span><strong>Anda belum ditugaskan mengajar di TA aktif.</strong> Anda masih bisa melihat arsip, tetapi tombol "Salin" dinonaktifkan sampai admin menugaskan Anda ke kelas dan mapel di TA yang sedang berjalan.</span>
        </p>
    @endif

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4" aria-label="Ringkasan arsip">
        @foreach($sections as $key => $cfg)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $arsip[$key]->count() }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $cfg['label'] }}</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $cfg['tone'] }}"><i class="fa-solid {{ $cfg['icon'] }}" aria-hidden="true"></i></span>
                </div>
            </article>
        @endforeach
    </section>

    <nav class="flex flex-wrap gap-2" aria-label="Jenis konten">
        @php
            $tabs = ['' => ['Semua', null]] + collect($sections)->map(fn ($cfg) => [$cfg['label'], $cfg['icon']])->all();
        @endphp
        @foreach($tabs as $tabKey => [$tabLabel, $tabIcon])
            @php
                $tabActive = ($filters['type'] ?? '') === (string) $tabKey;
                $tabQuery = $tabKey === '' ? request()->except('type') : array_merge(request()->all(), ['type' => $tabKey]);
            @endphp
            <a href="{{ route('guru.lms.arsip.index', $tabQuery) }}" class="inline-flex min-h-10 items-center gap-1.5 whitespace-nowrap rounded-full px-4 text-xs font-bold no-underline transition {{ $tabActive ? 'bg-brand-600 text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-600 hover:border-brand-300 hover:text-brand-700' }}">
                @if($tabIcon)<i class="fa-solid {{ $tabIcon }}" aria-hidden="true"></i>@endif{{ $tabLabel }}
            </a>
        @endforeach
    </nav>

    <form method="GET" action="{{ route('guru.lms.arsip.index') }}" class="grid min-w-0 grid-cols-1 gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[repeat(3,minmax(0,1fr))_auto] lg:items-end">
        @if($filters['type'])<input type="hidden" name="type" value="{{ $filters['type'] }}">@endif

        <label class="min-w-0 text-xs font-bold text-slate-600">Tahun ajaran
            <select name="tahun_ajaran_id" class="{{ $inputClass }}">
                <option value="">Semua TA</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected(($filters['tahun_ajaran_id'] ?? null) == $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>
                @endforeach
            </select>
        </label>
        <label class="min-w-0 text-xs font-bold text-slate-600">Mata pelajaran
            <select name="mapel_id" class="{{ $inputClass }}">
                <option value="">Semua mapel</option>
                @foreach($mataPelajarans as $mp)
                    <option value="{{ $mp->id }}" @selected(($filters['mapel_id'] ?? null) == $mp->id)>{{ $mp->nama_mapel }}</option>
                @endforeach
            </select>
        </label>
        <label class="min-w-0 text-xs font-bold text-slate-600">Cari judul
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Kata kunci..." class="{{ $inputClass }}">
        </label>
        <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
            <button type="submit" class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fa-solid fa-filter" aria-hidden="true"></i>Filter</button>
            <a href="{{ route('guru.lms.arsip.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 px-4 text-xs font-bold text-slate-600 no-underline hover:bg-slate-50">Reset</a>
        </div>
    </form>

    @if($totalKonten === 0)
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-box-archive" aria-hidden="true"></i></span>
            <h2 class="mt-4 font-extrabold text-slate-900">Belum ada konten</h2>
            <p class="mt-1 text-sm text-slate-500">Anda belum pernah membuat materi, tugas, atau ujian, atau filter terlalu sempit.</p>
        </section>
    @else
        <form
            method="POST"
            action="{{ route('guru.lms.arsip.salin-bulk') }}"
            class="min-w-0 space-y-5"
            x-data="{
                selected: [],
                tujuan: @js($firstTujuan),
                sections: @js($sectionValues),
                allIn(key) { return this.sections[key].length > 0 && this.sections[key].every((v) => this.selected.includes(v)); },
                someIn(key) { return this.sections[key].some((v) => this.selected.includes(v)); },
                toggleSection(key, checked) {
                    const rest = this.selected.filter((v) => !this.sections[key].includes(v));
                    this.selected = checked ? rest.concat(this.sections[key]) : rest;
                },
            }"
            @submit="if (!selected.length || !tujuan) $event.preventDefault()"
        >
            @csrf
            {{-- Pertahankan filter aktif setelah salin (redirect balik ke arsip) --}}
            @if($filters['type'])<input type="hidden" name="type" value="{{ $filters['type'] }}">@endif
            @if($filters['tahun_ajaran_id'] ?? null)<input type="hidden" name="tahun_ajaran_id" value="{{ $filters['tahun_ajaran_id'] }}">@endif
            @if($filters['mapel_id'] ?? null)<input type="hidden" name="mapel_id" value="{{ $filters['mapel_id'] }}">@endif
            @if($filters['search'] ?? null)<input type="hidden" name="search" value="{{ $filters['search'] }}">@endif

            @foreach($sections as $sectionKey => $cfg)
                @if($arsip[$sectionKey]->isNotEmpty())
                    <section class="min-w-0">
                        <header class="mb-3 flex flex-wrap items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $cfg['tone'] }}"><i class="fa-solid {{ $cfg['icon'] }}" aria-hidden="true"></i></span>
                            <h2 class="text-base font-extrabold text-slate-900">{{ $cfg['label'] }}</h2>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">{{ $arsip[$sectionKey]->count() }}</span>
                            @if($bisaSalin)
                                <label class="ml-auto inline-flex min-h-9 cursor-pointer items-center gap-2 text-xs font-bold text-slate-600">
                                    <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" :checked="allIn('{{ $sectionKey }}')" x-effect="$el.indeterminate = someIn('{{ $sectionKey }}') && !allIn('{{ $sectionKey }}')" @change="toggleSection('{{ $sectionKey }}', $el.checked)">
                                    Pilih semua
                                </label>
                            @endif
                        </header>

                        <div class="grid min-w-0 gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($arsip[$sectionKey] as $item)
                                @php
                                    $judul = match($sectionKey) {
                                        'materi' => $item->judul_materi,
                                        'tugas' => $item->judul_tugas,
                                        default => $item->judul_ujian,
                                    };
                                    $tanggal = match($sectionKey) {
                                        'materi' => $item->tanggal_upload,
                                        default => $item->tanggal_mulai,
                                    };
                                    $isAktif = $item->kelas?->tahunAjaran?->is_active;
                                    $nilaiItem = $sectionKey.':'.$item->id;
                                @endphp
                                <article class="flex min-w-0 flex-col rounded-2xl border bg-white p-4 shadow-sm transition" :class="selected.includes('{{ $nilaiItem }}') ? 'border-brand-400 ring-2 ring-brand-100' : 'border-slate-200 hover:shadow-md'">
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="inline-flex max-w-full items-center truncate rounded-full px-2.5 py-1 text-[10px] font-extrabold {{ $isAktif ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $item->kelas?->tahunAjaran?->nama_tahun_ajaran ?? 'TA -' }}@if($isAktif) · Aktif @endif
                                        </span>
                                        @if($bisaSalin)
                                            <input type="checkbox" name="items[]" value="{{ $nilaiItem }}" x-model="selected" class="h-5 w-5 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500" title="Pilih untuk salin massal" aria-label="Pilih {{ $judul }}">
                                        @endif
                                    </div>

                                    <div class="mt-3 flex min-w-0 items-start gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $cfg['tone'] }}"><i class="fa-solid {{ $cfg['icon'] }}" aria-hidden="true"></i></span>
                                        <h3 class="min-w-0 break-words text-sm font-extrabold leading-snug text-slate-900">{{ $judul }}</h3>
                                    </div>

                                    <dl class="mt-3 flex-1 space-y-1.5 text-xs text-slate-500">
                                        <div class="flex items-center gap-2"><dt class="sr-only">Kelas</dt><i class="fa-solid fa-school w-4 text-center text-slate-400" aria-hidden="true"></i><dd class="min-w-0 truncate">{{ $item->kelas?->nama_kelas ?? '-' }}</dd></div>
                                        <div class="flex items-center gap-2"><dt class="sr-only">Mata pelajaran</dt><i class="fa-solid fa-book w-4 text-center text-slate-400" aria-hidden="true"></i><dd class="min-w-0 truncate">{{ $item->mataPelajaran?->nama_mapel ?? '-' }}</dd></div>
                                        @if($tanggal)
                                            <div class="flex items-center gap-2"><dt class="sr-only">Tanggal</dt><i class="fa-solid fa-calendar w-4 text-center text-slate-400" aria-hidden="true"></i><dd>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d M Y') }}</dd></div>
                                        @endif
                                        @if($sectionKey === 'ujian' || $sectionKey === 'latihan')
                                            @php $tipeLabel = method_exists($item, 'getTipeLabelAttribute') ? $item->tipe_label : $item->tipe_ujian; @endphp
                                            <div class="flex items-center gap-2"><dt class="sr-only">Tipe</dt><i class="fa-solid fa-tag w-4 text-center text-slate-400" aria-hidden="true"></i><dd class="min-w-0 truncate">{{ $tipeLabel }}</dd></div>
                                        @endif
                                    </dl>

                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        <a href="{{ route('guru.lms.arsip.preview', [$sectionKey, $item->id]) }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fa-solid fa-eye" aria-hidden="true"></i>Preview</a>
                                        @if($bisaSalin)
                                            <a href="{{ route('guru.lms.arsip.form-salin', [$sectionKey, $item->id]) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-brand-600 px-3 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fa-solid fa-copy" aria-hidden="true"></i>Salin</a>
                                        @else
                                            <button type="button" disabled title="Anda belum ditugaskan ke kelas TA aktif" class="inline-flex min-h-10 cursor-not-allowed items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-slate-100 px-3 text-xs font-bold text-slate-400"><i class="fa-solid fa-ban" aria-hidden="true"></i>Salin</button>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach

            @if($bisaSalin)
                <div x-cloak x-show="selected.length > 0" x-transition class="sticky bottom-4 z-30 rounded-2xl border border-brand-200 bg-white/95 p-4 shadow-xl backdrop-blur">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <p class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-square-check text-brand-600" aria-hidden="true"></i><span x-text="selected.length"></span> item dipilih</p>
                        <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-center">
                            <label class="flex min-w-0 items-center gap-2 text-xs font-bold text-slate-600">
                                <span class="shrink-0">Tujuan</span>
                                <select x-model="tujuan" class="h-10 min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100 sm:w-72">
                                    @foreach($kelasMapelTujuan as $t)
                                        <option value="{{ $t['kelas_id'] }}|{{ $t['mata_pelajaran_id'] }}">{{ $t['kelas']?->nama_kelas ?? '-' }} — {{ $t['mata_pelajaran']?->nama_mapel ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="inline-flex items-center gap-2 whitespace-nowrap text-xs font-bold text-slate-600">
                                <input type="checkbox" name="sertakan_soal" value="1" checked class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"> Sertakan soal
                            </label>
                            <input type="hidden" name="kelas_id" :value="tujuan.split('|')[0]">
                            <input type="hidden" name="mata_pelajaran_id" :value="tujuan.split('|')[1]">
                            <div class="flex gap-2">
                                <button type="button" @click="selected = []" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-lg border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50 sm:flex-none">Batal</button>
                                <button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700 sm:flex-none"><i class="fa-solid fa-copy" aria-hidden="true"></i>Salin <span x-text="selected.length"></span> item</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </form>
    @endif
</div>
@endsection
