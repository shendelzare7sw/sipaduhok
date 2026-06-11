<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Nilai Kelas - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/nilai/print-all.css') }}">
</head>
<body>
    @php
        $semesterLabel = ucfirst($semester ?? 'genap');
    @endphp

    {{-- ── Controls bar ── --}}
    <div class="ctrl-bar">
        <div class="zoom-group">
            <button class="btn-c" data-zoom-action="out" title="Perkecil">−</button>
            <span class="zoom-label" id="zoomLabel">100%</span>
            <button class="btn-c" data-zoom-action="in" title="Perbesar">+</button>
            <button class="btn-c" data-zoom-action="fit" title="Sesuaikan layar" style="font-size:11px; padding:7px 10px;">Fit</button>
        </div>
        <button class="btn-c btn-print" data-print-page>
            <i class="bi bi-printer-fill"></i> Cetak / PDF
        </button>
    </div>

    {{-- ── Scroll wrapper ── --}}
    <div id="scaleWrapper">
        <div class="page-container" id="pageContainer">
            @include('partials.print-header', ['cabang' => $cabang ?? null])

            <div style="text-align:center; margin-bottom:8px;">
                <strong style="font-size:11pt; text-transform:uppercase;">Rekapitulasi Nilai Akademik</strong>
            </div>

            <div class="info-bar">
                <div>
                    <strong>Kelas:</strong> {{ $kelas->nama_kelas }} &nbsp;|&nbsp;
                    <strong>Tahun Ajaran:</strong> {{ $kelas->tahunAjaran->nama_tahun_ajaran }} &nbsp;|&nbsp;
                    <strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap ?? '-' }}
                </div>
                <span class="semester-badge">
                    <i class="bi bi-calendar3-week"></i> Semester {{ $semesterLabel }}
                </span>
            </div>

            <table class="data">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:22px;">No</th>
                        <th rowspan="2" style="width:50px;">NIS</th>
                        <th rowspan="2" style="min-width:120px; text-align:left;">Nama Siswa</th>
                        <th colspan="2">Tugas</th>
                        <th colspan="2">Latihan</th>
                        <th colspan="2">UH</th>
                        <th rowspan="2" style="width:32px;">PTS</th>
                        <th rowspan="2" style="width:32px;">PAS</th>
                        <th rowspan="2" class="nilai-akhir" style="width:50px;">N. Akhir</th>
                        <th rowspan="2" style="width:58px;">Status</th>
                    </tr>
                    <tr>
                        <th style="width:26px;">Jml</th>
                        <th class="rata" style="width:32px;">Rata</th>
                        <th style="width:26px;">Jml</th>
                        <th class="rata" style="width:32px;">Rata</th>
                        <th style="width:26px;">Jml</th>
                        <th class="rata" style="width:32px;">Rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaList as $index => $siswa)
                        @php
                            $nilaiSiswa = $allNilaiData->where('siswa_id', $siswa->id);
                            $totalTugasCount = $totalLatihanCount = $totalUhCount = 0;
                            $totalRataTugas = $totalRataLatihan = $totalRataUh = [];
                            $totalPts = $totalPas = $totalNilaiAkhir = [];

                            foreach ($nilaiSiswa as $n) {
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($n->{'tugas_'.$i}   !== null) $totalTugasCount++;
                                    if ($n->{'latihan_'.$i} !== null) $totalLatihanCount++;
                                    if ($n->{'uh_'.$i}      !== null) $totalUhCount++;
                                }
                                if ($n->rata_tugas   !== null) $totalRataTugas[]   = $n->rata_tugas;
                                if ($n->rata_latihan !== null) $totalRataLatihan[] = $n->rata_latihan;
                                if ($n->rata_uh      !== null) $totalRataUh[]      = $n->rata_uh;
                                if ($n->pts          !== null) $totalPts[]          = $n->pts;
                                if ($n->pas          !== null) $totalPas[]          = $n->pas;
                                if ($n->nilai_akhir  !== null) $totalNilaiAkhir[]  = $n->nilai_akhir;
                            }

                            $avgRataTugas   = count($totalRataTugas)   > 0 ? array_sum($totalRataTugas)   / count($totalRataTugas)   : null;
                            $avgRataLatihan = count($totalRataLatihan) > 0 ? array_sum($totalRataLatihan) / count($totalRataLatihan) : null;
                            $avgRataUh      = count($totalRataUh)      > 0 ? array_sum($totalRataUh)      / count($totalRataUh)      : null;
                            $avgPts         = count($totalPts)         > 0 ? array_sum($totalPts)         / count($totalPts)         : null;
                            $avgPas         = count($totalPas)         > 0 ? array_sum($totalPas)         / count($totalPas)         : null;
                            $avgNilaiAkhir  = count($totalNilaiAkhir)  > 0 ? array_sum($totalNilaiAkhir)  / count($totalNilaiAkhir)  : null;

                            $mapelCount = count($mataPelajaranList ?? []);
                            $maxCount   = $mapelCount * 5;
                            $isTuntas   = $avgNilaiAkhir !== null && $avgNilaiAkhir >= 70;
                        @endphp
                        <tr>
                            <td style="text-align:center;">{{ $index + 1 }}</td>
                            <td style="text-align:center;">{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama_lengkap }}</td>
                            <td style="text-align:center;">{{ $totalTugasCount }}/{{ $maxCount }}</td>
                            <td class="rata" style="text-align:center;">{{ $avgRataTugas   !== null ? number_format($avgRataTugas, 1)   : '-' }}</td>
                            <td style="text-align:center;">{{ $totalLatihanCount }}/{{ $maxCount }}</td>
                            <td class="rata" style="text-align:center;">{{ $avgRataLatihan !== null ? number_format($avgRataLatihan, 1) : '-' }}</td>
                            <td style="text-align:center;">{{ $totalUhCount }}/{{ $maxCount }}</td>
                            <td class="rata" style="text-align:center;">{{ $avgRataUh      !== null ? number_format($avgRataUh, 1)      : '-' }}</td>
                            <td style="text-align:center;">{{ $avgPts        !== null ? number_format($avgPts, 0)        : '-' }}</td>
                            <td style="text-align:center;">{{ $avgPas        !== null ? number_format($avgPas, 0)        : '-' }}</td>
                            <td class="nilai-akhir" style="text-align:center;">{{ $avgNilaiAkhir !== null ? number_format($avgNilaiAkhir, 2) : '-' }}</td>
                            <td style="text-align:center;">{{ $avgNilaiAkhir !== null ? ($isTuntas ? 'Tuntas' : 'Blm Tuntas') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="footer">
                <p class="print-date">Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</p>
                <div class="signature-area">
                    <p>Mengetahui, Wali Kelas</p>
                    <div class="signature-line"></div>
                    <p>{{ $kelas->waliKelas->nama_lengkap ?? '...............................' }}</p>
                </div>
            </div>
        </div>{{-- end .page-container --}}
    </div>{{-- end #scaleWrapper --}}
    <script src="{{ asset('js/wali-kelas/nilai/print-all.js') }}"></script>
</body>
</html>
