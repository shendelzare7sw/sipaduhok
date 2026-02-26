<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan - {{ $siswa->nama_lengkap }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            padding: 20mm;
        }
        .header {
            text-align: center;
        }
        .title {
            text-align: center;
            margin: 20px 0;
        }
        .title h3 {
            font-size: 14pt;
            text-decoration: underline;
        }
        .info-siswa {
            margin-bottom: 20px;
        }
        .info-siswa table {
            width: 60%;
        }
        .info-siswa td {
            padding: 3px 10px 3px 0;
        }
        .info-siswa td:first-child {
            width: 140px;
        }
        .tagihan-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .tagihan-table th, .tagihan-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
        }
        .tagihan-table th {
            background: #f0f0f0;
        }
        .tagihan-table .text-right {
            text-align: right;
        }
        .tagihan-table tfoot td {
            font-weight: bold;
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
            display: flex;
            justify-content: space-between;
        }
        .footer .sign {
            width: 45%;
            text-align: center;
        }
        .footer .sign-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .status-lunas {
            color: green;
            font-weight: bold;
        }
        .status-belum {
            color: red;
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

    @include('partials.print-header', ['cabang' => $siswa->cabang ?? null])

    <div class="title">
        <h3>RINCIAN TAGIHAN SISWA</h3>
        <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</p>
    </div>

    <div class="info-siswa">
        <table>
            <tr>
                <td>Nama Siswa</td>
                <td>: <strong>{{ $siswa->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td>NISN</td>
                <td>: {{ $siswa->nisn }}</td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: {{ $siswa->kelas->nama_kelas ?? '-' }} ({{ $siswa->kelas->jenjang ?? '-' }})</td>
            </tr>
            <tr>
                <td>Cabang</td>
                <td>: {{ $siswa->cabang->nama_cabang ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table class="tagihan-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Jenis Tagihan</th>
                <th style="width: 150px;">Jumlah</th>
                <th style="width: 120px;">Jatuh Tempo</th>
                <th style="width: 100px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tagihan as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $jenisTagihan[$item->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $item->tanggal_jatuh_tempo ? $item->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;">
                        @if($item->status === 'sudah_bayar')
                            <span class="status-lunas">LUNAS</span>
                        @else
                            <span class="status-belum">Belum</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada tagihan</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right;">TOTAL TAGIHAN:</td>
                <td class="text-right">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Total Tagihan</td>
                <td>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Terbayar</td>
                <td>Rp {{ number_format($tagihanLunas, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 1px solid #000;">
                <td><strong>Sisa Tagihan</strong></td>
                <td><strong>Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div class="sign">
            <p>Orang Tua/Wali Siswa</p>
            <div class="sign-line">
                ( ................................ )
            </div>
        </div>
        <div class="sign">
            <p>Tangerang Selatan, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Bendahara</p>
            <div class="sign-line">
                ( ................................ )
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; font-size: 10pt; color: #666;">
        <p><em>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</em></p>
    </div>
</body>
</html>