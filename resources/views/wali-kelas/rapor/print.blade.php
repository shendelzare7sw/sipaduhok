<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor - {{ $rapor->siswa->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 20px; }
        .header h1 { font-size: 20px; margin-bottom: 8px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; }
        .header p { font-size: 11px; }
        .info-section { margin: 20px 0; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { width: 200px; }
        .info-colon { width: 20px; }
        .info-value { flex: 1; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { padding: 8px; border: 1px solid #000; font-size: 11px; }
        table th { background: #f0f0f0; font-weight: bold; text-align: left; }
        .text-center { text-align: center; }
        .catatan-section { margin: 20px 0; padding: 15px; border: 1px solid #000; }
        .kehadiran-section { margin: 20px 0; }
        .kehadiran-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .signature-section { margin-top: 40px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 60px; }
        .signature-box { text-align: center; }
        .signature-line { margin-top: 70px; border-top: 1px solid #000; padding-top: 5px; display: inline-block; min-width: 200px; }
        .ketua-section { margin-top: 40px; text-align: center; }
        @media print {
            body { padding: 15mm; }
            @page { margin: 15mm; size: A4; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENCAPAIAN KOMPETENSI PESERTA DIDIK</h1>
        <h2>PKBM HOUSE OF KNOWLEDGE</h2>
        <p>Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan</p>
        <p>Telp: (021) 1234567 | Email: info@hokpkbm.sch.id</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Nama Peserta Didik</div>
            <div class="info-colon">:</div>
            <div class="info-value">{{ strtoupper($rapor->siswa->nama_lengkap) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">NISN / Nomor Induk</div>
            <div class="info-colon">:</div>
            <div class="info-value">{{ $rapor->siswa->nisn }} / {{ $rapor->siswa->nis }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tempat, Tanggal Lahir</div>
            <div class="info-colon">:</div>
            <div class="info-value">{{ $rapor->siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($rapor->siswa->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kelas / Fase</div>
            <div class="info-colon">:</div>
            <div class="info-value">{{ $rapor->kelas->nama_kelas }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Semester</div>
            <div class="info-colon">:</div>
            <div class="info-value">{{ ucfirst($rapor->semester) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tahun Ajaran</div>
            <div class="info-colon">:</div>
            <div class="info-value">{{ $rapor->tahunAjaran->nama_tahun_ajaran }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th>Mata Pelajaran</th>
                <th style="width: 70px;" class="text-center">Nilai</th>
                <th style="width: 60px;" class="text-center">Predikat</th>
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
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $raporNilai->mataPelajaran->nama_mapel }}</strong></td>
                    <td class="text-center"><strong>{{ number_format($raporNilai->nilai_angka, 0) }}</strong></td>
                    <td class="text-center"><strong>{{ $raporNilai->nilai_huruf }}</strong></td>
                    <td style="font-size: 10px;">{{ $raporNilai->deskripsi ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right; font-weight: bold;">RATA-RATA</td>
                <td class="text-center" style="font-weight: bold; font-size: 14px;">
                    {{ $jumlahMapel > 0 ? number_format($totalNilai / $jumlahMapel, 0) : '0' }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="kehadiran-section">
        <strong>KETIDAKHADIRAN</strong>
        <div class="kehadiran-grid" style="margin-top: 10px;">
            <div>Sakit: <strong>{{ $rapor->jumlah_sakit }}</strong> hari</div>
            <div>Izin: <strong>{{ $rapor->jumlah_izin }}</strong> hari</div>
            <div>Tanpa Keterangan: <strong>{{ $rapor->jumlah_alpha }}</strong> hari</div>
        </div>
    </div>

    @if($rapor->catatan_wali_kelas)
        <div class="catatan-section">
            <strong>CATATAN WALI KELAS:</strong>
            <p style="margin-top: 10px; line-height: 1.6; text-align: justify;">{{ $rapor->catatan_wali_kelas }}</p>
        </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div style="margin-bottom: 10px;">Mengetahui,</div>
            <div>Orang Tua / Wali</div>
            <div class="signature-line">( .................................................. )</div>
        </div>
        <div class="signature-box">
            <div style="margin-bottom: 10px;">
                Tangerang Selatan, {{ $rapor->tanggal_terbit ? \Carbon\Carbon::parse($rapor->tanggal_terbit)->locale('id')->isoFormat('D MMMM YYYY') : now()->locale('id')->isoFormat('D MMMM YYYY') }}
            </div>
            <div>Wali Kelas</div>
            <div class="signature-line">{{ $rapor->kelas->waliKelas->nama_lengkap }}</div>
        </div>
    </div>

    <div class="ketua-section">
        <div style="margin-bottom: 10px;">Mengetahui,</div>
        <div style="font-weight: bold;">Ketua PKBM House of Knowledge</div>
        <div style="margin-top: 70px;">
            <div style="border-top: 1px solid #000; display: inline-block; padding-top: 5px; min-width: 250px;">
                <strong>Fransisda Tiodora Ferdiansyah, S.Psi., MM</strong>
            </div>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
