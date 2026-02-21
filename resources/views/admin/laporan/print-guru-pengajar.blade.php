<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Guru Pengajar - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.4; }
        .container { max-width: 210mm; margin: 0 auto; padding: 10mm; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 15px; }
        .header h1 { font-size: 14pt; font-weight: bold; margin-bottom: 3px; }
        .header h2 { font-size: 12pt; margin-bottom: 8px; }
        .header p { font-size: 9pt; color: #333; }
        .title { text-align: center; margin: 15px 0; }
        .title h3 { font-size: 12pt; text-decoration: underline; margin-bottom: 5px; }
        .title p { font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10pt; }
        table th, table td { border: 1px solid #000; padding: 5px 8px; text-align: left; vertical-align: top; }
        table th { background: #f0f0f0; text-align: center; font-weight: bold; }
        table td.center { text-align: center; }
        .assignment-list { font-size: 9pt; padding-left: 15px; margin: 0; }
        .assignment-list li { margin-bottom: 2px; }
        .no-assignment { color: #666; font-style: italic; font-size: 9pt; }
        .summary { margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; font-size: 10pt; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10pt; }
        .footer-right { text-align: center; }
        .signature-line { margin-top: 50px; border-bottom: 1px solid #000; width: 180px; margin-left: auto; margin-right: auto; }
        .print-date { font-size: 9pt; color: #666; }
        .btn-actions { position: fixed; top: 15px; right: 15px; display: flex; gap: 8px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; }
        .btn-print { background: #14b8a6; color: white; }
        .btn-back { background: #6b7280; color: white; }
        @media print { .no-print { display: none !important; } .container { padding: 0; } }
    </style>
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR GURU PENGAJAR</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua' }}</p>
        </div>

        @if($guruList->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
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
                                        <li>{{ $assignment->kelas->nama_kelas ?? '-' }} - {{ $assignment->mataPelajaran->nama_mapel ?? '-' }}</li>
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
                <strong>Ringkasan:</strong>
                Total Guru: {{ $guruList->count() }} |
                Total Penugasan: {{ $guruList->sum(fn($g) => $g->guruKelas->count()) }}
            </div>
        @else
            <p style="text-align: center; padding: 30px;">Tidak ada data guru pengajar.</p>
        @endif

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
