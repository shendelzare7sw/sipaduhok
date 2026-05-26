<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #165fac;
            padding-bottom: 20px;
        }

        .header-school {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 90px;
            margin-bottom: 12px;
        }

        .school-logo-img {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 70px;
            width: auto;
        }

        .school-name {
            font-size: 16px;
            font-weight: bold;
            color: #165fac;
            margin-bottom: 3px;
        }

        .school-sub {
            font-size: 12px;
            color: #555;
            margin-bottom: 3px;
        }

        .school-address {
            font-size: 11px;
            color: #777;
        }

        .header h1 {
            color: #165fac;
            font-size: 24px;
            margin-bottom: 0;
        }

        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-box div {
            flex: 1;
        }

        .info-box strong {
            display: block;
            color: #165fac;
            margin-bottom: 5px;
        }

        .day-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .day-header {
            background: #165fac;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px 5px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table thead {
            background: #e5e7eb;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        table td {
            padding: 12px;
            border: 1px solid #d1d5db;
            background: white;
        }

        table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        /* Highlight untuk Istirahat */
        table tbody tr.break-row {
            background: #fff9c4 !important;
        }

        table tbody tr.break-row td {
            font-weight: bold;
            color: #f57f17;
        }

        .no-schedule {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
            background: #f9fafb;
            border: 1px dashed #d1d5db;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .print-actions {
            position: sticky;
            top: 0;
            z-index: 100;
            background: white;
            padding: 10px 0;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 16px;
        }

        .print-button {
            padding: 10px 20px;
            background: #165fac;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .print-button:hover {
            background: #0f4a8a;
        }

        .back-button {
            padding: 10px 20px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-button:hover {
            background: #4b5563;
            color: white;
        }

        /* Mobile Responsive */
        @media (max-width: 576px) {
            body { padding: 10px; }
            .header { margin-bottom: 16px; padding-bottom: 12px; }
            .header h1 { font-size: 18px; }
            .header-school { padding: 0 0 0 60px; flex-direction: row; }
            .school-logo-img { height: 50px; }
            .school-name { font-size: 13px; }
            .school-sub { font-size: 10px; }
            .school-address { font-size: 9px; }
            .info-box { flex-direction: column; gap: 8px; padding: 10px; }
            .info-box div { flex: unset; }
            .day-header { font-size: 14px; padding: 8px 12px; }
            table th, table td { padding: 6px 4px; font-size: 11px; }
            table th:first-child { width: 30px !important; }
            table th:nth-child(2) { width: 70px !important; }
            .day-section { margin-bottom: 16px; }
            .footer { margin-top: 20px; }
            .signature-box { min-width: 150px; font-size: 12px; }
            .signature-line { margin-top: 40px; }
            .print-actions { flex-direction: row; gap: 6px; }
            .print-button, .back-button { padding: 8px 14px; font-size: 13px; flex: 1; justify-content: center; }
        }

        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            html, body { padding: 0; margin: 0; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            #printRoot { transform-origin: top left; }
            .day-section { page-break-inside: avoid; }
            .no-print, .print-actions { display: none !important; }
            nav, header, aside, .navbar, .sidebar, .topbar, .footer-app,
            .app-menu, .layout-wrapper, .layout-container, .content-wrapper {
                display: none !important;
            }
            table { page-break-inside: avoid; }
            thead { display: table-header-group; }
            .footer { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Tombol Print & Back -->
    <div class="print-actions no-print">
        <a href="javascript:history.back()" class="back-button">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <button class="print-button" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak / Simpan PDF
        </button>
    </div>

<div id="printRoot">
    <div class="header">
        @php
            $cabang = $kelas->cabang ?? null;
            $namaSekolah = $cabang
                ? strtoupper(preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i', '', $cabang->nama_cabang))
                : 'PKBM HOUSE OF KNOWLEDGE';
            $alamatCabang = $cabang
                ? ($cabang->alamat ?? 'Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan')
                : 'Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan';
        @endphp
        <div class="header-school">
            <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="school-logo-img">
            <div>
                <div class="school-name">{{ $namaSekolah }}</div>
                <div class="school-sub">PUSAT KEGIATAN BELAJAR MASYARAKAT</div>
                <div class="school-address">{{ $alamatCabang }}</div>
            </div>
        </div>
        <h1>JADWAL PELAJARAN</h1>
    </div>

    <div class="info-box">
        <div>
            <strong>Kelas:</strong>
            {{ $kelas->nama_kelas }}
        </div>
        <div>
            <strong>Jenjang:</strong>
            {{ strtoupper($kelas->jenjang) }}
        </div>
        <div>
            <strong>Tahun Ajaran:</strong>
            {{ $kelas->tahunAjaran->nama_tahun_ajaran }}
        </div>
        <div>
            <strong>Wali Kelas:</strong>
            {{ $kelas->waliKelas->nama_lengkap }}
        </div>
    </div>

    @foreach($hariList as $hari)
        <div class="day-section">
            <div class="day-header">{{ $hari }}</div>

            @if($jadwalPerHari[$hari]->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th style="width: 150px;">Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Kode</th>
                            <th>Guru Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalPerHari[$hari] as $index => $item)
                            @if($item['type'] === 'istirahat')
                                @php
                                    $istirahat = $item['data'];
                                @endphp
                                <tr class="break-row">
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td>
                                        {{ substr($istirahat->jam_mulai, 0, 5) }} -
                                        {{ substr($istirahat->jam_selesai, 0, 5) }}
                                    </td>
                                    <td>
                                        <strong>{{ $istirahat->nama_istirahat }}</strong>
                                    </td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @else
                                @php
                                    $jadwal = $item['data'];
                                @endphp
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </td>
                                    <td>
                                        <strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong>
                                    </td>
                                    <td>{{ $jadwal->mataPelajaran->kode_mapel }}</td>
                                    <td>{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-schedule">Tidak ada jadwal pelajaran</div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <div class="signature-box">
            <div>Tangerang Selatan, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            <div>Wali Kelas</div>
            <div class="signature-line">{{ $kelas->waliKelas->nama_lengkap }}</div>
        </div>
    </div>
</div>

    <script>
        // ── Auto fit-to-one-page A4 saat print (khusus jadwal: data sedikit, sebaiknya 1 hal) ──
        (function() {
            var MARGIN_MM = 10;
            // A4 landscape usable: (297 - 2*10) × (210 - 2*10) = 277mm × 190mm
            // 1mm ≈ 3.7795px at 96dpi
            var usableHeightPx = (210 - 2 * MARGIN_MM) * 3.7795;  // ≈ 718px
            var PRINT_SHRINK_FACTOR = 0.72; // print mode lebih kompak dari screen
            var MIN_APPLY = 0.65;
            var MAX_APPLY = 0.99;

            function injectScale() {
                var el = document.getElementById('printRoot');
                if (!el) return;
                var screenHeight = el.scrollHeight;
                if (!screenHeight) return;

                var estimatedPrintHeight = screenHeight * PRINT_SHRINK_FACTOR;
                var scale = usableHeightPx / estimatedPrintHeight;

                var existing = document.getElementById('jadwalPrintScale');
                if (existing) existing.parentNode.removeChild(existing);

                if (scale >= MAX_APPLY) return;
                if (scale < MIN_APPLY) return;

                var css = '@media print { #printRoot { ' +
                    'transform: scale(' + scale.toFixed(4) + ') !important; ' +
                    'transform-origin: top left !important; ' +
                    'width: ' + (100 / scale).toFixed(2) + '% !important; ' +
                    '} }';
                var style = document.createElement('style');
                style.id = 'jadwalPrintScale';
                style.textContent = css;
                document.head.appendChild(style);
            }

            if (document.readyState === 'complete') {
                injectScale();
            } else {
                window.addEventListener('load', injectScale);
            }
        })();
    </script>
</body>
</html>
