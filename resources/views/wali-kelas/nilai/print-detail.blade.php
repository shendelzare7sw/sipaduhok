<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai {{ $selectedMapel->nama_mapel }} - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/nilai/print-detail.css') }}">
</head>
<body>
    @php
        $semesterLabel = ucfirst($semester ?? 'genap');
    @endphp

    <div class="ctrl-bar">
        <div class="zoom-group">
            <button class="btn-c" data-zoom-action="out" title="Perkecil">-</button>
            <span class="zoom-label" id="zoomLabel">100%</span>
            <button class="btn-c" data-zoom-action="in" title="Perbesar">+</button>
            <button class="btn-c" data-zoom-action="fit" title="Sesuaikan layar" style="font-size:11px; padding:7px 10px;">Fit</button>
        </div>
        <button class="btn-c btn-print" data-print-page>
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
    <script src="{{ asset('js/wali-kelas/nilai/print-detail.js') }}"></script>
</body>
</html>
