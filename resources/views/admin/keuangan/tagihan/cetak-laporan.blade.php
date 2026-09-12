@extends('layouts.print')

@php $tagihanRoute = request()->routeIs('admin.*') ? 'admin.keuangan.tagihan' : 'bendahara.tagihan'; @endphp

@section('title', 'Laporan Rekap Tagihan')
@section('back-url', route($tagihanRoute.'.index', array_filter($filters ?? [])))
@section('document-width', 'min-w-[1080px]')
@section('report-title', 'Laporan Rekap Tagihan Siswa')

@section('report-meta')
    <div class="mt-2 space-y-0.5 text-[10px] leading-4 text-slate-600 print:text-[8pt]">
        <p>Tahun Ajaran: <strong>{{ $selectedYear->nama_tahun_ajaran ?? '-' }}</strong></p>
        <p>Kelas: <strong>{{ $selectedKelas ? $selectedKelas->nama_kelas . ' (' . $selectedKelas->jenjang . ')' : 'Semua Kelas' }}</strong></p>
        @if(!empty($filters['search']))<p>Pencarian: <strong>“{{ $filters['search'] }}”</strong></p>@endif
    </div>
@endsection

@section('report-content')
    @php
        $totalLunas = $siswaList->filter(fn ($siswa) => $siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0)->count();
        $totalBelumLunas = $siswaList->filter(fn ($siswa) => $siswa->sisa_tagihan > 0)->count();
    @endphp

    <section class="mb-5 grid grid-cols-4 divide-x divide-slate-200 rounded-xl border border-slate-300 print:mb-4 print:rounded-none">
        <div class="p-3"><span class="block text-[9px] font-bold uppercase tracking-wide text-slate-500 print:text-[7pt]">Jumlah siswa</span><strong class="mt-1 block text-sm print:text-[9pt]">{{ $siswaList->count() }} siswa</strong></div>
        <div class="p-3"><span class="block text-[9px] font-bold uppercase tracking-wide text-slate-500 print:text-[7pt]">Total tagihan</span><strong class="mt-1 block text-sm tabular-nums print:text-[9pt]">Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</strong></div>
        <div class="p-3"><span class="block text-[9px] font-bold uppercase tracking-wide text-slate-500 print:text-[7pt]">Total terbayar</span><strong class="mt-1 block text-sm tabular-nums print:text-[9pt]">Rp {{ number_format($grandTotalLunas, 0, ',', '.') }}</strong></div>
        <div class="p-3"><span class="block text-[9px] font-bold uppercase tracking-wide text-slate-500 print:text-[7pt]">Total tunggakan</span><strong class="mt-1 block text-sm tabular-nums text-red-700 print:text-[9pt]">Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</strong></div>
    </section>

    <table class="w-full table-fixed border-collapse text-[9px] leading-4 print:text-[7pt]">
        <colgroup><col class="w-8"><col class="w-[18%]"><col class="w-[11%]"><col class="w-[8%]"><col class="w-[16%]"><col class="w-[13%]"><col class="w-[13%]"><col class="w-[13%]"><col class="w-[8%]"></colgroup>
        <thead><tr class="bg-slate-100"><th class="border border-slate-400 px-1.5 py-2 text-center">No</th><th class="border border-slate-400 px-1.5 py-2 text-left">Nama siswa</th><th class="border border-slate-400 px-1.5 py-2">NISN</th><th class="border border-slate-400 px-1.5 py-2">Kelas</th><th class="border border-slate-400 px-1.5 py-2">Cabang</th><th class="border border-slate-400 px-1.5 py-2 text-right">Tagihan</th><th class="border border-slate-400 px-1.5 py-2 text-right">Terbayar</th><th class="border border-slate-400 px-1.5 py-2 text-right">Sisa</th><th class="border border-slate-400 px-1.5 py-2">Status</th></tr></thead>
        <tbody>
            @forelse($siswaList as $index => $siswa)
                <tr class="break-inside-avoid"><td class="border border-slate-300 px-1.5 py-2 text-center">{{ $index + 1 }}</td><td class="border border-slate-300 px-1.5 py-2 font-bold">{{ $siswa->nama_lengkap }}</td><td class="border border-slate-300 px-1.5 py-2 text-center">{{ $siswa->nisn ?? '-' }}</td><td class="border border-slate-300 px-1.5 py-2 text-center">{{ $siswa->kelas->nama_kelas ?? '-' }}</td><td class="border border-slate-300 px-1.5 py-2">{{ $siswa->cabang->nama_cabang ?? '-' }}</td><td class="border border-slate-300 px-1.5 py-2 text-right tabular-nums">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td><td class="border border-slate-300 px-1.5 py-2 text-right tabular-nums">Rp {{ number_format($siswa->tagihan_lunas, 0, ',', '.') }}</td><td class="border border-slate-300 px-1.5 py-2 text-right font-bold tabular-nums">Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}</td><td class="border border-slate-300 px-1.5 py-2 text-center font-bold {{ $siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0 ? 'text-emerald-700' : ($siswa->total_tagihan == 0 ? 'text-slate-500' : 'text-red-700') }}">{{ $siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0 ? 'LUNAS' : ($siswa->total_tagihan == 0 ? 'KOSONG' : 'BELUM') }}</td></tr>
            @empty
                <tr><td colspan="9" class="border border-slate-300 px-3 py-8 text-center text-slate-500">Tidak ada data siswa sesuai filter yang dipilih.</td></tr>
            @endforelse
        </tbody>
        <tfoot><tr class="bg-slate-100 font-extrabold"><td colspan="5" class="border border-slate-400 px-1.5 py-2 text-right">GRAND TOTAL</td><td class="border border-slate-400 px-1.5 py-2 text-right tabular-nums">Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</td><td class="border border-slate-400 px-1.5 py-2 text-right tabular-nums">Rp {{ number_format($grandTotalLunas, 0, ',', '.') }}</td><td class="border border-slate-400 px-1.5 py-2 text-right tabular-nums">Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</td><td class="border border-slate-400"></td></tr></tfoot>
    </table>

    <section class="mt-5 ml-auto w-80 border border-slate-300 text-[10px] leading-5 print:mt-4 print:text-[8pt]"><h2 class="border-b border-slate-300 bg-slate-100 px-3 py-2 font-extrabold">Ringkasan status siswa</h2><dl class="divide-y divide-slate-200"><div class="flex justify-between gap-4 px-3 py-1.5"><dt>Siswa lunas</dt><dd class="font-bold">{{ $totalLunas }} siswa</dd></div><div class="flex justify-between gap-4 px-3 py-1.5"><dt>Siswa belum lunas</dt><dd class="font-bold">{{ $totalBelumLunas }} siswa</dd></div><div class="flex justify-between gap-4 px-3 py-1.5"><dt>Total tunggakan</dt><dd class="font-extrabold">Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</dd></div></dl></section>
@endsection

@section('report-footer')
    <footer class="mt-8 flex items-start justify-between gap-8 text-[10px] leading-5 print:mt-6 print:text-[8pt]"><div><p>Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</p><p>Jumlah data: {{ $siswaList->count() }} siswa</p></div><div class="w-64 text-center"><p>Tangerang Selatan, {{ now()->locale('id')->translatedFormat('d F Y') }}</p><p>Admin / Kepala Sekolah</p><div class="h-16"></div><p class="border-t border-slate-900 pt-1">( ................................ )</p></div></footer>
@endsection
