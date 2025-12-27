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
            margin-bottom: 30px;
            border-bottom: 3px solid #165fac;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #165fac;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header h2 {
            color: #333;
            font-size: 18px;
            font-weight: normal;
        }
        
        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .info-box div {
            flex: 1;
        }
        
        .info-box strong {
            display: block;
            color: #165fac;
            margin-bottom: 5px;
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
        
        .no-schedule {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
            background: #f9fafb;
            border: 1px dashed #d1d5db;
        }
        
        .footer {
            margin-top: 40px;
            text-align: right;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .day-section {
                page-break-inside: avoid;
            }
            
            @page {
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>JADWAL PELAJARAN</h1>
        <h2>PKBM House of Knowledge</h2>
    </div>

    <div class="info-box">
        <div>
            <strong>Kelas:</strong>
            {{ $kelas->nama_kelas }}
        </div>
        <div>
            <strong>Jenjang:</strong>
            {{ strtoupper($kelas->jenjang) }}
        </div>
        <div>
            <strong>Tahun Ajaran:</strong>
            {{ $kelas->tahunAjaran->nama_tahun_ajaran }}
        </div>
        <div>
            <strong>Wali Kelas:</strong>
            {{ $kelas->waliKelas->nama_lengkap }}
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
                        @foreach($jadwalPerHari[$hari] as $index => $jadwal)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                </td>
                                <td><strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong></td>
                                <td>{{ $jadwal->mataPelajaran->kode_mapel }}</td>
                                <td>{{ $jadwal->guru->nama_lengkap }}</td>
                            </tr>
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
            <div>Wali Kelas</div>
            <div class="signature-line">{{ $kelas->waliKelas->nama_lengkap }}</div>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>