<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Semua Mata Pelajaran - {{ $kelas->nama_kelas }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #165fac; padding-bottom: 20px; }
        .header h1 { color: #165fac; font-size: 22px; margin-bottom: 10px; }
        .header h2 { color: #666; font-size: 14px; margin: 0; }
        .info-box { margin-bottom: 20px; padding: 12px; background: #f8f9fa; border-radius: 8px; font-size: 10px; }
        .info-box div { margin-bottom: 4px; }
        
        .page-break { page-break-after: always; }
        
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { 
            padding: 6px; 
            background: #165fac; 
            color: white; 
            border: 1px solid #ccc; 
            text-align: center; 
            font-size: 10px;
            font-weight: bold;
        }
        table td { 
            padding: 5px; 
            border: 1px solid #ccc; 
            text-align: center; 
            font-size: 9px;
        }
        table td.nama { text-align: left; }
        
        .grade-A { background: #dcfce7; color: #065f46; font-weight: bold; }
        .grade-B { background: #dbeafe; color: #1e3a8a; font-weight: bold; }
        .grade-C { background: #fef3c7; color: #92400e; font-weight: bold; }
        .grade-D { background: #fee2e2; color: #991b1b; font-weight: bold; }
        .grade-E { background: #fee2e2; color: #991b1b; font-weight: bold; }
        
        .mapel-header { 
            background: #e0e7ff; 
            padding: 8px; 
            margin-top: 15px; 
            font-weight: bold; 
            color: #165fac;
            border-radius: 4px;
        }
        
        .footer { 
            margin-top: 40px; 
            text-align: right; 
            font-size: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        .footer div { text-align: center; }
        .signature-line { border-top: 1px solid #333; padding-top: 5px; min-height: 60px; }
        
        @media print { 
            body { padding: 10px; } 
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP NILAI SEMUA MATA PELAJARAN</h1>
        <h2>PKBM House of Knowledge</h2>
    </div>

    <div class="info-box">
        <div><strong>Kelas:</strong> {{ $kelas->nama_kelas }}</div>
        <div><strong>Tahun Ajaran:</strong> {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</div>
        <div><strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap }}</div>
        <div><strong>Dicetak:</strong> {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}</div>
    </div>

    @foreach($mataPelajaranList as $mapel)
        <div class="mapel-header">
            {{ $mapel->nama_mapel }}
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 70px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 50px;">Tugas</th>
                    <th style="width: 50px;">UTS</th>
                    <th style="width: 50px;">UAS</th>
                    <th style="width: 60px;">Akhir</th>
                    <th style="width: 40px;">Huruf</th>
                    <th style="width: 70px;">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaList as $index => $siswa)
                    @php
                        $nilai = $nilaiData[$siswa->id][$mapel->id] ?? null;
                        $predikat = $nilai ? $nilai->nilaiHuruf() : '-';
                        $status = $nilai && $nilai->nilai_akhir >= 70 ? 'Tuntas' : 'Belum Tuntas';
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $siswa->nis }}</td>
                        <td class="nama">{{ $siswa->nama_lengkap }}</td>
                        <td>{{ $nilai && $nilai->nilai_tugas ? number_format($nilai->nilai_tugas, 1) : '-' }}</td>
                        <td>{{ $nilai && $nilai->nilai_uts ? number_format($nilai->nilai_uts, 1) : '-' }}</td>
                        <td>{{ $nilai && $nilai->nilai_uas ? number_format($nilai->nilai_uas, 1) : '-' }}</td>
                        <td style="font-weight: bold;">
                            {{ $nilai && $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 1) : '-' }}
                        </td>
                        <td class="grade-{{ $predikat }}">{{ $predikat }}</td>
                        <td>{{ $status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer">
        <div>
            <div>Mengetahui,</div>
            <div style="margin-top: 8px; font-weight: bold;">Kepala Sekolah</div>
            <div class="signature-line"></div>
        </div>
        <div>
            <div>Tangerang Selatan, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            <div style="margin-top: 8px; font-weight: bold;">Wali Kelas</div>
            <div class="signature-line">{{ $kelas->waliKelas->nama_lengkap }}</div>
        </div>
    </div>
</body>
</html>
