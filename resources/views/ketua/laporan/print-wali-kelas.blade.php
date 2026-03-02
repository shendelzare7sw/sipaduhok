<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Daftar Wali Kelas - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
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
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10pt; }
        table th, table td { border: 1px solid #000; padding: 5px 8px; text-align: left; }
        table th { background: #f0f0f0; text-align: center; font-weight: bold; }
        table td.center { text-align: center; }
        .group-header { background: #e5e7eb; font-weight: bold; }
        .summary { margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; font-size: 10pt; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10pt; }
        .footer-right { text-align: center; }
        .signature-line { margin-top: 50px; border-bottom: 1px solid #000; width: 180px; margin-left: auto; margin-right: auto; }
        .print-date { font-size: 9pt; color: #666; }
        .btn-actions { position: sticky; top: 0; z-index: 100; background: #f8f9fa; padding: 10px 15px; display: flex; justify-content: flex-end; gap: 8px; border-bottom: 1px solid #e5e7eb; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; }
        .btn-print { background: #f59e0b; color: white; }
        .btn-back { background: #6b7280; color: white; }
        @media (max-width: 768px) { body { font-size: 9pt; } .container { padding: 5mm; } table { font-size: 8pt; } table th, table td { padding: 3px 5px; } .header h1 { font-size: 12pt; } .header h2 { font-size: 10pt; } .btn-actions { padding: 8px 10px; } .btn { padding: 8px 14px; font-size: 12px; } .footer { flex-direction: column; gap: 15px; } } @media print { .no-print { display: none !important; } .container { padding: 0; } }
    </style>
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route(auth()->user()->role === 'admin' ? 'admin.laporan.index' : 'ketua.laporan.index') }}" class="btn btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="btn btn-print"><i class="bi bi-printer"></i> Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR WALI KELAS</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua' }}</p>
        </div>

        @if($kelasList->count() > 0)
            @php $currentJenjang = ''; @endphp
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kelas</th>
                        <th>Jenjang</th>
                        <th>Wali Kelas</th>
                        <th>NIP</th>
                        <th>Telepon</th>
                        <th style="width: 60px;">Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($kelasList as $kelas)
                        @if($currentJenjang !== $kelas->jenjang)
                            @php $currentJenjang = $kelas->jenjang; @endphp
                            <tr class="group-header">
                                <td colspan="7">Jenjang {{ $currentJenjang }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                            <td class="center">{{ $kelas->jenjang }}</td>
                            <td><strong>{{ $kelas->waliKelas->nama_lengkap ?? '-' }}</strong></td>
                            <td>{{ $kelas->waliKelas->nip ?? '-' }}</td>
                            <td>{{ $kelas->waliKelas->telepon ?? '-' }}</td>
                            <td class="center">{{ $kelas->siswa_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <strong>Ringkasan:</strong>
                Total Kelas dengan Wali: {{ $kelasList->count() }} |
                Total Siswa: {{ $kelasList->sum('siswa_count') }}
            </div>
        @else
            <p style="text-align: center; padding: 30px;">Tidak ada data wali kelas.</p>
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
