@extends('layouts.print')

@section('title', 'Daftar Siswa')
@section('back-url', route('admin.manajemen-siswa.index', request()->query()))
@section('report-title', 'Daftar Siswa')
@section('report-meta')
<p class="mt-1 text-xs text-slate-600 print:text-[8pt]">
    @if($kelas)Kelas {{ $kelas->nama_kelas }} &middot; {{ $kelas->jenjang }}
    @elseif($cabang)Cabang {{ $cabang->nama_cabang }}
    @else Semua siswa
    @endif
    &middot; Urut {{ ['kelas'=>'per kelas','cabang'=>'per cabang','nama'=>'abjad'][$sortBy] ?? 'abjad' }}
</p>
@endsection

@section('report-content')
<table class="w-full table-fixed border-collapse text-[9px] print:text-[7pt]">
    <colgroup><col class="w-8"><col class="w-24"><col class="w-20"><col class="w-[25%]"><col class="w-10"><col><col class="w-20"></colgroup>
    <thead><tr class="bg-slate-800 text-white"><th class="border border-slate-500 p-2">No</th><th class="border border-slate-500 p-2">NISN</th><th class="border border-slate-500 p-2">NIS</th><th class="border border-slate-500 p-2">Nama lengkap</th><th class="border border-slate-500 p-2">JK</th><th class="border border-slate-500 p-2">Tempat, tanggal lahir</th>@unless($kelas)<th class="border border-slate-500 p-2">Kelas</th>@endunless</tr></thead>
    <tbody>
        @php $currentKelas = null; @endphp
        @forelse($siswaList as $siswa)
            @if($sortBy === 'kelas' && !$kelas && $currentKelas !== ($siswa->kelas->nama_kelas ?? 'Tanpa Kelas'))
                @php $currentKelas = $siswa->kelas->nama_kelas ?? 'Tanpa Kelas'; @endphp
                <tr><td colspan="7" class="border border-slate-300 bg-blue-50 p-2 font-extrabold text-blue-900">{{ $currentKelas }}{{ $siswa->kelas ? ' ('.$siswa->kelas->jenjang.')' : '' }}</td></tr>
            @endif
            <tr><td class="border border-slate-300 p-2 text-center">{{ $loop->iteration }}</td><td class="whitespace-nowrap border border-slate-300 p-2">{{ $siswa->nisn ?: '-' }}</td><td class="whitespace-nowrap border border-slate-300 p-2">{{ $siswa->nis ?: '-' }}</td><td class="border border-slate-300 p-2 font-bold">{{ $siswa->nama_lengkap }}</td><td class="border border-slate-300 p-2 text-center">{{ $siswa->jenis_kelamin }}</td><td class="border border-slate-300 p-2">{{ $siswa->tempat_lahir ?: '-' }}, {{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>@unless($kelas)<td class="whitespace-nowrap border border-slate-300 p-2">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>@endunless</tr>
        @empty
            <tr><td colspan="7" class="border border-slate-300 p-10 text-center">Tidak ada data siswa.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="mt-4 grid grid-cols-3 overflow-hidden rounded-xl border border-slate-300 text-center text-[9px] print:text-[7pt]"><div class="p-3"><span class="block text-slate-500">Total siswa</span><strong class="text-base">{{ $siswaList->count() }}</strong></div><div class="border-x border-slate-300 p-3"><span class="block text-slate-500">Laki-laki</span><strong class="text-base">{{ $siswaList->where('jenis_kelamin','L')->count() }}</strong></div><div class="p-3"><span class="block text-slate-500">Perempuan</span><strong class="text-base">{{ $siswaList->where('jenis_kelamin','P')->count() }}</strong></div></div>
@endsection
