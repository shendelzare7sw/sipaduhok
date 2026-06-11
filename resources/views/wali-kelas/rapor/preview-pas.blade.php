<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor PAS - {{ $rapor->siswa->nama_lengkap }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('partials.anti-screenshot')
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/rapor/preview-pas.css') }}">
</head>
<body>
    @php
        $alignmentValue = fn($value, $default) => in_array($value, ['left', 'center', 'right', 'justify'], true) ? $value : $default;
        $deskripsiAlignment = $alignmentValue($rapor->deskripsi_alignment ?? null, 'left');
        $keteranganEkstraAlignment = $alignmentValue($rapor->keterangan_ekstra_alignment ?? null, 'left');
        $catatanAlignment = $alignmentValue($rapor->catatan_alignment ?? null, 'center');
    @endphp

    <div class="print-bar no-print">
        <a href="#" class="btn-back" data-history-back>
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div class="zoom-controls">
            <button class="btn-zoom" data-zoom-action="out" title="Perkecil"><i class="bi bi-dash"></i></button>
            <span class="zoom-level" id="zoomLevel">100%</span>
            <button class="btn-zoom" data-zoom-action="in" title="Perbesar"><i class="bi bi-plus"></i></button>
            <button class="btn-zoom" data-zoom-action="reset" title="Reset" style="font-size: 12px;">Fit</button>
        </div>
        <button class="btn-print" data-print-page
                title="Setelah klik Cetak, untuk hasil paling bersih di Chrome: buka 'More settings' di dialog print → uncheck 'Headers and footers'.">
            <i class="bi bi-printer-fill"></i> Cetak Rapor
        </button>
        <i class="bi bi-info-circle text-muted ms-2" style="font-size: 14px; cursor: help;"
           title="Tip: di dialog Print Chrome → 'More settings' → uncheck 'Headers and footers' supaya cetakan bersih dari tanggal & URL."></i>
    </div>
<table class="print-page-frame">
    <thead><tr><td class="page-margin-top"></td></tr></thead>
    <tfoot><tr><td class="page-margin-bottom"></td></tr></tfoot>
    <tbody><tr><td>
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
                    <td style="text-align: {{ $deskripsiAlignment }};">{{ $raporNilai->deskripsi ?? '' }}</td>
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
                    <td style="text-align: {{ $deskripsiAlignment }};">{{ $raporNilai->deskripsi ?? '' }}</td>
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
                    <td style="text-align: {{ $deskripsiAlignment }};">{{ $raporNilai->deskripsi ?? '' }}</td>
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
                    <td style="text-align: {{ $keteranganEkstraAlignment }};">{{ $kegiatan->keterangan ?? '' }}</td>
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
        <div class="catatan-box" style="text-align: {{ $catatanAlignment }};">
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
    </td></tr></tbody>
</table>
    <script src="{{ asset('js/wali-kelas/rapor/preview-pas.js') }}"></script>
</body>
</html>
