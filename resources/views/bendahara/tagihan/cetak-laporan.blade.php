<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap Tagihan - {{ $selectedYear->nama_tahun_ajaran ?? '-' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            padding: 15mm;
        }
        .header { text-align: center; }
        .title {
            text-align: center;
            margin: 15px 0 10px;
        }
        .title h3 {
            font-size: 14pt;
            text-decoration: underline;
            margin-bottom: 4px;
        }
        .title p { font-size: 11pt; margin: 2px 0; }
        .info {
            margin-bottom: 15px;
            font-size: 10pt;
        }
        .info table { width: 55%; }
        .info td { padding: 2px 10px 2px 0; }
        .info td:first-child { width: 130px; }
        .tagihan-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .tagihan-table th, .tagihan-table td {
            border: 1px solid #000;
            padding: 5px 7px;
        }
        .tagihan-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .tagihan-table .text-right { text-align: right; }
        .tagihan-table .text-center { text-align: center; }
        .tagihan-table tfoot td {
            font-weight: bold;
            background: #f5f5f5;
        }
        .status-lunas { color: green; font-weight: bold; }
        .status-belum { color: red; }
        .status-kosong { color: #888; }
        .summary {
            margin-top: 15px;
            border: 1px solid #000;
            padding: 12px;
            width: 45%;
            margin-left: auto;
            font-size: 10pt;
        }
        .summary table { width: 100%; }
        .summary td { padding: 4px 0; }
        .summary td:last-child { text-align: right; font-weight: bold; }
        .summary .grand-total { border-top: 1px solid #000; padding-top: 6px; }
        .footer {
            margin-top: 35px;
            text-align: right;
        }
        .footer .sign {
            display: inline-block;
            width: 220px;
            text-align: center;
        }
        .footer .sign-line {
            margin-top: 55px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        @media print {
            body { padding: 10mm; }
            .no-print { display: none; }
        }
    </style>
    @include('partials.print-head')
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
        <h3>LAPORAN REKAP TAGIHAN SISWA</h3>
        <p>Tahun Ajaran: {{ $selectedYear->nama_tahun_ajaran ?? '-' }}</p>
        @if($selectedKelas)
            <p>Kelas: {{ $selectedKelas->nama_kelas }} ({{ $selectedKelas->jenjang }})</p>
        @else
            <p>Kelas: Semua Kelas</p>
        @endif
        @if(!empty($filters['search']))
            <p>Pencarian: "{{ $filters['search'] }}"</p>
        @endif
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Jumlah Siswa</td>
                <td>: {{ $siswaList->count() }} siswa</td>
            </tr>
            <tr>
                <td>Total Tagihan</td>
                <td>: Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Terbayar</td>
                <td>: Rp {{ number_format($grandTotalLunas, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Tunggakan</td>
                <td>: Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="table-wrapper">
    <table class="tagihan-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Nama Siswa</th>
                <th style="width: 100px;">NISN</th>
                <th style="width: 120px;">Kelas</th>
                <th style="width: 110px;">Cabang</th>
                <th class="text-right" style="width: 110px;">Total Tagihan</th>
                <th class="text-right" style="width: 110px;">Terbayar</th>
                <th class="text-right" style="width: 110px;">Sisa</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaList as $index => $siswa)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td class="text-center">{{ $siswa->nisn ?? '-' }}</td>
                    <td class="text-center">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ $siswa->cabang->nama_cabang ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($siswa->tagihan_lunas, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0)
                            <span class="status-lunas">LUNAS</span>
                        @elseif($siswa->total_tagihan == 0)
                            <span class="status-kosong">KOSONG</span>
                        @else
                            <span class="status-belum">BELUM</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        Tidak ada data siswa sesuai filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">GRAND TOTAL:</td>
                <td class="text-right">Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandTotalLunas, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td colspan="2"><strong>Ringkasan Tagihan</strong></td>
            </tr>
            <tr>
                <td>Jumlah Siswa</td>
                <td>{{ $siswaList->count() }} siswa</td>
            </tr>
            <tr>
                <td>Siswa Lunas</td>
                <td>{{ $siswaList->filter(fn($s) => $s->sisa_tagihan <= 0 && $s->total_tagihan > 0)->count() }} siswa</td>
            </tr>
            <tr>
                <td>Siswa Belum Lunas</td>
                <td>{{ $siswaList->filter(fn($s) => $s->sisa_tagihan > 0)->count() }} siswa</td>
            </tr>
            <tr class="grand-total">
                <td>Total Tagihan</td>
                <td>Rp {{ number_format($grandTotalTagihan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Terbayar</td>
                <td>Rp {{ number_format($grandTotalLunas, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Tunggakan</strong></td>
                <td><strong>Rp {{ number_format($grandTotalSisa, 0, ',', '.') }}</strong></td>
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

    <div style="margin-top: 20px; font-size: 9pt; color: #666;">
        <em>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</em>
    </div>
</body>
</html>
