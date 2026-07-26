<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Kelas - {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua Tahun' }}</title>
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
        table th, table td { border: 1px solid #000; padding: 5px 8px; text-align: left; }
        table th { background: #f0f0f0; text-align: center; font-weight: bold; }
        table td.center { text-align: center; }
        .group-header { background: #e5e7eb; font-weight: bold; }
        .summary { margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; font-size: 10pt; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10pt; }
        .footer-right { text-align: center; }
        .signature-line { margin-top: 50px; border-bottom: 1px solid #000; width: 180px; margin-left: auto; margin-right: auto; }
        .print-date { font-size: 9pt; color: #666; }
        .btn-actions { position: fixed; top: 15px; right: 15px; display: flex; gap: 8px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; }
        .btn-print { background: #8b5cf6; color: white; }
        .btn-back { background: #6b7280; color: white; }
        @media print { .no-print { display: none !important; } .container { padding: 0; } }
    </style>
@include('partials.print-head')
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR KELAS</h3>
            <p>Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? 'Semua' }} @if($cabang) | Cabang: {{ $cabang->nama_cabang }} @endif</p>
        </div>

        @if($kelasList->count() > 0)
            @php $currentJenjang = ''; @endphp
            <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kode Kelas</th>
                        <th>Nama Kelas</th>
                        <th>Cabang</th>
                        <th>Wali Kelas</th>
                        <th style="width: 60px;">Siswa</th>
                        <th style="width: 60px;">Kuota</th>
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
                            <td>{{ $kelas->kode_kelas }}</td>
                            <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                            <td>{{ $kelas->cabang->nama_cabang ?? '-' }}</td>
                            <td>{{ $kelas->waliKelas->nama_lengkap ?? '-' }}</td>
                            <td class="center">{{ $kelas->siswa_count }}</td>
                            <td class="center">{{ $kelas->kuota_siswa }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="summary">
                <strong>Ringkasan:</strong>
                Total Kelas: {{ $kelasList->count() }} |
                Total Siswa: {{ $kelasList->sum('siswa_count') }} |
                Total Kuota: {{ $kelasList->sum('kuota_siswa') }}
            </div>
        @else
            <p style="text-align: center; padding: 30px;">Tidak ada data kelas.</p>
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
