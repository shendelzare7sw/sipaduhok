<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Siswa {{ $kelas ? '- Kelas ' . $kelas->nama_kelas : '' }}</title>
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
        .title p { font-size: 11pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #000; padding: 8px 10px; text-align: left; }
        table th { background: #f0f0f0; text-align: center; font-weight: bold; }
        table td.center { text-align: center; }
        .summary { margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; }
        .summary h4 { font-size: 11pt; margin-bottom: 10px; }
        .summary-grid { display: flex; gap: 30px; }
        .summary-item .label { font-size: 9pt; color: #666; }
        .summary-item .value { font-size: 14pt; font-weight: bold; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
        .footer-right { text-align: center; }
        .signature-line { margin-top: 60px; border-bottom: 1px solid #000; width: 200px; margin: 60px auto 0; }
        .print-date { font-size: 10pt; color: #666; margin-top: 30px; }
        .print-button { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; }
        .back-button { position: fixed; top: 20px; right: 130px; padding: 12px 24px; background: #6b7280; color: white; border: none; border-radius: 8px; text-decoration: none; }
        @media print { .no-print { display: none !important; } .container { padding: 0; } }
    </style>
</head>
<body>
    <a href="{{ route('admin.manajemen-siswa.index') }}" class="back-button no-print">← Kembali</a>
    <button onclick="window.print()" class="print-button no-print"><i class="fas fa-print"></i> Cetak</button>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR SISWA</h3>
            <p>
                @if($kelas)
                    Kelas: {{ $kelas->nama_kelas }} | Jenjang: {{ $kelas->jenjang }}
                @elseif($cabang)
                    Cabang: {{ $cabang->nama_cabang }}
                @else
                    Semua Siswa
                @endif
                @if($sortBy == 'kelas')
                    | Urut: Per Kelas
                @elseif($sortBy == 'cabang')
                    | Urut: Per Cabang
                @else
                    | Urut: Abjad
                @endif
            </p>
        </div>

        @if($siswaList->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th style="width: 40px;">JK</th>
                        <th>Tempat, Tgl Lahir</th>
                        @if(!$kelas)
                        <th>Kelas</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; $currentKelas = ''; @endphp
                    @foreach($siswaList as $siswa)
                        @if($sortBy == 'kelas' && !$kelas && $currentKelas !== ($siswa->kelas->nama_kelas ?? 'Tanpa Kelas'))
                            @php $currentKelas = $siswa->kelas->nama_kelas ?? 'Tanpa Kelas'; @endphp
                            <tr style="background: #e5e7eb;">
                                <td colspan="{{ $kelas ? 6 : 7 }}" style="font-weight: bold;">
                                    {{ $currentKelas }} {{ $siswa->kelas ? '(' . $siswa->kelas->jenjang . ')' : '' }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td>{{ $siswa->nisn }}</td>
                            <td>{{ $siswa->nis ?? '-' }}</td>
                            <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
                            <td class="center">{{ $siswa->jenis_kelamin }}</td>
                            <td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }}</td>
                            @if(!$kelas)
                            <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <h4>Ringkasan:</h4>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="label">Total Siswa</div>
                        <div class="value">{{ $siswaList->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Laki-laki</div>
                        <div class="value">{{ $siswaList->where('jenis_kelamin', 'L')->count() }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">Perempuan</div>
                        <div class="value">{{ $siswaList->where('jenis_kelamin', 'P')->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p style="text-align: center; padding: 40px; color: #666;">Tidak ada data siswa.</p>
        @endif

        <div class="footer">
            <div class="footer-left">
                <p class="print-date">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
            </div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                @if($kelas && $kelas->waliKelas)
                    <p>Wali Kelas {{ $kelas->nama_kelas }}</p>
                    <div class="signature-line"></div>
                    <p><strong>{{ $kelas->waliKelas->nama_lengkap }}</strong></p>
                @else
                    <p>Kepala PKBM House of Knowledge</p>
                    <div class="signature-line"></div>
                    <p><strong>(_________________________)</strong></p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
