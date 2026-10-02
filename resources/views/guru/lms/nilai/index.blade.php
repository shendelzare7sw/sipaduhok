@extends('layouts.lms-guru')

@section('title', 'Rekap Nilai Siswa')
@section('page-title', 'Nilai Siswa')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php
    $isKelasAkhir = $kelas->isTingkatAkhir();
    $args = [$kelas->id, $mapel->id];
    $grupHarian = [
        'tugas' => ['label' => 'Tugas', 'prefix' => 'T', 'head' => 'bg-sky-50 text-sky-800', 'box' => 'border-sky-100 bg-sky-50/60', 'rata' => 'rata_tugas'],
        'latihan' => ['label' => 'Latihan', 'prefix' => 'L', 'head' => 'bg-violet-50 text-violet-800', 'box' => 'border-violet-100 bg-violet-50/60', 'rata' => 'rata_latihan'],
        'uh' => ['label' => 'Ulangan harian', 'prefix' => 'UH', 'head' => 'bg-amber-50 text-amber-800', 'box' => 'border-amber-100 bg-amber-50/60', 'rata' => 'rata_uh'],
    ];
    $fieldAkhir = ['to_1' => 'TO 1', 'to_2' => 'TO 2', 'to_3' => 'TO 3', 'upk' => 'UPK', 'ujian_praktek' => 'Ujian praktek'];
    $semuaField = collect(array_keys($grupHarian))->flatMap(fn ($k) => collect(range(1, 5))->map(fn ($i) => "{$k}_{$i}"))->merge(['pts', 'pas'])->all();

    // Nilai tampil ringkas di input (75.00 -> 75, 9.80 -> 9.8); nilai yang tersimpan tidak berubah.
    $ringkas = fn ($v) => ($v === null || $v === '') ? '' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
    $rata = fn ($v, $d = 1) => $v !== null ? number_format($v, $d) : '-';

    // Satu template kolom dipakai header dan setiap baris pada desktop agar kolom selalu sejajar.
    $kolomHarian = 'lg:grid-cols-[2rem_minmax(9.5rem,1fr)_repeat(6,2.9rem)_repeat(6,2.9rem)_repeat(6,2.9rem)_2.9rem_2.9rem_3.6rem]';
    $kolomAkhir = 'lg:grid-cols-[2rem_minmax(12rem,1fr)_repeat(5,minmax(4.5rem,7rem))]';

    $scoreInput = 'block h-10 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-1 text-center text-sm font-semibold tabular-nums text-slate-800 placeholder:text-slate-300 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 lg:h-8 lg:rounded-md lg:text-[13px]';
    $stepBtn = 'flex h-5 w-5 items-center justify-center rounded text-[10px] leading-none text-slate-400 hover:bg-indigo-50 hover:text-indigo-700';
@endphp

