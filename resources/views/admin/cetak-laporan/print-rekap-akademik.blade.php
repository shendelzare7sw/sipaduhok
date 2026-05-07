<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Rekap Akademik - {{ $tahunAjaran->nama_tahun_ajaran }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.4; color: #1a1a1a; }
        .container { max-width: 210mm; margin: 0 auto; padding: 15mm 12mm; }
        .title { text-align: center; margin: 12px 0 18px; }
        .title h3 { font-size: 14pt; font-weight: 800; }
        .title p { font-size: 10pt; color: #555; margin-top: 4px; }

        .stats-grid {
            display: grid; grid-template-columns: repeat(5, 1fr);
            gap: 8px; margin-bottom: 20px;
        }
        .stat-box {
            border: 1.5px solid #cbd5e1; border-radius: 6px;
            padding: 10px 8px; text-align: center;
        }
        .stat-box .label { font-size: 9pt; color: #475569; text-transform: uppercase; font-weight: 700; }
        .stat-box .value { font-size: 22pt; font-weight: 800; line-height: 1.1; margin-top: 4px; }
        .stat-box.naik .value { color: #16a34a; }
        .stat-box.tidak .value { color: #dc2626; }
        .stat-box.lulus .value { color: #4361ee; }
        .stat-box.disp .value { color: #d97706; }

        .section { margin-top: 16px; }
        .section h4 {
            font-size: 11pt; font-weight: 700;
            background: #f1f5f9; padding: 6px 10px;
            border-left: 4px solid #4361ee;
            margin-bottom: 6px;
        }
        .section.tidak h4 { border-left-color: #dc2626; }
        .section.lulus h4 { border-left-color: #16a34a; }
        .section.disp h4 { border-left-color: #d97706; }

        table { width: 100%; border-collapse: collapse; font-size: 9.5pt; }
        thead th {
            background: #e2e8f0; padding: 6px 8px;
            text-align: left; font-weight: 700; font-size: 9pt;
            border: 1px solid #cbd5e1;
        }
        tbody td {
            padding: 5px 8px; border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        tbody tr:nth-child(even) { background: #fafbfc; }
        .center { text-align: center; }
        .empty { padding: 18px; text-align: center; color: #94a3b8; font-style: italic; font-size: 10pt; }

        .btn-actions { padding: 12px; background: #f1f5f9; }
        .btn-actions a, .btn-actions button {
            display: inline-block; padding: 8px 14px; border-radius: 6px;
            font-size: 11pt; text-decoration: none; border: none; cursor: pointer; margin-right: 6px;
        }
        .btn-back { background: #6b7280; color: white; }
        .btn-print { background: #4361ee; color: white; }

        @media print {
            .no-print, .btn-actions { display: none !important; }
            body { background: white; }
            .container { padding: 0; }
            .stat-box, table, .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="btn-actions no-print">
        <a href="{{ route('admin.cetak-laporan.index') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="btn-print"><i class="bi bi-printer"></i> Cetak</button>
    </div>

    <div class="container">
        @include('partials.print-header', ['cabang' => $cabang ?? null])

        <div class="title">
            <h3>REKAP AKADEMIK PER TAHUN AJARAN</h3>
            <p>
                Tahun Ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>
                @if($cabang) | Cabang: <strong>{{ $cabang->nama_cabang }}</strong> @endif
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-box"><div class="label">Total Siswa</div><div class="value">{{ $stats['total'] }}</div></div>
            <div class="stat-box naik"><div class="label">Naik Kelas</div><div class="value">{{ $stats['naik'] }}</div></div>
            <div class="stat-box tidak"><div class="label">Tidak Naik</div><div class="value">{{ $stats['tidak_naik'] }}</div></div>
            <div class="stat-box lulus"><div class="label">Lulus</div><div class="value">{{ $stats['lulus'] }}</div></div>
            <div class="stat-box disp"><div class="label">Dispensasi</div><div class="value">{{ $stats['dispensasi'] }}</div></div>
        </div>

        @php
            $sectionMap = [
                'NAIK_KELAS' => ['Naik Kelas', ''],
                'NAIK_KELAS_TUNGGAKAN' => ['Naik Kelas (via Dispensasi/Tunggakan)', 'disp'],
                'TIDAK_NAIK_KELAS' => ['Tidak Naik Kelas', 'tidak'],
                'LULUS' => ['Lulus', 'lulus'],
                'LULUS_TUNGGAKAN' => ['Lulus (via Dispensasi/Tunggakan)', 'disp'],
            ];
        @endphp

        @foreach($sectionMap as $statusKey => [$label, $sectionClass])
            @php $rows = $byStatus->get($statusKey, collect()); @endphp
            <div class="section {{ $sectionClass }}">
                <h4>{{ $label }} ({{ $rows->count() }})</h4>
                @if($rows->isEmpty())
                    <div class="empty">Tidak ada siswa pada kategori ini.</div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th class="center" style="width: 30px;">No</th>
                                <th>Nama</th>
                                <th style="width: 90px;">NIS / NISN</th>
                                <th style="width: 130px;">Kelas Asal → Tujuan</th>
                                <th class="center" style="width: 60px;">% Tuntas</th>
                                <th class="center" style="width: 90px;">Pembayaran</th>
                                @if(in_array($statusKey, ['NAIK_KELAS_TUNGGAKAN', 'LULUS_TUNGGAKAN']))
                                    <th class="center" style="width: 60px;">Dispensasi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="center">{{ $loop->iteration }}</td>
                                    <td><strong>{{ $row->siswa?->nama_lengkap ?? '-' }}</strong></td>
                                    <td>{{ $row->siswa?->nis ?? '-' }}<br><small>{{ $row->siswa?->nisn ?? '' }}</small></td>
                                    <td>
                                        {{ $row->kelas_asal ?? '-' }}
                                        @if($row->kelas_tujuan) → {{ $row->kelas_tujuan }} @endif
                                    </td>
                                    <td class="center">{{ number_format((float) ($row->persentase_nilai_tuntas ?? 0), 1) }}%</td>
                                    <td class="center">{{ $row->status_pembayaran ?? '-' }}</td>
                                    @if(in_array($statusKey, ['NAIK_KELAS_TUNGGAKAN', 'LULUS_TUNGGAKAN']))
                                        <td class="center">{{ $row->izin_khusus_ketua ? '✓ Ya' : '-' }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach

        <div style="margin-top: 30px; font-size: 9pt; color: #666;">
            <p>Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
            <p>Sumber data: <code>status_naik_kelas_siswa</code> (snapshot per TA dari hasil promosi).</p>
        </div>
    </div>
</body>
</html>
