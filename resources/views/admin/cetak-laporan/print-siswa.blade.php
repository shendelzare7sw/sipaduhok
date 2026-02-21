<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Siswa {{ $kelas ? '- ' . $kelas->nama_kelas : '' }}</title>
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
        .summary-grid { display: flex; gap: 30px; }
        .summary-item .label { font-size: 9pt; color: #666; }
        .summary-item .value { font-size: 12pt; font-weight: bold; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 10pt; }
        .footer-right { text-align: center; }
        .signature-line { margin-top: 50px; border-bottom: 1px solid #000; width: 180px; margin-left: auto; margin-right: auto; }
        .print-date { font-size: 9pt; color: #666; }
        .btn-actions { position: fixed; top: 15px; right: 15px; display: flex; gap: 8px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; }
        .btn-print { background: #3b82f6; color: white; }
        .btn-back { background: #6b7280; color: white; }
        @media print { .no-print { display: none !important; } .container { padding: 0; } }
    </style>
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route('admin.cetak-laporan.index') }}" class="btn btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR SISWA</h3>
            <p>
                @if($kelas) Kelas: {{ $kelas->nama_kelas }} @endif
                @if($cabang) | Cabang: {{ $cabang->nama_cabang }} @endif
                @if($tahunAjaran) | Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran }} @endif
            </p>
        </div>

        @if($siswaList->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th style="width: 30px;">JK</th>
                        <th>Tempat, Tgl Lahir</th>
                        @if(!$kelas)<th>Kelas</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; $currentGroup = ''; @endphp
                    @foreach($siswaList as $siswa)
                        @if($sortBy == 'kelas' && !$kelas)
                            @php $groupName = $siswa->kelas->nama_kelas ?? 'Tanpa Kelas'; @endphp
                            @if($currentGroup !== $groupName)
                                @php $currentGroup = $groupName; @endphp
                                <tr class="group-header">
                                    <td colspan="{{ $kelas ? 6 : 7 }}">{{ $currentGroup }} {{ $siswa->kelas ? '(' . $siswa->kelas->jenjang . ')' : '' }}</td>
                                </tr>
                            @endif
                        @endif
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td>{{ $siswa->nisn }}</td>
                            <td>{{ $siswa->nis ?? '-' }}</td>
                            <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                            <td class="center">{{ $siswa->jenis_kelamin }}</td>
                            <td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }}</td>
                            @if(!$kelas)<td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>@endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <strong>Ringkasan:</strong>
                <div class="summary-grid">
                    <div class="summary-item"><span class="label">Total:</span> <span class="value">{{ $siswaList->count() }}</span></div>
                    <div class="summary-item"><span class="label">Laki-laki:</span> <span class="value">{{ $siswaList->where('jenis_kelamin', 'L')->count() }}</span></div>
                    <div class="summary-item"><span class="label">Perempuan:</span> <span class="value">{{ $siswaList->where('jenis_kelamin', 'P')->count() }}</span></div>
                </div>
            </div>
        @else
            <p style="text-align: center; padding: 30px;">Tidak ada data siswa.</p>
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
