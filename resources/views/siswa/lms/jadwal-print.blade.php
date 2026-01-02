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
            font-family: Arial, sans-serif;
            padding: 20px;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #000;
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header h2 {
            color: #333;
            font-size: 14px;
            font-weight: normal;
        }

        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }

        .info-box div {
            flex: 1;
        }

        .info-box strong {
            color: #000;
            font-weight: 600;
        }

        .day-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .day-header {
            background: #165fac;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px 5px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table thead {
            background: #e5e7eb;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        table td {
            padding: 12px;
            border: 1px solid #d1d5db;
            background: white;
        }

        table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        /* Highlight untuk Istirahat */
        table tbody tr.break-row {
            background: #fff9c4 !important;
        }

        table tbody tr.break-row td {
            font-weight: bold;
            color: #f57f17;
        }

        .no-schedule {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
            background: #f9fafb;
            border: 1px dashed #d1d5db;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            font-size: 11px;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-weight: 600;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #1565c0;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 9999;
            transition: all 0.3s;
        }

        .print-button:hover {
            background: #0d47a1;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 15mm;
            }

            body {
                padding: 10px;
            }

            /* Hide print button and any navigation elements */
            .no-print, .print-button {
                display: none !important;
            }

            /* Hide any layout elements */
            nav, header, aside, .navbar, .sidebar, .topbar, .footer-app,
            .app-menu, .layout-wrapper, .layout-container, .content-wrapper {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Tombol Print Manual -->
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Cetak / Simpan PDF
    </button>

    <div class="header">
        <h1>Jadwal Mata Pelajaran {{ strtoupper($kelas->jenjang) }}</h1>
        <h2>PKBM House of Knowledge - Tahun Ajaran {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</h2>
    </div>

    <div class="info-box">
        <div>
            <strong>Kelas:</strong> {{ $kelas->nama_kelas }}
        </div>
        <div>
            <strong>Jenjang:</strong> {{ strtoupper($kelas->jenjang) }}
        </div>
        <div>
            <strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap ?? '-' }}
        </div>
    </div>

    @foreach($hariList as $hari)
        <div class="day-section">
            <div class="day-header">{{ $hari }}</div>

            @if($jadwalPerHari[$hari]->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th style="width: 150px;">Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Kode</th>
                            <th>Guru Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalPerHari[$hari] as $index => $item)
                            @if($item['type'] === 'istirahat')
                                @php
                                    $istirahat = $item['data'];
                                @endphp
                                <tr class="break-row">
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td>
                                        {{ substr($istirahat->jam_mulai, 0, 5) }} -
                                        {{ substr($istirahat->jam_selesai, 0, 5) }}
                                    </td>
                                    <td>
                                        <strong>{{ $istirahat->nama_istirahat }}</strong>
                                    </td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @else
                                @php
                                    $jadwal = $item['data'];
                                @endphp
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </td>
                                    <td>
                                        <strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong>
                                    </td>
                                    <td>{{ $jadwal->mataPelajaran->kode_mapel }}</td>
                                    <td>{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-schedule">Tidak ada jadwal pelajaran</div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <div class="signature-box">
            <div>Tangerang Selatan, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            <div style="margin-top: 5px;">Wali Kelas</div>
            <div class="signature-line">{{ $kelas->waliKelas->nama_lengkap ?? '____________________' }}</div>
        </div>
    </div>

    <script>
        // Auto print setelah halaman selesai dimuat (opsional)
        // Dinonaktifkan agar user bisa melihat preview dulu
        // window.onload = function() {
        //     setTimeout(() => window.print(), 500);
        // }
    </script>
</body>
</html>
