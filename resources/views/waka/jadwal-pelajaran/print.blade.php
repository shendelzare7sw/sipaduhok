<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - {{ $kelas->nama_kelas }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .kelas-info {
            margin-bottom: 20px;
        }

        .kelas-info table {
            width: 100%;
            margin-bottom: 10px;
        }

        .kelas-info td {
            padding: 3px 0;
        }

        .kelas-info td:first-child {
            width: 150px;
            font-weight: bold;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .schedule-table th,
        .schedule-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .schedule-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        .schedule-table th.hari {
            width: 12%;
        }

        .schedule-item {
            margin-bottom: 10px;
            padding: 5px;
            background: #f9f9f9;
            border-left: 3px solid #3b82f6;
        }

        .schedule-item:last-child {
            margin-bottom: 0;
        }

        .mapel {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .guru {
            font-size: 11px;
            color: #333;
            margin-bottom: 2px;
        }

        .jam {
            font-size: 10px;
            color: #666;
            font-family: 'Courier New', monospace;
        }

        .empty-cell {
            text-align: center;
            color: #999;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: landscape;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>JADWAL PELAJARAN</h1>
        <h2>{{ $kelas->cabang->nama_cabang }}</h2>
        <p>{{ $currentTahunAjaran->nama_tahun_ajaran }}</p>
    </div>

    {{-- Kelas Info --}}
    <div class="kelas-info">
        <table>
            <tr>
                <td>Kelas</td>
                <td>: {{ $kelas->nama_kelas }}</td>
            </tr>
            <tr>
                <td>Jenjang</td>
                <td>: {{ $kelas->jenjang }}</td>
            </tr>
            @if($kelas->waliKelas)
            <tr>
                <td>Wali Kelas</td>
                <td>: {{ $kelas->waliKelas->nama_lengkap }}</td>
            </tr>
            @endif
            <tr>
                <td>Tahun Ajaran</td>
                <td>: {{ $currentTahunAjaran->nama_tahun_ajaran }}</td>
            </tr>
        </table>
    </div>

    {{-- Schedule Table --}}
    <table class="schedule-table">
        <thead>
            <tr>
                @foreach($hariList as $hari)
                    <th class="hari">{{ $hari }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($hariList as $hari)
                    <td>
                        @if($jadwalByHari[$hari]->count() > 0)
                            @foreach($jadwalByHari[$hari] as $jadwal)
                                <div class="schedule-item">
                                    <div class="mapel">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                    <div class="guru">
                                        {{ $jadwal->guru ? $jadwal->guru->nama_lengkap : 'Belum ditentukan' }}
                                    </div>
                                    <div class="jam">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-cell">-</div>
                        @endif
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    {{-- Footer with Signatures --}}
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
            <p><strong>{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '.............................' }}</strong></p>
            <div class="signature-line">
                <p>(...........................)</p>
            </div>
        </div>
    </div>

    @if(!isset($preview) || !$preview)
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
    @else
    {{-- Preview mode: Add print button --}}
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" style="padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="fas fa-print" style="margin-right: 8px;"></i>Cetak Sekarang
        </button>
        <button onclick="window.close()" style="padding: 12px 24px; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-left: 8px;">
            <i class="fas fa-times" style="margin-right: 8px;"></i>Tutup
        </button>
    </div>
    @endif
</body>
</html>
