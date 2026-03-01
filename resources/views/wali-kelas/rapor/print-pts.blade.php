<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor PTS - {{ $rapor->siswa->nama_lengkap }}</title>
    <style>
        @page { margin: 15mm; size: A4 portrait; }
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 16pt; margin: 5px 0; }
        .header h2 { font-size: 14pt; margin: 5px 0; font-weight: normal; }
        .header p { margin: 3px 0; font-size: 9pt; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 8px; }
        .info-table td:first-child { width: 150px; font-weight: bold; }
        .nilai-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        .nilai-table th, .nilai-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 10pt; }
        .nilai-table th { background-color: #f0f0f0; font-weight: bold; }
        .nilai-table td:nth-child(2) { text-align: left; }
        .ekstra-table { width: 60%; border-collapse: collapse; margin-bottom: 20px; }
        .ekstra-table th, .ekstra-table td { border: 1px solid #000; padding: 6px; }
        .ekstra-table th { background-color: #f0f0f0; }
        .kehadiran-table { width: 50%; border-collapse: collapse; margin-bottom: 20px; }
        .kehadiran-table td { border: 1px solid #000; padding: 6px; }
        .kehadiran-table td:first-child { font-weight: bold; width: 150px; }
        .signature { margin-top: 40px; }
        .signature table { width: 100%; }
        .signature td { text-align: center; padding: 10px; vertical-align: top; }
        .signature .sign-line { border-top: 1px solid #000; margin-top: 60px; padding-top: 5px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>HOUSE OF KNOWLEDGE</h1>
        <h2>The Second Home For Your Children - 家庭教育</h2>
        <p>Jl. Contoh No. 123, Kota, Provinsi | Telp: (021) 1234567 | Email: info@hok.sch.id</p>
        <hr style="margin: 15px 0;">
        <h1>LAPORAN PENILAIAN TENGAH SEMESTER</h1>
    </div>

    <!-- Info Siswa -->
    <table class="info-table">
        <tr>
            <td>Nama Siswa</td>
            <td>: {{ $rapor->siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Nomor Induk</td>
            <td>: {{ $rapor->siswa->nis }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: {{ $rapor->kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Tahun Ajaran</td>
            <td>: {{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '-' }}</td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>: {{ ucfirst($rapor->semester) }}</td>
        </tr>
    </table>

    <!-- Tabel Nilai -->
    <table class="nilai-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Mata Pelajaran</th>
                <th width="45">KKM</th>
                <th width="45">Tugas</th>
                <th width="45">U1</th>
                <th width="45">U2</th>
                <th width="45">PTS</th>
                <th width="80">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalNilai = 0;
                $count = 0;
                $visibleNilai = $rapor->raporNilai->filter(fn($rn) => $rn->is_visible);
            @endphp
            @foreach($visibleNilai as $nilai)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $nilai->mataPelajaran->nama_mapel ?? '-' }}</td>
                <td>{{ $nilai->mataPelajaran->kkm ?? 75 }}</td>
                <td>{{ $nilai->nilai->rata_tugas ?? '-' }}</td>
                <td>{{ $nilai->nilai->rata_latihan ?? '-' }}</td>
                <td>{{ $nilai->nilai->rata_uh ?? '-' }}</td>
                <td>{{ $nilai->nilai->pts ?? '-' }}</td>
                <td>{{ ($nilai->nilai->pts ?? 0) >= ($nilai->mataPelajaran->kkm ?? 75) ? 'Tuntas' : 'Tidak Tuntas' }}</td>
            </tr>
            @php
                $totalNilai += $nilai->nilai->pts ?? 0;
                $count++;
            @endphp
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="6">Jumlah</td>
                <td>{{ $totalNilai }}</td>
                <td></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="6">Rata-rata</td>
                <td>{{ $count > 0 ? number_format($totalNilai / $count, 2) : 0 }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Kegiatan Ekstrakurikuler -->
    <h3>Kegiatan Ekstrakurikuler</h3>
    <table class="ekstra-table">
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Nama Kegiatan</th>
                <th width="80">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rapor->kegiatanEkstra as $ekstra)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $ekstra->kegiatan_nama }}</td>
                <td style="text-align: center;">{{ $ekstra->predikat ?? '-' }}</td>
            </tr>
            @empty
            @foreach(\App\Models\RaporKegiatanEkstra::getDefaultKegiatan() as $kegiatan)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $kegiatan }}</td>
                <td style="text-align: center;">-</td>
            </tr>
            @endforeach
            @endforelse
        </tbody>
    </table>

    <!-- Ketidakhadiran -->
    <h3>Ketidakhadiran</h3>
    <table class="kehadiran-table">
        <tr>
            <td>Sakit</td>
            <td>: {{ $rapor->jumlah_sakit }} hari</td>
        </tr>
        <tr>
            <td>Izin</td>
            <td>: {{ $rapor->jumlah_izin }} hari</td>
        </tr>
        <tr>
            <td>Tanpa Keterangan</td>
            <td>: {{ $rapor->jumlah_alpha }} hari</td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Jumlah</td>
            <td>: {{ $rapor->jumlah_sakit + $rapor->jumlah_izin + $rapor->jumlah_alpha }} hari</td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <div class="signature">
        <table>
            <tr>
                <td width="33%">
                    <div>Orang Tua/Wali</div>
                    <div class="sign-line">(...........................)</div>
                </td>
                <td width="34%" style="text-align: center;">
                    <div>{{ $rapor->kelas->cabang->kota ?? 'Kota' }}, {{ now()->format('d F Y') }}</div>
                </td>
                <td width="33%">
                    <div>Wali Kelas</div>
                    <div class="sign-line">{{ $rapor->kelas->waliKelas->nama ?? '(...........................)' }}</div>
                </td>
            </tr>
        </table>
        <div style="text-align: right; margin-top: 30px; font-weight: bold;">
            <div style="margin-bottom: 5px;">{{ $rapor->kelas->cabang->kota ?? 'Tangerang Selatan' }}, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            <div style="display: inline-block; text-align: center;">
                <div>Ketua PKBM House of Knowledge</div>
                <div style="margin-top: 60px; border-top: 1px solid #000; display: inline-block; padding-top: 5px;">
                    Fransisda Tiodora Ferdiansyah, S.Psi., MM
                </div>
            </div>
        </div>
    </div>
</body>
</html>
