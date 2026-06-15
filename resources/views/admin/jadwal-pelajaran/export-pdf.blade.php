<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - {{ $tahunAjaran ? $tahunAjaran->nama_tahun_ajaran : '' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/jadwal-pelajaran/export-pdf.css') }}">
</head>
<body>
    <button class="print-button no-print" type="button" data-print-page>
        <i class="fas fa-print"></i> Cetak / Simpan PDF
    </button>

    <div class="header">
        <div class="header-school">
            <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="school-logo-img">
            <div>
                <div class="school-name">PKBM HOUSE OF KNOWLEDGE</div>
                <div class="school-sub">PUSAT KEGIATAN BELAJAR MASYARAKAT</div>
                <div class="school-address">Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan</div>
            </div>
        </div>
        <h1>Jadwal Pelajaran</h1>
        <h2>{{ $tahunAjaran ? $tahunAjaran->nama_tahun_ajaran : 'Semua Tahun Ajaran' }}</h2>
        @if($filterInfo['cabang'] || $filterInfo['jenjang'] || $filterInfo['kelas'] || $filterInfo['guru'])
        <p class="filter-info">
            Filter:
            @if($filterInfo['cabang'])
                Cabang: {{ $filterInfo['cabang'] }}
            @endif
            @if($filterInfo['jenjang'])
                {{ $filterInfo['cabang'] ? ' | ' : '' }}Jenjang: {{ $filterInfo['jenjang'] }}
            @endif
            @if($filterInfo['kelas'])
                {{ ($filterInfo['cabang'] || $filterInfo['jenjang']) ? ' | ' : '' }}Kelas: {{ $filterInfo['kelas'] }}
            @endif
            @if($filterInfo['guru'])
                {{ ($filterInfo['cabang'] || $filterInfo['jenjang'] || $filterInfo['kelas']) ? ' | ' : '' }}Guru: {{ $filterInfo['guru'] }}
            @endif
        </p>
        @endif
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y HH:mm') }} WIB</p>
    </div>

    @php
        // Prepare combined data - merge jadwal with istirahat, sorted by hari then jam_mulai
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Group jadwal by hari
        $jadwalByHari = $jadwalList->groupBy('hari');

        // Get unique jenjang from filtered jadwal (handle many-to-many kelas)
        $jenjangs = $jadwalList->flatMap(function ($jadwal) {
            return $jadwal->kelas->pluck('jenjang');
        })->unique()->values()->toArray();

        // Filter istirahat by relevant jenjang
        $relevanIstirahat = isset($pengaturanIstirahat) ? $pengaturanIstirahat->filter(function($ist) use ($jenjangs, $filterInfo) {
            // If jenjang filter is applied, only show istirahat for that jenjang
            if ($filterInfo['jenjang']) {
                return $ist->jenjang === $filterInfo['jenjang'];
            }
            // Otherwise show istirahat for all jenjang in the data
            return empty($jenjangs) || in_array($ist->jenjang, $jenjangs);
        }) : collect();

        $rowNumber = 1;
    @endphp

    @if($jadwalList->isEmpty())
        <div class="empty-data">
            <p>Tidak ada data jadwal pelajaran</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="text-center col-no">No</th>
                    <th class="col-kelas">Kelas</th>
                    <th class="col-cabang">Cabang</th>
                    <th class="col-hari">Hari</th>
                    <th class="text-center col-time">Jam Mulai</th>
                    <th class="text-center col-time">Jam Selesai</th>
                    <th>Mata Pelajaran</th>
                    <th class="col-guru">Guru Pengajar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hariOrder as $hari)
                    @php
                        // Get all jadwal for this day
                        $jadwalHari = isset($jadwalByHari[$hari]) ? $jadwalByHari[$hari] : collect();

                        // Get istirahat for this day
                        $istirahatHari = $relevanIstirahat->filter(function($ist) use ($hari) {
                            $hariAktif = is_array($ist->hari_aktif) ? $ist->hari_aktif : json_decode($ist->hari_aktif, true);
                            return in_array($hari, $hariAktif ?? []);
                        });

                        // Create combined items array
                        $combinedItems = collect();

                        // Add jadwal items
                        foreach ($jadwalHari as $jadwal) {
                            $combinedItems->push([
                                'type' => 'jadwal',
                                'jam_mulai' => $jadwal->jam_mulai,
                                'jam_selesai' => $jadwal->jam_selesai,
                                'sort_time' => $jadwal->jam_mulai ? $jadwal->jam_mulai->format('H:i') : '00:00',
                                'data' => $jadwal,
                            ]);
                        }

                        // Add istirahat items
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
                        {{-- Day header --}}
                        <tr class="hari-header">
                            <td colspan="8">
                                @php
                                    $hariClass = [
                                        'Senin' => 'badge-senin',
                                        'Selasa' => 'badge-selasa',
                                        'Rabu' => 'badge-rabu',
                                        'Kamis' => 'badge-kamis',
                                        'Jumat' => 'badge-jumat',
                                        'Sabtu' => 'badge-sabtu',
                                    ][$hari] ?? '';
                                @endphp
                                <span class="badge badge-lg {{ $hariClass }}">{{ $hari }}</span>
                            </td>
                        </tr>

                        @foreach($combinedItems as $item)
                            @if($item['type'] === 'jadwal')
                                @php $jadwal = $item['data']; @endphp
                                <tr>
                                    <td class="text-center">{{ $rowNumber++ }}</td>
                                    <td><strong>{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</strong></td>
                                    <td class="cabang-cell">{{ $jadwal->kelas->pluck('cabang.nama_cabang')->unique()->join(', ') }}</td>
                                    <td>
                                        <span class="badge {{ $hariClass }}">{{ $jadwal->hari }}</span>
                                    </td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</td>
                                    <td><strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong></td>
                                    <td>{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</td>
                                </tr>
                            @else
                                @php $ist = $item['data']; @endphp
                                <tr class="istirahat-row">
                                    <td class="text-center">-</td>
                                    <td colspan="2"><strong>☕ ISTIRAHAT</strong></td>
                                    <td>{{ $hari }}</td>
                                    <td class="text-center">{{ substr($ist->jam_mulai, 0, 5) }}</td>
                                    <td class="text-center">{{ substr($ist->jam_selesai, 0, 5) }}</td>
                                    <td colspan="2">{{ $ist->nama_istirahat }} ({{ $ist->jenjang }})</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Tangerang Selatan, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            <p>Kepala Sekolah / Penanggung Jawab,</p>
            <div class="signature">
                <strong>(__________________________)</strong>
            </div>
        </div>
    @endif
    <script src="{{ asset('js/admin/jadwal-pelajaran/print.js') }}"></script>
</body>
</html>
