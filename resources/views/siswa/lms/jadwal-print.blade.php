<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - {{ $kelas->nama_kelas }}</title>
    @vite(['resources/css/siswa/lms/jadwal-print.css'])
</head>
<body class="siswa-lms-jadwal-print-page">
    <!-- Tombol Print Manual -->
    <button type="button" class="print-button no-print" data-print-button>
        Cetak / Simpan PDF
    </button>

    <div class="header">
        <h1>Jadwal Mata Pelajaran {{ strtoupper($kelas->jenjang) }}</h1>
        <h2>PKBM House of Knowledge - Tahun Ajaran {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</h2>
    </div>

    <div class="info-box">
        <div>
            <strong>Kelas:</strong> {{ $kelas->nama_kelas }}
        </div>
        <div>
            <strong>Jenjang:</strong> {{ strtoupper($kelas->jenjang) }}
        </div>
        <div>
            <strong>Wali Kelas:</strong> {{ $kelas->waliKelas->nama_lengkap ?? '-' }}
        </div>
    </div>

    @foreach($hariList as $hari)
        <div class="day-section">
            <div class="day-header">{{ $hari }}</div>

            @if($jadwalPerHari[$hari]->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th class="col-number">No</th>
                            <th class="col-time">Jam</th>
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
                                    <td class="text-center">{{ $index + 1 }}</td>
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
                                    <td class="text-center">{{ $index + 1 }}</td>
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
            <div class="signature-role">Wali Kelas</div>
            <div class="signature-line">{{ $kelas->waliKelas->nama_lengkap ?? '____________________' }}</div>
        </div>
    </div>

    @vite(['resources/js/siswa/lms/jadwal-print.js'])
</body>
</html>
