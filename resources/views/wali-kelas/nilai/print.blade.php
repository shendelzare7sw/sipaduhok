<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai - {{ $kelas->nama_kelas }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #165fac; padding-bottom: 20px; }
        .header h1 { color: #165fac; font-size: 24px; margin-bottom: 10px; }
        .info-box { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th { padding: 10px; background: #165fac; color: white; border: 1px solid #ccc; text-align: center; font-size: 12px; }
        table td { padding: 8px; border: 1px solid #ccc; text-align: center; }
        .grade-A { background: #dcfce7; color: #065f46; font-weight: bold; }
        .grade-B { background: #dbeafe; color: #1e3a8a; font-weight: bold; }
        .grade-C { background: #fef3c7; color: #92400e; font-weight: bold; }
        .grade-D { background: #fee2e2; color: #991b1b; font-weight: bold; }
        .grade-E { background: #fee2e2; color: #991b1b; font-weight: bold; }
        @media print { body { padding: 10px; } @page { margin: 15mm; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP NILAI SISWA</h1>
        <h2>PKBM House of Knowledge</h2>
    </div>

    <div class="info-box">
        <div><strong>Kelas:</strong> {{ $kelas->nama_kelas }}</div>
        @if($selectedMapel)
            <div><strong>Mata Pelajaran:</strong> {{ $selectedMapel->nama_mapel }}</div>
        @else
            <div><strong>Mata Pelajaran:</strong> Semua Mata Pelajaran</div>
        @endif
        <div><strong>Tahun Ajaran:</strong> {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</div>
        <div><strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap }}</div>
        <div><strong>Dicetak:</strong> {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}</div>
    </div>

    @if($selectedMapel)
        {{-- Nilai untuk 1 Mata Pelajaran --}}
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 40px;">No</th>
                    <th rowspan="2" style="width: 100px;">NIS</th>
                    <th rowspan="2">Nama Siswa</th>
                    <th colspan="3">Komponen Nilai</th>
                    <th rowspan="2" style="width: 100px;">Nilai Akhir</th>
                    <th rowspan="2" style="width: 60px;">Huruf</th>
                    <th rowspan="2">Predikat</th>
                </tr>
                <tr>
                    <th style="width: 80px;">Tugas</th>
                    <th style="width: 80px;">UTS</th>
                    <th style="width: 80px;">UAS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaList as $index => $siswa)
                    @php
                        $nilai = $nilaiData[$siswa->id] ?? null;
                        $predikat = $nilai ? $nilai->nilaiHuruf() : '-';
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $siswa->nis }}</td>
                        <td style="text-align: left;">{{ $siswa->nama_lengkap }}</td>
                        <td>{{ $nilai ? number_format($nilai->nilai_tugas, 2) : '-' }}</td>
                        <td>{{ $nilai ? number_format($nilai->nilai_uts, 2) : '-' }}</td>
                        <td>{{ $nilai ? number_format($nilai->nilai_uas, 2) : '-' }}</td>
                        <td style="font-weight: bold; font-size: 14px;">
                            {{ $nilai ? number_format($nilai->nilai_akhir, 2) : '-' }}
                        </td>
                        <td class="grade-{{ $predikat }}">{{ $predikat }}</td>
                        <td>{{ $nilai ? $nilai->predikat() : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Statistik --}}
        <div style="margin-top: 30px; padding: 15px; background: #f0f9ff; border-radius: 8px;">
            <h3 style="margin-bottom: 15px; color: #165fac;">Statistik Kelas</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div style="text-align: center;">
                    <strong style="display: block; font-size: 24px; color: #165fac;">{{ number_format($rataRataKelas, 2) }}</strong>
                    <div>Rata-rata Kelas</div>
                </div>
                <div style="text-align: center;">
                    <strong style="display: block; font-size: 24px; color: #10b981;">{{ number_format($nilaiTertinggi, 2) }}</strong>
                    <div>Nilai Tertinggi</div>
                </div>
                <div style="text-align: center;">
                    <strong style="display: block; font-size: 24px; color: #ef4444;">{{ number_format($nilaiTerendah, 2) }}</strong>
                    <div>Nilai Terendah</div>
                </div>
            </div>
        </div>
    @else
        {{-- Ringkasan Semua Mata Pelajaran --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 100px;">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width: 120px;">Rata-rata Nilai</th>
                    <th style="width: 100px;">Peringkat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaList as $index => $siswa)
                    @php
                        $nilaiSiswa = $nilaiData[$siswa->id] ?? [];
                        $total = 0;
                        $count = 0;
                        foreach($nilaiSiswa as $n) {
                            $total += $n->nilai_akhir;
                            $count++;
                        }
                        $rataRata = $count > 0 ? $total / $count : 0;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $siswa->nis }}</td>
                        <td style="text-align: left;">{{ $siswa->nama_lengkap }}</td>
                        <td style="font-weight: bold;">{{ number_format($rataRata, 2) }}</td>
                        <td>{{ $index + 1 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div style="margin-top: 40px; text-align: right;">
        <div style="display: inline-block; text-align: center; min-width: 200px;">
            <div>Tangerang Selatan, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            <div>Wali Kelas</div>
            <div style="margin-top: 60px; border-top: 1px solid #333; padding-top: 5px;">
                {{ $kelas->waliKelas->nama_lengkap }}
            </div>
        </div>
    </div>

    <script>window.print();</script>
</body>
</html>