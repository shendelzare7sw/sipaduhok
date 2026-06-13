<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran - {{ $namaBulan }} {{ $tahun }}</title>
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
        .pembayaran-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .pembayaran-table th, .pembayaran-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        .pembayaran-table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .pembayaran-table .text-right {
            text-align: right;
        }
        .pembayaran-table .text-center {
            text-align: center;
        }
        .pembayaran-table tfoot td {
            font-weight: bold;
            background: #f9f9f9;
        }
        .summary {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 15px;
            width: 40%;
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
        <h3>LAPORAN PEMBAYARAN BULANAN</h3>
        <p>Periode: {{ $namaBulan }} {{ $tahun }}</p>
        @if($kelas)
            <p>Kelas: {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})</p>
        @endif
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Total Transaksi</td>
                <td>: {{ $pembayaranList->count() }} transaksi</td>
            </tr>
            <tr>
                <td>Total Pembayaran</td>
                <td>: Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="pembayaran-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 100px;">Kode</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jenis Tagihan</th>
                <th class="text-right" style="width: 100px;">Jumlah</th>
                <th class="text-center" style="width: 70px;">Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayaranList as $index => $bayar)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                    <td style="font-size: 9pt;">{{ $bayar->kode_pembayaran }}</td>
                    <td>{{ $bayar->siswa->nama_lengkap ?? '-' }}</td>
                    <td>{{ $bayar->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>
                        @if($bayar->tagihan)
                            {{ ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan)) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $bayar->metode_pembayaran === 'transfer' ? 'Direct Transfer' : ucfirst($bayar->metode_pembayaran) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada transaksi pembayaran pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">TOTAL PEMBAYARAN:</td>
                <td class="text-right">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td><strong>Ringkasan Pembayaran</strong></td>
                <td></td>
            </tr>
            <tr>
                <td>Jumlah Transaksi</td>
                <td>{{ $pembayaranList->count() }}</td>
            </tr>
            <tr>
                <td>Total Pembayaran</td>
                <td>Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</td>
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
