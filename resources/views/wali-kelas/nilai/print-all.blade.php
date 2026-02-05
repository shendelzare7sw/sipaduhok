<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai Kelas - {{ $kelas->nama_kelas }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 8pt; padding: 8mm; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { font-size: 12pt; margin-bottom: 3px; }
        .header h2 { font-size: 10pt; font-weight: normal; margin-bottom: 5px; }
        .info { margin-bottom: 10px; font-size: 9pt; }
        table.data { width: 100%; border-collapse: collapse; font-size: 8pt; }
        table.data th, table.data td { border: 1px solid #333; padding: 3px 2px; }
        table.data th { background: #e9ecef; text-align: center; font-weight: bold; }
        table.data .rata { background: #d4edda; }
        table.data .nilai-akhir { background: #c3e6cb; font-weight: bold; }
        .footer { margin-top: 15px; font-size: 7pt; }
        @page { size: landscape; margin: 8mm; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP NILAI SELURUH SISWA</h1>
        <h2>{{ config('app.name', 'SIPADUHOK') }}</h2>
    </div>

    <div class="info">
        <strong>Kelas:</strong> {{ $kelas->nama_kelas }} | 
        <strong>Tahun Ajaran:</strong> {{ $kelas->tahunAjaran->nama_tahun_ajaran }} |
        <strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap ?? '-' }}
    </div>

    <table class="data">
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">No</th>
                <th rowspan="2" style="width: 30px;">NIS</th>
                <th rowspan="2" style="width: 100px;">Nama Siswa</th>
                <th colspan="2">Tugas</th>
                <th colspan="2">Latihan</th>
                <th colspan="2">UH</th>
                <th rowspan="2" style="width: 30px;">PTS</th>
                <th rowspan="2" style="width: 30px;">PAS</th>
                <th rowspan="2" class="nilai-akhir" style="width: 40px;">N. Akhir</th>
                <th rowspan="2" style="width: 35px;">Pred</th>
                <th rowspan="2" style="width: 45px;">Status</th>
            </tr>
            <tr>
                <th style="width: 25px;">Jml</th>
                <th class="rata" style="width: 30px;">Rata</th>
                <th style="width: 25px;">Jml</th>
                <th class="rata" style="width: 30px;">Rata</th>
                <th style="width: 25px;">Jml</th>
                <th class="rata" style="width: 30px;">Rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswaList as $index => $siswa)
                @php
                    // Aggregate all nilai for this siswa across mapel
                    $nilaiSiswa = $allNilaiData->where('siswa_id', $siswa->id);
                    
                    $totalTugasCount = 0;
                    $totalLatihanCount = 0;
                    $totalUhCount = 0;
                    $totalRataTugas = [];
                    $totalRataLatihan = [];
                    $totalRataUh = [];
                    $totalPts = [];
                    $totalPas = [];
                    $totalNilaiAkhir = [];
                    
                    foreach ($nilaiSiswa as $n) {
                        for ($i = 1; $i <= 5; $i++) {
                            if ($n->{'tugas_'.$i} !== null) $totalTugasCount++;
                            if ($n->{'latihan_'.$i} !== null) $totalLatihanCount++;
                            if ($n->{'uh_'.$i} !== null) $totalUhCount++;
                        }
                        if ($n->rata_tugas !== null) $totalRataTugas[] = $n->rata_tugas;
                        if ($n->rata_latihan !== null) $totalRataLatihan[] = $n->rata_latihan;
                        if ($n->rata_uh !== null) $totalRataUh[] = $n->rata_uh;
                        if ($n->pts !== null) $totalPts[] = $n->pts;
                        if ($n->pas !== null) $totalPas[] = $n->pas;
                        if ($n->nilai_akhir !== null) $totalNilaiAkhir[] = $n->nilai_akhir;
                    }
                    
                    $avgRataTugas = count($totalRataTugas) > 0 ? array_sum($totalRataTugas) / count($totalRataTugas) : null;
                    $avgRataLatihan = count($totalRataLatihan) > 0 ? array_sum($totalRataLatihan) / count($totalRataLatihan) : null;
                    $avgRataUh = count($totalRataUh) > 0 ? array_sum($totalRataUh) / count($totalRataUh) : null;
                    $avgPts = count($totalPts) > 0 ? array_sum($totalPts) / count($totalPts) : null;
                    $avgPas = count($totalPas) > 0 ? array_sum($totalPas) / count($totalPas) : null;
                    $avgNilaiAkhir = count($totalNilaiAkhir) > 0 ? array_sum($totalNilaiAkhir) / count($totalNilaiAkhir) : null;
                    
                    $kkm = 70;
                    $isTuntas = $avgNilaiAkhir !== null && $avgNilaiAkhir >= $kkm;
                    
                    // Predikat berdasarkan rata-rata nilai akhir
                    $predikat = '-';
                    if ($avgNilaiAkhir !== null) {
                        if ($avgNilaiAkhir >= 90) $predikat = 'A';
                        elseif ($avgNilaiAkhir >= 80) $predikat = 'B';
                        elseif ($avgNilaiAkhir >= 70) $predikat = 'C';
                        else $predikat = 'D';
                    }
                    
                    $mapelCount = count($mataPelajaranList ?? []);
                    $maxCount = $mapelCount * 5;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center;">{{ $siswa->nis }}</td>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td style="text-align: center;">{{ $totalTugasCount }}/{{ $maxCount }}</td>
                    <td class="rata" style="text-align: center;">{{ $avgRataTugas !== null ? number_format($avgRataTugas, 1) : '-' }}</td>
                    <td style="text-align: center;">{{ $totalLatihanCount }}/{{ $maxCount }}</td>
                    <td class="rata" style="text-align: center;">{{ $avgRataLatihan !== null ? number_format($avgRataLatihan, 1) : '-' }}</td>
                    <td style="text-align: center;">{{ $totalUhCount }}/{{ $maxCount }}</td>
                    <td class="rata" style="text-align: center;">{{ $avgRataUh !== null ? number_format($avgRataUh, 1) : '-' }}</td>
                    <td style="text-align: center;">{{ $avgPts !== null ? number_format($avgPts, 0) : '-' }}</td>
                    <td style="text-align: center;">{{ $avgPas !== null ? number_format($avgPas, 0) : '-' }}</td>
                    <td class="nilai-akhir" style="text-align: center;">{{ $avgNilaiAkhir !== null ? number_format($avgNilaiAkhir, 2) : '-' }}</td>
                    <td style="text-align: center;">{{ $predikat }}</td>
                    <td style="text-align: center;">{{ $avgNilaiAkhir !== null ? ($isTuntas ? 'Tuntas' : 'Blm Tuntas') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <small>Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }}</small>
    </div>
</body>
</html>
