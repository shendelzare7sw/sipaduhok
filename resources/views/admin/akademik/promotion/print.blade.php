@extends('layouts.print')

@section('title', 'Laporan Kenaikan Kelas')
@section('report-title', 'Laporan Kenaikan Kelas')
@section('back-url', url()->previous())

@section('report-meta')
    <p class="mt-1 text-[11px] text-slate-600 print:text-[9pt]">Tahun Ajaran {{ $selectedYear->nama_tahun_ajaran }}</p>
    @if ($filterStatus)
        <p class="mt-1 text-[10px] font-bold text-slate-700 print:text-[8pt]">Status: {{ str_replace('_', ' ', $filterStatus) }}</p>
    @endif
@endsection

@section('report-content')
    <section class="mb-5 grid grid-cols-5 gap-2 print:mb-4">
        <article class="rounded-lg border border-emerald-200 bg-emerald-50 p-2 text-center">
            <p class="text-lg font-extrabold text-emerald-800 print:text-[12pt]">{{ $stats['NAIK_KELAS'] ?? 0 }}</p>
            <p class="text-[9px] font-bold uppercase text-emerald-700 print:text-[7pt]">Naik Kelas</p>
        </article>
        <article class="rounded-lg border border-sky-200 bg-sky-50 p-2 text-center">
            <p class="text-lg font-extrabold text-sky-800 print:text-[12pt]">{{ $stats['LULUS'] ?? 0 }}</p>
            <p class="text-[9px] font-bold uppercase text-sky-700 print:text-[7pt]">Lulus</p>
        </article>
        <article class="rounded-lg border border-amber-200 bg-amber-50 p-2 text-center">
            <p class="text-lg font-extrabold text-amber-800 print:text-[12pt]">{{ $stats['NAIK_KELAS_TUNGGAKAN'] ?? 0 }}</p>
            <p class="text-[9px] font-bold uppercase text-amber-700 print:text-[7pt]">Naik Dispensasi</p>
        </article>
        <article class="rounded-lg border border-violet-200 bg-violet-50 p-2 text-center">
            <p class="text-lg font-extrabold text-violet-800 print:text-[12pt]">{{ $stats['LULUS_TUNGGAKAN'] ?? 0 }}</p>
            <p class="text-[9px] font-bold uppercase text-violet-700 print:text-[7pt]">Lulus Dispensasi</p>
        </article>
        <article class="rounded-lg border border-red-200 bg-red-50 p-2 text-center">
            <p class="text-lg font-extrabold text-red-800 print:text-[12pt]">{{ $stats['TIDAK_NAIK_KELAS'] ?? 0 }}</p>
            <p class="text-[9px] font-bold uppercase text-red-700 print:text-[7pt]">Tidak Naik</p>
        </article>
    </section>

    <table class="w-full table-fixed border-collapse text-[10px] print:text-[7.5pt]">
        <colgroup>
            <col class="w-10">
            <col class="w-[22%]">
            <col class="w-[12%]">
            <col class="w-[15%]">
            <col class="w-[15%]">
            <col class="w-[13%]">
            <col>
        </colgroup>
        <thead>
            <tr class="bg-slate-100 text-left uppercase text-slate-700">
                <th class="border border-slate-400 px-2 py-2 text-center">No</th>
                <th class="border border-slate-400 px-2 py-2">Nama Siswa</th>
                <th class="border border-slate-400 px-2 py-2">NIS</th>
                <th class="border border-slate-400 px-2 py-2">Kelas Asal</th>
                <th class="border border-slate-400 px-2 py-2">Kelas Tujuan</th>
                <th class="border border-slate-400 px-2 py-2 text-center">Keuangan</th>
                <th class="border border-slate-400 px-2 py-2 text-center">Hasil</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $index => $data)
                <tr class="break-inside-avoid">
                    <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $index + 1 }}</td>
                    <td class="break-words border border-slate-300 px-2 py-1.5 font-bold">{{ $data->nama_lengkap }}</td>
                    <td class="break-words border border-slate-300 px-2 py-1.5">{{ $data->nis ?? '-' }}</td>
                    <td class="break-words border border-slate-300 px-2 py-1.5">{{ $data->kelas_asal }}</td>
                    <td class="break-words border border-slate-300 px-2 py-1.5">{{ $data->kelas_tujuan ?? '-' }}</td>
                    <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $data->status_pembayaran === 'LUNAS' ? 'Lunas' : 'Belum Lunas' }}</td>
                    <td class="border border-slate-300 px-2 py-1.5 text-center font-bold">
                        @switch($data->status_kelulusan)
                            @case('NAIK_KELAS') Naik Kelas @break
                            @case('LULUS') Lulus @break
                            @case('NAIK_KELAS_TUNGGAKAN') Naik (Disp.) @break
                            @case('LULUS_TUNGGAKAN') Lulus (Disp.) @break
                            @case('TIDAK_NAIK_KELAS') Tidak Naik @break
                            @default {{ str_replace('_', ' ', $data->status_kelulusan) }}
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border border-slate-300 px-3 py-8 text-center text-slate-500">Belum ada data kenaikan kelas yang dieksekusi.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($students->count() > 0)
            <tfoot>
                <tr class="bg-slate-50 font-extrabold">
                    <td colspan="6" class="border border-slate-400 px-2 py-2 text-right">Total Siswa</td>
                    <td class="border border-slate-400 px-2 py-2 text-center">{{ $students->count() }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
@endsection
