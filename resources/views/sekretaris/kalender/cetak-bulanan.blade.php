<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Akademik - {{ $bulan }}</title>
    <style>
        @page {
            margin: 12mm;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            color: #000;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #3b82f6;
            margin-bottom: 8px;
            padding-bottom: 8px;
        }

        .header-title {
            text-align: center;
            margin-bottom: 8px;
        }

        .header-title h1 {
            font-size: 17pt;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .header-title h2 {
            font-size: 13pt;
            color: #374151;
            margin-bottom: 2px;
        }

        .header-title p {
            font-size: 9pt;
            color: #6b7280;
        }

        .calendar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .calendar th {
            background: #3b82f6;
            color: white;
            padding: 5px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #2563eb;
        }

        .calendar td {
            border: 1px solid #d1d5db;
            padding: 3px 4px;
            vertical-align: top;
            height: 72px;
            width: 14.28%;
        }

        .calendar td.empty {
            background: #f9fafb;
        }

        .calendar td.today {
            background: #fef3c7;
            border: 2px solid #f59e0b;
        }

        .day-number {
            font-size: 10pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .event {
            background: #e0f2fe;
            border-left: 3px solid #0ea5e9;
            padding: 2px 4px;
            margin-bottom: 2px;
            font-size: 7pt;
            line-height: 1.2;
            border-radius: 2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .event.field_trip { background: #cffafe; border-left-color: #06b6d4; }
        .event.outing     { background: #d1fae5; border-left-color: #10b981; }
        .event.live_in    { background: #ede9fe; border-left-color: #8b5cf6; }
        .event.hokfest    { background: #fed7aa; border-left-color: #f97316; }
        .event.pts        { background: #fee2e2; border-left-color: #dc2626; }
        .event.pas        { background: #dbeafe; border-left-color: #3b82f6; }
        .event.libur      { background: #fecaca; border-left-color: #ef4444; }
        .event.ujian      { background: #fce7f3; border-left-color: #ec4899; }
        .event.acara_sekolah { background: #ccfbf1; border-left-color: #14b8a6; }

        .legend {
            margin-top: 8px;
            padding: 8px 12px;
            background: #f9fafb;
            border-radius: 6px;
        }

        .legend h3 {
            font-size: 9pt;
            margin-bottom: 6px;
            color: #1f2937;
        }

        .legend-swatch {
            width: 14px;
            height: 10px;
        }

        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            text-align: right;
            font-size: 8pt;
            color: #6b7280;
        }

        .event-list {
            margin-top: 12px;
            page-break-before: auto;
        }

        .event-list h3 {
            font-size: 10pt;
            margin-bottom: 6px;
            color: #1f2937;
            padding-bottom: 4px;
            border-bottom: 2px solid #3b82f6;
        }

        .event-list-item {
            padding: 6px 8px;
            margin-bottom: 5px;
            background: white;
            border-left: 3px solid #3b82f6;
            border-radius: 3px;
            font-size: 8pt;
        }

        .event-list-item strong {
            color: #1f2937;
            font-size: 9pt;
        }

        .event-list-item .date {
            color: #6b7280;
            font-size: 8pt;
            margin-top: 2px;
        }

        .event-list-item .desc {
            color: #374151;
            font-size: 8pt;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    {{-- Header with logo (table-based for dompdf compatibility) --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="75" style="vertical-align: middle; padding-bottom: 8px;">
                <img src="{{ public_path('img/logo/hok-watermark.png') }}" alt="Logo HOK" style="height: 60px; width: auto;">
            </td>
            <td style="text-align: center; vertical-align: middle; padding-bottom: 8px;">
                <div style="font-size: 13pt; font-weight: bold; margin-bottom: 2px;">PKBM HOUSE OF KNOWLEDGE</div>
                <div style="font-size: 9pt; margin-bottom: 2px;">PUSAT KEGIATAN BELAJAR MASYARAKAT</div>
                <div style="font-size: 8pt; color: #555;">Jl. Ruko Reni Jaya Blok AF No. 22-23, Pamulang Barat, Tangerang Selatan</div>
            </td>
            <td width="75"></td>
        </tr>
    </table>

    <div class="header-title">
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
                                        {{ Str::limit($event->nama_kegiatan, 18) }}
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
        <table width="100%" cellpadding="3" cellspacing="0" style="font-size: 8pt;">
            <tr>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #cffafe; border-left: 3px solid #06b6d4;"></div></td>
                        <td style="padding-left: 3px;">Field Trip</td>
                    </tr></table>
                </td>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #d1fae5; border-left: 3px solid #10b981;"></div></td>
                        <td style="padding-left: 3px;">Outing</td>
                    </tr></table>
                </td>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #ede9fe; border-left: 3px solid #8b5cf6;"></div></td>
                        <td style="padding-left: 3px;">Live In</td>
                    </tr></table>
                </td>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #fed7aa; border-left: 3px solid #f97316;"></div></td>
                        <td style="padding-left: 3px;">HOK Fest</td>
                    </tr></table>
                </td>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #fee2e2; border-left: 3px solid #dc2626;"></div></td>
                        <td style="padding-left: 3px;">PTS</td>
                    </tr></table>
                </td>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #dbeafe; border-left: 3px solid #3b82f6;"></div></td>
                        <td style="padding-left: 3px;">PAS</td>
                    </tr></table>
                </td>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #fecaca; border-left: 3px solid #ef4444;"></div></td>
                        <td style="padding-left: 3px;">Libur</td>
                    </tr></table>
                </td>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #fce7f3; border-left: 3px solid #ec4899;"></div></td>
                        <td style="padding-left: 3px;">Ujian</td>
                    </tr></table>
                </td>
                <td style="white-space: nowrap;">
                    <table cellpadding="0" cellspacing="0"><tr>
                        <td><div class="legend-swatch" style="background: #ccfbf1; border-left: 3px solid #14b8a6;"></div></td>
                        <td style="padding-left: 3px;">Acara Sekolah</td>
                    </tr></table>
                </td>
            </tr>
        </table>
    </div>

    @if($kegiatan->count() > 0)
    <div class="event-list">
        <h3>Daftar Kegiatan Bulan {{ $namaBulan }} {{ $tahun }}</h3>
        @foreach($kegiatan as $event)
            <div class="event-list-item">
                <strong>{{ $event->nama_kegiatan }}</strong>
                <div class="date">
                    {{ $event->tanggal_mulai->format('d F Y') }}
                    @if($event->tanggal_selesai)
                        - {{ $event->tanggal_selesai->format('d F Y') }}
                    @endif
                    @if($event->waktu_mulai)
                        | {{ $event->waktu_mulai }} - {{ $event->waktu_selesai }}
                    @endif
                    | {{ $event->jenis_label }}
                    @if($event->is_hidden_siswa)
                        | <span style="color: #6b7280; font-weight: bold;">(Disembunyikan)</span>
                    @endif
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
