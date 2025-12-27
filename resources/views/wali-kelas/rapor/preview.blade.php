<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Rapor - {{ $rapor->siswa->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 30px; background: #f5f5f5; }
        .rapor-container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #165fac; padding-bottom: 20px; }
        .header h1 { color: #165fac; font-size: 28px; margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        .info-item { margin-bottom: 10px; }
        .info-item strong { display: block; color: #666; font-size: 12px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th { padding: 12px; background: #165fac; color: white; border: 1px solid #ccc; text-align: left; font-size: 13px; }
        table td { padding: 10px; border: 1px solid #ccc; }
        .catatan-box { margin-top: 30px; padding: 20px; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 4px; }
        .kehadiran-box { margin-top: 20px; padding: 15px; background: #f0f9ff; border-radius: 8px; }
        .signature-section { margin-top: 40px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 40px; }
        .signature-box { text-align: center; }
        .signature-line { margin-top: 60px; border-top: 1px solid #333; padding-top: 5px; }
        .btn-print { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background: #165fac; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.2); }
        .btn-print:hover { background: #0f4c8a; }
        @media print { body { padding: 0; background: white; } .rapor-container { box-shadow: none; } .btn-print { display: none; } }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">
        🖨️ Cetak Rapor
    </button>

    <div class="rapor-container">
        <div class="header">
            <h1>LAPORAN PENCAPAIAN KOMPETENSI PESERTA DIDIK</h1>
            <h2>PKBM House of Knowledge</h2>
            <p>Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan</p>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-item">
                    <strong>Nama Peserta Didik</strong>
                    <div style="font-size: 18px; font-weight: bold;">{{ $rapor->siswa->nama_lengkap }}</div>
                </div>
                <div class="info-item">
                    <strong>NISN / NIS</strong>
                    <div>{{ $rapor->siswa->nisn }} / {{ $rapor->siswa->nis }}</div>
                </div>
                <div class="info-item">
                    <strong>Tempat, Tanggal Lahir</strong>
                    <div>{{ $rapor->siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($rapor->siswa->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <strong>Kelas / Fase</strong>
                    <div style="font-size: 16px; font-weight: bold;">{{ $rapor->kelas->nama_kelas }}</div>
                </div>
                <div class="info-item">
                    <strong>Semester</strong>
                    <div>{{ ucfirst($rapor->semester) }}</div>
                </div>
                <div class="info-item">
                    <strong>Tahun Ajaran</strong>
                    <div>{{ $rapor->tahunAjaran->nama_tahun_ajaran }}</div>
                </div>
            </div>
        </div>

        <h3 style="color: #165fac; margin: 30px 0 15px 0; border-bottom: 2px solid #165fac; padding-bottom: 8px;">
            PENCAPAIAN KOMPETENSI
        </h3>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 100px; text-align: center;">Nilai</th>
                    <th style="width: 80px; text-align: center;">Predikat</th>
                    <th>Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                @php $totalNilai = 0; $jumlahMapel = 0; @endphp
                @foreach($rapor->raporNilai as $index => $raporNilai)
                    @php
                        $totalNilai += $raporNilai->nilai_angka;
                        $jumlahMapel++;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td><strong>{{ $raporNilai->mataPelajaran->nama_mapel }}</strong></td>
                        <td style="text-align: center; background: #f0f9ff;">
                            <strong style="font-size: 16px;">{{ number_format($raporNilai->nilai_angka, 0) }}</strong>
                        </td>
                        <td style="text-align: center;">
                            <strong>{{ $raporNilai->nilai_huruf }}</strong>
                        </td>
                        <td style="font-size: 12px;">{{ $raporNilai->deskripsi ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f3f4f6;">
                    <td colspan="2" style="text-align: right; font-weight: bold; padding: 16px;">RATA-RATA:</td>
                    <td style="text-align: center; font-weight: bold; font-size: 18px; color: #165fac;">
                        {{ $jumlahMapel > 0 ? number_format($totalNilai / $jumlahMapel, 0) : '0' }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <div class="kehadiran-box">
            <h4 style="color: #165fac; margin-bottom: 15px;">KETIDAKHADIRAN</h4>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div>Sakit: <strong>{{ $rapor->jumlah_sakit }} hari</strong></div>
                <div>Izin: <strong>{{ $rapor->jumlah_izin }} hari</strong></div>
                <div>Tanpa Keterangan: <strong>{{ $rapor->jumlah_alpha }} hari</strong></div>
            </div>
        </div>

        @if($rapor->catatan_wali_kelas)
            <div class="catatan-box">
                <h4 style="color: #f59e0b; margin-bottom: 10px;">CATATAN WALI KELAS</h4>
                <p style="line-height: 1.8;">{{ $rapor->catatan_wali_kelas }}</p>
            </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <div>Orang Tua / Wali</div>
                <div class="signature-line">( ......................................................... )</div>
            </div>
            <div class="signature-box">
                <div>Tangerang Selatan, {{ $rapor->tanggal_terbit ? \Carbon\Carbon::parse($rapor->tanggal_terbit)->locale('id')->isoFormat('D MMMM YYYY') : now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                <div>Wali Kelas</div>
                <div class="signature-line">{{ $rapor->kelas->waliKelas->nama_lengkap }}</div>
            </div>
        </div>

        <div style="margin-top: 40px; text-align: center;">
            <div>Mengetahui,</div>
            <div style="margin-top: 10px; font-weight: bold;">Ketua PKBM House of Knowledge</div>
            <div style="margin-top: 70px; border-top: 1px solid #333; display: inline-block; padding-top: 5px; min-width: 250px;">
                Fransisda Tiodora Ferdiansyah, S.Psi., MM
            </div>
        </div>
    </div>
</body>
</html>
