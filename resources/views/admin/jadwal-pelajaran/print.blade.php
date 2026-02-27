<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; padding: 20px; font-size: 11px; }
        
        @media print {
            body { padding: 0; margin: 10mm; }
            .no-print { display: none !important; }
            @page { size: landscape; margin: 10mm; }
        }

        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header-school { position: relative; display: flex; align-items: center; justify-content: center; padding: 0 90px; margin-bottom: 10px; }
        .school-logo-img { position: absolute; left: 0; top: 50%; transform: translateY(-50%); height: 70px; width: auto; }
        .school-name { font-size: 15px; font-weight: bold; margin-bottom: 2px; }
        .school-sub { font-size: 11px; margin-bottom: 2px; }
        .school-address { font-size: 10px; color: #555; }
        .header h1 { font-size: 18px; margin-bottom: 5px; text-transform: uppercase; }
        .header p { font-size: 12px; color: #444; }

        .kelas-info { margin-bottom: 15px; }
        .kelas-info table { width: 100%; border-collapse: collapse; }
        .kelas-info td { padding: 3px 0; font-size: 12px; }
        .kelas-info td:first-child { width: 120px; font-weight: bold; }

        .schedule-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        .schedule-table th, .schedule-table td { border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle; overflow: hidden; }
        .schedule-table th { background-color: #f0f0f0; font-weight: bold; height: 35px; }
        
        .col-jam { width: 10%; }
        
        /* Jenjang Colors */
        .style-SMA th { background-color: #90CAF9 !important; }
        .style-SMP th { background-color: #FFF59D !important; }
        .style-SD th { background-color: #A5D6A7 !important; }
        .style-TK th, .style-TKA th, .style-TKB th { background-color: #FFCC80 !important; }
        .style-KB th { background-color: #F48FB1 !important; }

        /* Break Colors */
        .break-SMA { background-color: #2196F3; color: white; font-weight: bold; }
        .break-SMP { background-color: #FFEB3B; font-weight: bold; }
        .break-SD { background-color: #81C784; font-weight: bold; }
        .break-TK, .break-TKA, .break-TKB { background-color: #FF9800; color: white; font-weight: bold; }
        .break-KB { background-color: #E91E63; color: white; font-weight: bold; }

        .footer { margin-top: 30px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px; page-break-inside: avoid; }
        .signature { width: 30%; text-align: center; margin-top: 20px; }
        .signature-line { margin-top: 60px; border-top: 1px solid #000; padding-top: 5px; }
    </style>
</head>
<body>
    @php
        $jenjangClass = str_replace(' ', '_', $kelas->jenjang);
        $days = $scheduleGrid['days'];
        $rows = $scheduleGrid['rows'];
        $colWidth = 90 / count($days);

        $cabangJadwal = $kelas->cabang ?? null;
        $namaSekolahJadwal = $cabangJadwal
            ? strtoupper(preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i', '', $cabangJadwal->nama_cabang))
            : 'PKBM HOUSE OF KNOWLEDGE';
        $alamatJadwal = $cabangJadwal
            ? ($cabangJadwal->alamat ?? 'Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan')
            : 'Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan';
    @endphp

    {{-- Header --}}
    <div class="header">
        <div class="header-school">
            <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="school-logo-img">
            <div>
                <div class="school-name">{{ $namaSekolahJadwal }}</div>
                <div class="school-sub">PUSAT KEGIATAN BELAJAR MASYARAKAT</div>
                <div class="school-address">{{ $alamatJadwal }}</div>
            </div>
        </div>
        <h1>Jadwal Pelajaran</h1>
        <p>Tahun Ajaran: {{ $currentTahunAjaran->nama_tahun_ajaran }}</p>
    </div>

    {{-- Kelas Info --}}
    <div class="kelas-info">
        <table>
            <tr>
                <td>Kelas</td>
                <td>: <strong>{{ $kelas->nama_kelas }}</strong> ({{ $kelas->jenjang }})</td>
                <td style="text-align: right;">Wali Kelas: <strong>{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '-' }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Schedule Table --}}
    <div style="overflow-x: auto;">
    <table class="schedule-table style-{{ $jenjangClass }}">
        <thead>
            <tr>
                <th class="col-jam">JAM</th>
                @foreach($days as $day)
                    <th style="width: {{ $colWidth }}%;">{{ strtoupper($day) }}</th>
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
                        // Simplified EndTime Calculation
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
                    <td><b>{{ $startTimeStr }} - {{ $endTimeLabel }}</b></td>

                    {{-- Day Cells --}}
                    @for($i = 0; $i < count($days); $i++)
                        @php 
                            $day = $days[$i];
                            $cell = $row['days'][$day]; 
                        @endphp

                        @if($cell['type'] == 'taken')
                            <!-- Spanned -->
                        @elseif($cell['type'] == 'empty')
                            <td></td>
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
                                $class = ($cell['type'] == 'break') ? 'break-' . $jenjangClass : '';
                            @endphp

                            <td {!! $rowAttr !!} {!! $colAttr !!} class="{{ $class }}">
                                @if($cell['type'] == 'break')
                                    {{ $cell['data']->nama_istirahat }}
                                @else
                                    @foreach($cell['data'] as $lesson)
                                        <div style="font-weight: bold;">{{ $lesson->mataPelajaran->nama_mapel }}</div>
                                        <div style="font-size: 10px; font-style: italic; margin-top: 2px;">{{ $lesson->guru ? $lesson->guru->nama_lengkap : '(-)' }}</div>
                                    @endforeach
                                @endif
                            </td>
                        @endif
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <p><strong>Kepala Sekolah</strong></p>
            <div class="signature-line">
                <p>(...........................)</p>
            </div>
        </div>
        <div class="signature">
            <p>Wali Kelas</p>
            <div style="height: 60px;"></div> {{-- Spacer for signature --}}
            <p style="text-decoration: underline; font-weight: bold;">{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '.............................' }}</p>
        </div>
    </div>

    @if(!isset($preview) || !$preview)
    <script>window.onload = function() { window.print(); }</script>
    @else
    <div class="no-print" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000; display: flex; justify-content: flex-end; align-items: center; gap: 8px; padding: 10px 16px; background: rgba(255,255,255,0.97); box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
        <button onclick="window.print()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:#3b82f6;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;"><i class="fas fa-print"></i> Cetak</button>
        <button onclick="window.close()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:#6b7280;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;"><i class="fas fa-times"></i> Tutup</button>
    </div>
    @endif
</body>
</html>
