<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Guru Pengajar - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5; }
        .container { max-width: 210mm; margin: 0 auto; padding: 15mm; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 16pt; font-weight: bold; margin-bottom: 5px; }
        .header h2 { font-size: 14pt; margin-bottom: 10px; }
        .header p { font-size: 10pt; color: #333; }
        .title { text-align: center; margin: 25px 0; }
        .title h3 { font-size: 14pt; text-decoration: underline; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #000; padding: 8px 10px; text-align: left; vertical-align: top; }
        table th { background: #f0f0f0; text-align: center; vertical-align: middle; }
        table td.center { text-align: center; }
        .assignment-list { font-size: 10pt; padding-left: 15px; margin: 0; }
        .assignment-list li { margin-bottom: 3px; }
        .no-assignment { color: #666; font-style: italic; font-size: 10pt; }
        .summary { margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; }
        .summary h4 { font-size: 11pt; margin-bottom: 10px; }
        .summary-grid { display: flex; gap: 30px; }
        .summary-item .label { font-size: 9pt; color: #666; }
        .summary-item .value { font-size: 14pt; font-weight: bold; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
        .footer-right { text-align: center; }
        .signature-line { margin-top: 60px; border-bottom: 1px solid #000; width: 200px; margin: 60px auto 0; }
        .print-date { font-size: 10pt; color: #666; margin-top: 30px; }
        .print-controls {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            display: flex; justify-content: flex-end; align-items: center;
            gap: 8px; padding: 10px 16px;
            background: rgba(255,255,255,0.97);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .print-button {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px; background: #14b8a6; color: white;
            border: none; border-radius: 6px; cursor: pointer;
            font-size: 13px; font-weight: 500; font-family: sans-serif;
        }
        .back-button {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px; background: #6b7280; color: white;
            border: none; border-radius: 6px; text-decoration: none;
            font-size: 13px; font-weight: 500; font-family: sans-serif;
        }
        @media print { .no-print { display: none !important; } .container { padding: 0; } }
        @media (max-width: 600px) {
            .print-controls { justify-content: center; flex-wrap: wrap; padding: 8px 10px; }
            .print-button, .back-button { font-size: 12px; padding: 7px 14px; }
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <a href="{{ route('waka.guru-pengajar.index') }}" class="back-button"><i class="fas fa-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="print-button"><i class="fas fa-print"></i> Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR GURU PENGAJAR</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun Ajaran' }}</p>
        </div>

        @if($guruList->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th>Telepon</th>
                        <th>Penugasan (Kelas - Mata Pelajaran)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guruList as $index => $guru)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td><strong>{{ $guru->nama_lengkap }}</strong></td>
                        <td>{{ $guru->nip ?? '-' }}</td>
                        <td>{{ $guru->telepon ?? '-' }}</td>
                        <td>
                            @if($guru->guruKelas->count() > 0)
                                <ul class="assignment-list">
                                    @foreach($guru->guruKelas as $assignment)
                                        <li>{{ $assignment->kelas->nama_kelas }} - {{ $assignment->mataPelajaran->nama_mapel }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="no-assignment">Belum ada penugasan</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <h4>Ringkasan:</h4>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="label">Total Guru</div>
                        <div class="value">{{ $guruList->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Total Penugasan</div>
                        <div class="value">{{ $guruList->sum(fn($g) => $g->guruKelas->count()) }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Guru dengan Penugasan</div>
                        <div class="value">{{ $guruList->filter(fn($g) => $g->guruKelas->count() > 0)->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p style="text-align: center; padding: 40px; color: #666;">Tidak ada data guru pengajar.</p>
        @endif

        <div class="footer">
            <div class="footer-left">
                <p class="print-date">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
            </div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                <p>Kepala PKBM House of Knowledge</p>
                <div class="signature-line"></div>
           