<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="application/vnd.ms-excel; charset=utf-8">
    <title>Jadwal {{ $kelas->nama_kelas }}</title>
</head>
<body>
    @php
        $days = $scheduleGrid['days'];
        $rows = $scheduleGrid['rows'];
        // Excel headers styling
        $headerStyle = 'background-color: #f0f0f0; font-weight: bold; text-align: center; border: 1px solid #000000;';
        $cellStyle = 'border: 1px solid #000000; vertical-align: top;';
    @endphp

    <table>
        <tr>
            <td colspan="{{ count($days) + 1 }}" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px;">
                JADWAL PELAJARAN
            </td>
        </tr>
        <tr>
            <td colspan="{{ count($days) + 1 }}" style="font-size: 12px; font-weight: bold; text-align: center;">
                {{ $kelas->cabang->nama_cabang }} - T.A. {{ $currentTahunAjaran->nama_tahun_ajaran }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ count($days) + 1 }}" style="height: 10px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Kelas: {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})</td>
            <td colspan="{{ count($days) - 1 }}" style="text-align: right; font-weight: bold;">
                Wali Kelas: {{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="{{ count($days) + 1 }}" style="height: 10px;"></td>
        </tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th style="{{ $headerStyle }} width: 100px;">JAM</th>
                @foreach($days as $day)
                    <th style="{{ $headerStyle }} width: 200px;">{{ strtoupper($day) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    {{-- Time Column --}}
                    @php
                        $startTimeStr = $row['time_start'];
                        $endTimeLabel = '...'; 
                        
                         foreach ($days as $d) {
                            if (isset($row['days'][$d]['data'])) {
                                $dataItems = is_array($row['days'][$d]['data']) ? $row['days'][$d]['data'] : [$row['days'][$d]['data']];
                                foreach($dataItems as $di) {
                                    if (is_object($di)) {
                                            $et = isset($di->jam_selesai) ? $di->jam_selesai : null;
                                            if ($et instanceof \Carbon\Carbon) {
                                                $etStr = $et->format('H:i');
                                            } else {
                                                $etStr = substr($et, 0, 5);
                                            }
                                            if ($etStr > $endTimeLabel || $endTimeLabel == '...') $endTimeLabel = $etStr;
                                    }
                                }
                            }
                        }
                    @endphp
                    <td style="{{ $cellStyle }} text-align: center; font-weight: bold;">
                        {{ $startTimeStr }} - {{ $endTimeLabel }}
                    </td>

                    {{-- Day Cells --}}
                    @for($i = 0; $i < count($days); $i++)
                        @php 
                            $day = $days[$i];
                            $cell = $row['days'][$day]; 
                        @endphp

                        @if($cell['type'] == 'taken')
                            <!-- Spanned -->
                        @elseif($cell['type'] == 'empty')
                            <td style="{{ $cellStyle }}"></td>
                        @else
                            @php
                                // Check horizontal merge (colspan) for Breaks
                                $colspan = 1;
                                if ($cell['type'] == 'break') {
                                    $breakName = $cell['data']->nama_istirahat;
                                    for ($j = $i + 1; $j < count($days); $j++) {
                                        $nextDay = $days[$j];
                                        $nextCell = $row['days'][$nextDay];
                                        if ($nextCell['type'] == 'break' && $nextCell['data']->nama_istirahat == $breakName && $nextCell['data']->jam_mulai == $cell['data']->jam_mulai) {
                                            $colspan++;
                                        } else {
                                            break;
                                        }
                                    }
                                }
                                $i += ($colspan - 1);
                                
                                $rowAttr = $cell['rowspan'] > 1 ? 'rowspan="'.$cell['rowspan'].'"' : '';
                                $colAttr = $colspan > 1 ? 'colspan="'.$colspan.'"' : '';
                                
                                $bgStyle = '';
                                if ($cell['type'] == 'break') {
                                    $bgStyle = 'background-color: #fff9c4; font-weight: bold; text-align: center; font-style: italic; vertical-align: middle;';
                                }
                            @endphp

                            <td {!! $rowAttr !!} {!! $colAttr !!} style="{{ $cellStyle }} {{ $bgStyle }}">
                                @if($cell['type'] == 'break')
                                    {{ $cell['data']->nama_istirahat }}
                                @else
                                    @foreach($cell['data'] as $lesson)
                                        <b>{{ $lesson->mataPelajaran->nama_mapel }}</b><br>
                                        <span style="font-size: 10px; font-style: italic;">{{ $lesson->guru ? $lesson->guru->nama_lengkap : '(-)' }}</span>
                                        @if(!$loop->last)<br><br>@endif
                                    @endforeach
                                @endif
                            </td>
                        @endif
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
