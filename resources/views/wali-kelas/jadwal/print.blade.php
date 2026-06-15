<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - {{ $kelas->nama_kelas }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/wali-kelas/jadwal/print.css') }}">
</head>
<body>
    <!-- Tombol Print & Back -->
    <div class="print-actions no-print">
        <a href="#" class="back-button" data-history-back>
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <button class="print-button" data-print-page>
            <i class="bi bi-printer"></i> Cetak / Simpan PDF
        </button>
    </div>

<div id="printRoot">
    <div class="header">
        @php
            $cabang = $kelas->cabang ?? null;
            $namaSekolah = $cabang
                ? strtoupper(preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i', '', $cabang->nama_cabang))
                : 'PKBM HOUSE OF KNOWLEDGE';
            $alamatCabang = $cabang
                ? ($cabang->alamat ?? 'Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan')
                : 'Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan';
        @endphp
        <div class="header-school">
            <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="school-logo-img">
            <div>
                <div class="school-name">{{ $namaSekolah }}</div>
                <div class="school-sub">PUSAT KEGIATAN BELAJAR MASYARAKAT</div>
                <div class="school-address">{{ $alamatCabang }}</div>
            </div>
        </div>
        <h1>JADWAL PELAJARAN</h1>
    </div>

    <div class="info-box">
        <div>
            <strong>Kelas:</strong>
            {{ $kelas->nama_kelas }}
        </div>
        <div>
            <strong>Jenjang:</strong>
            {{ strtoupper($kelas->jenjang) }}
        </div>
        <div>
            <strong>Tahun Ajaran:</strong>
            {{ $kelas->tahunAjaran->nama_tahun_ajaran }}
        </div>
        <div>
            <strong>Wali Kelas:</strong>
            {{ $kelas->waliKelas->nama_lengkap }}
        </div>
    </div>

    @foreach($hariList as $hari)
        <div class="day-section">
            <div class="day-header">{{ $hari }}</div>

            @if($jadwalPerHari[$hari]->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th style="width: 150px;">Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Kode</th>
                            <th>Guru Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalPerHari[$hari] as $index => $item)
                            @if($item['type'] === 'istirahat')
                                @php
                                    $istirahat = $item['data'];
                                @endphp
                                <tr class="break-row">
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td>
                                        {{ substr($istirahat->jam_mulai, 0, 5) }} -
                                        {{ substr($istirahat->jam_selesai, 0, 5) }}
                                    </td>
                                    <td>
                                        <strong>{{ $istirahat->nama_istirahat }}</strong>
                                    </td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @else
                                @php
                                    $jadwal = $item['data'];
                                @endphp
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </td>
                                    <td>
                                        <strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong>
                                    </td>
                                    <td>{{ $jadwal->mataPelajaran->kode_mapel }}</td>
                                    <td>{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-schedule">Tidak ada jadwal pelajaran</div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <div class="signature-box">
            <div>Tangerang Selatan, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            <div>Wali Kelas</div>
            <div class="signature-line">{{ $kelas->waliKelas->nama_lengkap }}</div>
        </div>
    </div>
</div>
    <script src="{{ asset('js/wali-kelas/jadwal/print.js') }}"></script>
</body>
</html>
