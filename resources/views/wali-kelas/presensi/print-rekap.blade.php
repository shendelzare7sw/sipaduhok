<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi - {{ $kelas->nama_kelas }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #165fac; padding-bottom: 20px; }
        .header h1 { color: #165fac; font-size: 24px; margin-bottom: 10px; }
        .info-box { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .info-box div { margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th { padding: 10px; background: #165fac; color: white; border: 1px solid #ccc; text-align: center; }
        table td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        .summary { margin-top: 30px; padding: 15px; background: #f0f9ff; border-radius: 8px; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .summary-item { text-align: center; }
        .summary-item strong { display: block; font-size: 24px; color: #165fac; margin-bottom: 5px; }
        @media print { body { padding: 10px; } @page { margin: 15mm; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP PRESENSI SISWA</h1>
        <h2>PKBM House of Knowledge</h2>
    </div>

    <div class="info-box">
        <div><strong>Kelas:</strong> {{ $kelas->nama_kelas }}</div>
        <div><strong>Periode:</strong> {{ \Carbon\Carbon::create($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY') }}</div>
        <div><strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap }}</div>
        <div><strong>Dicetak:</strong> {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">No</th>
                <th rowspan="2" style="width: 100px;">NIS</th>
                <th rowspan="2">Nama Siswa</th>
                <th colspan="4">Jumlah Kehadiran</th>
                <th rowspan="2" style="width: 80px;">Total</th>
            </tr>
            <tr>
                <th style="width: 60px; background: #10b981;">Hadir</th>
                <th style="width: 60px; background: #f59e0b;">Sakit</th>
                <th style="width: 60px; background: #3b82f6;">Izin</th>
                <th style="width: 60px; background: #ef4444;">Alpha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswaList as $index => $siswa)
                @php
                    $rekap = $rekapBulan[$siswa->id];
                    $total = $rekap['hadir'] + $rekap['sakit'] + $rekap['izin'] + $rekap['alpha'];
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td style="text-align: left;"><strong>{{ $siswa->nama_lengkap }}</strong></td>
                    <td style="background: #dcfce7;"><strong>{{ $rekap['hadir'] }}</strong></td>
                    <td style="background: #fef3c7;"><strong>{{ $rekap['sakit'] }}</strong></td>
                    <td style="background: #dbeafe;"><strong>{{ $rekap['izin'] }}</strong></td>
                    <td style="background: #fee2e2;"><strong>{{ $rekap['alpha'] }}</strong></td>
                    <td><strong>{{ $total }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3 style="margin-bottom: 15px; color: #165fac;">Ringkasan Presensi</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <strong style="color: #10b981;">{{ array_sum(array_column($rekapBulan, 'hadir')) }}</strong>
                <div>Total Hadir</div>
            </div>
            <div class="summary-item">
                <strong style="color: #f59e0b;">{{ array_sum(array_column($rekapBulan, 'sakit')) }}</strong>
                <div>Total Sakit</div>
            </div>
            <div class="summary-item">
                <strong style="color: #3b82f6;">{{ array_sum(array_column($rekapBulan, 'izin')) }}</strong>
                <div>Total Izin</div>
            </div>
            <div class="summary-item">
                <strong style="color: #ef4444;">{{ array_sum(array_column($rekapBulan, 'alpha')) }}</strong>
                <div>Total Alpha</div>
            </div>
        </div>
    </div>

    <div style="margin-top: 40px; text-align: right;">
        <div style="display: inline-block; text-align: center; min-width: 200px;">
            <div>Tangerang Selatan, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            <div>Wali Kelas</div>
            <div style="margin-top: 60px; border-top: 1px solid #333; padding-top: 5px;">
                {{ $kelas->waliKelas->nama_lengkap }}
            </div>
        </div>
    </div>

    <script>window.print();</script>
</body>
</html>