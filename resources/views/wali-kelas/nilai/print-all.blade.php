<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Nilai Kelas - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            background: #f0f2f5;
            color: #000;
        }

        /* ── Controls bar (screen only) ── */
        @media screen {
            .ctrl-bar {
                position: fixed; top: 0; left: 0; right: 0; height: 46px;
                background: #2c3340; color: #fff;
                display: flex; align-items: center; justify-content: space-between;
                padding: 0 14px; z-index: 9999; gap: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,.4);
            }
            .btn-c {
                background: rgba(255,255,255,.08); color: #fff;
                border: 1px solid rgba(255,255,255,.28);
                padding: 7px 14px; border-radius: 5px; cursor: pointer;
                font-size: 14px; font-weight: 700; line-height: 1;
                display: inline-flex; align-items: center; gap: 5px;
                -webkit-tap-highlight-color: transparent;
                touch-action: manipulation; user-select: none;
            }
            .btn-c:active { background: rgba(255,255,255,.25); }
            .btn-print { background: #dc3545 !important; border-color: #dc3545 !important; }
            .btn-print:active { background: #bb2d3b !important; }
            .zoom-group { display: flex; align-items: center; gap: 6px; }
            .zoom-label {
                min-width: 46px; text-align: center;
                font-size: 12px; font-weight: 700; color: #d0d0d0;
            }

            body { padding-top: 54px; }

            /* ── Wrapper: scrollable saat zoom in ── */
            #scaleWrapper {
                display: flex;
                justify-content: center;
                overflow-x: auto;
                padding: 20px 10px 50px;
                min-height: calc(100vh - 54px);
            }

            /* ── Page container: lebar LANDSCAPE A4 ── */
            .page-container {
                background: white;
                box-shadow: 0 4px 20px rgba(0,0,0,.15);
                width: 1058px;
                min-height: 750px;
                padding: 18px 22px;
                flex-shrink: 0;
            }
        }

        /* ── Print: sembunyikan ctrl-bar ── */
        @media print {
            @page { size: A4 landscape; margin: 6mm; }
            .ctrl-bar { display: none !important; }
            body { background: white; padding: 0; font-size: 8.5pt; }
            #scaleWrapper { display: block; padding: 0; }
            .page-container { box-shadow: none; padding: 0; width: 100%; min-height: auto; zoom: 1 !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }

        /* ── Info bar ── */
        .info-bar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 8px; padding-bottom: 8px;
            border-bottom: 1px solid #ccc; font-size: 8.5pt;
        }

        /* ── Semester badge ── */
        .semester-badge {
            display: inline-block;
            background: #e3f2fd; border: 1px solid #90caf9;
            color: #1565c0; border-radius: 4px;
            padding: 2px 10px; font-size: 8pt; font-weight: bold;
        }

        /* ── Data table ── */
        table.data { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        table.data th, table.data td { border: 1px solid #444; padding: 4px 3px; vertical-align: middle; }
        table.data th { background: #e9ecef; text-align: center; font-weight: bold; font-size: 8pt; text-transform: uppercase; }
        .rata        { background: #e8f5e9; }
        .nilai-akhir { background: #e3f2fd; font-weight: bold; }

        /* ── Footer ── */
        .footer { margin-top: 16px; display: flex; justify-content: space-between; align-items: flex-end; font-size: 8.5pt; }
        .print-date { font-style: italic; color: #555; font-size: 7.5pt; }
        .signature-area { text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 48px; width: 210px; }
    </style>
</head>
<body>
    @php
        $semesterLabel = ucfirst($semester ?? 'genap');
    @endphp

    {{-- ── Controls bar ── --}}
    <div class="ctrl-bar">
        <div class="zoom-group">
            <button class="btn-c" onclick="zoomOut()" title="Perkecil">−</button>
            <span class="zoom-label" id="zoomLabel">100%</span>
            <button class="btn-c" onclick="zoomIn()" title="Perbesar">+</button>
            <button class="btn-c" onclick="fitScreen()" title="Sesuaikan layar" style="font-size:11px; padding:7px 10px;">Fit</button>
        </div>
        <button class="btn-c btn-print" onclick="window.print()">
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

    <script>
        const NATIVE_W = 1058;
        let zoomLevel  = 1;

        function baseScale() {
            const avail = window.innerWidth - 20;
            return avail < NATIVE_W ? avail / NATIVE_W : 1;
        }

        function applyScale() {
            const el  = document.getElementById('pageContainer');
            const lbl = document.getElementById('zoomLabel');
            if (!el) return;

            const scale = Math.round(baseScale() * zoomLevel * 1000) / 1000;
            el.style.zoom = scale;

            if (lbl) lbl.textContent = Math.round(scale * 100) + '%';
        }

        function zoomIn()    { zoomLevel = Math.min(+(zoomLevel + 0.15).toFixed(2), 4);   applyScale(); }
        function zoomOut()   { zoomLevel = Math.max(+(zoomLevel - 0.15).toFixed(2), 0.1); applyScale(); }
        function fitScreen() { zoomLevel = 1; applyScale(); }

        window.addEventListener('load',   applyScale);
        window.addEventListener('resize', () => { zoomLevel = 1; applyScale(); });
    </script>
</body>
</html>
