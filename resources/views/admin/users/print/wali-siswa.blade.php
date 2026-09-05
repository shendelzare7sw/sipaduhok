@extends('layouts.print')

@section('title', 'Laporan Data Wali Siswa')
@section('back-url', route('admin.users.wali-siswa'))
@section('report-title', 'Laporan Data Wali Siswa')

@section('report-meta')
    @if(!empty($filterInfo))<p class="mt-1 text-xs text-slate-600 print:text-[8pt]">{{ implode(' · ', $filterInfo) }}</p>@endif
    <p class="mt-1 text-xs font-semibold print:text-[8pt]">Total: {{ count($orangTua) }} wali siswa</p>
@endsection

@section('report-content')
<table class="w-full table-fixed border-collapse text-[10px] print:text-[7.5pt]">
    <colgroup><col class="w-[5%]"><col class="w-[23%]"><col class="w-[24%]"><col class="w-[37%]"><col class="w-[11%]"></colgroup>
    <thead class="bg-slate-100"><tr>@foreach(['No', 'Nama wali', 'Username / email', 'Anak terhubung', 'Status'] as $heading)<th class="border border-slate-900 px-2 py-2 text-center font-extrabold">{{ $heading }}</th>@endforeach</tr></thead>
    <tbody>
        @forelse($orangTua as $index => $item)
            <tr class="break-inside-avoid align-top">
                <td class="border border-slate-900 px-2 py-1.5 text-center tabular-nums">{{ $index + 1 }}</td>
                <td class="border border-slate-900 px-2 py-1.5 font-semibold">{{ $item->name }}</td>
                <td class="border border-slate-900 px-2 py-1.5"><p class="font-semibold">{{ $item->username }}</p><p class="break-all text-slate-600">{{ $item->email }}</p></td>
                <td class="border border-slate-900 px-2 py-1.5">
                    @forelse($item->studentParents as $studentParent)
                        <p class="mb-1 last:mb-0">• {{ $studentParent->siswa?->nama_lengkap ?? '-' }} @if($studentParent->siswa?->kelas)<span class="whitespace-nowrap text-slate-600">({{ $studentParent->siswa->kelas->nama_kelas }} {{ $studentParent->siswa->kelas->jenjang }})</span>@endif</p>
                    @empty
                        <span class="text-slate-500">Belum ada siswa terhubung</span>
                    @endforelse
                </td>
                <td class="border border-slate-900 px-2 py-1.5 text-center">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="border border-slate-900 p-6 text-center text-slate-500">Tidak ada data wali siswa.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
