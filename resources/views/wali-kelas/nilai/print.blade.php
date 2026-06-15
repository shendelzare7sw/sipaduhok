<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai - {{ $siswa->nama_lengkap }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/nilai/print.css') }}">
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
    <script src="{{ asset('js/wali-kelas/nilai/print.js') }}"></script>
</body>
</html>
