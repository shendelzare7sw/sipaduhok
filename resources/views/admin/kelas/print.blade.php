@extends('layouts.print')

@section('title', 'Daftar Kelas')
@section('back-url', route('admin.kelas.index'))
@section('report-title', 'Daftar Kelas')

@section('report-meta')
    <p class="mt-1 text-xs text-slate-600 print:text-[8pt]">Tahun ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua tahun ajaran' }} &middot; Total {{ $kelas->count() }} kelas</p>
@endsection

@section('report-content')
    @if($kelas->isNotEmpty())
        <div class="mb-4 grid grid-cols-3 gap-2 print:grid-cols-3">@foreach([['Total kelas', $kelas->count()], ['Total siswa', $kelas->sum('siswa_count')], ['Total kuota', $kelas->sum('kuota_siswa')]] as $item)<div class="rounded-lg border border-slate-300 px-2 py-1.5 text-center"><p class="text-[9px] font-bold uppercase text-slate-500 print:text-[7pt]">{{ $item[0] }}</p><p class="text-sm font-extrabold print:text-[9pt]">{{ $item[1] }}</p></div>@endforeach</div>
        <table class="w-full table-fixed border-collapse text-[9px] print:text-[7pt]"><colgroup><col class="w-[5%]"><col class="w-[19%]"><col class="w-[14%]"><col class="w-[8%]"><col class="w-[19%]"><col><col class="w-[7%]"><col class="w-[7%]"></colgroup><thead class="bg-slate-100"><tr>@foreach(['No', 'Kode kelas', 'Nama kelas', 'Jenjang', 'Cabang', 'Wali kelas', 'Siswa', 'Kuota'] as $heading)<th class="border border-slate-900 px-1.5 py-2 text-center font-extrabold">{{ $heading }}</th>@endforeach</tr></thead><tbody>@foreach($kelas as $index => $item)<tr class="break-inside-avoid"><td class="border border-slate-900 px-1.5 py-1.5 text-center">{{ $index + 1 }}</td><td class="border border-slate-900 px-1.5 py-1.5 font-mono">{{ $item->kode_kelas }}</td><td class="border border-slate-900 px-1.5 py-1.5 font-semibold">{{ $item->nama_kelas }}</td><td class="border border-slate-900 px-1.5 py-1.5 text-center">{{ $item->jenjang }}</td><td class="border border-slate-900 px-1.5 py-1.5">{{ $item->cabang->nama_cabang ?? '-' }}</td><td class="border border-slate-900 px-1.5 py-1.5">{{ $item->waliKelas->nama_lengkap ?? '-' }}</td><td class="border border-slate-900 px-1.5 py-1.5 text-center">{{ $item->siswa_count }}</td><td class="border border-slate-900 px-1.5 py-1.5 text-center">{{ $item->kuota_siswa }}</td></tr>@endforeach</tbody></table>
    @else
        <div class="border border-slate-300 p-8 text-center text-sm text-slate-500">Tidak ada kelas yang sesuai filter.</div>
    @endif
@endsection
