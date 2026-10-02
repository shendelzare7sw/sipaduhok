@extends('layouts.app')

@section('title', 'Data Penilaian')
@section('page-title', 'Data Penilaian Harian')
@section('page-subtitle', 'Rekap rincian nilai tugas, latihan, dan ujian Anda')

@section('content')
@php
    $komponen = [
        ['key' => 'rata_tugas', 'label' => 'Rata tugas', 'bobot' => '10%', 'tone' => 'text-sky-700'],
        ['key' => 'rata_latihan', 'label' => 'Rata latihan', 'bobot' => '10%', 'tone' => 'text-cyan-700'],
        ['key' => 'rata_uh', 'label' => 'Rata UH', 'bobot' => '20%', 'tone' => 'text-indigo-700'],
        ['key' => 'pts', 'label' => 'Nilai PTS', 'bobot' => '30%', 'tone' => 'text-amber-700'],
        ['key' => 'pas', 'label' => 'Nilai PAS', 'bobot' => '30%', 'tone' => 'text-rose-700'],
    ];

    $predikat = function ($nilai) {
        if ($nilai >= 90) {
            return ['A (Sangat Baik)', 'bg-emerald-50 text-emerald-700 ring-emerald-200'];
        }
        if ($nilai >= 80) {
            return ['B (Baik)', 'bg-blue-50 text-blue-700 ring-blue-200'];
        }
        if ($nilai >= 70) {
            return ['C (Cukup)', 'bg-sky-50 text-sky-700 ring-sky-200'];
        }
        if ($nilai >= 60) {
            return ['D (Kurang)', 'bg-amber-50 text-amber-700 ring-amber-200'];
        }

        return ['E (Sangat Kurang)', 'bg-rose-50 text-rose-700 ring-rose-200'];
    };
@endphp

<div class="min-w-0 w-full space-y-5">
    <header class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:px-5 md:flex-row md:items-center md:justify-between">
        <div class="flex min-w-0 items-center gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
            <div class="min-w-0">
                <h1 class="text-base font-extrabold text-slate-900">Statistik belajar</h1>
                <p class="mt-0.5 text-xs text-slate-500">
                    Kelas <strong class="text-slate-700">{{ $siswa->kelas?->nama_kelas ?? '-' }}</strong>
                    <span class="mx-1">·</span>
                    TA <strong class="text-slate-700">{{ $siswa->kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}</strong>
                </p>
            </div>
        </div>

        <form method="GET" action="" x-data class="md:w-56">
            <label class="block text-xs font-bold text-slate-600">
                Semester
                <select name="semester" @change="$el.form.submit()" class="mt-1 block h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100">
                    <option value="ganjil" @selected($semester == 'ganjil')>Semester Ganjil</option>
                    <option value="genap" @selected($semester == 'genap')>Semester Genap</option>
                </select>
            </label>
        </form>
    </header>

    @forelse($nilaiList as $nilai)
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5">
                <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-book-open text-brand-600" aria-hidden="true"></i><span class="min-w-0 truncate">{{ $nilai->mataPelajaran->nama_mapel }}</span></h2>
                <p class="mt-1 text-xs font-semibold text-slate-500"><i class="fa-solid fa-user-tie mr-1" aria-hidden="true"></i>Pengampu: {{ $nilai->guru->nama_lengkap ?? '-' }}</p>
            </header>

            <div class="grid grid-cols-2 gap-2 p-4 sm:gap-3 sm:p-5 lg:grid-cols-5">
                @foreach($komponen as $item)
                    @php $skor = $nilai->{$item['key']}; @endphp
                    <div class="min-w-0 rounded-xl border border-slate-200 bg-slate-50 p-3 text-center {{ $loop->last && $loop->count % 2 === 1 ? 'col-span-2 lg:col-span-1' : '' }}">
                        <p class="truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $item['label'] }} ({{ $item['bobot'] }})</p>
                        <p class="mt-1 text-xl font-extrabold tabular-nums {{ is_null($skor) ? 'text-slate-400' : $item['tone'] }}">{{ is_null($skor) ? '-' : number_format($skor, 1) }}</p>
                    </div>
                @endforeach
            </div>

            <footer class="flex flex-wrap items-center gap-2 border-t border-slate-100 bg-slate-50/60 px-4 py-3 sm:px-5">
                <i class="fa-solid fa-award text-brand-600" aria-hidden="true"></i>
                <span class="text-xs font-bold text-slate-500">Predikat capaian:</span>
                @if(!is_null($nilai->nilai_akhir))
                    @php [$predikatLabel, $predikatTone] = $predikat($nilai->nilai_akhir); @endphp
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-extrabold ring-1 ring-inset {{ $predikatTone }}">{{ $predikatLabel }}</span>
                @else
                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 ring-1 ring-inset ring-slate-200">Belum tersedia</span>
                @endif
            </footer>
        </section>
    @empty
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-chart-column" aria-hidden="true"></i></span>
            <h2 class="mt-4 font-extrabold text-slate-900">Data penilaian belum diinput oleh guru</h2>
            <p class="mt-1 text-sm text-slate-500">Silakan hubungi wali kelas jika mata pelajaran belum muncul.</p>
        </section>
    @endforelse

    <section class="rounded-2xl border border-sky-200 bg-sky-50 p-4 sm:p-5">
        <h2 class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wide text-sky-800"><i class="fa-solid fa-circle-info" aria-hidden="true"></i>Panduan penilaian</h2>
        <dl class="mt-3 grid gap-2 text-xs leading-5 text-slate-600 md:grid-cols-2">
            <div><dt class="inline font-bold text-slate-800">Nilai tugas:</dt> <dd class="inline">Diambil dari rata-rata aktivitas harian.</dd></div>
            <div><dt class="inline font-bold text-slate-800">Nilai akhir:</dt> <dd class="inline">Hasil akumulasi sesuai bobot kurikulum.</dd></div>
            <div><dt class="inline font-bold text-slate-800">Nilai PTS/PAS:</dt> <dd class="inline">Skor murni hasil ujian semester.</dd></div>
            <div><dt class="inline font-bold text-slate-800">Status:</dt> <dd class="inline">Nilai ini bersifat sementara sebelum dirilis di E-Rapor.</dd></div>
        </dl>
    </section>
</div>
@endsection
