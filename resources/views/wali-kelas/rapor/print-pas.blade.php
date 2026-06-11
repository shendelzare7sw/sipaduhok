<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor PAS - {{ $rapor->siswa->nama_lengkap }}</title>
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/rapor/print-pas.css') }}">
</head>
<body>
    @php
        $alignmentValue = fn($value, $default) => in_array($value, ['left', 'center', 'right', 'justify'], true) ? $value : $default;
        $deskripsiAlignment = $alignmentValue($rapor->deskripsi_alignment ?? null, 'left');
        $keteranganEkstraAlignment = $alignmentValue($rapor->keterangan_ekstra_alignment ?? null, 'left');
        $catatanAlignment = $alignmentValue($rapor->catatan_alignment ?? null, 'center');
    @endphp

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
                    <td style="text-align: {{ $deskripsiAlignment }};">{{ $nilai->deskripsi ?? '-' }}</td>
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
                    <td style="text-align: {{ $deskripsiAlignment }};">{{ $nilai->deskripsi ?? '-' }}</td>
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
                <td style="text-align: {{ $keteranganEkstraAlignment }};">{{ $ekstra->keterangan ?? '-' }}</td>
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
    <div class="catatan" style="text-align: {{ $catatanAlignment }};">
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
