<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/jadwal-pelajaran/print.css') }}">
</head>
<body data-auto-print="{{ !isset($preview) || !$preview ? '1' : '0' }}">
    @php
        $jenjangClass = str_replace(' ', '_', $kelas->jenjang);
        $days = $scheduleGrid['days'];
        $rows = $scheduleGrid['rows'];

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
                <td class="text-right">Wali Kelas: <strong>{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '-' }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Schedule Table --}}
    <div class="schedule-scroll">
    <table class="schedule-table style-{{ $jenjangClass }}">
        <thead>
            <tr>
                <th class="col-jam">JAM</th>
                @foreach($days as $day)
                    <th>{{ strtoupper($day) }}</th>
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
                                        <div class="lesson-mapel">{{ $lesson->mataPelajaran->nama_mapel }}</div>
                                        <div class="lesson-guru">{{ $lesson->guru ? $lesson->guru->nama_lengkap : '(-)' }}</div>
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
            <div class="signature-spacer"></div>
            <p class="signature-name">{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '.............................' }}</p>
        </div>
    </div>

    @if(isset($preview) && $preview)
    <div class="no-print preview-toolbar">
        <button type="button" class="preview-btn preview-btn-print" data-print-page><i class="fas fa-print"></i> Cetak</button>
        <button type="button" class="preview-btn preview-btn-close" data-close-page><i class="fas fa-times"></i> Tutup</button>
    </div>
    @endif
    <script src="{{ asset('js/admin/jadwal-pelajaran/print.js') }}"></script>
</body>
</html>
