<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai - {{ $siswa->nama_lengkap }}</title>
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
                width: 1058px;       /* A4 landscape usable width */
                min-height: 750px;
                padding: 18px 22px;
                flex-shrink: 0;      /* jangan menyusut dalam flex */
            }
        }

        /* ── Print: sembunyikan ctrl-bar ── */
        @media print {
            @page { size: A4 landscape; margin: 6mm; }
            .ctrl-bar { display: none !important; }
            body { background: white; padding: 0; font-size: 8.5pt; }
            #scaleWrapper { display: block; padding: 0; }
            .page-container { box-shadow: none; padding: 0; width: 100%; min-height: auto; zoom: 1 !important; }
            table { page-break-inside: avoid; }
        }

        /* ── Semester badge ── */
        .semester-badge {
            display: inline-block;
            background: #e3f2fd; border: 1px solid #90caf9;
            color: #1565c0; border-radius: 4px;
            padding: 2px 10px; font-size: 8pt; font-weight: bold;
            margin-bottom: 8px;
        }

        /* ── Info table ── */
        .info-table { margin-bottom: 8px; font-size: 8.5pt; width: 100%; }
        .info-table td { padding: 2px 6px; }
        .info-table .label { font-weight: bold; width: 110px; white-space: nowrap; }

        /* ── Data table ── */
        table.data { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        table.data th, table.data td { border: 1px solid #444; padding: 4px 3px; }
        table.data th { background: #e9ecef; text-align: center; font-weight: bold; }
        .rata        { background: #d4edda; }
        .nilai-akhir { background: #c3e6cb; font-weight: bold; }

        /* ── Footer ── */
        .footer { margin-top: 14px; }
        .print-date { font-size: 7.5pt; color: #555; font-style: italic; margin-bottom: 8px; }
        .ttd { display: flex; justify-content: space-between; }
        .ttd-item { text-align: center; width: 30%; font-size: 8.5pt; }
        .ttd-line { border-bottom: 1px solid #333; height: 38px; }
    </style>
</head>
<body>
    @php
        $isKelasAkhir = str_contains(strtolower($kelas->nama_kelas), '9') ||
                        str_contains(strtolower($kelas->nama_kelas), '12') ||
                        str_contains(strtolower($kelas->nama_kelas), 'ix') ||
                        str_contains(strtolower($kelas->nama_kelas), 'xii');
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
                <strong style="font-size:11pt; text-transform:uppercase;">Rekap Nilai Siswa</strong>
            </div>

            <div class="semester-badge">
                <i class="bi bi-calendar3-week"></i> Semester {{ $semesterLabel }}
            </div>

            <table class="info-table">
                <tr>
                    <td class="label">Nama Siswa</td><td>: <strong>{{ $siswa->nama_lengkap }}</strong></td>
                    <td class="label">Kelas</td><td>: {{ $kelas->nama_kelas }}</td>
                    <td class="label">NIS / NISN</td><td>: {{ $siswa->nis }} / {{ $siswa->nisn }}</td>
                    <td class="label">Tahun Ajaran</td><td>: {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</td>
                </tr>
            </table>

            <table class="data">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:24px;">No</th>
                        <th rowspan="2" style="min-width:110px; text-align:left;">Mata Pelajaran</th>
                        <th colspan="2">Tugas</th>
                        <th colspan="2">Latihan</th>
                        <th colspan="2">UH</th>
                        <th rowspan="2" style="width:38px;">PTS</th>
                        <th rowspan="2" style="width:38px;">PAS</th>
                        <th rowspan="2" class="nilai-akhir" style="width:48px;">N. Akhir</th>
                        <th rowspan="2" style="width:44px;">Predikat</th>
                        <th rowspan="2" style="width:60px;">Status</th>
                    </tr>
                    <tr>
                        <th style="width:30px;">Jml</th>
                        <th class="rata" style="width:36px;">Rata</th>
                        <th style="width:30px;">Jml</th>
                        <th class="rata" style="width:36px;">Rata</th>
                        <th style="width:30px;">Jml</th>
                        <th class="rata" style="width:36px;">Rata</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($mataPelajaranList as $mapel)
                        @php
                            $nilai = $nilaiData[$mapel->id] ?? null;
                            $tugasCount = $latihanCount = $uhCount = 0;
                            if ($nilai) {
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($nilai->{'tugas_'.$i}   !== null) $tugasCount++;
                                    if ($nilai->{'latihan_'.$i} !== null) $latihanCount++;
                                    if ($nilai->{'uh_'.$i}      !== null) $uhCount++;
                                }
                            }
                            $isTuntas = $nilai && $nilai->nilai_akhir !== null && $nilai->nilai_akhir >= 70;
                        @endphp
                        <tr>
                            <td style="text-align:center;">{{ $no++ }}</td>
                            <td>{{ $mapel->nama_mapel }}</td>
                            <td style="text-align:center;">{{ $tugasCount }}/5</td>
                            <td class="rata" style="text-align:center;">{{ $nilai && $nilai->rata_tugas !== null ? number_format($nilai->rata_tugas, 1) : '-' }}</td>
                            <td style="text-align:center;">{{ $latihanCount }}/5</td>
                            <td class="rata" style="text-align:center;">{{ $nilai && $nilai->rata_latihan !== null ? number_format($nilai->rata_latihan, 1) : '-' }}</td>
                            <td style="text-align:center;">{{ $uhCount }}/5</td>
                            <td class="rata" style="text-align:center;">{{ $nilai && $nilai->rata_uh !== null ? number_format($nilai->rata_uh, 1) : '-' }}</td>
                            <td style="text-align:center;">{{ $nilai && $nilai->pts !== null ? number_format($nilai->pts, 0) : '-' }}</td>
                            <td style="text-align:center;">{{ $nilai && $nilai->pas !== null ? number_format($nilai->pas, 0) : '-' }}</td>
                            <td class="nilai-akhir" style="text-align:center;">{{ $nilai && $nilai->nilai_akhir !== null ? number_format($nilai->nilai_akhir, 2) : '-' }}</td>
                            <td style="text-align:center;">{{ $nilai ? $nilai->predikat() : '-' }}</td>
                            <td style="text-align:center;">{{ $nilai && $nilai->nilai_akhir !== null ? ($isTuntas ? 'Tuntas' : 'Blm Tuntas') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($isKelasAkhir)
            <h3 style="margin-top:14px; margin-bottom:6px; font-size:9.5pt;">Penilaian Tingkat Akhir</h3>
            <table class="data">
                <thead>
                    <tr>
                        <th style="width:24px;">No</th>
                        <th style="min-width:130px; text-align:left;">Mata Pelajaran</th>
                        <th style="width:52px;">TO 1</th>
                        <th style="width:52px;">TO 2</th>
                        <th style="width:52px;">TO 3</th>
                        <th style="width:52px;">UPK</th>
                        <th style="width:70px;">Ujian Praktek</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($mataPelajaranList as $mapel)
                        @php $nilai = $nilaiData[$mapel->id] ?? null; @endphp
                        <tr>
                            <td style="text-align:center;">{{ $no++ }}</td>
                            <td>{{ $mapel->nama_mapel }}</td>
                            <td style="text-align:center;">{{ $nilai && $nilai->to_1 !== null ? number_format($nilai->to_1, 0) : '-' }}</td>
                            <td style="text-align:center;">{{ $nilai && $nilai->to_2 !== null ? number_format($nilai->to_2, 0) : '-' }}</td>
                            <td style="text-align:center;">{{ $nilai && $nilai->to_3 !== null ? number_format($nilai->to_3, 0) : '-' }}</td>
                            <td style="text-align:center;">{{ $nilai && $nilai->upk !== null ? number_format($nilai->upk, 0) : '-' }}</td>
                            <td style="text-align:center;">{{ $nilai && $nilai->ujian_praktek !== null ? number_format($nilai->ujian_praktek, 0) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <div class="footer">
                <p class="print-date">Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }}</p>
                <div class="ttd">
                    <div class="ttd-item">
                        <p>Mengetahui, Wali Kelas</p>
                        <div class="ttd-line"></div>
                        <p>( _________________________ )</p>
                    </div>
                    <div class="ttd-item">
                        <p>Orang Tua / Wali Siswa</p>
                        <div class="ttd-line"></div>
                        <p>( _________________________ )</p>
                    </div>
                </div>
            </div>
        </div>{{-- end .page-container --}}
    </div>{{-- end #scaleWrapper --}}

    <script>
        const NATIVE_W = 1058;
        let zoomLevel  = 1; // multiplier di atas baseScale

        function baseScale() {
            // Hitung skala agar container fit ke lebar layar
            const avail = window.innerWidth - 20; // 10px sisi kiri + kanan
            return avail < NATIVE_W ? avail / NATIVE_W : 1;
        }

        function applyScale() {
            const el  = document.getElementById('pageContainer');
            const lbl = document.getElementById('zoomLabel');
            if (!el) return;

            // CSS zoom mempengaruhi layout (berbeda dari transform scale)
            // → overflow/scrollbar wrapper muncul otomatis saat zoom in
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
