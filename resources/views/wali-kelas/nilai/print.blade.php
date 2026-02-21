<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai - {{ $siswa->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9pt; padding: 10mm; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { font-size: 12pt; margin-bottom: 3px; }
        .header h2 { font-size: 10pt; font-weight: normal; margin-bottom: 5px; }
        .info-table { margin-bottom: 15px; font-size: 8pt; }
        .info-table td { padding: 2px 5px; }
        .info-table .label { font-weight: bold; width: 100px; }
        table.data { width: 100%; border-collapse: collapse; font-size: 9pt; }
        table.data th, table.data td { border: 1px solid #333; padding: 4px 3px; }
        table.data th { background: #e9ecef; text-align: center; font-weight: bold; }
        table.data .rata { background: #d4edda; }
        table.data .nilai-akhir { background: #c3e6cb; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 8pt; }
        .ttd { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd-item { text-align: center; width: 30%; }
        .ttd-line { border-bottom: 1px solid #333; height: 40px; }
        @page { size: landscape; margin: 10mm; }
    </style>
</head>
<body>
    @php
        $isKelasAkhir = str_contains(strtolower($kelas->nama_kelas), '9') || 
                        str_contains(strtolower($kelas->nama_kelas), '12') ||
                        str_contains(strtolower($kelas->nama_kelas), 'ix') ||
                        str_contains(strtolower($kelas->nama_kelas), 'xii');
    @endphp

    @include('partials.print-header', ['cabang' => $cabang ?? null])

    <div style="text-align: center; margin-bottom: 15px;"><strong style="font-size: 11pt;">REKAP NILAI SISWA</strong></div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Siswa</td><td>: {{ $siswa->nama_lengkap }}</td>
            <td class="label">Kelas</td><td>: {{ $kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td class="label">NIS / NISN</td><td>: {{ $siswa->nis }} / {{ $siswa->nisn }}</td>
            <td class="label">Tahun Ajaran</td><td>: {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 120px;">Mata Pelajaran</th>
                <th colspan="2">Tugas</th>
                <th colspan="2">Latihan</th>
                <th colspan="2">UH</th>
                <th rowspan="2" style="width: 35px;">PTS</th>
                <th rowspan="2" style="width: 35px;">PAS</th>
                <th rowspan="2" class="nilai-akhir" style="width: 45px;">N. Akhir</th>
                <th rowspan="2" style="width: 45px;">Predikat</th>
                <th rowspan="2" style="width: 55px;">Status</th>
            </tr>
            <tr>
                <th style="width: 30px;">Jml</th>
                <th class="rata" style="width: 35px;">Rata</th>
                <th style="width: 30px;">Jml</th>
                <th class="rata" style="width: 35px;">Rata</th>
                <th style="width: 30px;">Jml</th>
                <th class="rata" style="width: 35px;">Rata</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($mataPelajaranList as $mapel)
                @php
                    $nilai = $nilaiData[$mapel->id] ?? null;
                    
                    $tugasCount = 0; $latihanCount = 0; $uhCount = 0;
                    if ($nilai) {
                        for ($i = 1; $i <= 5; $i++) {
                            if ($nilai->{'tugas_'.$i} !== null) $tugasCount++;
                            if ($nilai->{'latihan_'.$i} !== null) $latihanCount++;
                            if ($nilai->{'uh_'.$i} !== null) $uhCount++;
                        }
                    }
                    $kkm = 70;
                    $isTuntas = $nilai && $nilai->nilai_akhir >= $kkm;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>{{ $mapel->nama_mapel }}</td>
                    <td style="text-align: center;">{{ $tugasCount }}/5</td>
                    <td class="rata" style="text-align: center;">{{ $nilai && $nilai->rata_tugas !== null ? number_format($nilai->rata_tugas, 1) : '-' }}</td>
                    <td style="text-align: center;">{{ $latihanCount }}/5</td>
                    <td class="rata" style="text-align: center;">{{ $nilai && $nilai->rata_latihan !== null ? number_format($nilai->rata_latihan, 1) : '-' }}</td>
                    <td style="text-align: center;">{{ $uhCount }}/5</td>
                    <td class="rata" style="text-align: center;">{{ $nilai && $nilai->rata_uh !== null ? number_format($nilai->rata_uh, 1) : '-' }}</td>
                    <td style="text-align: center;">{{ $nilai && $nilai->pts !== null ? number_format($nilai->pts, 0) : '-' }}</td>
                    <td style="text-align: center;">{{ $nilai && $nilai->pas !== null ? number_format($nilai->pas, 0) : '-' }}</td>
                    <td class="nilai-akhir" style="text-align: center;">{{ $nilai && $nilai->nilai_akhir !== null ? number_format($nilai->nilai_akhir, 2) : '-' }}</td>
                    <td style="text-align: center;">{{ $nilai ? $nilai->predikat() : '-' }}</td>
                    <td style="text-align: center;">{{ $nilai && $nilai->nilai_akhir !== null ? ($isTuntas ? 'Tuntas' : 'Blm Tuntas') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($isKelasAkhir)
    <h3 style="margin-top: 20px; margin-bottom: 10px; font-size: 10pt;">Penilaian Tingkat Akhir</h3>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 150px;">Mata Pelajaran</th>
                <th style="width: 50px;">TO 1</th>
                <th style="width: 50px;">TO 2</th>
                <th style="width: 50px;">TO 3</th>
                <th style="width: 50px;">UPK</th>
                <th style="width: 70px;">Ujian Praktek</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($mataPelajaranList as $mapel)
                @php $nilai = $nilaiData[$mapel->id] ?? null; @endphp
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>{{ $mapel->nama_mapel }}</td>
                    <td style="text-align: center;">{{ $nilai && $nilai->to_1 !== null ? number_format($nilai->to_1, 0) : '-' }}</td>
                    <td style="text-align: center;">{{ $nilai && $nilai->to_2 !== null ? number_format($nilai->to_2, 0) : '-' }}</td>
                    <td style="text-align: center;">{{ $nilai && $nilai->to_3 !== null ? number_format($nilai->to_3, 0) : '-' }}</td>
                    <td style="text-align: center;">{{ $nilai && $nilai->upk !== null ? number_format($nilai->upk, 0) : '-' }}</td>
                    <td style="text-align: center;">{{ $nilai && $nilai->ujian_praktek !== null ? number_format($nilai->ujian_praktek, 0) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <small>Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }}</small>
    </div>

    <div class="ttd">
        <div class="ttd-item">
            <p>Mengetahui,</p>
            <p>Wali Kelas</p>
            <div class="ttd-line"></div>
            <p>(_____________________)</p>
        </div>
        <div class="ttd-item">
            <p>Orang Tua / Wali</p>
            <div class="ttd-line"></div>
            <p>(_____________________)</p>
        </div>
    </div>
</body>
</html>