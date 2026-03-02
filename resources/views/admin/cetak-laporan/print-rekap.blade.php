<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Rekap Statistik - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.4; }
        .container { max-width: 100%; margin: 0 auto; padding: 10mm; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 15px; }
        .header h1 { font-size: 14pt; font-weight: bold; margin-bottom: 3px; }
        .header h2 { font-size: 12pt; margin-bottom: 8px; }
        .header p { font-size: 9pt; color: #333; }
        .title { text-align: center; margin: 15px 0; }
        .title h3 { font-size: 12pt; text-decoration: underline; margin-bottom: 5px; }
        .title p { font-size: 10pt; }
        .section-title { font-size: 11pt; font-weight: bold; margin: 20px 0 10px; padding-bottom: 5px; border-bottom: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10pt; }
        table th, table td { border: 1px solid #000; padding: 6px 10px; text-align: left; }
        table th { background: #f0f0f0; text-align: center; font-weight: bold; }
        table td.center { text-align: center; }
        table td.right { text-align: right; }
        .total-row { background: #e5e7eb; font-weight: bold; }
        .summary-box { background: #f0f9ff; border: 2px solid #3b82f6; border-radius: 8px; padding: 15px; margin: 20px 0; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .summary-item { text-align: center; }
        .summary-item .value { font-size: 20pt; font-weight: bold; color: #1e40af; }
        .summary-item .label { font-size: 9pt; color: #666; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10pt; }
        .footer-right { text-align: center; }
        .signature-line { margin-top: 50px; border-bottom: 1px solid #000; width: 180px; margin-left: auto; margin-right: auto; }
        .print-date { font-size: 9pt; color: #666; }
        .btn-actions { position: sticky; top: 0; z-index: 100; background: #f8f9fa; padding: 10px 15px; display: flex; justify-content: flex-end; gap: 8px; border-bottom: 1px solid #e5e7eb; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; }
        .btn-print { background: #ec4899; color: white; }
        .btn-back { background: #6b7280; color: white; }
        @media (max-width: 768px) { body { font-size: 9pt; } .container { padding: 5mm; } table { font-size: 8pt; } table th, table td { padding: 3px 5px; } .header h1 { font-size: 12pt; } .header h2 { font-size: 10pt; } .btn-actions { padding: 8px 10px; } .btn { padding: 8px 14px; font-size: 12px; } .footer { flex-direction: column; gap: 15px; } } @media print { .no-print { display: none !important; } .container { padding: 0; } }
    </style>
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route('admin.cetak-laporan.index') }}" class="btn btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="btn btn-print"><i class="bi bi-printer"></i> Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>REKAP STATISTIK SEKOLAH</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua' }}</p>
        </div>

        {{-- Summary Box --}}
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="value">{{ $summary['total_siswa'] }}</div>
                    <div class="label">Total Siswa Aktif</div>
                </div>
                <div class="summary-item">
                    <div class="value">{{ $summary['total_guru'] }}</div>
                    <div class="label">Total Tenaga Pendidik</div>
                </div>
                <div class="summary-item">
                    <div class="value">{{ $summary['total_kelas'] }}</div>
                    <div class="label">Total Kelas</div>
                </div>
            </div>
        </div>

        {{-- Rekap Per Cabang --}}
        <div class="section-title">📍 Rekap Per Cabang</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Cabang</th>
                    <th style="width: 80px;">Kelas</th>
                    <th style="width: 80px;">Siswa (L)</th>
                    <th style="width: 80px;">Siswa (P)</th>
                    <th style="width: 80px;">Total Siswa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cabangs as $index => $cabang)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $cabang->nama_cabang }}</strong></td>
                    <td class="center">{{ $cabang->total_kelas }}</td>
                    <td class="center">{{ $cabang->siswa_l }}</td>
                    <td class="center">{{ $cabang->siswa_p }}</td>
                    <td class="center"><strong>{{ $cabang->total_siswa }}</strong></td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" class="right">TOTAL</td>
                    <td class="center">{{ $cabangs->sum('total_kelas') }}</td>
                    <td class="center">{{ $cabangs->sum('siswa_l') }}</td>
                    <td class="center">{{ $cabangs->sum('siswa_p') }}</td>
                    <td class="center"><strong>{{ $cabangs->sum('total_siswa') }}</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- Rekap Per Jenjang --}}
        <div class="section-title"><i class="fas fa-books"></i> Rekap Per Jenjang</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Jenjang</th>
                    <th style="width: 80px;">Kelas</th>
                    <th style="width: 80px;">Siswa (L)</th>
                    <th style="width: 80px;">Siswa (P)</th>
                    <th style="width: 80px;">Total Siswa</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; $totalKelas = 0; $totalL = 0; $totalP = 0; $totalSiswa = 0; @endphp
                @foreach($jenjangStats as $jenjang => $stats)
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td><strong>{{ $jenjang }}</strong></td>
                    <td class="center">{{ $stats['total_kelas'] }}</td>
                    <td class="center">{{ $stats['siswa_l'] }}</td>
                    <td class="center">{{ $stats['siswa_p'] }}</td>
                    <td class="center"><strong>{{ $stats['total_siswa'] }}</strong></td>
                </tr>
                @php 
                    $totalKelas += $stats['total_kelas'];
                    $totalL += $stats['siswa_l'];
                    $totalP += $stats['siswa_p'];
                    $totalSiswa += $stats['total_siswa'];
                @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="2" class="right">TOTAL</td>
                    <td class="center">{{ $totalKelas }}</td>
                    <td class="center">{{ $totalL }}</td>
                    <td class="center">{{ $totalP }}</td>
                    <td class="center"><strong>{{ $totalSiswa }}</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- Komposisi Gender --}}
        <div class="section-title"><i class="fas fa-users"></i> Komposisi Gender Siswa</div>
        <table>
            <thead>
                <tr>
                    <th>Gender</th>
                    <th style="width: 100px;">Jumlah</th>
                    <th style="width: 100px;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Laki-laki</td>
                    <td class="center">{{ $summary['siswa_l'] }}</td>
                    <td class="center">{{ $summary['total_siswa'] > 0 ? round(($summary['siswa_l'] / $summary['total_siswa']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>Perempuan</td>
                    <td class="center">{{ $summary['siswa_p'] }}</td>
                    <td class="center">{{ $summary['total_siswa'] > 0 ? round(($summary['siswa_p'] / $summary['total_siswa']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr class="total-row">
                    <td class="right"><strong>TOTAL</strong></td>
                    <td class="center"><strong>{{ $summary['total_siswa'] }}</strong></td>
                    <td class="center"><strong>100%</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <div class="print-date">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                <p>Kepala PKBM House of Knowledge</p>
                <div class="signature-line"></div>
                <p><strong>(_________________________)</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
