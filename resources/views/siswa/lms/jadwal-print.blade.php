@php
    // Dokumen murni Tailwind: lepas bridge Bootstrap di layouts.print.
    $bootstrapFree = true;
@endphp
@extends('layouts.print')

@section('title', 'Jadwal Pelajaran - ' . $kelas->nama_kelas)
@section('back-url', route('siswa.lms.jadwal'))
@section('document-width', 'min-w-[720px]')
@section('report-title', 'Jadwal Mata Pelajaran ' . strtoupper($kelas->jenjang))

@section('report-meta')
    <p class="mt-1 text-xs text-slate-600 print:text-[9pt]">Tahun Ajaran {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</p>
@endsection

@section('report-content')
    <div class="mb-4 grid grid-cols-3 gap-3 border border-slate-300 bg-slate-50 px-4 py-2.5 text-xs print:text-[9pt]">
        <p><strong>Kelas:</strong> {{ $kelas->nama_kelas }}</p>
        <p><strong>Jenjang:</strong> {{ strtoupper($kelas->jenjang) }}</p>
        <p><strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap ?? '-' }}</p>
    </div>

    @foreach($hariList as $hari)
        <section class="mb-5 break-inside-avoid print:mb-4">
            <h2 class="bg-[#165fac] px-3 py-2 text-sm font-bold text-white print:text-[10pt]">{{ $hari }}</h2>
            @if($jadwalPerHari[$hari]->count() > 0)
                <table class="w-full table-fixed border-collapse text-xs print:text-[9pt]">
                    <colgroup><col class="w-12"><col class="w-32"><col><col class="w-28"><col></colgroup>
                    <thead class="bg-slate-200">
                        <tr>
                            <th class="border border-slate-300 px-2 py-1.5">No</th>
                            <th class="border border-slate-300 px-2 py-1.5">Jam</th>
                            <th class="border border-slate-300 px-2 py-1.5 text-left">Mata Pelajaran</th>
                            <th class="border border-slate-300 px-2 py-1.5 text-left">Kode</th>
                            <th class="border border-slate-300 px-2 py-1.5 text-left">Guru Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalPerHari[$hari] as $index => $item)
                            @if($item['type'] === 'istirahat')
                                <tr class="bg-amber-50 italic">
                                    <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $index + 1 }}</td>
                                    <td class="border border-slate-300 px-2 py-1.5 text-center">{{ substr($item['data']->jam_mulai, 0, 5) }} – {{ substr($item['data']->jam_selesai, 0, 5) }}</td>
                                    <td class="border border-slate-300 px-2 py-1.5 font-bold">{{ $item['data']->nama_istirahat }}</td>
                                    <td class="border border-slate-300 px-2 py-1.5">-</td>
                                    <td class="border border-slate-300 px-2 py-1.5">-</td>
                                </tr>
                            @else
                                @php $jadwal = $item['data']; @endphp
                                <tr>
                                    <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $index + 1 }}</td>
                                    <td class="border border-slate-300 px-2 py-1.5 text-center">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                    <td class="border border-slate-300 px-2 py-1.5 font-bold">{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                    <td class="border border-slate-300 px-2 py-1.5">{{ $jadwal->mataPelajaran->kode_mapel }}</td>
                                    <td class="border border-slate-300 px-2 py-1.5">{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="border border-t-0 border-slate-300 px-3 py-3 text-center text-xs italic text-slate-500 print:text-[9pt]">Tidak ada jadwal pelajaran</p>
            @endif
        </section>
    @endforeach
@endsection

@section('report-footer')
    <footer class="mt-8 flex justify-end text-xs leading-5 print:mt-6 print:text-[9pt]">
        <div class="w-64 text-center">
            <p>Tangerang Selatan, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</p>
            <p>Wali Kelas</p>
            <div class="h-16"></div>
            <p class="border-t border-slate-900 pt-1 font-bold">{{ $kelas->waliKelas->nama_lengkap ?? '____________________' }}</p>
        </div>
    </footer>
@endsection
