@extends('layouts.print')

@section('title', 'Rekap Presensi - '.$kelas->nama_kelas)
@section('back-url', route('wali.presensi.index'))
@section('document-width', 'min-w-[760px] max-w-5xl mx-auto')
@section('report-title', 'Rekap Presensi Siswa')

@php
    $waliNames = $kelas->waliKelasMultiple->pluck('nama_lengkap')->filter()->implode(', ') ?: ($kelas->waliKelas?->nama_lengkap ?? '-');
@endphp

@section('document-header')
    @include('layouts.partials.print-header', ['cabang' => $kelas->cabang ?? null])
@endsection

@section('report-meta')
    <dl class="mt-3 grid grid-cols-2 gap-x-8 gap-y-1 text-left text-xs print:text-[9pt]">
        <div><dt class="inline font-bold">Kelas:</dt> <dd class="inline">{{ $kelas->nama_kelas }}</dd></div>
        <div><dt class="inline font-bold">Periode:</dt> <dd class="inline">{{ $semester ? 'Semester '.ucfirst($semester).' · '.($kelas->tahunAjaran->nama_tahun_ajaran ?? '-') : \Carbon\Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y') }}</dd></div>
        <div><dt class="inline font-bold">Wali kelas:</dt> <dd class="inline">{{ $waliNames }}</dd></div>
        <div><dt class="inline font-bold">Dicetak:</dt> <dd class="inline">{{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</dd></div>
    </dl>
@endsection

@section('report-content')
    @php
        $totals = [
            'hadir' => array_sum(array_column($rekapBulan, 'hadir')),
            'sakit' => array_sum(array_column($rekapBulan, 'sakit')),
            'izin' => array_sum(array_column($rekapBulan, 'izin')),
            'alpha' => array_sum(array_column($rekapBulan, 'alpha')),
        ];
    @endphp
    <table class="w-full table-fixed border-collapse text-left text-xs print:text-[8pt]">
        <colgroup><col class="w-10"><col class="w-28"><col><col class="w-14"><col class="w-14"><col class="w-14"><col class="w-14"><col class="w-16"></colgroup>
        <thead class="bg-slate-100 font-bold uppercase text-slate-900"><tr><th class="border border-slate-300 px-2 py-2 text-center">No</th><th class="border border-slate-300 px-2 py-2">NIS</th><th class="border border-slate-300 px-2 py-2">Nama siswa</th><th class="border border-slate-300 px-2 py-2 text-center">H</th><th class="border border-slate-300 px-2 py-2 text-center">S</th><th class="border border-slate-300 px-2 py-2 text-center">I</th><th class="border border-slate-300 px-2 py-2 text-center">A</th><th class="border border-slate-300 px-2 py-2 text-center">Total</th></tr></thead>
        <tbody>
            @forelse($siswaList as $index => $siswa)
                @php($rekap = $rekapBulan[$siswa->id])
                <tr class="break-inside-avoid"><td class="border border-slate-300 px-2 py-2 text-center">{{ $index + 1 }}</td><td class="border border-slate-300 px-2 py-2">{{ $siswa->nis ?? $siswa->nisn ?? '-' }}</td><td class="border border-slate-300 px-2 py-2 font-semibold">{{ $siswa->nama_lengkap }}</td><td class="border border-slate-300 bg-emerald-50 px-2 py-2 text-center">{{ $rekap['hadir'] }}</td><td class="border border-slate-300 bg-amber-50 px-2 py-2 text-center">{{ $rekap['sakit'] }}</td><td class="border border-slate-300 bg-sky-50 px-2 py-2 text-center">{{ $rekap['izin'] }}</td><td class="border border-slate-300 bg-rose-50 px-2 py-2 text-center">{{ $rekap['alpha'] }}</td><td class="border border-slate-300 px-2 py-2 text-center font-bold">{{ $rekap['hadir'] + $rekap['sakit'] + $rekap['izin'] + $rekap['alpha'] }}</td></tr>
            @empty
                <tr><td colspan="8" class="border border-slate-300 px-3 py-8 text-center text-slate-500">Belum ada siswa aktif.</td></tr>
            @endforelse
        </tbody>
    </table>
    <section class="mt-5 break-inside-avoid"><h2 class="text-sm font-extrabold text-slate-900 print:text-[10pt]">Ringkasan kehadiran</h2><dl class="mt-2 grid grid-cols-4 gap-2 text-center text-xs"><div class="rounded-lg bg-emerald-50 p-2"><dt>Hadir</dt><dd class="mt-1 text-lg font-extrabold text-emerald-800">{{ $totals['hadir'] }}</dd></div><div class="rounded-lg bg-amber-50 p-2"><dt>Sakit</dt><dd class="mt-1 text-lg font-extrabold text-amber-800">{{ $totals['sakit'] }}</dd></div><div class="rounded-lg bg-sky-50 p-2"><dt>Izin</dt><dd class="mt-1 text-lg font-extrabold text-sky-800">{{ $totals['izin'] }}</dd></div><div class="rounded-lg bg-rose-50 p-2"><dt>Alpha</dt><dd class="mt-1 text-lg font-extrabold text-rose-800">{{ $totals['alpha'] }}</dd></div></dl></section>
@endsection

@section('report-footer')
    <footer class="mt-10 ml-auto w-64 text-center text-xs print:text-[9pt]"><p>Tangerang Selatan, {{ now()->locale('id')->translatedFormat('d F Y') }}</p><p class="mt-1">Wali Kelas</p><div class="h-16"></div><p class="border-t border-slate-900 pt-1 font-bold">{{ $waliNames }}</p></footer>
@endsection
