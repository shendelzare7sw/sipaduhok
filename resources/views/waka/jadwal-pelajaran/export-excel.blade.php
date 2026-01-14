<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="application/vnd.ms-excel; charset=utf-8">
    <title>Export Jadwal Pelajaran</title>
</head>

<body>
    @php
        // Prepare combined data - merge jadwal with istirahat, sorted by hari then jam_mulai
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Group jadwal by hari
        $jadwalByHari = $jadwalList->groupBy('hari');

        // Get unique jenjang from filtered jadwal
        $jenjangs = $jadwalList->pluck('kelas.jenjang')->unique()->values()->toArray();

        // Filter istirahat by relevant jenjang
        $relevanIstirahat = isset($pengaturanIstirahat) ? $pengaturanIstirahat->filter(function ($ist) use ($jenjangs, $filterInfo) {
            if ($filterInfo['jenjang']) {
                return $ist->jenjang === $filterInfo['jenjang'];
            }
            return empty($jenjangs) || in_array($ist->jenjang, $jenjangs);
        }) : collect();

        $rowNumber = 1;
    @endphp

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
            @foreach($hariOrder as $hari)
                @php
                    // Get all jadwal for this day
                    $jadwalHari = isset($jadwalByHari[$hari]) ? $jadwalByHari[$hari] : collect();

                    // Get istirahat for this day
                    $istirahatHari = $relevanIstirahat->filter(function ($ist) use ($hari) {
                        $hariAktif = is_array($ist->hari_aktif) ? $ist->hari_aktif : json_decode($ist->hari_aktif, true);
                        return in_array($hari, $hariAktif ?? []);
                    });

                    // Create combined items array
                    $combinedItems = collect();

                    foreach ($jadwalHari as $jadwal) {
                        $combinedItems->push([
                            'type' => 'jadwal',
                            'jam_mulai' => $jadwal->jam_mulai,
                            'jam_selesai' => $jadwal->jam_selesai,
                            'sort_time' => $jadwal->jam_mulai ? $jadwal->jam_mulai->format('H:i') : '00:00',
                            'data' => $jadwal,
                        ]);
                    }

                    foreach ($istirahatHari as $ist) {
                        $combinedItems->push([
                            'type' => 'istirahat',
                            'jam_mulai' => $ist->jam_mulai,
                            'jam_selesai' => $ist->jam_selesai,
                            'sort_time' => substr($ist->jam_mulai, 0, 5),
                            'data' => $ist,
                        ]);
                    }

                    // Sort by sort_time
                    $combinedItems = $combinedItems->sortBy('sort_time')->values();
                @endphp

                @if($combinedItems->count() > 0)
                    {{-- Day header row --}}
                    <tr>
                        <td colspan="8" style="background-color: #e3f2fd; font-weight: bold;">
                            {{ $hari }}
                        </td>
                    </tr>

                    @foreach($combinedItems as $item)
                        @if($item['type'] === 'jadwal')
                            @php $jadwal = $item['data']; @endphp
                            <tr>
                                <td>{{ $rowNumber++ }}</td>
                                <td>{{ $jadwal->kelas->nama_kelas }}</td>
                                <td>{{ $jadwal->kelas->cabang->nama_cabang }}</td>
                                <td>{{ $jadwal->hari }}</td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                <td>{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : 'Belum ditentukan' }}</td>
                            </tr>
                        @else
                            @php $ist = $item['data']; @endphp
                            <tr style="background-color: #fff9c4;">
                                <td style="text-align: center;">-</td>
                                <td colspan="2" style="font-style: italic;">☕ ISTIRAHAT</td>
                                <td>{{ $hari }}</td>
                                <td>{{ substr($ist->jam_mulai, 0, 5) }}</td>
                                <td>{{ substr($ist->jam_selesai, 0, 5) }}</td>
                                <td colspan="2" style="font-style: italic;">{{ $ist->nama_istirahat }} ({{ $ist->jenjang }})</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
</body>

</html>