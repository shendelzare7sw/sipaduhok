@extends('layouts.print')

@section('title', 'Jadwal Pelajaran - '.$kelas->nama_kelas)
@section('back-url', route('wali.jadwal.index'))
@section('document-width', 'min-w-[760px] max-w-5xl mx-auto')
@section('report-title', 'Jadwal Pelajaran')

@php
    $waliNames = $kelas->waliKelasMultiple->pluck('nama_lengkap')->filter()->implode(', ') ?: ($kelas->waliKelas?->nama_lengkap ?? '-');
@endphp

@section('report-meta')
    <div class="mt-3 grid grid-cols-2 gap-x-8 gap-y-1 text-left text-xs print:text-[9pt]">
        <p><strong>Kelas:</strong> {{ $kelas->nama_kelas }}</p>
        <p><strong>Jenjang:</strong> {{ strtoupper($kelas->jenjang) }}</p>
        <p><strong>Tahun ajaran:</strong> {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</p>
        <p><strong>Wali kelas:</strong> {{ $waliNames }}</p>
    </div>
@endsection

@section('report-content')
    <div class="space-y-6">
        @foreach($hariList as $hari)
            <section class="break-inside-avoid">
                <h2 class="mb-2 rounded-lg bg-slate-100 px-3 py-2 text-sm font-bold text-slate-900 print:rounded-none print:text-[10pt]">{{ $hari }}</h2>
                @if($jadwalPerHari[$hari]->isNotEmpty())
                    <table class="w-full table-fixed border-collapse text-left text-xs print:text-[8pt]">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="w-10 border border-slate-300 px-2 py-2 text-center">No</th>
                                <th class="w-28 border border-slate-300 px-2 py-2">Jam</th>
                                <th class="border border-slate-300 px-2 py-2">Mata pelajaran</th>
                                <th class="w-16 border border-slate-300 px-2 py-2">Kode</th>
                                <th class="w-40 border border-slate-300 px-2 py-2">Guru pengajar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalPerHari[$hari] as $index => $item)
                                @if($item['type'] === 'istirahat')
                                    @php($istirahat = $item['data'])
                                    <tr class="bg-amber-50">
                                        <td class="border border-slate-300 px-2 py-2 text-center">{{ $index + 1 }}</td>
                                        <td class="border border-slate-300 px-2 py-2">{{ substr($istirahat->jam_mulai, 0, 5) }}–{{ substr($istirahat->jam_selesai, 0, 5) }}</td>
                                        <td class="border border-slate-300 px-2 py-2 font-bold">{{ $istirahat->nama_istirahat }}</td>
                                        <td class="border border-slate-300 px-2 py-2">-</td>
                                        <td class="border border-slate-300 px-2 py-2">-</td>
                                    </tr>
                                @else
                                    @php($jadwal = $item['data'])
                                    <tr>
                                        <td class="border border-slate-300 px-2 py-2 text-center">{{ $index + 1 }}</td>
                                        <td class="border border-slate-300 px-2 py-2">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                        <td class="border border-slate-300 px-2 py-2 font-semibold">{{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}</td>
                                        <td class="border border-slate-300 px-2 py-2">{{ $jadwal->mataPelajaran->kode_mapel ?? '-' }}</td>
                                        <td class="border border-slate-300 px-2 py-2">{{ $jadwal->guru->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="px-3 py-2 text-xs text-slate-500">Tidak ada jadwal pelajaran.</p>
                @endif
            </section>
        @endforeach
    </div>
@endsection

@section('report-footer')
    <footer class="mt-10 ml-auto w-64 text-center text-xs print:text-[9pt]">
        <p>Tangerang Selatan, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
        <p class="mt-1">Wali Kelas</p>
        <div class="h-16"></div>
        <p class="border-t border-slate-900 pt-1 font-bold">{{ $waliNames }}</p>
    </footer>
@endsection
