@extends('layouts.app')

@section('title', 'Nilai Siswa')
@section('page-title', 'Nilai Siswa')
@section('page-subtitle', isset($kelas) && $kelas ? 'Nilai kelas ' . $kelas->nama_kelas : 'Kelola nilai siswa')

@section('content')
@php
    $score = fn ($value, $decimals = 1) => $value === null ? '—' : number_format((float) $value, $decimals, ',', '.');
@endphp
<div class="min-w-0 w-full space-y-4">
    @if($error ?? false)
        <p class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">{{ $error }}</p>
    @else
        <header class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div><p class="text-xs font-bold uppercase tracking-wide text-sky-700">Kelas {{ $kelas->nama_kelas }}</p><h1 class="mt-1 text-lg font-extrabold text-slate-900">Rekap nilai akademik</h1><p class="mt-1 text-xs text-slate-500">Pilih semester dan mata pelajaran, lalu buka nilai tiap siswa.</p></div>
            <a href="{{ route('wali.nilai.print', ['mata_pelajaran_id' => $selectedMapelId ?? '', 'semester' => $semester]) }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700 hover:bg-slate-50"><i class="fas fa-print" aria-hidden="true"></i>Cetak rekap</a>
        </header>

        <form action="{{ route('wali.nilai.index') }}" method="GET" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[180px_minmax(220px,1fr)_auto] lg:items-end">
            <label class="min-w-0 text-xs font-bold text-slate-700">Semester<select name="semester" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800"><option value="ganjil" @selected($semester === 'ganjil')>Ganjil</option><option value="genap" @selected($semester === 'genap')>Genap</option></select></label>
            <label class="min-w-0 text-xs font-bold text-slate-700">Mata pelajaran<select name="mata_pelajaran_id" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800"><option value="">Semua mapel · daftar siswa</option>@foreach($mataPelajaranList as $mapel)<option value="{{ $mapel->id }}" @selected($selectedMapelId == $mapel->id)>{{ $mapel->nama_mapel }}</option>@endforeach</select></label>
            <div class="flex gap-2"><button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-lg bg-sky-700 px-4 text-xs font-bold text-white hover:bg-sky-800"><i class="fas fa-filter" aria-hidden="true"></i>Terapkan</button><a href="{{ route('wali.nilai.index', ['semester' => $semester]) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700" aria-label="Reset filter mapel"><i class="fas fa-rotate-left" aria-hidden="true"></i></a></div>
        </form>

        @if($selectedMapel)
            <section class="grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Statistik nilai mapel">
                @foreach(['Rata-rata kelas' => $score($rataRataKelas, 2), 'Tertinggi' => $score($nilaiTertinggi, 2), 'Terendah' => $score($nilaiTerendah, 2), 'Tuntas' => $jumlahTuntas.' / '.$siswaList->count()] as $label => $value)
                    <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p><p class="mt-1 text-lg font-extrabold text-slate-900">{{ $value }}</p></div>
                @endforeach
            </section>
            @if(($jumlahGuruUpdate ?? 0) > 0)
                <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-900"><strong>{{ $jumlahGuruUpdate }} siswa memiliki pembaruan nilai dari guru.</strong> Buka Edit Nilai siswa untuk membandingkan dan memilih sinkronisasi. Tidak ada perubahan otomatis.</p>
            @endif
        @endif

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="siswa-heading">
            <div class="border-b border-slate-100 px-4 py-3"><h2 id="siswa-heading" class="text-sm font-extrabold text-slate-900">Daftar siswa{{ $selectedMapel ? ' · '.$selectedMapel->nama_mapel : '' }}</h2><p class="mt-0.5 text-xs text-slate-500">{{ $siswaList->count() }} siswa · Semester {{ ucfirst($semester) }}</p></div>
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full min-w-[850px] text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-600"><tr><th scope="col" class="px-4 py-3">Siswa</th><th scope="col" class="px-3 py-3">NIS</th>@if($selectedMapel)@foreach(['Tugas', 'Latihan', 'UH', 'PTS', 'PAS', 'Akhir'] as $label)<th scope="col" class="px-2 py-3 text-center">{{ $label }}</th>@endforeach
                    @endif<th scope="col" class="px-4 py-3 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($siswaList as $siswa)
                            @php
                                $nilai = $selectedMapel ? ($nilaiData[$siswa->id] ?? null) : null;
                                $guruBaru = $nilai && $nilai->hasGuruUpdate() && $nilai->guru_terakhir_simpan_at
                                    && (!$nilai->wali_terakhir_edit_at || $nilai->guru_terakhir_simpan_at->gt($nilai->wali_terakhir_edit_at));
                            @endphp
                            <tr><th scope="row" class="px-4 py-3 font-bold text-slate-900">{{ $siswa->nama_lengkap }}@if($guruBaru)<span class="ml-2 rounded-full bg-amber-50 px-2 py-1 text-[10px] text-amber-900">Update guru</span>@endif</th><td class="px-3 py-3 text-slate-600">{{ $siswa->nis ?: '—' }}</td>@if($selectedMapel)@foreach(['rata_tugas','rata_latihan','rata_uh','pts','pas','nilai_akhir'] as $field)<td class="px-2 py-3 text-center font-semibold text-slate-800">{{ $score($nilai?->$field) }}</td>@endforeach
                    @endif<td class="px-4 py-3"><div class="flex justify-end gap-2"><a href="{{ route('wali.nilai.show', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex min-h-9 items-center rounded-lg border border-slate-300 px-3 font-bold text-slate-700">Detail</a><a href="{{ route('wali.nilai.edit', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex min-h-9 items-center rounded-lg bg-sky-700 px-3 font-bold text-white">Edit</a></div></td></tr>
                        @empty
                            <tr><td colspan="{{ $selectedMapel ? 9 : 3 }}" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada siswa yang cocok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse($siswaList as $siswa)
                    @php
                        $nilai = $selectedMapel ? ($nilaiData[$siswa->id] ?? null) : null;
                        $guruBaru = $nilai && $nilai->hasGuruUpdate() && $nilai->guru_terakhir_simpan_at
                            && (!$nilai->wali_terakhir_edit_at || $nilai->guru_terakhir_simpan_at->gt($nilai->wali_terakhir_edit_at));
                    @endphp
                    <article class="space-y-2 px-4 py-3"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="text-sm font-bold text-slate-900">{{ $siswa->nama_lengkap }}</h3><p class="text-[11px] text-slate-500">NIS {{ $siswa->nis ?: '—' }}</p></div>@if($guruBaru)<span class="shrink-0 rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-900">Update guru</span>@endif</div>
                        @if($selectedMapel)<dl class="grid grid-cols-3 gap-2 rounded-lg bg-slate-50 p-2 text-[11px]">@foreach(['Tugas' => 'rata_tugas','Latihan' => 'rata_latihan','UH' => 'rata_uh','PTS' => 'pts','PAS' => 'pas','Akhir' => 'nilai_akhir'] as $label => $field)<div><dt class="text-slate-500">{{ $label }}</dt><dd class="text-sm font-bold text-slate-900">{{ $score($nilai?->$field) }}</dd></div>@endforeach</dl>@endif
                        <div class="flex gap-2"><a href="{{ route('wali.nilai.show', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex min-h-9 flex-1 items-center justify-center rounded-lg border border-slate-300 text-xs font-bold text-slate-700">Detail</a><a href="{{ route('wali.nilai.edit', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex min-h-9 flex-1 items-center justify-center rounded-lg bg-sky-700 text-xs font-bold text-white">Edit nilai</a></div>
                    </article>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-slate-500">Belum ada siswa yang cocok.</p>
                @endforelse
            </div>
        </section>
    @endif
</div>
@endsection
