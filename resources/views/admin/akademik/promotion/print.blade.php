<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kenaikan Kelas - {{ $selectedYear->nama_tahun_ajaran }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.4; color: #000; }
        .container { max-width: 210mm; margin: 0 auto; padding: 10mm; }

        /* Header */
        .header { position: relative; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 15px; }
        .header img { position: absolute; left: 0; top: 50%; transform: translateY(-50%); height: 80px; width: auto; }
        .header-text { text-align: center; }
        .header-text h1 { font-size: 14pt; font-weight: bold; margin-bottom: 3px; }
        .header-text h2 { font-size: 12pt; font-weight: normal; margin-bottom: 5px; }
        .header-text p { font-size: 9pt; color: #333; margin: 0; }

        /* Title */
        .title { text-align: center; margin: 15px 0 10px; }
        .title h3 { font-size: 13pt; font-weight: bold; text-decoration: underline; margin-bottom: 4px; }
        .title p { font-size: 10pt; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin: 12px 0; }
        .stat-box { border: 1px solid #ccc; border-radius: 6px; padding: 8px; text-align: center; }
        .stat-box .stat-value { font-size: 18pt; font-weight: bold; }
        .stat-box .stat-label { font-size: 8pt; color: #555; }
        .stat-box.naik { border-color: #16a34a; background: #f0fdf4; }
        .stat-box.naik .stat-value { color: #16a34a; }
        .stat-box.lulus { border-color: #0891b2; background: #ecfeff; }
        .stat-box.lulus .stat-value { color: #0891b2; }
        .stat-box.dispensasi { border-color: #d97706; background: #fffbeb; }
        .stat-box.dispensasi .stat-value { color: #d97706; }
        .stat-box.lulus-disp { border-color: #2563eb; background: #eff6ff; }
        .stat-box.lulus-disp .stat-value { color: #2563eb; }
        .stat-box.tidak-naik { border-color: #dc2626; background: #fef2f2; }
        .stat-box.tidak-naik .stat-value { color: #dc2626; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        table th, table td { border: 1px solid #000; padding: 5px 8px; }
        table th { background: #e5e7eb; font-weight: bold; text-align: center; }
        table td.center { text-align: center; }
        .badge-naik { color: #16a34a; font-weight: bold; }
        .badge-lulus { color: #0891b2; font-weight: bold; }
        .badge-dispensasi { color: #d97706; font-weight: bold; }
        .badge-tidak-naik { color: #dc2626; font-weight: bold; }

        /* Footer */
        .footer { margin-top: 25px; display: flex; justify-content: space-between; font-size: 10pt; }
        .footer-right { text-align: center; width: 200px; }
        .signature-line { margin-top: 55px; border-bottom: 1px solid #000; }

        /* No-print buttons */
        .btn-actions { position: fixed; top: 15px; right: 15px; display: flex; gap: 8px; z-index: 999; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; text-decoration: none; font-family: sans-serif; }
        .btn-print { background: #7367f0; color: white; }
        .btn-back { background: #6b7280; color: white; }

        .print-meta { font-size: 9pt; color: #666; margin-bottom: 8px; }

        @media print {
            .no-print { display: none !important; }
            .container { padding: 5mm; }
        }
    </style>
</head>
<body>

    <div class="btn-actions no-print">
        <a href="javascript:history.back()" class="btn btn-back">&#8592; Kembali</a>
        <button onclick="window.print()" class="btn btn-print">&#128438; Cetak</button>
    </div>

    <div class="container">
        {{-- Header --}}
        @php
            $namaSekolah = $cabang ? preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i', '', $cabang->nama_cabang) : 'PKBM HOUSE OF KNOWLEDGE';
            $alamat = $cabang ? ($cabang->alamat ?? 'Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan') : 'Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan';
            $telepon = $cabang ? ($cabang->telepon ?? '021-7412345') : '021-7412345';
        @endphp
        <div class="header">
            <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK">
            <div class="header-text">
                <h1>{{ $namaSekolah }}</h1>
                <h2>PUSAT KEGIATAN BELAJAR MASYARAKAT</h2>
                <p>{{ $alamat }}</p>
                <p>Telp: {{ $telepon }} | Email: info@hok.sch.id</p>
            </div>
        </div>

        {{-- Title --}}
        <div class="title">
            <h3>LAPORAN KENAIKAN KELAS</h3>
            <p>Tahun Ajaran: {{ $selectedYear->nama_tahun_ajaran }}</p>
            @if($filterStatus)
                <p>Filter Status: <strong>{{ str_replace('_', ' ', $filterStatus) }}</strong></p>
            @endif
        </div>

        <p class="print-meta">Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>

        {{-- Statistics --}}
        <div class="stats-grid">
            <div class="stat-box naik">
                <div class="stat-value">{{ $stats['NAIK_KELAS'] ?? 0 }}</div>
                <div class="stat-label">Naik Kelas</div>
            </div>
            <div class="stat-box lulus">
                <div class="stat-value">{{ $stats['LULUS'] ?? 0 }}</div>
                <div class="stat-label">Lulus</div>
            </div>
            <div class="stat-box dispensasi">
                <div class="stat-value">{{ $stats['NAIK_KELAS_TUNGGAKAN'] ?? 0 }}</div>
                <div class="stat-label">Naik (Dispensasi)</div>
            </div>
            <div class="stat-box lulus-disp">
                <div class="stat-value">{{ $stats['LULUS_TUNGGAKAN'] ?? 0 }}</div>
                <div class="stat-label">Lulus (Dispensasi)</div>
            </div>
            <div class="stat-box tidak-naik">
                <div class="stat-value">{{ $stats['TIDAK_NAIK_KELAS'] ?? 0 }}</div>
                <div class="stat-label">Tidak Naik</div>
            </div>
        </div>

        {{-- Student Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th>Kelas Asal</th>
                    <th>Kelas Tujuan</th>
                    <th>Status Bayar</th>
                    <th>Hasil Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $i => $data)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $data->nama_lengkap }}</td>
                    <td class="center">{{ $data->nis ?? '-' }}</td>
                    <td>{{ $data->kelas_asal }}</td>
                    <td>{{ $data->kelas_tujuan ?? '-' }}</td>
                    <td class="center">
                        @if($data->status_pembayaran == 'LUNAS')
                            Lunas
                        @else
                            Belum Lunas
                        @endif
                    </td>
                    <td class="center">
                        @php
                            $cls = match($data->status_kelulusan) {
                                'NAIK_KELAS' => 'badge-naik',
                                'LULUS' => 'badge-lulus',
                                'NAIK_KELAS_TUNGGAKAN', 'LULUS_TUNGGAKAN' => 'badge-dispensasi',
                                'TIDAK_NAIK_KELAS' => 'badge-tidak-naik',
                                default => ''
                            };
                            $label = match($data->status_kelulusan) {
                                'NAIK_KELAS' => 'Naik Kelas',
                                'LULUS' => 'Lulus',
                                'NAIK_KELAS_TUNGGAKAN' => 'Naik (Disp.)',
                                'LULUS_TUNGGAKAN' => 'Lulus (Disp.)',
                                'TIDAK_NAIK_KELAS' => 'Tidak Naik',
                                default => str_replace('_', ' ', $data->status_kelulusan)
                            };
                        @endphp
                        <span class="{{ $cls }}">{{ $label }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="center">Belum ada data kenaikan kelas yang dieksekusi.</td>
                </tr>
                @endforelse
            </tbody>
            @if($students->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold;">Total Siswa:</td>
                    <td class="center" style="font-weight: bold;">{{ $students->count() }}</td>
                </tr>
            </tfoot>
            @endif
        </table>

        {{-- Signature Footer --}}
        <div class="footer">
            <div></div>
            <div class="footer-right">
                <p>{{ now()->format('d F Y') }}</p>
                <br>
                <p>Kepala Sekolah,</p>
                <div class="signature-line"></div>
                <p>(_________________________)</p>
            </div>
        </div>
    </div>

</body>
</html>
