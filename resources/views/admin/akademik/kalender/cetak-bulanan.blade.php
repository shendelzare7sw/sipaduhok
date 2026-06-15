<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Akademik - {{ $bulan }}</title>
    <link rel="stylesheet" href="{{ public_path('css/admin/akademik/kalender/cetak-bulanan.css') }}">
</head>
<body>
    <div class="header">
        <h1>KALENDER AKADEMIK</h1>
        <h2>{{ $bulan }}</h2>
        <p>Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</p>
    </div>

    <table class="calendar">
        <thead>
            <tr>
                <th>Senin</th>
                <th>Selasa</th>
                <th>Rabu</th>
                <th>Kamis</th>
                <th>Jumat</th>
                <th>Sabtu</th>
                <th>Minggu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($calendarGrid as $week)
                <tr>
                    @foreach($week as $day)
                        @if($day === null)
                            <td class="empty"></td>
                        @else
                            <td class="{{ $day['isToday'] ? 'today' : '' }}">
                                <div class="day-number">{{ $day['date'] }}</div>
                                @foreach($day['events'] as $event)
                                    <div class="event {{ $event->jenis_kegiatan }}" title="{{ $event->nama_kegiatan }}">
                                        {{ Str::limit($event->nama_kegiatan, 20) }}
                                    </div>
                                @endforeach
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <h3>Keterangan Jenis Kegiatan:</h3>
        <div class="legend-grid">
            <div class="legend-item">
                <div class="legend-color field-trip"></div>
                <span>Field Trip</span>
            </div>
            <div class="legend-item">
                <div class="legend-color outing"></div>
                <span>Outing</span>
            </div>
            <div class="legend-item">
                <div class="legend-color live-in"></div>
                <span>Live In</span>
            </div>
            <div class="legend-item">
                <div class="legend-color hokfest"></div>
                <span>HOK Fest</span>
            </div>
            <div class="legend-item">
                <div class="legend-color pts"></div>
                <span>PTS</span>
            </div>
            <div class="legend-item">
                <div class="legend-color pas"></div>
                <span>PAS</span>
            </div>
            <div class="legend-item">
                <div class="legend-color libur"></div>
                <span>Libur</span>
            </div>
            <div class="legend-item">
                <div class="legend-color ujian"></div>
                <span>Ujian</span>
            </div>
            <div class="legend-item">
                <div class="legend-color acara-sekolah"></div>
                <span>Acara Sekolah</span>
            </div>
        </div>
    </div>

    @if($kegiatan->count() > 0)
    <div class="event-list">
        <h3>Daftar Kegiatan Bulan {{ $namaBulan }} {{ $tahun }}</h3>
        @foreach($kegiatan as $event)
            <div class="event-list-item">
                <strong>{{ $event->nama_kegiatan }}</strong>
                <div class="date">
                    <i class="fas fa-calendar"></i> {{ $event->tanggal_mulai->format('d F Y') }}
                    @if($event->tanggal_selesai)
                        - {{ $event->tanggal_selesai->format('d F Y') }}
                    @endif
                    @if($event->waktu_mulai)
                        | <i class="fas fa-clock"></i> {{ $event->waktu_mulai }} - {{ $event->waktu_selesai }}
                    @endif
                    | <i class="fas fa-tag"></i> {{ $event->jenis_label }}
                </div>
                @if($event->keterangan)
                    <div class="desc">{{ $event->keterangan }}</div>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
