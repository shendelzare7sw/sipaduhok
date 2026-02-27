<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kelas - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
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
            color: #000;
            background: #fff;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
        }
        
        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 10pt;
            color: #333;
        }
        
        /* Title */
        .title {
            text-align: center;
            margin: 25px 0;
        }
        
        .title h3 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        
        .title p {
            font-size: 11pt;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th,
        table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }
        
        table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        table td.center {
            text-align: center;
        }
        
        table tr:nth-child(even) {
            background: #fafafa;
        }
        
        /* Summary */
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        
        .summary h4 {
            font-size: 11pt;
            margin-bottom: 10px;
        }
        
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .summary-item {
            flex: 1;
            min-width: 120px;
        }
        
        .summary-item .label {
            font-size: 9pt;
            color: #666;
        }
        
        .summary-item .value {
            font-size: 14pt;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .footer-left,
        .footer-right {
            width: 45%;
        }
        
        .footer-right {
            text-align: center;
        }
        
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .print-date {
            font-size: 10pt;
            color: #666;
            margin-top: 30px;
            text-align: right;
        }
        
        /* Print styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .container {
                padding: 0;
            }
        }
        
        /* Print button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .print-button:hover {
            background: #2563eb;
        }

        .back-button {
            position: fixed;
            top: 20px;
            right: 120px;
            padding: 10px 20px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .back-button:hover {
            background: #4b5563;
        }

        /* Mobile: make table scrollable */
        .table-wrapper {
            overflow-x: auto;
        }

        @media (max-width: 575.98px) {
            .print-button {
                top: auto;
                bottom: 20px;
                right: 20px;
                font-size: 13px;
                padding: 8px 14px;
            }
            .back-button {
                top: auto;
                bottom: 20px;
                right: 110px;
                font-size: 13px;
                padding: 8px 14px;
            }
        }
    </style>
</head>
<body>
    <a href="{{ route('waka.kelas.index') }}" class="back-button no-print">
        ← Kembali
    </a>
    <button onclick="window.print()" class="print-button no-print">
        🖨️ Cetak
    </button>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR KELAS</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun Ajaran' }}</p>
        </div>

        @if($kelas->count() > 0)
            <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Kode Kelas</th>
                        <th>Nama Kelas</th>
                        <th>Jenjang</th>
                        <th>Cabang</th>
                        <th>Wali Kelas</th>
                        <th style="width: 60px;">Siswa</th>
                        <th style="width: 60px;">Kuota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelas as $index => $k)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $k->kode_kelas }}</td>
                        <td><strong>{{ $k->nama_kelas }}</strong></td>
                        <td class="center">{{ $k->jenjang }}</td>
                        <td>{{ $k->cabang->nama_cabang ?? '-' }}</td>
                        <td>{{ $k->waliKelas->nama_lengkap ?? '-' }}</td>
                        <td class="center">{{ $k->siswa_count }}</td>
                        <td class="center">{{ $k->kuota_siswa }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="summary">
                <h4>Ringkasan:</h4>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="label">Total Kelas</div>
                        <div class="value">{{ $kelas->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Total Siswa</div>
                        <div class="value">{{ $kelas->sum('siswa_count') }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Total Kuota</div>
                        <div class="value">{{ $kelas->sum('kuota_siswa') }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Kelas PAUD</div>
                        <div class="value">{{ $kelas->where('jenjang', 'PAUD')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Kelas SD</div>
                        <div class="value">{{ $kelas->where('jenjang', 'SD')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Kelas SMP</div>
                        <div class="value">{{ $kelas->where('jenjang', 'SMP')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Kelas SMA</div>
                        <div class="value">{{ $kelas->where('jenjang', 'SMA')->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p style="text-align: center; padding: 40px; color: #666;">
                Tidak ada data kelas untuk ditampilkan.
            </p>
        @endif

        <div class="footer">
            <div class="footer-left">
                <p class="print-date">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
            </div>
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