<div class="min-w-0 w-full space-y-5"
     x-data="{
        mengimpor: false,
        bersihkan(input) {
            let v = input.value.replace(/,/g, '.').replace(/[^0-9.]/g, '');
            const titik = v.indexOf('.');
            if (titik !== -1) v = v.slice(0, titik + 1) + v.slice(titik + 1).replace(/\./g, '');
            if (v !== input.value) input.value = v;
            if (v === '') return;
            if (parseFloat(v) > 100) {
                if (! v.includes('.')) {
                    const koreksi = v.slice(0, -1) + '.' + v.slice(-1);
                    if (parseFloat(koreksi) <= 100) { input.value = koreksi; return; }
                }
                input.value = 100;
            }
        },
        rapikan(input) {
            const nilai = parseFloat(input.value.replace(/,/g, '.'));
            if (Number.isNaN(nilai)) { input.value = ''; return; }
            input.value = String(Math.round(Math.max(0, Math.min(100, nilai)) * 100) / 100);
        },
        ubah(input, delta) {
            input.value = Math.round(Math.max(0, Math.min(100, (parseFloat(input.value) || 0) + delta)) * 100) / 100;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        },
        tombol(event) {
            const input = event.target;
            if (event.key === 'ArrowUp' || event.key === 'ArrowDown') { event.preventDefault(); this.ubah(input, event.key === 'ArrowUp' ? 1 : -1); return; }
            const k = event.key;
            if (k.length !== 1 || event.ctrlKey || event.metaKey) return;
            if (! /[0-9.,]/.test(k)) event.preventDefault();
        },
        geser(el, delta) { this.ubah(el.closest('[data-score-cell]').querySelector('input'), delta); },
     }"
     @input="if ($event.target.matches('[data-nilai]')) bersihkan($event.target)"
     @keydown="if ($event.target.matches('[data-nilai]')) tombol($event)"
     @focusout="if ($event.target.matches('[data-nilai]')) rapikan($event.target)">

    <header class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:px-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="min-w-0">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-chart-line text-indigo-600" aria-hidden="true"></i>Daftar nilai siswa</h2>
            <p class="mt-0.5 text-xs text-slate-500">Tahun ajaran aktif · Semester {{ ucfirst($semester) }} · {{ $nilaiList->count() }} siswa</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
            <form action="{{ route('guru.lms.nilai.index', $args) }}" method="GET" x-data class="flex items-center gap-2">
                <label for="pilih-semester" class="whitespace-nowrap text-xs font-bold text-slate-600"><i class="fa-solid fa-calendar-days mr-1" aria-hidden="true"></i>Semester</label>
                <select id="pilih-semester" name="semester" @change="$el.form.submit()" class="h-10 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="ganjil" @selected($semester == 'ganjil')>Ganjil (Jul-Des)</option>
                    <option value="genap" @selected($semester == 'genap')>Genap (Jan-Jun)</option>
                </select>
                @if($semester == $currentSemester)
                    <span class="whitespace-nowrap rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700"><i class="fa-solid fa-check mr-1" aria-hidden="true"></i>Aktif</span>
                @endif
            </form>

            <div class="grid grid-cols-2 gap-2 sm:flex">
                <form action="{{ route('guru.lms.nilai.recalculate', $args) }}" method="POST" data-confirm data-confirm-title="Hitung ulang nilai?" data-confirm-message="Semua nilai dihitung ulang berdasarkan tugas dan ujian yang ada." data-confirm-text="Ya, hitung ulang">
                    @csrf
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-1.5 whitespace-nowrap rounded-xl border border-indigo-200 bg-indigo-50 px-3 text-xs font-bold text-indigo-700 hover:bg-indigo-100"><i class="fa-solid fa-rotate" aria-hidden="true"></i>Hitung ulang</button>
                </form>

                <div class="relative sm:static" x-data="{ buka: false }" @click.outside="buka = false" @keydown.escape="buka = false">
                    <button type="button" @click="buka = ! buka" :aria-expanded="buka" class="inline-flex min-h-10 w-full items-center justify-center gap-1.5 whitespace-nowrap rounded-xl bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700"><i class="fa-solid fa-file-excel" aria-hidden="true"></i>Excel<i class="fa-solid fa-chevron-down text-[10px]" aria-hidden="true"></i></button>
                    <div x-cloak x-show="buka" x-transition class="absolute inset-x-0 z-30 mt-2 w-auto overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl sm:inset-x-auto sm:right-8 sm:w-56">
                        <a href="{{ route('guru.lms.nilai.export-excel', [...$args, 'semester' => $semester]) }}" target="_blank" class="flex min-h-10 items-center gap-2 whitespace-nowrap px-4 text-xs font-semibold text-slate-700 no-underline hover:bg-slate-50"><i class="fa-solid fa-download w-4 text-emerald-600" aria-hidden="true"></i>Ekspor nilai</a>
                        <a href="{{ route('guru.lms.nilai.download-template', [...$args, 'semester' => $semester]) }}" class="flex min-h-10 items-center gap-2 whitespace-nowrap px-4 text-xs font-semibold text-slate-700 no-underline hover:bg-slate-50"><i class="fa-solid fa-file-arrow-down w-4 text-emerald-600" aria-hidden="true"></i>Unduh template</a>
                        <button type="button" @click="buka = false; document.getElementById('importNilaiDialog').showModal()" class="flex min-h-10 w-full items-center gap-2 whitespace-nowrap border-t border-slate-100 px-4 text-left text-xs font-semibold text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-file-arrow-up w-4 text-indigo-600" aria-hidden="true"></i>Impor nilai</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <form action="{{ route('guru.lms.nilai.updateBatch', $args) }}" method="POST" id="nilaiForm" class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        @csrf
        <input type="hidden" name="semester" value="{{ $semester }}">
        <div class="flex flex-col gap-2 border-b border-slate-100 px-4 py-3 text-xs text-slate-500 sm:px-5 lg:flex-row lg:items-center lg:justify-between">
            <p><i class="fa-solid fa-circle-info mr-1 text-sky-600" aria-hidden="true"></i>Desimal memakai <strong>titik</strong> (<code class="rounded bg-slate-100 px-1">9.8</code>); koma otomatis diubah. <span class="hidden lg:inline">Tekan <kbd class="rounded border border-slate-200 bg-slate-50 px-1">↑</kbd>/<kbd class="rounded border border-slate-200 bg-slate-50 px-1">↓</kbd> pada kolom untuk menambah/mengurangi 1.</span></p>
            @if($nilaiList->count() > 0)
                <div class="grid grid-cols-2 gap-2 lg:hidden">
                    <button type="button" @click="$dispatch('nilai-semua', true)" class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-700"><i class="fa-solid fa-angles-down" aria-hidden="true"></i>Buka semua</button>
                    <button type="button" @click="$dispatch('nilai-semua', false)" class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-700"><i class="fa-solid fa-angles-up" aria-hidden="true"></i>Tutup semua</button>
                </div>
            @endif
        </div>

        <div class="lg:overflow-x-auto">
            <div class="lg:min-w-[66rem]">
                {{-- Header kolom (desktop saja) --}}
                <div class="hidden gap-x-1 border-b border-slate-200 bg-slate-50 px-3 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500 lg:grid {{ $kolomHarian }}">
                    <span class="self-center row-span-2 py-2">No</span>
                    <span class="self-center row-span-2 py-2 text-left">Nama siswa</span>
                    @foreach($grupHarian as $grup)
                        <span class="col-span-6 mt-1.5 rounded-t-md py-1 {{ $grup['head'] }}">{{ $grup['label'] }}</span>
                    @endforeach
                    <span class="self-center row-span-2 py-2">PTS</span>
                    <span class="self-center row-span-2 py-2">PAS</span>
                    <span class="self-center row-span-2 rounded-md bg-indigo-50 py-2 text-indigo-800">Akhir</span>
                    @foreach($grupHarian as $grup)
                        @for($i = 1; $i <= 5; $i++)<span class="pb-1.5 pt-1 {{ $grup['head'] }}">{{ $grup['prefix'] }}{{ $i }}</span>@endfor
                        <span class="rounded-b-none pb-1.5 pt-1 {{ $grup['head'] }}">Rata</span>
                    @endforeach
                </div>

                <div class="space-y-3 p-3 lg:space-y-0 lg:divide-y lg:divide-slate-100 lg:p-0">
                    @forelse($nilaiList as $index => $nilai)
                        @php
                            $nama = $nilai->siswa->nama_lengkap ?? '-';
                            // Wali sudah mengedit: nilai aktif milik wali, input guru menampilkan snapshot guru agar simpan tidak menimpa snapshot dengan nilai wali.
                            $diEditWali = $nilai->wali_terakhir_edit_at !== null;
                            $pakaiSnapshot = $diEditWali && $nilai->guru_terakhir_simpan_at !== null;
                            $isian = fn ($f) => $pakaiSnapshot ? $nilai->{$f.'_guru'} : $nilai->$f;
                            $beda = $diEditWali ? collect($semuaField)->filter(fn ($f) => $ringkas($nilai->{$f.'_guru'}) !== $ringkas($nilai->$f))->count() : 0;
                            $terisi = collect($semuaField)->filter(fn ($f) => $isian($f) !== null && $isian($f) !== '')->count();
                        @endphp
                        <article x-data="{
                                    buka: false, versi: 0,
                                    angka(el) { const v = (el?.value ?? '').replace(',', '.'); return v === '' || Number.isNaN(parseFloat(v)) ? null : parseFloat(v); },
                                    rata(grup) { this.versi; const isi = [...this.$root.querySelectorAll(`[data-grup='${grup}']`)].map((el) => this.angka(el)).filter((v) => v !== null); return isi.length ? isi.reduce((a, b) => a + b, 0) / isi.length : null; },
                                    ujian(field) { this.versi; return this.angka(this.$root.querySelector(`[data-field='${field}']`)); },
                                    akhir() {
                                        const t = this.rata('tugas'), l = this.rata('latihan'), u = this.rata('uh'), pts = this.ujian('pts'), pas = this.ujian('pas');
                                        if ([t, l, u, pts, pas].every((v) => v === null)) return null;
                                        return ((t ?? 0) + (l ?? 0) + 2 * (u ?? 0) + 3 * (pts ?? 0) + 3 * (pas ?? 0)) / 10;
                                    },
                                    tampil(v, d = 1) { return v === null ? '-' : v.toFixed(d); },
                                 }"
                                 @nilai-semua.window="buka = $event.detail"
                                 @input="versi++" @focusout="$nextTick(() => versi++)"
                                 class="rounded-2xl border border-slate-200 p-3 lg:grid lg:items-center lg:gap-x-1 lg:rounded-none lg:border-0 lg:px-3 lg:py-1.5 lg:hover:bg-slate-50/70 {{ $kolomHarian }}">
                            {{-- Kepala kartu (mobile) / kolom No + Nama (desktop) --}}
                            <button type="button" @click="buka = ! buka" :aria-expanded="buka" class="flex w-full items-center gap-3 text-left lg:contents">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-extrabold text-slate-600 lg:h-auto lg:w-auto lg:bg-transparent lg:text-center lg:text-xs lg:font-bold lg:text-slate-500">{{ $index + 1 }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-extrabold text-slate-900 lg:text-xs lg:font-bold" title="{{ $nama }}">{{ $nama }}</span>
                                    <span class="block truncate text-[11px] text-slate-500 lg:text-[10px]">{{ $nilai->siswa->nis ?? $nilai->siswa->nisn ?? '-' }}<span class="lg:hidden"> · {{ $terisi }}/{{ count($semuaField) }} nilai terisi</span></span>
                                    @if($diEditWali)
                                        <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800" title="Wali kelas sudah mengedit nilai siswa ini. Simpanan Anda masuk ke versi guru; nilai rapor tetap versi wali sampai wali menyinkronkan.">
                                            <i class="fa-solid fa-user-pen" aria-hidden="true"></i>Diedit wali{{ $beda > 0 ? ' · '.$beda.' beda' : '' }}
                                        </span>
                                    @endif
                                </span>
                                <span class="shrink-0 text-right lg:hidden">
                                    <span class="block text-[10px] font-bold uppercase text-slate-400">Akhir</span>
                                    <span class="block text-lg font-extrabold tabular-nums text-indigo-700" x-text="tampil(akhir(), 2)">{{ $rata($nilai->nilai_akhir, 2) }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-down shrink-0 text-xs text-slate-400 transition-transform lg:hidden" :class="buka && 'rotate-180'" aria-hidden="true"></i>
                            </button>
                            <input type="hidden" name="nilai[{{ $nilai->id }}][id]" value="{{ $nilai->id }}">

                            {{-- Ringkasan rata-rata saat kartu tertutup (mobile) --}}
                            <div x-show="! buka" class="mt-2 flex flex-wrap gap-1.5 text-[11px] font-bold lg:hidden">
                                @foreach($grupHarian as $grupKey => $grup)
                                    <span class="rounded-md px-2 py-1 {{ $grup['head'] }}">{{ $grup['prefix'] }} <span x-text="tampil(rata('{{ $grupKey }}'))">{{ $rata($nilai->{$grup['rata']}) }}</span></span>
                                @endforeach
                                <span class="rounded-md bg-slate-100 px-2 py-1 text-slate-700">PTS <span x-text="ujian('pts') ?? '-'">{{ $ringkas($isian('pts')) ?: '-' }}</span></span>
                                <span class="rounded-md bg-slate-100 px-2 py-1 text-slate-700">PAS <span x-text="ujian('pas') ?? '-'">{{ $ringkas($isian('pas')) ?: '-' }}</span></span>
                            </div>

                            {{-- Isian nilai: grup kartu (mobile) / kolom baris (desktop) --}}
                            <div class="mt-3 space-y-3 lg:contents" :class="buka ? '' : 'max-lg:hidden'">
                                @foreach($grupHarian as $key => $grup)
                                    <div role="group" aria-label="{{ $grup['label'] }}" class="rounded-xl border p-2.5 lg:contents {{ $grup['box'] }}">
                                        <p class="mb-1.5 px-1 text-[11px] font-extrabold uppercase tracking-wide lg:hidden {{ explode(' ', $grup['head'])[1] }}">{{ $grup['label'] }}</p>
                                        <div class="grid grid-cols-3 gap-2 sm:grid-cols-6 lg:contents">
                                            @for($i = 1; $i <= 5; $i++)
                                                <label class="block min-w-0" data-score-cell>
                                                    <span class="mb-0.5 block text-center text-[10px] font-bold text-slate-500 lg:sr-only">{{ $grup['prefix'] }}{{ $i }}</span>
                                                    <span class="flex items-center gap-0.5">
                                                        <input type="text" maxlength="6" inputmode="decimal" placeholder="-" data-nilai aria-label="{{ $grup['prefix'] }}{{ $i }} {{ $nama }}" data-grup="{{ $key }}" name="nilai[{{ $nilai->id }}][{{ $key }}_{{ $i }}]" id="{{ $key }}_{{ $nilai->id }}_{{ $i }}" value="{{ $ringkas($isian($key.'_'.$i)) }}" @if($diEditWali && $ringkas($nilai->{$key.'_'.$i}) !== $ringkas($isian($key.'_'.$i))) title="Nilai aktif (wali): {{ $ringkas($nilai->{$key.'_'.$i}) ?: '-' }}" @endif class="{{ $scoreInput }} {{ $diEditWali && $ringkas($nilai->{$key.'_'.$i}) !== $ringkas($isian($key.'_'.$i)) ? '!border-amber-400 !bg-amber-50' : '' }}">
                                                        <span class="flex flex-col lg:hidden"><button type="button" @click="geser($el, 1)" class="{{ $stepBtn }}" aria-label="Naikkan">&uarr;</button><button type="button" @click="geser($el, -1)" class="{{ $stepBtn }}" aria-label="Turunkan">&darr;</button></span>
                                                    </span>
                                                </label>
                                            @endfor
                                            <div class="min-w-0 text-center">
                                                <span class="mb-0.5 block text-[10px] font-bold text-slate-500 lg:sr-only">Rata</span>
                                                <span class="flex h-10 items-center justify-center rounded-lg text-sm font-extrabold tabular-nums text-slate-800 lg:h-8 lg:text-xs {{ $grup['head'] }}" id="{{ $grup['rata'] }}_{{ $nilai->id }}" x-text="tampil(rata('{{ $key }}'))">{{ $rata($nilai->{$grup['rata']}) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div role="group" aria-label="Ujian semester" class="rounded-xl border border-slate-200 bg-slate-50/60 p-2.5 lg:contents">
                                    <p class="mb-1.5 px-1 text-[11px] font-extrabold uppercase tracking-wide text-slate-600 lg:hidden">Ujian semester</p>
                                    <div class="grid grid-cols-2 gap-2 lg:contents">
                                        @foreach(['pts' => 'PTS', 'pas' => 'PAS'] as $field => $fieldLabel)
                                            <label class="block min-w-0" data-score-cell>
                                                <span class="mb-0.5 block text-center text-[10px] font-bold text-slate-500 lg:sr-only">{{ $fieldLabel }}</span>
                                                <span class="flex items-center gap-0.5">
                                                    <input type="text" maxlength="6" inputmode="decimal" placeholder="-" data-nilai aria-label="{{ $fieldLabel }} {{ $nama }}" data-field="{{ $field }}" name="nilai[{{ $nilai->id }}][{{ $field }}]" id="{{ $field }}_{{ $nilai->id }}" value="{{ $ringkas($isian($field)) }}" @if($diEditWali && $ringkas($nilai->$field) !== $ringkas($isian($field))) title="Nilai aktif (wali): {{ $ringkas($nilai->$field) ?: '-' }}" @endif class="{{ $scoreInput }} {{ $diEditWali && $ringkas($nilai->$field) !== $ringkas($isian($field)) ? '!border-amber-400 !bg-amber-50' : '' }}">
                                                    <span class="flex flex-col lg:hidden"><button type="button" @click="geser($el, 1)" class="{{ $stepBtn }}" aria-label="Naikkan">&uarr;</button><button type="button" @click="geser($el, -1)" class="{{ $stepBtn }}" aria-label="Turunkan">&darr;</button></span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                @if($diEditWali)
                                    <p class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-[11px] leading-5 text-amber-900 lg:hidden"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>Wali kelas sudah mengedit nilai siswa ini. Isian, rata-rata, dan nilai akhir di sini adalah versi Anda (guru). Nilai rapor tetap versi wali sampai wali menyinkronkan. Kotak kuning = berbeda dari nilai aktif wali.</p>
                                @endif
                            </div>

                            <span class="hidden h-8 items-center justify-center rounded-md bg-indigo-50 text-[13px] font-extrabold tabular-nums text-indigo-800 lg:flex" id="nilai_akhir_{{ $nilai->id }}" x-text="tampil(akhir(), 2)">{{ $rata($nilai->nilai_akhir, 2) }}</span>
                        </article>
                    @empty
                        <p class="px-4 py-10 text-center text-sm text-slate-500">Belum ada siswa di kelas ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @if($nilaiList->count() > 0)
            <div class="sticky bottom-3 z-20 mx-3 mb-3 flex items-center justify-between gap-3 rounded-2xl border border-indigo-200 bg-white/95 p-3 shadow-lg backdrop-blur lg:static lg:m-0 lg:rounded-none lg:rounded-b-2xl lg:border-0 lg:border-t lg:border-slate-200 lg:bg-slate-50 lg:shadow-none">
                <p class="text-[11px] text-slate-500">Rata-rata &amp; nilai akhir dihitung langsung saat mengetik dan disimpan saat Anda klik Simpan.</p>
                <button type="submit" class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>Simpan semua</button>
            </div>
        @endif
    </form>

    @if($isKelasAkhir)
        <form action="{{ route('guru.lms.nilai.updateBatch', $args) }}" method="POST" class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf
            <input type="hidden" name="semester" value="{{ $semester }}">
            <header class="border-b border-slate-200 px-4 py-3 sm:px-5">
                <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-graduation-cap text-indigo-600" aria-hidden="true"></i>Penilaian tingkat akhir (TO, UPK, ujian praktek)</h2>
                <p class="mt-0.5 text-xs text-slate-500">Khusus kelas tingkat akhir: kelas 6 SD, kelas 9 SMP, kelas 12 SMA.</p>
            </header>

            <div class="hidden gap-x-2 border-b border-slate-200 bg-slate-50 px-3 py-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500 lg:grid {{ $kolomAkhir }}">
                <span>No</span><span class="text-left">Nama siswa</span>
                @foreach($fieldAkhir as $fieldLabel)<span class="text-violet-800">{{ $fieldLabel }}</span>@endforeach
            </div>
            <div class="space-y-3 p-3 lg:space-y-0 lg:divide-y lg:divide-slate-100 lg:p-0">
                @forelse($nilaiList as $index => $nilai)
                    @php
                        $nama = $nilai->siswa->nama_lengkap ?? '-';
                        // Sama seperti tabel utama: setelah wali merevisi, input menampilkan versi guru (snapshot).
                        $diEditWali = $nilai->wali_terakhir_edit_at !== null;
                        $pakaiSnapshot = $diEditWali && $nilai->guru_terakhir_simpan_at !== null;
                        $isian = fn ($f) => $pakaiSnapshot ? $nilai->{$f.'_guru'} : $nilai->$f;
                        $bedaAkhir = $diEditWali ? collect(array_keys($fieldAkhir))->filter(fn ($f) => $ringkas($nilai->{$f.'_guru'}) !== $ringkas($nilai->$f))->count() : 0;
                    @endphp
                    <article class="rounded-2xl border border-slate-200 p-3 lg:grid lg:items-center lg:gap-x-2 lg:rounded-none lg:border-0 lg:px-3 lg:py-1.5 {{ $kolomAkhir }}">
                        <span class="hidden text-center text-xs font-bold text-slate-500 lg:block">{{ $index + 1 }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-extrabold text-slate-900 lg:text-xs lg:font-bold" title="{{ $nama }}"><span class="lg:hidden">{{ $index + 1 }}. </span>{{ $nama }}</p>
                            <p class="truncate text-[11px] text-slate-500 lg:text-[10px]">{{ $nilai->siswa->nis ?? $nilai->siswa->nisn ?? '-' }}</p>
                            @if($diEditWali)
                                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800" title="Wali kelas sudah mengedit nilai siswa ini. Simpanan Anda masuk ke versi guru; nilai rapor tetap versi wali sampai wali menyinkronkan.">
                                    <i class="fa-solid fa-user-pen" aria-hidden="true"></i>Diedit wali{{ $bedaAkhir > 0 ? ' · '.$bedaAkhir.' beda' : '' }}
                                </span>
                            @endif
                            <input type="hidden" name="nilai[{{ $nilai->id }}][id]" value="{{ $nilai->id }}">
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-5 lg:contents">
                            @foreach($fieldAkhir as $field => $fieldLabel)
                                <label class="block min-w-0" data-score-cell>
                                    <span class="mb-0.5 block text-center text-[10px] font-bold text-slate-500 lg:sr-only">{{ $fieldLabel }}</span>
                                    <span class="flex items-center gap-0.5">
                                        <input type="text" maxlength="6" inputmode="decimal" placeholder="-" data-nilai aria-label="{{ $fieldLabel }} {{ $nama }}" name="nilai[{{ $nilai->id }}][{{ $field }}]" id="{{ $field }}_{{ $nilai->id }}" value="{{ $ringkas($isian($field)) }}" @if($diEditWali && $ringkas($nilai->$field) !== $ringkas($isian($field))) title="Nilai aktif (wali): {{ $ringkas($nilai->$field) ?: '-' }}" @endif class="{{ $scoreInput }} {{ $diEditWali && $ringkas($nilai->$field) !== $ringkas($isian($field)) ? '!border-amber-400 !bg-amber-50' : '' }}">
                                        <span class="flex flex-col lg:hidden"><button type="button" @click="geser($el, 1)" class="{{ $stepBtn }}" aria-label="Naikkan">&uarr;</button><button type="button" @click="geser($el, -1)" class="{{ $stepBtn }}" aria-label="Turunkan">&darr;</button></span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </article>
                @empty
                    <p class="px-4 py-10 text-center text-sm text-slate-500">Belum ada siswa di kelas ini.</p>
                @endforelse
            </div>
            @if($nilaiList->count() > 0)
                <div class="flex justify-end rounded-b-2xl border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5">
                    <button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-emerald-600 px-5 text-xs font-bold text-white hover:bg-emerald-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>Simpan nilai tingkat akhir</button>
                </div>
            @endif
        </form>
    @endif

    <section class="flex items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-xs leading-5 text-sky-950 sm:p-5">
        <i class="fa-solid fa-circle-info mt-0.5 text-lg text-sky-600" aria-hidden="true"></i>
        <div>
            <h2 class="text-sm font-extrabold">Informasi penilaian</h2>
            <ul class="mt-1 list-disc space-y-1 pl-4">
                <li>Isi nilai <strong>Tugas 1-5</strong>, <strong>Latihan 1-5</strong>, dan <strong>UH 1-5</strong> secara parsial. Rata-rata dihitung otomatis saat disimpan.</li>
                <li>Kolom kosong <strong>tidak dihitung sebagai 0</strong> dalam rata-rata.</li>
                <li>Nilai akhir: <strong>((Rata Tugas x 1) + (Rata Latihan x 1) + (Rata UH x 2) + (PTS x 3) + (PAS x 3)) / 10</strong>.</li>
                <li>Klik <strong>Simpan semua</strong> untuk menyimpan semua perubahan sekaligus.</li>
                <li>Siswa berlabel <strong>Diedit wali</strong>: wali kelas sudah mengubah nilainya. Simpanan Anda tersimpan sebagai versi guru dan tidak langsung mengubah nilai rapor; wali kelas meninjaunya lewat <strong>Sinkron dari Guru</strong>.</li>
            </ul>
        </div>
    </section>

    <dialog id="importNilaiDialog" x-ref="importDialog" @click.self="$el.close()" @close="$refs.importForm.reset(); mengimpor = false" class="w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <form x-ref="importForm" action="{{ route('guru.lms.nilai.import-excel', $args) }}" method="POST" enctype="multipart/form-data" class="px-5 py-5"
              @submit="
                const file = $refs.berkas.files[0];
                if (! file) { $event.preventDefault(); window.Swal?.fire({ icon: 'warning', title: 'Pilih file Excel terlebih dahulu' }); return; }
                if (! /(\.xlsx|\.xls)$/i.test(file.name)) { $event.preventDefault(); $refs.berkas.value = ''; window.Swal?.fire({ icon: 'warning', title: 'File harus berformat .xlsx atau .xls' }); return; }
                if (file.size > 5 * 1024 * 1024) { $event.preventDefault(); $refs.berkas.value = ''; window.Swal?.fire({ icon: 'warning', title: 'Ukuran file maksimal 5 MB' }); return; }
                mengimpor = true;
              ">
            @csrf
            <input type="hidden" name="semester" value="{{ $semester }}">
            <h3 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-file-arrow-up text-indigo-600" aria-hidden="true"></i>Impor nilai dari Excel</h3>
            <ol class="mt-3 list-decimal space-y-1 rounded-xl bg-sky-50 py-3 pl-8 pr-4 text-xs leading-5 text-sky-900">
                <li>Unduh template Excel terlebih dahulu.</li>
                <li>Isi nilai siswa pada kolom yang tersedia (0-100).</li>
                <li><strong>Jangan mengubah</strong> kolom No, Nama Siswa, dan NIS/NISN.</li>
                <li><strong>Kosongkan sel</strong> jika tidak ingin mengubah nilai yang sudah ada; isi nilai baru untuk menimpanya.</li>
                <li>Unggah file Excel yang sudah diisi.</li>
            </ol>
            <label class="mt-4 block text-xs font-bold text-slate-700">Pilih file Excel
                <input type="file" name="file" x-ref="berkas" accept=".xlsx,.xls" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-bold file:text-indigo-700">
                <span class="mt-1 block font-normal text-slate-500">Format .xlsx atau .xls, maksimal 5 MB.</span>
            </label>
            <p class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs leading-5 text-emerald-900"><strong>Impor pintar:</strong> nilai yang diisi di Excel menimpa nilai lama, sel kosong mempertahankan nilai lama, dan Anda bisa mengimpor sebagian siswa saja.</p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$refs.importDialog.close()" class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" :disabled="mengimpor" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white hover:bg-indigo-700 disabled:cursor-wait disabled:opacity-80"><i class="fa-solid" :class="mengimpor ? 'fa-spinner fa-spin' : 'fa-upload'" aria-hidden="true"></i><span x-text="mengimpor ? 'Mengimpor...' : 'Impor nilai'">Impor nilai</span></button>
            </div>
        </form>
    </dialog>
</div>
@endsection
