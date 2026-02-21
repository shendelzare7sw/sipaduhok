<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Tagihan per Kelas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            padding: 15mm;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16pt;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14pt;
            font-weight: normal;
        }
        .header p {
            font-size: 10pt;
            color: #333;
        }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h3 {
            font-size: 14pt;
            text-decoration: underline;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 50%;
        }
        .info td {
            padding: 3px 10px 3px 0;
        }
        .info td:first-child {
            width: 120px;
        }
        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .rekap-table th, .rekap-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        .rekap-table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .rekap-table .text-right {
            text-align: right;
        }
        .rekap-table .text-center {
            text-align: center;
        }
        .rekap-table tfoot td {
            font-weight: bold;
            background: #f9f9f9;
        }
        .summary {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 15px;
            width: 50%;
            margin-left: auto;
        }
        .summary table {
            width: 100%;
        }
        .summary td {
            padding: 5px 0;
        }
        .summary td:last-child {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .footer .sign {
            width: 200px;
            margin-left: auto;
            text-align: center;
        }
        .footer .sign-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        @media print {
            body {
                padding: 10mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    @include('partials.print-header', ['cabang' => $cabang ?? null])

    <div class="title">
        <h3>REKAP TAGIHAN PER KELAS</h3>
        <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Jumlah Kelas</td>
                <td>: {{ $kelasList->count() }} kelas</td>
            </tr>
            <tr>
                <td>Jumlah Siswa</td>
                <td>: {{ $kelasList->sum('total_siswa') }} siswa</td>
            </tr>
        </table>
    </div>

    <table class="rekap-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th>Kelas</th>
                <th class="text-center" style="width: 70px;">Jenjang</th>
                <th class="text-center" style="width: 60px;">Siswa</th>
                <th class="text-right" style="width: 110px;">Total Tagihan</th>
                <th class="text-right" style="width: 110px;">Terbayar</th>
                <th class="text-right" style="width: 110px;">Sisa</th>
                <th class="text-center" style="width: 50px;">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kelasList as $index => $kelas)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $kelas->nama_kelas }}</td>
                    <td class="text-center">{{ $kelas->jenjang }}</td>
                    <td class="text-center">{{ $kelas->total_siswa }}</td>
                    <td class="text-right">Rp {{ number_format($kelas->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($kelas->total_bayar, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($kelas->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $kelas->persentase }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data kelas</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">GRAND TOTAL:</td>
                <td class="text-center">{{ $kelasList->sum('total_siswa') }}</td>
                <td class="text-right">Rp {{ number_format($grandTotal['tagihan'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandTotal['bayar'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandTotal['sisa'], 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td><strong>Ringkasan</strong></td>
                <td></td>
            </tr>
            <tr>
                <td>Total Tagihan</td>
                <td>Rp {{ number_format($grandTotal['tagihan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Terbayar</td>
                <td>Rp {{ number_format($grandTotal['bayar'], 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td><strong>Total Sisa</strong></td>
                <td><strong>Rp {{ number_format($grandTotal['sisa'], 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="sign">
            <p>Tangerang Selatan, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Bendahara</p>
            <div class="sign-line">
                ( ................................ )
            </div>
        </div>
    </div>
</body>
</html>