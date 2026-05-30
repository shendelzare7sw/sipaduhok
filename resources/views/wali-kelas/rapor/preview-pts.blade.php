<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor {{ $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }} - {{ $rapor->siswa->nama_lengkap }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('partials.anti-screenshot')
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.32;
            padding: 0;
            background: #f5f5f5;
            overflow-x: hidden;
        }

        /* Frame untuk simulasi margin per-halaman saat print (thead/tfoot repeat per page) */
        .print-page-frame { width: 100%; border-collapse: collapse; }
        .print-page-frame > thead > tr > td,
        .print-page-frame > tbody > tr > td,
        .print-page-frame > tfoot > tr > td {
            border: 0;
            padding: 0;
        }

        .rapor-wrapper {
            width: 900px;
            margin: 0 auto;
            background: white;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            transform-origin: top left;
        }

        /* Watermark as background - More reliable for large images */
        .rapor-wrapper.with-watermark::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset('img/logo/hok-watermark.png') }}?v={{ filemtime(public_path('img/logo/hok-watermark.png')) }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 75%;
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .rapor-wrapper > * {
            position: relative;
            z-index: 1;
        }

        /* Header Section - Simple logo + address */
        .header {
            margin-bottom: 10px;
        }

        .header-logo {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 5px auto;
        }

        .header-address {
            font-size: 9pt;
            line-height: 1.35;
            color: #374151;
            text-align: center;
        }

        .header-separator {
            border-top: 3px solid #000;
            margin-top: 6px;
            margin-bottom: 10px;
        }

        /* Report Title */
        .report-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        /* Student Info (2-column) */
        .student-info {
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 16px;
            margin-bottom: 10px;
            font-size: 10pt;
        }

        .student-info .info-item {
            margin-bottom: 3px;
        }

        .student-info .info-label {
            display: inline-block;
            width: 110px;
            font-weight: normal;
        }

        .student-info .info-value {
            display: inline-block;
            font-weight: bold;
        }

        /* Tables - CRITICAL: border-collapse */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10pt;
            table-layout: fixed;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
            background-color: transparent;
        }

        table th {
            background-color: rgba(229, 231, 235, 0.7);
            font-weight: bold;
            font-size: 9pt;
        }

        /* Utilities */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Subscript for U1, U2 numbers */
        sub {
            font-size: 0.75em;
            vertical-align: sub;
        }

        /* Grade Table Specific */
        .grade-table th {
            vertical-align: middle;
            text-align: center;
        }

        .grade-table .tidak-tuntas {
            background-color: rgba(254, 226, 226, 0.7);
        }

        .grade-table tfoot td {
            font-weight: bold;
            background-color: rgba(243, 244, 246, 0.7);
        }

        /* Kegiatan Ekstra - Semi-transparent green */
        .bg-ekstra {
            background-color: rgba(212, 237, 218, 0.7);
        }

        /* Kehadiran - Semi-transparent blue */
        .bg-kehadiran {
            background-color: rgba(209, 236, 241, 0.7);
        }

        /* Print/Action bar */
        .print-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(4px);
            padding: 10px 20px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 16px;
        }
        .btn-print {
            padding: 9px 18px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .btn-print:hover { background: #1e40af; }
        .btn-back {
            padding: 9px 18px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back:hover { background: #4b5563; color: white; }

        .btn-zoom {
            padding: 7px 12px;
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            transition: all 0.2s;
        }
        .btn-zoom:hover { background: #e5e7eb; }
        .zoom-level {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            min-width: 45px;
            text-align: center;
            user-select: none;
        }
        .zoom-controls {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 2px 4px;
        }

        .print-bar,
        .print-bar * {
            box-sizing: border-box;
        }

        .print-bar .btn-back,
        .print-bar .btn-print,
        .print-bar .btn-zoom,
        .print-bar .zoom-controls {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        /* Mobile: handled by JS scale — no reflow needed */
        @media (max-width: 900px) {
            body { padding: 0; background: #e5e7eb; }
        }

        @media (max-width: 575.98px) {
            .print-bar {
                max-width: 100vw;
                overflow-x: auto;
                justify-content: flex-start;
                gap: 6px;
                padding: 8px max(8px, env(safe-area-inset-left)) 8px max(8px, env(safe-area-inset-left));
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .print-bar::-webkit-scrollbar {
                display: none;
            }

            .btn-back,
            .btn-print {
                padding: 8px 10px;
                font-size: 12px;
            }

            .btn-zoom {
                min-width: 32px;
                padding: 7px 9px;
            }

            .zoom-level {
                min-width: 38px;
                font-size: 12px;
            }

            .print-bar .bi-info-circle {
                display: none;
            }
        }

        /* Print Styles */
        @media print {
            /* @page margin = 0 → suppress browser-added header/footer.
               Per-page margin via thead/tfoot table yang repeat per halaman. */
            @page {
                margin: 0;
                size: A4 portrait;
            }

            html, body {
                margin: 0;
                padding: 0;
                background: white;
                font-size: 8.7pt;
                line-height: 1.18;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-page-frame > thead > tr > td.page-margin-top { height: 6mm; }
            .print-page-frame > tfoot > tr > td.page-margin-bottom { height: 6mm; }

            .rapor-wrapper {
                box-shadow: none;
                padding: 0 8mm !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                box-sizing: border-box !important;
                transform: none !important;
            }
            .rapor-wrapper * { box-sizing: border-box !important; }
            .rapor-wrapper table { width: 100% !important; max-width: 100% !important; }

            .header {
                margin-bottom: 2mm !important;
            }

            .header-logo {
                max-height: 24mm !important;
                width: auto !important;
                max-width: 100% !important;
                object-fit: contain !important;
                margin-bottom: 1mm !important;
            }

            .header-address {
                font-size: 7.3pt !important;
                line-height: 1.12 !important;
            }

            .header-separator {
                border-top-width: 2px !important;
                margin-top: 1.5mm !important;
                margin-bottom: 2.5mm !important;
            }

            .report-title {
                font-size: 10pt !important;
                margin-bottom: 2.5mm !important;
            }

            .student-info {
                gap: 12px !important;
                margin-bottom: 2.5mm !important;
                font-size: 8.2pt !important;
                line-height: 1.16 !important;
            }

            .student-info .info-item {
                margin-bottom: .8mm !important;
            }

            .student-info .info-label {
                width: 82px !important;
            }

            table {
                font-size: 7.8pt;
                margin-bottom: 2.5mm !important;
            }

            table th, table td {
                padding: 1.1mm 1.5mm !important;
            }

            table th {
                font-size: 7.6pt !important;
            }

            .rapor-wrapper table[style*="margin-top"] {
                margin-top: 2.5mm !important;
            }

            .grade-table tfoot td {
                padding: 1.1mm 1.5mm !important;
            }

            td[style*="padding: 10px"],
            th[style*="padding: 10px"],
            td[style*="padding: 8px"],
            th[style*="padding: 8px"],
            td[style*="padding: 5px"],
            th[style*="padding: 5px"] {
                padding: 1.2mm 1.5mm !important;
            }

            td[style*="height: 120px"] {
                height: 18mm !important;
            }

            div[style*="height: 80px"] {
                height: 14mm !important;
            }

            div[style*="margin-top: 30px"] {
                margin-top: 3mm !important;
                padding-right: 16mm !important;
            }

            .btn-print {
                display: none;
            }
            .print-bar {
                display: none;
            }

            /* Watermark: fixed ke halaman — selalu center A4 + repeat tiap halaman */
            .rapor-wrapper.with-watermark::before {
                content: '';
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                background-image: url('{{ asset('img/logo/hok-watermark.png') }}?v={{ filemtime(public_path('img/logo/hok-watermark.png')) }}');
                background-repeat: no-repeat;
                background-position: center center;
                background-size: 75%;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                opacity: 0.10 !important;
                display: block !important;
                z-index: 0;
                pointer-events: none;
            }

            /* Preserve preview colors in browser print preview. */
            table th,
            table td,
            .grade-table tfoot td,
            .grade-table .tidak-tuntas,
            .grade-table .tidak-tuntas td,
            .bg-ekstra,
            .bg-kehadiran,
            .text-center,
            .font-bold,
            .info-value {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .watermark-container {
                position: absolute;
            }
        }
    </style>
</head>
<body>
    <div class="print-bar no-print">
        <a href="#" onclick="event.preventDefault(); if(document.referrer && window.history.length > 1) { window.history.back(); } else { window.close(); }" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div class="zoom-controls">
            <button class="btn-zoom" onclick="zoomOut()" title="Perkecil"><i class="bi bi-dash"></i></button>
            <span class="zoom-level" id="zoomLevel">100%</span>
            <button class="btn-zoom" onclick="zoomIn()" title="Perbesar"><i class="bi bi-plus"></i></button>
            <button class="btn-zoom" onclick="zoomReset()" title="Reset" style="font-size: 12px;">Fit</button>
        </div>
        <button class="btn-print" onclick="handlePrintClick()"
                title="Setelah klik Cetak, untuk hasil paling bersih di Chrome: buka 'More settings' di dialog print → uncheck 'Headers and footers'.">
            <i class="bi bi-printer-fill"></i> Cetak Rapor
        </button>
        <i class="bi bi-info-circle text-muted ms-2" style="font-size: 14px; cursor: help;"
           title="Tip: di dialog Print Chrome → 'More settings' → uncheck 'Headers and footers' supaya cetakan bersih dari tanggal & URL."></i>
    </div>
    <script>
        function handlePrintClick() { window.print(); }
    </script>

    <!-- Watermark implemented via CSS background -->

<table class="print-page-frame">
    <thead><tr><td class="page-margin-top"></td></tr></thead>
    <tfoot><tr><td class="page-margin-bottom"></td></tr></tfoot>
    <tbody><tr><td>
    <div class="rapor-wrapper{{ $rapor->jenis_rapor === 'tengah_semester' ? ' with-watermark' : '' }}">
        <!-- Header - Logo only (text already in PNG) -->
        <div class="header">
            <img src="{{ asset('img/logo/hok-logo.png') }}?v={{ filemtime(public_path('img/logo/hok-logo.png')) }}" alt="HOK Logo" class="header-logo">
            <div class="header-address">
                Komplek Ruko Reni Jaya Baru Jl.Ketapang III Blok AF 5 No 22-23 Pamulang Barat – Tangerang Selatan<br>
                Telp. 021 – 7427521 / 085811278144 - e-mail : hokhomeshool@gmail.com
            </div>
        </div>
        <div class="header-separator"></div>

        <!-- Report Title -->
        <div class="report-title">
            Laporan Penilaian {{ $rapor->jenis_rapor === 'tengah_semester' ? 'Tengah Semester' : 'Akhir Semester' }}
        </div>

        <!-- Student Info -->
        <div class="student-info">
            <div>
                <div class="info-item">
                    <span class="info-label">Nama Siswa</span>
                    <span>: <span class="info-value">{{ $rapor->siswa->nama_lengkap }}</span></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nomor Induk</span>
                    <span>: <span class="info-value">{{ $rapor->siswa->nis }}</span></span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Tahun Ajaran</span>
                    <span>: <span class="info-value">{{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Semester</span>
                    <span>: <span class="info-value">{{ ucfirst($rapor->semester) }}</span></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kelas</span>
                    <span>: <span class="info-value">
                        @php
                            // Extract grade number from kelas name (e.g., "9A" -> 9)
                            $kelasNama = $rapor->kelas->nama_kelas;
                            preg_match('/^(\d+)/', $kelasNama, $matches);
                            $gradeNumber = $matches[1] ?? 0;
                            
                            // Convert to Roman numerals
                            $romanNumerals = [
                                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
                                6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX',
                                10 => 'X', 11 => 'XI', 12 => 'XII', 13 => 'XIII'
                            ];
                            $kelasRomawi = $romanNumerals[$gradeNumber] ?? $kelasNama;
                            
                            // Convert to Indonesian words
                            $indonesianWords = [
                                1 => 'Satu', 2 => 'Dua', 3 => 'Tiga', 4 => 'Empat', 5 => 'Lima',
                                6 => 'Enam', 7 => 'Tujuh', 8 => 'Delapan', 9 => 'Sembilan',
                                10 => 'Sepuluh', 11 => 'Sebelas', 12 => 'Dua Belas', 13 => 'Tiga Belas'
                            ];
                            $kelasIndonesia = $indonesianWords[$gradeNumber] ?? '';
                            $kelasDisplay = $kelasIndonesia ? "$kelasRomawi ($kelasIndonesia)" : $kelasRomawi;
                        @endphp
                        {{ $kelasDisplay }}
                    </span></span>
                </div>
            </div>
        </div>

        <!-- Nilai Mata Pelajaran Table -->
        <table class="grade-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 30px;">No</th>
                    <th rowspan="2">Mata Pelajaran</th>
                    <th rowspan="2" style="width: 40px;">KKM</th>
                    <th colspan="4">Nilai</th>
                    <th rowspan="2" style="width: 80px;">Keterangan</th>
                </tr>
                <tr>
                    <th style="width: 45px;">Tugas</th>
                    <th style="width: 40px;">U<sub>1</sub></th>
                    <th style="width: 40px;">U<sub>2</sub></th>
                    <th style="width: 40px;">PTS</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPTS = 0;
                    $jumlahMapel = 0;
                    $visibleNilai = $rapor->raporNilai->filter(fn($rn) => $rn->is_visible)->values();
                @endphp
                @foreach($visibleNilai as $index => $raporNilai)
                    @php
                        $nilai = $raporNilai->nilai;
                        $mapel = $raporNilai->mataPelajaran;
                        $kkm = $mapel->kkm ?? 70;
                        
                        // Get grade components
                        $tugas = $nilai->rata_tugas ?? 0;
                        $u1 = $nilai->rata_latihan ?? 0;
                        $u2 = $nilai->rata_uh ?? 0;
                        $pts = $nilai->pts ?? 0;
                        
                        // Determine tuntas/tidak tuntas
                        $tuntas = $pts >= $kkm;
                        
                        $totalPTS += $pts;
                        $jumlahMapel++;
                    @endphp
                    <tr class="{{ !$tuntas ? 'tidak-tuntas' : '' }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $mapel->nama_mapel }}</td>
                        <td class="text-center">{{ $kkm }}</td>
                        <td class="text-center">{{ $tugas > 0 ? number_format($tugas, 0) : '-' }}</td>
                        <td class="text-center">{{ $u1 > 0 ? number_format($u1, 0) : '-' }}</td>
                        <td class="text-center">{{ $u2 > 0 ? number_format($u2, 0) : '-' }}</td>
                        <td class="text-center"><strong>{{ $pts > 0 ? number_format($pts, 0) : '-' }}</strong></td>
                        <td class="text-center">{{ $tuntas ? 'Tuntas' : 'Tidak Tuntas' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-center font-bold" style="padding: 5px;">Jumlah</td>
                    <td class="text-center font-bold">{{ number_format($totalPTS, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="7" class="text-center font-bold" style="padding: 5px;">Rata-rata</td>
                    <td class="text-center font-bold">{{ $jumlahMapel > 0 ? number_format($totalPTS / $jumlahMapel, 2, ',', '.') : '0' }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Kegiatan Ekstra Table -->
        <table style="margin-top: 20px;">
            @if($rapor->kegiatanEkstra->count() > 0)
                @foreach($rapor->kegiatanEkstra as $index => $ekstra)
                    <tr>
                        @if($index === 0)
                            <td rowspan="{{ $rapor->kegiatanEkstra->count() }}" class="bg-ekstra font-bold text-center" style="width: 150px; vertical-align: middle; padding: 10px;">
                                Kegiatan Ekstra
                            </td>
                        @endif
                        <td style="padding: 5px 10px;">{{ $ekstra->kegiatan_nama }}</td>
                        <td class="text-center" style="width: 50px; padding: 5px;">{{ $ekstra->predikat ?? '-' }}</td>
                    </tr>
                @endforeach
            @else
                <!-- Fallback: show 4 empty rows if no data -->
                @for($i = 0; $i < 4; $i++)
                    <tr>
                        @if($i === 0)
                            <td rowspan="4" class="bg-ekstra font-bold text-center" style="width: 150px; vertical-align: middle; padding: 10px;">
                                Kegiatan Ekstra
                            </td>
                        @endif
                        <td style="padding: 5px 10px;">-</td>
                        <td class="text-center" style="width: 50px; padding: 5px;">-</td>
                    </tr>
                @endfor
            @endif
        </table>

        <!-- Kehadiran Table -->
        <table style="margin-top: 15px;">
            <tr>
                <td rowspan="4" class="bg-kehadiran font-bold text-center" style="width: 150px; vertical-align: middle; padding: 10px;">
                    Kehadiran
                </td>
                <td style="padding: 5px 10px;">Sakit</td>
                <td class="text-center" style="width: 50px; padding: 5px;">{{ ($rapor->jumlah_sakit ?? 0) > 0 ? $rapor->jumlah_sakit : '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 10px;">Ijin</td>
                <td class="text-center" style="padding: 5px;">{{ ($rapor->jumlah_izin ?? 0) > 0 ? $rapor->jumlah_izin : '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 10px;">Tanpa Keterangan</td>
                <td class="text-center" style="padding: 5px;">{{ ($rapor->jumlah_alpha ?? 0) > 0 ? $rapor->jumlah_alpha : '-' }}</td>
            </tr>
            <tr>
                <td class="font-bold" style="padding: 5px 10px;">Jumlah</td>
                <td class="text-center font-bold" style="padding: 5px;">
                    @php
                        $totalKehadiran = ($rapor->jumlah_sakit ?? 0) + ($rapor->jumlah_izin ?? 0) + ($rapor->jumlah_alpha ?? 0);
                    @endphp
                    {{ $totalKehadiran > 0 ? $totalKehadiran : '-' }}
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan Table -->
        <table style="margin-top: 15px; margin-bottom: 20px;">
            <tr>
                <th rowspan="2" class="font-bold text-center" style="width: 150px; vertical-align: middle; padding: 10px;">
                    Tanda Tangan
                </th>
                <td class="text-center font-bold" style="padding: 8px;">
                    Orang Tua
                </td>
                <td class="text-center font-bold" style="padding: 8px;">
                    Wali Kelas
                </td>
            </tr>
            <tr>
                <td style="height: 120px; vertical-align: bottom; padding: 10px;">
                    <!-- Empty space for parent signature -->
                </td>
                <td class="text-center" style="height: 120px; vertical-align: bottom; padding: 10px;">
                    {{ $rapor->kelas->waliKelas->nama_lengkap ?? '-' }}
                </td>
            </tr>
        </table>

        <!-- Footer - RIGHT aligned -->
        <!-- Footer - RIGHT aligned -->
        <div style="margin-top: 30px; text-align: right; padding-right: 50px;">
            <p style="margin: 0; line-height: 1.6;">
                {{ $rapor->kelas->cabang->kota ?? 'Tangerang Selatan' }}, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}
            </p>
            <div style="display: inline-block; text-align: center; margin-top: 5px;">
                <p style="margin: 0; line-height: 1.6;">Ketua PKBM House of Knowledge</p>
                
                <!-- Space for signature -->
                <div style="height: 80px;"></div>
                
                <p style="margin: 0; line-height: 1.6; border-bottom: 1px solid #000; display: inline-block; padding-bottom: 2px; font-weight: bold;">
                    Fransisda Tiodora Ferdiansyah, S.Psi., MM
                </p>
            </div>
        </div>
    </div>
    </td></tr></tbody>
</table>

    <script>
    (function() {
        var DOC_WIDTH = 900;
        var currentScale = 1;
        var fitScale = 1;
        var manualZoom = false;

        function applyScale(scale) {
            var wrapper = document.querySelector('.rapor-wrapper');
            if (!wrapper) return;
            currentScale = scale;
            wrapper.style.transform = 'scale(' + scale + ')';
            wrapper.style.transformOrigin = 'top left';
            wrapper.style.marginLeft = '0';
            wrapper.style.marginRight = '0';
            document.body.style.height = Math.ceil(wrapper.scrollHeight * scale) + 'px';
            var label = document.getElementById('zoomLevel');
            if (label) label.textContent = Math.round(scale * 100) + '%';
        }

        function fitToScreen() {
            var wrapper = document.querySelector('.rapor-wrapper');
            if (!wrapper) return;

            var vw = Math.min(window.innerWidth, document.documentElement.clientWidth);

            if (vw < DOC_WIDTH) {
                fitScale = vw / DOC_WIDTH;
            } else {
                fitScale = 1;
            }

            if (!manualZoom) {
                applyScale(fitScale);
            }
        }

        window.zoomIn = function() {
            manualZoom = true;
            applyScale(Math.min(currentScale + 0.1, 2));
        };
        window.zoomOut = function() {
            manualZoom = true;
            applyScale(Math.max(currentScale - 0.1, 0.3));
        };
        window.zoomReset = function() {
            manualZoom = false;
            fitToScreen();
        };

        window.addEventListener('load', fitToScreen);
        window.addEventListener('resize', function() {
            if (!manualZoom) fitToScreen();
        });

        window.addEventListener('beforeprint', function() {
            var wrapper = document.querySelector('.rapor-wrapper');
            if (wrapper) { wrapper.style.transform = 'none'; wrapper.style.width = ''; }
        });
        window.addEventListener('afterprint', function() {
            if (manualZoom) { applyScale(currentScale); } else { fitToScreen(); }
        });
    })();
    </script>
</body>
</html>
