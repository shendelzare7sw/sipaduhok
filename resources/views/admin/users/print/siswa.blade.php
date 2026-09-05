@extends('layouts.print')

@section('title', 'Laporan Data Siswa')
@section('back-url', route('admin.users.siswa'))
@section('report-title', 'Laporan Data Siswa')

@section('report-meta')
    @if(!empty($filterInfo))<p class="mt-1 text-xs text-slate-600 print:text-[8pt]">{{ implode(' · ', $filterInfo) }}</p>@endif
    <p class="mt-1 text-xs font-semibold print:text-[8pt]">Total: {{ count($siswa) }} siswa</p>
@endsection

@section('report-content')
<table class="w-full table-fixed border-collapse text-[10px] print:text-[7.5pt]">
    <colgroup><col class="w-[5%]"><col class="w-[16%]"><col class="w-[23%]"><col class="w-[6%]"><col class="w-[15%]"><col class="w-[23%]"><col class="w-[12%]"></colgroup>
    <thead class="bg-slate-100"><tr>@foreach(['No', 'NIS / NISN', 'Nama lengkap', 'L/P', 'Kelas', 'Cabang', 'Status'] as $heading)<th class="border border-slate-900 px-2 py-2 text-center font-extrabold">{{ $heading }}</th>@endforeach</tr></thead>
    <tbody>
        @forelse($siswa as $index => $item)
            <tr class="break-inside-avoid">
                <td class="border border-slate-900 px-2 py-1.5 text-center tabular-nums">{{ $index + 1 }}</td>
                <td class="border border-slate-900 px-2 py-1.5"><p class="font-semibold">{{ $item->nis ?? '-' }}</p><p class="text-slate-600">{{ $item->nisn ?? '-' }}</p></td>
                <td class="border border-slate-900 px-2 py-1.5 font-semibold">{{ $item->nama_lengkap }}</td>
                <td class="border border-slate-900 px-2 py-1.5 text-center">{{ $item->jenis_kelamin }}</td>
                <td class="border border-slate-900 px-2 py-1.5"><span class="whitespace-nowrap">{{ $item->kelas->nama_kelas ?? '-' }} {{ $item->kelas->jenjang ?? '' }}</span></td>
                <td class="border border-slate-900 px-2 py-1.5">{{ $item->cabang->nama_cabang ?? '-' }}</td>
                <td class="border border-slate-900 px-2 py-1.5 text-center">{{ ucfirst($item->status ?? '-') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="border border-slate-900 p-6 text-center text-slate-500">Tidak ada data siswa.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
