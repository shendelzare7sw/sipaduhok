@extends('layouts.print')

@section('title', 'Daftar Mata Pelajaran')
@section('back-url', route('admin.mata-pelajaran.index'))
@section('report-title', 'Daftar Mata Pelajaran')

@section('report-meta')
    <p class="mt-1 text-xs text-slate-600 print:text-[8pt]">{{ !empty($jenjangFilter) ? 'Jenjang: ' . implode(', ', $jenjangFilter) : 'Semua jenjang' }} · Total {{ $mataPelajaranList->count() }} pelajaran</p>
@endsection

@section('report-content')
    @if($mataPelajaranList->isNotEmpty())
        <div class="mb-4 grid grid-cols-4 gap-2 sm:grid-cols-7 print:grid-cols-7">
            @foreach($stats as $jenjang => $total)<div class="rounded-lg border border-slate-300 px-2 py-1.5 text-center"><p class="text-[9px] font-bold uppercase text-slate-500 print:text-[7pt]">{{ $jenjang }}</p><p class="text-sm font-extrabold print:text-[9pt]">{{ $total }}</p></div>@endforeach
            <div class="rounded-lg border border-slate-900 bg-slate-100 px-2 py-1.5 text-center"><p class="text-[9px] font-bold uppercase text-slate-600 print:text-[7pt]">Ditampilkan</p><p class="text-sm font-extrabold print:text-[9pt]">{{ $mataPelajaranList->count() }}</p></div>
        </div>

        <table class="w-full table-fixed border-collapse text-[10px] print:text-[7.5pt]">
            <colgroup><col class="w-[5%]"><col class="w-[14%]"><col class="w-[24%]"><col class="w-[10%]"><col class="w-[12%]"><col></colgroup>
            <thead class="bg-slate-100"><tr>@foreach(['No', 'Kode', 'Nama mata pelajaran', 'Jenjang', 'Kelompok', 'Deskripsi'] as $heading)<th class="border border-slate-900 px-2 py-2 text-center font-extrabold">{{ $heading }}</th>@endforeach</tr></thead>
            <tbody>
                @php $currentJenjang = null; @endphp
                @foreach($mataPelajaranList as $index => $mapel)
                    @if($currentJenjang !== $mapel->jenjang)
                        @php $currentJenjang = $mapel->jenjang; @endphp
                        <tr class="break-inside-avoid bg-slate-100"><td colspan="6" class="border border-slate-900 px-2 py-1.5 font-extrabold">Jenjang {{ $mapel->jenjang }}</td></tr>
                    @endif
                    <tr class="break-inside-avoid"><td class="border border-slate-900 px-2 py-1.5 text-center tabular-nums">{{ $index + 1 }}</td><td class="border border-slate-900 px-2 py-1.5 text-center font-mono">{{ $mapel->kode_mapel ?: '-' }}</td><td class="border border-slate-900 px-2 py-1.5 font-semibold">{{ $mapel->nama_mapel }}</td><td class="border border-slate-900 px-2 py-1.5 text-center">{{ $mapel->jenjang }}</td><td class="border border-slate-900 px-2 py-1.5 text-center">{{ $mapel->kelompok ? 'Kel. ' . $mapel->kelompok : '-' }}</td><td class="border border-slate-900 px-2 py-1.5">{{ $mapel->deskripsi ?: '-' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="border border-slate-300 p-8 text-center text-sm text-slate-500">Tidak ada data mata pelajaran yang sesuai filter.</div>
    @endif
@endsection
