<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor PAS - {{ $rapor->siswa->nama_lengkap }}</title>
    @include('partials.anti-screenshot')
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            margin: 0;
            padding: 20px;
            position: relative;
        }

        .rapor-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
        }

        /* Watermark as background */
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

        /* Header Title */
        h1 {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
        }

        /* Student Info Table - No borders */
        .student-info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .student-info-table td {
            padding: 3px;
            font-size: 10pt;
        }

        .student-info-table .label {
            width: 23%;
        }

        .student-info-table .colon {
            width: 2%;
        }

        .student-info-table .value {
            width: 33%;
            font-weight: bold;
        }

        .student-info-table .spacer {
            width: 2%;
        }

        .student-info-table .label-right {
            width: 18%;
        }

        .student-info-table .value-right {
            width: 18%;
            font-weight: bold;
        }

        /* Subject Table */
        table.grade-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.grade-table th,
        table.grade-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            background-color: transparent;
        }

        table.grade-table th {
            background-color: rgba(184, 212, 232, 0.7);
            font-weight: bold;
            text-align: center;
        }

        table.grade-table td.text-center {
            text-align: center;
        }

        table.grade-table .kelompok-header {
            font-weight: bold;
            background-color: rgba(243, 244, 246, 0.7);
        }

        /* Ekstra Table */
        table.ekstra-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table.ekstra-table th,
        table.ekstra-table td {
            border: 1px solid #000;
            padding: 6px;
            background-color: transparent;
        }

        table.ekstra-table th {
            background-color: rgba(184, 212, 232, 0.7);
            font-weight: bold;
            text-align: center;
        }

        /* Attendance Table */
        table.attendance-table {
            width: 40%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.attendance-table td {
            border: 1px solid #000;
            padding: 6px;
            background-color: transparent;
        }

        table.attendance-table td.text-center {
            text-align: center;
        }

        /* Catatan Box */
        .catatan-box {
            border: 1px solid #000;
            min-height: 100px;
            padding: 12px;
            margin-bottom: 25px;
            margin-top: 10px;
        }

        /* Section Title */
        h3 {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        /* Signature Table */
        .signature-table {
            width: 100%;
            margin-top: 30px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 20px;
        }

        .signature-space {
            height: 90px;
        }

        .signature-line {
            margin: 0;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 50px 2px 50px;
        }

        .signature-line-wali {
            margin: 0;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 10px 2px 10px;
        }

        /* Ketua PKBM Section */
        .ketua-section {
            text-align: center;
            margin-top: 40px;
        }

        /* Print Button */
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .btn-print:hover {
            background: #1e40af;
        }

        /* Print Styles */
        @media print {
            @page {
                margin: 0.5cm;
            }

            body {
                padding: 0;
                background: white;
            }

            .rapor-wrapper {
                box-shadow: none;
                padding: 20px;
            }

            .btn-print {
                display: none;
            }

            /* Force watermark to print */
            .rapor-wrapper.with-watermark::before {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                opacity: 0.08 !important;
                display: block !important;
            }

            /* Force all table backgrounds transparent */
            table th, table td {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                background-color: transparent !important;
            }

            table th {
                background-color: transparent !important;
            }

            .grade-table .kelompok-header {
                background-color: transparent !important;
            }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">
        🖨️ Cetak Rapor
    </button>

    <div class="rapor-wrapper{{ $rapor->jenis_rapor === 'akhir_semester' ? ' with-watermark' : '' }}">
        <!-- Title -->
        <h1>PENCAPAIAN KOMPETENSI PESERTA DIDIK</h1>

        <!-- Student Info - 8 Column Layout -->
        <table class="student-info-table">
            <tr>
                <td class="spacer"></td>
                <td class="label">Nama Sekolah</td>
                <td class="colon">:</td>
                <td class="value">House Of Knowledge</td>
                <td class="spacer"></td>
                <td class="label-right">Kelas/Fase</td>
                <td class="colon">:</td>
                <td class="value-right">
                    @php
                        $kelasNama = $rapor->kelas->nama_kelas;
                        preg_match('/^(\d+)/', $kelasNama, $matches);
                        $gradeNumber = $matches[1] ?? 0;
                        
                        $romanNumerals = [
                            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
                            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX',
                            10 => 'X', 11 => 'XI', 12 => 'XII', 13 => 'XIII'
                        ];
                        $kelasRomawi = $romanNumerals[$gradeNumber] ?? $kelasNama;
                        
                        // Determine jenjang for header
                        $isSMA = ($gradeNumber >= 10 && $gradeNumber <= 12);
                        $headerKolom4 = $isSMA ? 'Deskripsi' : 'Capaian Kompetensi';
                        
                        // Fase mapping
                        $faseMap = [
                            1 => 'A', 2 => 'A', 3 => 'B', 4 => 'B', 5 => 'C', 6 => 'C',
                            7 => 'D', 8 => 'D', 9 => 'D',
                            10 => 'E', 11 => 'E', 12 => 'F'
                        ];
                        $fase = $faseMap[$gradeNumber] ?? '-';
                    @endphp
                    {{ $kelasRomawi }}/{{ $fase }}
                </td>
            </tr>
            <tr>
                <td class="spacer"></td>
                <td class="label">Nama Peserta Didik</td>
                <td class="colon">:</td>
                <td class="value">{{ $rapor->siswa->nama_lengkap }}</td>
                <td class="spacer"></td>
                <td class="label-right">Semester</td>
                <td class="colon">:</td>
                <td class="value-right">{{ $rapor->semester === 'ganjil' ? '1 (satu)' : '2 (dua)' }}</td>
            </tr>
            <tr>
                <td class="spacer"></td>
                <td class="label">Nomor Induk</td>
                <td class="colon">:</td>
                <td class="value">{{ $rapor->siswa->nis }}</td>
                <td class="spacer"></td>
                <td class="label-right">Tahun Ajaran</td>
                <td class="colon">:</td>
                <td class="value-right">{{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '2025/2026' }}</td>
            </tr>
        </table>

        <!-- Grade Table with Kelompok A/B -->
        <table class="grade-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Mata Pelajaran</th>
                    <th style="width: 8%;">Nilai</th>
                    <th style="width: 57%;">{{ $headerKolom4 }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Filter hanya yang visible
                    $visibleNilai = $rapor->raporNilai->filter(fn($rn) => $rn->is_visible);

                    // Use kelompok_override if set, otherwise use mataPelajaran.kelompok
                    $mapelKelompokA = $visibleNilai->filter(function($rn) {
                        $kel = $rn->kelompok_override ?? ($rn->mataPelajaran->kelompok ?? '');
                        return trim($kel) == 'A';
                    })->values();

                    $mapelKelompokB = $visibleNilai->filter(function($rn) {
                        $kel = $rn->kelompok_override ?? ($rn->mataPelajaran->kelompok ?? '');
                        return trim($kel) == 'B';
                    })->values();

                    // Handle subjects with NULL kelompok (legacy data)
                    $mapelNoKelompok = $visibleNilai->filter(function($rn) {
                        $kel = $rn->kelompok_override ?? ($rn->mataPelajaran->kelompok ?? '');
                        return $kel === null || trim($kel) === '';
                    })->values();
                @endphp

                <!-- Kelompok A -->
                @if($mapelKelompokA->count() > 0)
                <tr>
                    <td colspan="4" class="kelompok-header">Kelompok A</td>
                </tr>
                @foreach($mapelKelompokA as $index => $raporNilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $raporNilai->mataPelajaran->nama_mapel }}</td>
                    <td class="text-center">{{ $raporNilai->nilai_angka ?? '-' }}</td>
                    <td>{{ $raporNilai->deskripsi ?? '' }}</td>
                </tr>
                @endforeach
                @endif

                <!-- Kelompok B -->
                @if($mapelKelompokB->count() > 0)
                <tr>
                    <td colspan="4" class="kelompok-header">Kelompok B</td>
                </tr>
                @foreach($mapelKelompokB as $index => $raporNilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $raporNilai->mataPelajaran->nama_mapel }}</td>
                    <td class="text-center">{{ $raporNilai->nilai_angka ?? '-' }}</td>
                    <td>{{ $raporNilai->deskripsi ?? '' }}</td>
                </tr>
                @endforeach
                @endif

                <!-- Uncategorized (if any) -->
                @if($mapelNoKelompok->count() > 0)
                <tr>
                    <td colspan="4" class="kelompok-header">Lainnya (Belum Dikategorikan)</td>
                </tr>
                @foreach($mapelNoKelompok as $index => $raporNilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $raporNilai->mataPelajaran->nama_mapel }}</td>
                    <td class="text-center">{{ $raporNilai->nilai_angka ?? '-' }}</td>
                    <td>{{ $raporNilai->deskripsi ?? '' }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

        <!-- Kegiatan Ekstra -->
        <table class="ekstra-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 40%;">Kegiatan Ekstra</th>
                    <th style="width: 12%;">Predikat</th>
                    <th style="width: 43%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rapor->kegiatanEkstra as $index => $kegiatan)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $kegiatan->kegiatan_nama }}</td>
                    <td class="text-center">{{ $kegiatan->predikat ?? '-' }}</td>
                    <td>{{ $kegiatan->keterangan ?? '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                        Belum ada data kegiatan ekstrakurikuler
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- E. Ketidakhadiran -->
        <h3>E. KETIDAKHADIRAN</h3>
        <table class="attendance-table">
            <tr>
                <td style="width: 65%;">Sakit</td>
                <td class="text-center">{{ ($rapor->jumlah_sakit ?? 0) > 0 ? $rapor->jumlah_sakit . ' hari' : '-' }}</td>
            </tr>
            <tr>
                <td>Ijin</td>
                <td class="text-center">{{ ($rapor->jumlah_izin ?? 0) > 0 ? $rapor->jumlah_izin . ' hari' : '-' }}</td>
            </tr>
            <tr>
                <td>Tanpa Keterangan</td>
                <td class="text-center">{{ ($rapor->jumlah_alpha ?? 0) > 0 ? $rapor->jumlah_alpha . ' hari' : '-' }}</td>
            </tr>
        </table>

        <!-- F. Catatan Wali Kelas -->
        <h3>F. CATATAN WALI KELAS</h3>
        <div class="catatan-box">
            {{ $rapor->catatan_wali_kelas ?? '' }}
        </div>

        <!-- Signatures -->
        <table class="signature-table" style="margin-top: 30px; width: 100%;">
            <!-- Baris 1: Tanggal (Kanan) -->
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center; padding-bottom: 5px;">
                    <p style="margin: 0;">Tangerang Selatan, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                </td>
            </tr>
            <!-- Baris 2: Judul Tanda Tangan -->
            <tr>
                <td style="text-align: center; vertical-align: top;">
                    <p style="margin: 0;">Mengetahui,</p>
                    <p style="margin: 0;">Orang Tua Siswa</p>
                </td>
                <td style="text-align: center; vertical-align: top;">
                    <br> <!-- Spacer to align simply with 'Orang Tua Siswa' below 'Mengetahui' -->
                    <p style="margin: 0;">Wali Kelas</p>
                </td>
            </tr>
            <!-- Baris 3: Spasi Tanda Tangan -->
            <tr>
                <td style="height: 100px;"></td>
                <td style="height: 100px;"></td>
            </tr>
            <!-- Baris 4: Nama Penanda Tangan -->
            <tr>
                <td style="text-align: center;">
                    <p class="signature-line" style="width: 200px; margin: 0 auto; display: block; border-bottom: 1px solid #000;">&nbsp;</p>
                </td>
                <td style="text-align: center;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $rapor->kelas->waliKelas->nama_lengkap ?? '.........................' }}</p>
                </td>
            </tr>
        </table>

        <!-- Ketua PKBM -->
        <div class="ketua-section">
            <p style="margin: 0 0 5px 0;">Mengetahui,</p>
            <p style="margin: 0 0 5px 0;">Ketua PKBM House Of Knowledge</p>
            <div class="signature-space"></div>
            <p class="signature-line" style="padding: 0 30px 2px 30px;">
                Fransisda Tiodora Ferdiansyah, S.Psi., MM
            </p>
        </div>
    </div>
</body>
</html>
