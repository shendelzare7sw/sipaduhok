<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="application/vnd.ms-excel; charset=utf-8">
    <title>Export Jadwal Pelajaran</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="8" style="text-align: center; font-size: 16px; font-weight: bold;">
                    JADWAL PELAJARAN {{ $tahunAjaran ? $tahunAjaran->nama_tahun_ajaran : '' }}
                </th>
            </tr>
            @if($filterInfo['cabang'] || $filterInfo['jenjang'] || $filterInfo['kelas'] || $filterInfo['guru'])
            <tr>
                <th colspan="8" style="text-align: center; font-size: 12px; background-color: #f0f0f0;">
                    Filter:
                    @if($filterInfo['cabang'])
                        Cabang: {{ $filterInfo['cabang'] }}
                    @endif
                    @if($filterInfo['jenjang'])
                        | Jenjang: {{ $filterInfo['jenjang'] }}
                    @endif
                    @if($filterInfo['kelas'])
                        | Kelas: {{ $filterInfo['kelas'] }}
                    @endif
                    @if($filterInfo['guru'])
                        | Guru: {{ $filterInfo['guru'] }}
                    @endif
                </th>
            </tr>
            @endif
            <tr>
                <th>No</th>
                <th>Kelas</th>
                <th>Cabang</th>
                <th>Hari</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
                <th>Mata Pelajaran</th>
                <th>Guru Pengajar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwalList as $index => $jadwal)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $jadwal->kelas->nama_kelas }}</td>
                <td>{{ $jadwal->kelas->cabang->nama_cabang }}</td>
                <td>{{ $jadwal->hari }}</td>
                <td>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                <td>{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : 'Belum ditentukan' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
