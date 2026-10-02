@extends('layouts.app')

@section('title', 'Detail Nilai - ' . $siswa->nama_lengkap)
@section('page-title', 'Detail Nilai Siswa')
@section('page-subtitle', $siswa->nama_lengkap . ' · ' . $kelas->nama_kelas)

@section('content')
@php
    $isKelasAkhir = $kelas->isTingkatAkhir();
    $score = fn ($value, $decimals = 1) => $value === null ? '—' : number_format((float) $value, $decimals, ',', '.');
    // Sama dengan halaman edit: guru menyimpan versi baru setelah revisi terakhir wali.
    $guruBaru = fn ($nilai) => $nilai && $nilai->hasGuruUpdate() && $nilai->guru_terakhir_simpan_at
        && (! $nilai->wali_terakhir_edit_at || $nilai->guru_terakhir_simpan_at->gt($nilai->wali_terakhir_edit_at));
    $mapelGuruBaru = $mataPelajaranList->filter(fn ($mapel) => $guruBaru($nilaiData[$mapel->id] ?? null));
@endphp
<div class="min-w-0 w-full space-y-4">
    <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-wide text-sky-700">Rekap nilai · Semester {{ ucfirst($semester) }}</p>
                <h1 class="mt-1 text-lg font-extrabold text-slate-900">{{ $siswa->nama_lengkap }}</h1>
                <p class="mt-1 text-xs text-slate-500">{{ $kelas->nama_kelas }} · {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? 'Tahun ajaran belum tersedia' }} · NIS {{ $siswa->nis ?: '—' }} · NISN {{ $siswa->nisn ?: '—' }}</p>
            </div>
            <div class="flex w-full flex-wrap gap-2 sm:w-auto">
                <a href="{{ route('wali.nilai.index', ['semester' => $semester]) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-left" aria-hidden="true"></i>Daftar</a>
                <a href="{{ route('wali.nilai.edit', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-sky-700 px-3 text-xs font-bold text-white hover:bg-sky-800"><i class="fas fa-pen" aria-hidden="true"></i>Edit nilai</a>
                <a href="{{ route('wali.nilai.print-siswa', ['siswa' => $siswa->id, 'semester' => $semester]) }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700 hover:bg-slate-50"><i class="fas fa-print" aria-hidden="true"></i>Cetak</a>
            </div>
        </div>
    </header>

    @if($mapelGuruBaru->isNotEmpty())
        <section class="flex flex-col gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between" role="status">
            <div class="flex min-w-0 items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-amber-700 ring-1 ring-amber-200"><i class="fas fa-user-pen" aria-hidden="true"></i></span>
                <div class="min-w-0 text-xs leading-5 text-amber-950">
                    <p class="text-sm font-extrabold">Guru mapel mengubah nilai yang sudah Anda revisi</p>
                    <p>{{ $mapelGuruBaru->pluck('nama_mapel')->implode(', ') }}. Nilai di halaman ini dan rapor masih versi Anda; bandingkan lalu sinkronkan jika setuju.</p>
                </div>
            </div>
            <a href="{{ route('wali.nilai.edit', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-lg bg-amber-500 px-3 text-xs font-bold text-white hover:bg-amber-600"><i class="fas fa-code-compare" aria-hidden="true"></i>Bandingkan &amp; sinkronkan</a>
        </section>
    @endif

    <section class="grid grid-cols-2 gap-3 sm:grid-cols-3" aria-label="Ringkasan nilai">
        <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Mapel bernilai</p><p class="mt-1 text-xl font-extrabold text-slate-900">{{ $totalNilai }}<span class="ml-1 text-xs font-medium text-slate-500">/ {{ $mataPelajaranList->count() }}</span></p></div>
        <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Rata-rata</p><p class="mt-1 text-xl font-extrabold text-slate-900">{{ $totalNilai ? $score($rataRataSiswa, 1) : '—' }}</p></div>
        <div class="col-span-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:col-span-1"><p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Tuntas</p><p class="mt-1 text-xl font-extrabold text-emerald-700">{{ $jumlahTuntas }}<span class="ml-1 text-xs font-medium text-slate-500">/ {{ $totalNilai }} mapel bernilai</span></p></div>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="nilai-heading">
        <div class="border-b border-slate-100 px-4 py-3"><h2 id="nilai-heading" class="text-sm font-extrabold text-slate-900">Nilai per mata pelajaran</h2><p class="mt-0.5 text-xs text-slate-500">Kosong berarti belum diisi; angka 0 tetap nilai yang tercatat.</p></div>
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-[1040px] w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-600"><tr><th scope="col" class="px-3 py-3">Mata pelajaran</th><th scope="col" class="px-2 py-3 text-center">Tugas</th><th scope="col" class="px-2 py-3 text-center">Rata tugas</th><th scope="col" class="px-2 py-3 text-center">Latihan</th><th scope="col" class="px-2 py-3 text-center">Rata latihan</th><th scope="col" class="px-2 py-3 text-center">UH</th><th scope="col" class="px-2 py-3 text-center">Rata UH</th><th scope="col" class="px-2 py-3 text-center">PTS</th><th scope="col" class="px-2 py-3 text-center">PAS</th><th scope="col" class="px-2 py-3 text-center">Akhir</th><th scope="col" class="px-3 py-3">Status</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mataPelajaranList as $mapel)
                        @php
                            $nilai = $nilaiData[$mapel->id] ?? null;
                            $counts = ['tugas' => 0, 'latihan' => 0, 'uh' => 0];
                            foreach (array_keys($counts) as $group) {
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($nilai?->{$group.'_'.$i} !== null) $counts[$group]++;
                                }
                            }
                            $hasFinal = $nilai?->nilai_akhir !== null;
                            $isTuntas = $hasFinal && $nilai->nilai_akhir >= 70;
                        @endphp
                        <tr>
                            <th scope="row" class="px-3 py-3 font-bold text-slate-900">{{ $mapel->nama_mapel }}@if($guruBaru($nilai))<span class="ml-1.5 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-800 ring-1 ring-amber-200">Update guru</span>@endif<span class="block text-[11px] font-normal text-slate-500">{{ $mapel->kode_mapel }}</span></th>
                            <td class="px-2 py-3 text-center">{{ $counts['tugas'] }}/5</td><td class="px-2 py-3 text-center">{{ $score($nilai?->rata_tugas) }}</td>
                            <td class="px-2 py-3 text-center">{{ $counts['latihan'] }}/5</td><td class="px-2 py-3 text-center">{{ $score($nilai?->rata_latihan) }}</td>
                            <td class="px-2 py-3 text-center">{{ $counts['uh'] }}/5</td><td class="px-2 py-3 text-center">{{ $score($nilai?->rata_uh) }}</td>
                            <td class="px-2 py-3 text-center">{{ $score($nilai?->pts, 0) }}</td><td class="px-2 py-3 text-center">{{ $score($nilai?->pas, 0) }}</td>
                            <td class="px-2 py-3 text-center font-extrabold text-slate-900">{{ $score($nilai?->nilai_akhir, 2) }}</td>
                            <td class="px-3 py-3"><span class="inline-flex rounded-full px-2 py-1 text-[11px] font-bold {{ !$hasFinal ? 'bg-slate-100 text-slate-700' : ($isTuntas ? 'bg-emerald-50 text-emerald-800' : 'bg-rose-50 text-rose-800') }}">{{ !$hasFinal ? 'Belum ada' : ($isTuntas ? 'Tuntas' : 'Belum tuntas') }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada mata pelajaran untuk kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($mataPelajaranList as $mapel)
                @php
                    $nilai = $nilaiData[$mapel->id] ?? null;
                    $counts = ['tugas' => 0, 'latihan' => 0, 'uh' => 0];
                    foreach (array_keys($counts) as $group) {
                        for ($i = 1; $i <= 5; $i++) {
                            if ($nilai?->{$group.'_'.$i} !== null) $counts[$group]++;
                        }
                    }
                    $hasFinal = $nilai?->nilai_akhir !== null;
                    $isTuntas = $hasFinal && $nilai->nilai_akhir >= 70;
                @endphp
                <details class="group px-4 py-3">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                        <span class="min-w-0"><span class="block text-sm font-bold text-slate-900">{{ $mapel->nama_mapel }}@if($guruBaru($nilai))<span class="ml-1.5 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-800 ring-1 ring-amber-200">Update guru</span>@endif</span><span class="text-[11px] text-slate-500">{{ $mapel->kode_mapel }} · {{ !$hasFinal ? 'Belum ada nilai akhir' : ($isTuntas ? 'Tuntas' : 'Belum tuntas') }}</span></span>
                        <span class="flex shrink-0 items-center gap-2"><span class="text-sm font-extrabold text-sky-800">{{ $score($nilai?->nilai_akhir, 2) }}</span><i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform group-open:rotate-180" aria-hidden="true"></i></span>
                    </summary>
                    <dl class="mt-3 grid grid-cols-2 gap-2 text-xs">
                        @foreach(['Tugas' => ['tugas', 'rata_tugas'], 'Latihan' => ['latihan', 'rata_latihan'], 'UH' => ['uh', 'rata_uh']] as $label => [$group, $average])
                            <div class="rounded-lg bg-slate-50 p-2"><dt class="text-slate-500">{{ $label }} · {{ $counts[$group] }}/5</dt><dd class="mt-1 font-bold text-slate-900">{{ $score($nilai?->$average) }}</dd></div>
                        @endforeach
                        <div class="rounded-lg bg-slate-50 p-2"><dt class="text-slate-500">PTS / PAS</dt><dd class="mt-1 font-bold text-slate-900">{{ $score($nilai?->pts, 0) }} / {{ $score($nilai?->pas, 0) }}</dd></div>
                    </dl>
                </details>
            @empty
                <p class="px-4 py-8 text-center text-sm text-slate-500">Belum ada mata pelajaran untuk kelas ini.</p>
            @endforelse
        </div>
    </section>

    @if($isKelasAkhir)
        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-extrabold text-slate-900">Penilaian tingkat akhir</h2>
            <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($mataPelajaranList as $mapel)
                    @php $nilai = $nilaiData[$mapel->id] ?? null; @endphp
                    <div class="rounded-lg border border-slate-200 p-3"><h3 class="text-xs font-bold text-slate-900">{{ $mapel->nama_mapel }}</h3><dl class="mt-2 grid grid-cols-5 gap-1 text-[11px]">@foreach(['TO 1' => 'to_1', 'TO 2' => 'to_2', 'TO 3' => 'to_3', 'UPK' => 'upk', 'Praktik' => 'ujian_praktek'] as $label => $field)<div><dt class="text-slate-500">{{ $label }}</dt><dd class="font-bold text-slate-900">{{ $score($nilai?->$field, 0) }}</dd></div>@endforeach</dl></div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-sky-200 bg-sky-50 p-4 text-xs text-sky-900">
        <p class="max-w-2xl leading-5">Rata-rata memakai nilai yang terisi saja. Nilai akhir menggunakan bobot Tugas 1, Latihan 1, UH 2, PTS 3, dan PAS 3; KKM acuan 70.</p>
        <a href="{{ route('wali.rapor.index', ['semester' => $semester, 'siswa_id' => $siswa->id]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-sky-700 px-3 font-bold text-white hover:bg-sky-800">Lanjut ke rapor<i class="fas fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</div>
@endsection
