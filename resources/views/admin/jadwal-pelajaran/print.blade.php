@extends('layouts.print')
@section('title', 'Jadwal ' . $kelas->nama_kelas)
@section('back-url', route('admin.jadwal-pelajaran.show', ['kelas' => $kelas->id, 'tahun_ajaran_id' => $currentTahunAjaran->id]))
@section('report-title', 'Jadwal Pelajaran')
@section('report-meta')<p class="mt-1 text-xs text-slate-600 print:text-[8pt]">Kelas {{ $kelas->nama_kelas }} · {{ $kelas->jenjang }} · Tahun ajaran {{ $currentTahunAjaran->nama_tahun_ajaran }}</p>@endsection

@section('report-content')
@if(!isset($preview) || !$preview)<span data-auto-print class="hidden"></span>@endif
@php
    $days = $scheduleGrid['days'];
    $rows = $scheduleGrid['rows'];
@endphp
<div class="mb-4 grid grid-cols-3 gap-3 rounded-xl border border-slate-300 p-3 text-[10px] print:text-[8pt]"><p><span class="block font-bold uppercase text-slate-500">Kelas</span>{{ $kelas->nama_kelas }}</p><p><span class="block font-bold uppercase text-slate-500">Cabang</span>{{ $kelas->cabang->nama_cabang ?? '-' }}</p><p><span class="block font-bold uppercase text-slate-500">Wali kelas</span>{{ $kelas->waliKelas->nama_lengkap ?? '-' }}</p></div>
<table class="w-full table-fixed border-collapse text-[10px] print:text-[7.5pt]"><thead><tr class="bg-slate-800 text-white"><th class="w-28 border border-slate-500 px-2 py-2">Jam</th>@foreach($days as $day)<th class="border border-slate-500 px-2 py-2">{{ strtoupper($day) }}</th>@endforeach</tr></thead><tbody>
@foreach($rows as $row)
    @php
        $endTime = '...';
        foreach ($days as $dayName) {
            if (!isset($row['days'][$dayName]['data'])) continue;
            foreach ((array) $row['days'][$dayName]['data'] as $entry) {
                if (!is_object($entry) || !isset($entry->jam_selesai)) continue;
                $candidate = $entry->jam_selesai instanceof \Carbon\Carbon ? $entry->jam_selesai->format('H:i') : substr($entry->jam_selesai, 0, 5);
                if ($endTime === '...' || $candidate > $endTime) $endTime = $candidate;
            }
        }
    @endphp
    <tr><td class="border border-slate-300 bg-slate-50 px-2 py-2 text-center font-bold">{{ $row['time_start'] }}–{{ $endTime }}</td>
    @for($i = 0; $i < count($days); $i++)
        @php
            $cell = $row['days'][$days[$i]];
        @endphp
        @if($cell['type'] === 'taken') @continue @endif
        @php
            $colspan = 1;
            if ($cell['type'] === 'break') {
                for ($next = $i + 1; $next < count($days); $next++) {
                    $nextCell = $row['days'][$days[$next]];
                    if ($nextCell['type'] === 'break' && $nextCell['data']->nama_istirahat === $cell['data']->nama_istirahat && $nextCell['data']->jam_mulai === $cell['data']->jam_mulai) $colspan++; else break;
                }
            }
            $i += $colspan - 1;
        @endphp
        <td @if(($cell['rowspan'] ?? 1) > 1) rowspan="{{ $cell['rowspan'] }}" @endif @if($colspan > 1) colspan="{{ $colspan }}" @endif @class(['border border-slate-300 px-2 py-2 text-center align-middle', 'bg-amber-50 font-bold text-amber-800' => $cell['type'] === 'break'])>
            @if($cell['type'] === 'break'){{ $cell['data']->nama_istirahat }}@elseif($cell['type'] === 'lesson')@foreach($cell['data'] as $lesson)<p class="font-extrabold text-slate-950">{{ $lesson->mataPelajaran->nama_mapel }}</p><p class="mt-0.5 text-[9px] text-slate-600 print:text-[6.5pt]">{{ $lesson->guru->nama_lengkap ?? 'Belum ada guru' }}</p>@endforeach @else &nbsp; @endif
        </td>
    @endfor</tr>
@endforeach
</tbody></table>
@endsection
