<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/presensi/print-rekap.css') }}">
    <script src="{{ asset('js/wali-kelas/presensi/print-rekap.js') }}" defer></script>
</head>
<body>
    <div class="btn-actions no-print">
        <button type="button" class="btn btn-print" data-print-page>&#128438; Cetak</button>
        <button type="button" class="btn btn-close" data-close-window>Tutup</button>
    </div>

    @include('partials.print-header', ['cabang' => $cabang ?? null])

    <div style="text-align: center; margin-bottom: 15px;"><strong style="font-size: 12pt;">REKAP PRESENSI SISWA</strong></div>

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

</body>
</html>
