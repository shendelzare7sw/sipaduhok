<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai {{ $selectedMapel->nama_mapel }} - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; background: #f0f2f5; color: #000; }

        @media screen {
            .ctrl-bar {
                position: fixed; top: 0; left: 0; right: 0; height: 46px;
                background: #2c3340; color: #fff; display: flex; align-items: center;
                justify-content: space-between; padding: 0 14px; z-index: 9999; gap: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,.4);
            }
            .btn-c {
                background: rgba(255,255,255,.08); color: #fff; border: 1px solid rgba(255,255,255,.28);
                padding: 7px 14px; border-radius: 5px; cursor: pointer; font-size: 14px;
                font-weight: 700; line-height: 1; display: inline-flex; align-items: center; gap: 5px;
            }
            .btn-print { background: #dc3545 !important; border-color: #dc3545 !important; }
            .zoom-group { display: flex; align-items: center; gap: 6px; }
            .zoom-label { min-width: 46px; text-align: center; font-size: 12px; font-weight: 700; color: #d0d0d0; }
            body { padding-top: 54px; }
            #scaleWrapper { display: flex; justify-content: center; overflow-x: auto; padding: 20px 10px 50px; min-height: calc(100vh - 54px); }
            .page-container { background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,.15); width: 1058px; min-height: 750px; padding: 18px 22px; flex-shrink: 0; }
        }

        @media print {
            @page { size: A4 landscape; margin: 6mm; }
            .ctrl-bar { display: none !important; }
            body { background: #fff; padding: 0; font-size: 8.5pt; }
            #scaleWrapper { display: block; padding: 0; }
            .page-container { box-shadow: none; padding: 0; width: 100%; min-height: auto; zoom: 1 !important; }
            tr { page-break-inside: avoid; }
        }

        .info-bar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #ccc; font-size: 8.5pt;
        }
        .semester-badge {
            display: inline-block; background: #e3f2fd; border: 1px solid #90caf9;
            color: #1565c0; border-radius: 4px; padding: 2px 10px; font-size: 8pt; font-weight: bold;
        }
        table.data { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        table.data th, table.data td { border: 1px solid #444; padding: 4px 3px; vertical-align: middle; }
        table.data th { background: #e9ecef; text-align: center; font-weight: bold; font-size: 8pt; text-transform: uppercase; }
        .rata { background: #e8f5e9; }
        .nilai-akhir { background: #e3f2fd; font-weight: bold; }
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

    <div class="ctrl-bar">
        <div class="zoom-group">
            <button class="btn-c" onclick="zoomOut()" title="Perkecil">-</button>
            <span class="zoom-label" id="zoomLabel">100%</span>
            <button class="btn-c" onclick="zoomIn()" title="Perbesar">+</button>
            <button class="btn-c" onclick="fitScreen()" title="Sesuaikan layar" style="font-size:11px; padding:7px 10px;">Fit</button>
        </div>
        <button class="btn-c btn-print" onclick="window.print()">
            <i class="bi bi-printer-fill"></i> Cetak / PDF
        </button>
    </div>

    <div id="scaleWrapper">
        <div class="page-container" id="pageContainer">
            @include('partials.print-header', ['cabang' => $cabang ?? null])

            <div style="text-align:center; margin-bottom:8px;">
                <strong style="font-size:11pt; text-transform:uppercase;">Rekap Nilai {{ $selectedMapel->nama_mapel }}</strong>
            </div>

            <div class="info-bar">
                <div>
                    <strong>Kelas:</strong> {{ $kelas->nama_kelas }} &nbsp;|&nbsp;
                    <strong>Tahun Ajaran:</strong> {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }} &nbsp;|&nbsp;
                    <strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap ?? ($wali->nama_lengkap ?? '-') }}
                </div>
                <span class="semester-badge">
                    <i class="bi bi-calendar3-week"></i> Semester {{ $semesterLabel }}
                </span>
            </div>

            <table class="data">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:28px;">No</th>
                        <th rowspan="2" style="width:70px;">NIS</th>
                        <th rowspan="2" style="min-width:160px; text-align:left;">Nama Siswa</th>
                        <th colspan="2">Tugas</th>
                        <th colspan="2">Latihan</th>
                        <th colspan="2">UH</th>
                        <th rowspan="2" style="width:45px;">PTS</th>
                        <th rowspan="2" style="width:45px;">PAS</th>
                        <th rowspan="2" class="nilai-akhir" style="width:58px;">N. Akhir</th>
                        <th rowspan="2" style="width:55px;">Predikat</th>
                        <th rowspan="2" style="width:70px;">Status</th>
                    </tr>
                    <tr>
                        <th style="width:34px;">Jml</th>
                        <th class="rata" style="width:42px;">Rata</th>
                        <th style="width:34px;">Jml</th>
                        <th class="rata" style="width:42px;">Rata</th>
                        <th style="width:34px;">Jml</th>
                        <th class="rata" style="width:42px;">Rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaList as $index => $siswa)
                        @php
                            $nilai = $nilaiData[$siswa->id] ?? null;
                            $tugasCount = $latihanCount = $uhCount = 0;
                            if ($nilai) {
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($nilai->{'tugas_'.$i} !== null) $tugasCount++;
                                    if ($nilai->{'latihan_'.$i} !== null) $latihanCount++;
                                    if ($nilai->{'uh_'.$i} !== null) $uhCount++;
                                }
                            }
                            $isTuntas = $nilai && $nilai->nilai_akhir !== null && $nilai->nilai_akhir >= 70;
                        @endphp
                        <tr>
                            <td style="text-align:center;">{{ $index + 1 }}</td>
                            <td style="text-align:center;">{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama_lengkap }}</td>
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

            <div class="footer">
                <p class="print-date">Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</p>
                <div class="signature-area">
                    <p>Wali Kelas</p>
                    <div class="signature-line"></div>
                    <p>{{ $kelas->waliKelas->nama_lengkap ?? ($wali->nama_lengkap ?? '_________________________') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const NATIVE_W = 1058;
        let zoomLevel = 1;

        function baseScale() {
            const avail = window.innerWidth - 20;
            return avail < NATIVE_W ? avail / NATIVE_W : 1;
        }

        function applyScale() {
            const el = document.getElementById('pageContainer');
            const lbl = document.getElementById('zoomLabel');
            if (!el) return;
            const scale = Math.round(baseScale() * zoomLevel * 1000) / 1000;
            el.style.zoom = scale;
            if (lbl) lbl.textContent = Math.round(scale * 100) + '%';
        }

        function zoomIn() { zoomLevel = Math.min(+(zoomLevel + 0.15).toFixed(2), 4); applyScale(); }
        function zoomOut() { zoomLevel = Math.max(+(zoomLevel - 0.15).toFixed(2), 0.1); applyScale(); }
        function fitScreen() { zoomLevel = 1; applyScale(); }

        window.addEventListener('load', applyScale);
        window.addEventListener('resize', () => { zoomLevel = 1; applyScale(); });
    </script>
</body>
</html>
