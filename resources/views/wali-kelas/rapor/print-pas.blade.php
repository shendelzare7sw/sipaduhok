<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor PAS - {{ $rapor->siswa->nama_lengkap }}</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 16pt; margin: 5px 0; }
        .header h2 { font-size: 14pt; margin: 5px 0; font-weight: normal; }
        .header p { margin: 3px 0; font-size: 9pt; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 8px; }
        .info-table td:first-child { width: 150px; font-weight: bold; }
        .nilai-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .nilai-table th, .nilai-table td { border: 1px solid #000; padding: 5px; }
        .nilai-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .nilai-table td:first-child { text-align: center; width: 40px; }
        .nilai-table td:nth-child(3) { text-align: center; width: 60px; }
        .group-header { background-color: #d0d0d0; font-weight: bold; text-align: center; }
        .ekstra-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .ekstra-table th, .ekstra-table td { border: 1px solid #000; padding: 5px; }
        .ekstra-table th { background-color: #f0f0f0; }
        .kehadiran-table { width: 50%; border-collapse: collapse; margin-bottom: 15px; }
        .kehadiran-table td { border: 1px solid #000; padding: 5px; }
        .kehadiran-table td:first-child { font-weight: bold; width: 150px; }
        .catatan { border: 1px solid #000; padding: 10px; min-height: 80px; margin-bottom: 15px; }
        .signature { margin-top: 30px; }
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
        <h1>PENCAPAIAN KOMPETENSI PESERTA DIDIK</h1>
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

    <!-- Kelompok A (Wajib) -->
    <table class="nilai-table">
        <thead>
            <tr class="group-header">
                <th colspan="4">KELOMPOK A (Wajib)</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Nilai</th>
                <th>Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $visibleNilai = $rapor->raporNilai->filter(fn($rn) => $rn->is_visible);
            @endphp
            @foreach($visibleNilai as $nilai)
                @php $kel = $nilai->kelompok_override ?? ($nilai->mataPelajaran->kelompok ?? ''); @endphp
                @if(trim($kel) == 'A')
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                    <td>{{ $nilai->nilai_angka }}</td>
                    <td>{{ $nilai->deskripsi ?? '-' }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Kelompok B (Pilihan) -->
    <table class="nilai-table">
        <thead>
            <tr class="group-header">
                <th colspan="4">KELOMPOK B (Pilihan)</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Nilai</th>
                <th>Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach($visibleNilai as $nilai)
                @php $kel = $nilai->kelompok_override ?? ($nilai->mataPelajaran->kelompok ?? ''); @endphp
                @if(trim($kel) == 'B')
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $nilai->mataPelajaran->nama_mapel }}</td>
                    <td>{{ $nilai->nilai_angka }}</td>
                    <td>{{ $nilai->deskripsi ?? '-' }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Kegiatan Ekstrakurikuler -->
    <table class="ekstra-table">
        <thead>
            <tr class="group-header">
                <th colspan="4">Kegiatan Ekstrakurikuler</th>
            </tr>
            <tr>
                <th width="40">No</th>
                <th>Kegiatan</th>
                <th width="80">Predikat</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rapor->kegiatanEkstra as $ekstra)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $ekstra->kegiatan_nama }}</td>
                <td style="text-align: center;">{{ $ekstra->predikat ?? '-' }}</td>
                <td>{{ $ekstra->keterangan ?? '-' }}</td>
            </tr>
            @empty
            @foreach(\App\Models\RaporKegiatanEkstra::getDefaultKegiatan() as $kegiatan)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $kegiatan }}</td>
                <td style="text-align: center;">-</td>
                <td>-</td>
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
    </table>

    <!-- Catatan Wali Kelas -->
    <h3>Catatan Wali Kelas</h3>
    <div class="catatan">
        {{ $rapor->catatan_wali_kelas ?? '-' }}
    </div>

    <!-- Tanda Tangan -->
    <div class="signature">
        <table>
            <tr>
                <td width="33%">
                    <div>Orang Tua/Wali</div>
                    <div class="sign-line">(...........................)</div>
                </td>
                <td width="34%" style="text-align: center;">
                    <div>{{ $rapor->kelas->cabang->kota ?? 'Tangerang Selatan' }}, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                </td>
                <td width="33%">
                    <div>Wali Kelas</div>
                    <div class="sign-line">{{ $rapor->kelas->waliKelas->nama ?? '(...........................)' }}</div>
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 30px; font-weight: bold;">
            Ketua PKBM House of Knowledge<br>
            <div style="margin-top: 60px; border-top: 1px solid #000; display: inline-block; padding-top: 5px;">
                Fransisda Tiodora Ferdiansyah, S.Psi., MM
            </div>
        </div>
    </div>
</body>
</html>
