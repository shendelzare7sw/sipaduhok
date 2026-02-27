<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mata Pelajaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.4; }
        .container { max-width: 210mm; margin: 0 auto; padding: 10mm; }
        .header { border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 15px; }
        .header h1 { font-size: 14pt; font-weight: bold; margin-bottom: 3px; }
        .header h2 { font-size: 12pt; font-weight: normal; margin-bottom: 5px; }
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
        .summary h4 { font-size: 10pt; margin-bottom: 8px; }
        .summary-grid { display: flex; flex-wrap: wrap; gap: 15px; }
        .summary-item .label { font-size: 9pt; color: #666; }
        .summary-item .value { font-size: 13pt; font-weight: bold; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px; font-size: 10pt; }
        .footer-right { text-align: center; }
        .signature-line { margin-top: 50px; border-bottom: 1px solid #000; width: 180px; margin-left: auto; margin-right: auto; }
        .print-date { font-size: 9pt; color: #666; }
        .table-wrapper { overflow-x: auto; }
        .btn-actions { position: fixed; top: 15px; right: 15px; display: flex; gap: 8px; z-index: 999; flex-wrap: wrap; max-width: calc(100vw - 30px); justify-content: flex-end; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .btn-print { background: #696cff; color: white; }
        .btn-back { background: #6b7280; color: white; }
        @media print { .no-print { display: none !important; } .container { padding: 0; } }
        @media (max-width: 575.98px) {
            .btn-actions { top: auto; bottom: 15px; }
        }
    </style>
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-back">&#8592; Kembali</a>
        <button onclick="window.print()" class="btn btn-print">&#128438; Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>DAFTAR MATA PELAJARAN</h3>
            <p>
                @if(!empty($jenjangFilter))
                    Jenjang: {{ implode(', ', $jenjangFilter) }}
                @else
                    Semua Jenjang
                @endif
            </p>
        </div>

        @if($mataPelajaranList->count() > 0)
            @php $currentJenjang = ''; $no = 1; @endphp
            <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th style="width: 80px;">Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th style="width: 55px;">Jenjang</th>
                        <th style="width: 65px;">Kelompok</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mataPelajaranList as $mapel)
                        @if($currentJenjang !== $mapel->jenjang)
                            @php $currentJenjang = $mapel->jenjang; @endphp
                            <tr class="group-header">
                                <td colspan="6">Jenjang: {{ $mapel->jenjang }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td class="center">{{ $mapel->kode_mapel ?? '-' }}</td>
                            <td><strong>{{ $mapel->nama_mapel }}</strong></td>
                            <td class="center">{{ $mapel->jenjang }}</td>
                            <td class="center">{{ $mapel->kelompok ? 'Kel. ' . $mapel->kelompok : '-' }}</td>
                            <td>{{ $mapel->deskripsi ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="summary">
                <h4>Ringkasan Per Jenjang:</h4>
                <div class="summary-grid">
                    @foreach($stats as $jenjang => $total)
                        <div class="summary-item">
                            <div class="label">{{ $jenjang }}</div>
                            <div class="value">{{ $total }}</div>
                        </div>
                    @endforeach
                    <div class="summary-item">
                        <div class="label"><strong>Total Ditampilkan</strong></div>
                        <div class="value">{{ $mataPelajaranList->count() }}</div>
                    </div>
                </div>
            </div>
        @else
            <p style="text-align: center; padding: 30px; color: #666;">Tidak ada data mata pelajaran ditemukan.</p>
        @endif

        <div class="footer">
            <div class="print-date">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            <div class="footer-right">
                <p>Tangerang Selatan, {{ now()->translatedFormat('d F Y') }}</p>
                <p>Kepala PKBM House of Knowledge</p>
                <div class="signature-line"></div>
                <p>(_________________________)</p>
            </div>
        </div>
    </div>
</body>
</html>
