<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Akademik - {{ $bulan }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }

        .header h1 {
            font-size: 20pt;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 16pt;
            color: #374151;
            margin-bottom: 3px;
        }

        .header p {
            font-size: 10pt;
            color: #6b7280;
        }

        .calendar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .calendar th {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 10px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            border: 1px solid #2563eb;
        }

        .calendar td {
            border: 1px solid #d1d5db;
            padding: 5px;
            vertical-align: top;
            height: 100px;
            width: 14.28%;
            position: relative;
        }

        .calendar td.empty {
            background: #f9fafb;
        }

        .calendar td.today {
            background: #fef3c7;
            border: 2px solid #f59e0b;
        }

        .day-number {
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .event {
            background: #e0f2fe;
            border-left: 3px solid #0ea5e9;
            padding: 3px 5px;
            margin-bottom: 3px;
            font-size: 8pt;
            line-height: 1.2;
            border-radius: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .event.field_trip { background: #cffafe; border-left-color: #06b6d4; }
        .event.outing { background: #d1fae5; border-left-color: #10b981; }
        .event.live_in { background: #ede9fe; border-left-color: #8b5cf6; }
        .event.hokfest { background: #fed7aa; border-left-color: #f97316; }
        .event.pts { background: #fef3c7; border-left-color: #f59e0b; }
        .event.pas { background: #fecaca; border-left-color: #ef4444; }
        .event.libur { background: #e5e7eb; border-left-color: #6b7280; }
        .event.ujian { background: #fce7f3; border-left-color: #ec4899; }
        .event.acara_sekolah { background: #ccfbf1; border-left-color: #14b8a6; }

        .legend {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .legend h3 {
            font-size: 12pt;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .legend-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            font-size: 9pt;
        }

        .legend-color {
            width: 20px;
            height: 15px;
            margin-right: 8px;
            border-radius: 2px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: right;
            font-size: 9pt;
            color: #6b7280;
        }

        .event-list {
            margin-top: 20px;
            page-break-before: auto;
        }

        .event-list h3 {
            font-size: 12pt;
            margin-bottom: 10px;
            color: #1f2937;
            padding-bottom: 5px;
            border-bottom: 2px solid #3b82f6;
        }

        .event-list-item {
            padding: 10px;
            margin-bottom: 8px;
            background: white;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
        }

        .event-list-item strong {
            color: #1f2937;
            font-size: 11pt;
        }

        .event-list-item .date {
            color: #6b7280;
            font-size: 9pt;
            margin-top: 3px;
        }

        .event-list-item .desc {
            color: #374151;
            font-size: 9pt;
            margin-top: 5px;
            line-height: 1.4;
        }
    </style>
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
                <div class="legend-color" style="background: #cffafe; border-left: 3px solid #06b6d4;"></div>
                <span>Field Trip</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #d1fae5; border-left: 3px solid #10b981;"></div>
                <span>Outing</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #ede9fe; border-left: 3px solid #8b5cf6;"></div>
                <span>Live In</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #fed7aa; border-left: 3px solid #f97316;"></div>
                <span>HOK Fest</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #fef3c7; border-left: 3px solid #f59e0b;"></div>
                <span>PTS</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #fecaca; border-left: 3px solid #ef4444;"></div>
                <span>PAS</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #e5e7eb; border-left: 3px solid #6b7280;"></div>
                <span>Libur</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #fce7f3; border-left: 3px solid #ec4899;"></div>
                <span>Ujian</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #ccfbf1; border-left: 3px solid #14b8a6;"></div>
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
                    📅 {{ $event->tanggal_mulai->format('d F Y') }}
                    @if($event->tanggal_selesai)
                        - {{ $event->tanggal_selesai->format('d F Y') }}
                    @endif
                    @if($event->waktu_mulai)
                        | ⏰ {{ $event->waktu_mulai }} - {{ $event->waktu_selesai }}
                    @endif
                    | 🏷️ {{ $event->jenis_label }}
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