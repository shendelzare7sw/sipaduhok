<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor {{ $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }} - {{ $rapor->siswa->nama_lengkap }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('partials.anti-screenshot')
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/rapor/preview-pts.css') }}">
</head>
<body>
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
    <script src="{{ asset('js/wali-kelas/rapor/preview-pts.js') }}"></script>
</body>
</html>
