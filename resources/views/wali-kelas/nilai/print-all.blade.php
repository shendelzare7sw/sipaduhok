<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai Kelas - {{ $kelas->nama_kelas }}</title>
    <style>
        /* CSS Reset & Basics */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 8pt; 
            padding: 20px;
            background: #fff;
            color: #000;
        }

        /* Screen Only (Preview Mode) */
        @media screen {
            body {
                background: #f0f2f5;
                padding: 40px;
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: 100vh;
            }
            .page-container {
                background: white;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                padding: 10mm;
                max-width: 297mm; /* A4 Landscape Width */
                width: 100%;
                margin-top: 20px;
            }
            .print-button {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #dc3545;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                font-weight: bold;
                cursor: pointer;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                z-index: 9999;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .print-button:hover { background: #bb2d3b; }
        }

        /* Print Only */
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { padding: 0; background: white; }
            .page-container { box-shadow: none; padding: 0; margin: 0; max-width: none; width: 100%; }
            .print-button { display: none; }
            .no-print { display: none; }
        }

        /* Report Styles */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 14pt; margin-bottom: 5px; text-transform: uppercase; font-weight: bold; }
        .header h2 { font-size: 11pt; font-weight: normal; margin-bottom: 5px; color: #333; }
        
        .info { margin-bottom: 15px; font-size: 9pt; display: flex; justify-content: space-between; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .info-item { margin-right: 20px; }
        
        table.data { width: 100%; border-collapse: collapse; font-size: 8pt; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #333; padding: 6px 4px; vertical-align: middle; }
        table.data th { background: #e9ecef; text-align: center; font-weight: bold; text-transform: uppercase; font-size: 7.5pt; }
        table.data tr:hover { background-color: #f8f9fa; }
        
        .text-center { text-align: center; }
        .rata { background: #e8f5e9; }
        .nilai-akhir { background: #e3f2fd; font-weight: bold; }
        
        .footer { margin-top: 20px; text-align: right; font-size: 8pt; font-style: italic; color: #666; }
        .signature-area { margin-top: 40px; text-align: right; margin-right: 20px; display: inline-block; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 60px; width: 200px; }
    </style>
</head>
<body>
    <!-- Font Awesome for Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Print Button -->
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>

    <div class="page-container">
        <div class="header">
            <h1>REKAPITULASI NILAI AKADEMIK</h1>
            <h2>{{ config('app.name', 'SIPADUHOK') }}</h2>
        </div>


    <div class="info">
        <div>
            <span class="info-item"><strong>Kelas:</strong> {{ $kelas->nama_kelas }}</span>
            <span class="info-item"><strong>Tahun Ajaran:</strong> {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</span>
        </div>
        <div>
            <span class="info-item"><strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap ?? '-' }}</span>
        </div>
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
                <th rowspan="2" class="nilai-akhir" style="width: 50px;">N. Akhir</th>
                <th rowspan="2" style="width: 55px;">Status</th>
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

                    <td style="text-align: center;">{{ $avgNilaiAkhir !== null ? ($isTuntas ? 'Tuntas' : 'Blm Tuntas') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</div>
        
        <div class="signature-area">
            <p>Mengetahui,</p>
            <p>Wali Kelas</p>
            <div class="signature-line">{{ $kelas->waliKelas->nama_lengkap ?? '...................................' }}</div>
        </div>
    </div>
    
    </div> <!-- End Page Container -->
</body>
</html>
