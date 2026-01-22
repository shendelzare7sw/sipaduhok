@extends('layouts.lms')

@php
    $title = 'Kalender Akademik';
    $subtitle = 'Lihat jadwal kegiatan dan agenda sekolah';
@endphp

@section('title', $title)
@section('page-title', $title)
@section('page-subtitle', $subtitle)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .calendar-page-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 18px;
            color: #0066cc;
            font-weight: 600;
        }

        .calendar-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .calendar-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a4d8f;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
        }

        .search-box input:focus {
            border-color: #0066cc;
        }

        .search-box .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .year-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a4d8f;
        }

        .main-content {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        .calendar-section {
            flex: 1;
            min-width: 0;
        }

        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .calendar-nav .btn {
            padding: 8px 16px;
        }

        .calendar-nav .month-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a4d8f;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #1a4d8f;
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed;
        }

        .calendar-table th {
            background: #1a4d8f;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
        }

        .calendar-table td {
            border: 1px solid #ddd;
            padding: 8px;
            height: 100px;
            vertical-align: top;
            background: white;
        }

        .calendar-table td.other-month {
            background: #f9fafb;
        }

        .calendar-table td.other-month .date-number {
            color: #ccc;
        }

        .calendar-table td.today {
            background: #fffacd;
        }

        .date-number {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .event {
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 4px;
            margin: 2px 0;
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: transform 0.2s;
            color: white;
            /* Default text white for better contrast with dark bg */
        }

        .event:hover {
            transform: scale(1.02);
        }

        .event-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
            background: white;
            /* Dot white by default on colored bg */
        }

        .event-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Event Colors Matching System (SekretarisController) */
        .event-field_trip {
            background: #17a2b8;
            color: white;
        }

        .event-outing {
            background: #28a745;
            color: white;
        }

        .event-live_in {
            background: #6610f2;
            color: white;
        }

        .event-hokfest {
            background: #fd7e14;
            color: white;
        }

        .event-pts {
            background: #ffc107;
            color: #212529;
        }

        /* Yellow needs dark text */
        .event-pts .event-dot {
            background: #212529;
        }

        .event-pas {
            background: #dc3545;
            color: white;
        }

        .event-libur {
            background: #6c757d;
            color: white;
        }

        .event-ujian {
            background: #e83e8c;
            color: white;
        }

        .event-acara_sekolah {
            background: #20c997;
            color: white;
        }

        .event-lainnya {
            background: #007bff;
            color: white;
        }

        /* Tambahan untuk tugas/deadline jika ada di LMS tapi belum di map system kalender utama */
        .event-tugas {
            background: #0891b2;
            color: white;
        }

        .event-deadline {
            background: #2563eb;
            color: white;
        }

        /* Legend */
        .legend {
            width: 200px;
            flex-shrink: 0;
        }

        .legend-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a4d8f;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #1a4d8f;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            font-size: 13px;
            color: #333;
        }

        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Legend Dots */
        .dot-field_trip {
            background: #17a2b8;
        }

        .dot-outing {
            background: #28a745;
        }

        .dot-live_in {
            background: #6610f2;
        }

        .dot-hokfest {
            background: #fd7e14;
        }

        .dot-pts {
            background: #ffc107;
        }

        .dot-pas {
            background: #dc3545;
        }

        .dot-libur {
            background: #6c757d;
        }

        .dot-ujian {
            background: #e83e8c;
        }

        .dot-acara_sekolah {
            background: #20c997;
        }

        .dot-lainnya {
            background: #007bff;
        }

        @media (max-width: 992px) {
            .main-content {
                flex-direction: column;
            }

            .legend {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }

            .legend-item {
                flex: 1;
                min-width: 140px;
            }

            .calendar-table td {
                height: 80px;
                padding: 4px;
            }

            .event {
                font-size: 9px;
            }
        }
    </style>

    <div class="calendar-page-header">
        <i class="fas fa-calendar-alt"></i>
        Kalender Akademik Tahun Ini
    </div>

    <div class="calendar-container">
        <div class="calendar-header">
            <h1 class="calendar-title">Kalender Akademik</h1>
            <div class="search-box">
                <input type="text" id="searchEvent" placeholder="Cari event...">
                <span class="search-icon"><i class="fas fa-search"></i></span>
            </div>
            <div class="year-title">Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</div>
        </div>

        <div class="main-content">
            <div class="calendar-section">
                {{-- Navigation --}}
                <div class="calendar-nav">
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group shadow-sm">
                            <a href="{{ route('siswa.lms.kalender', ['mode' => 'minggu', 'date' => $baseDate->format('Y-m-d')]) }}"
                                class="btn {{ $viewMode == 'minggu' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm fw-bold">Minggu</a>
                            <a href="{{ route('siswa.lms.kalender', ['mode' => 'bulan', 'month' => $month, 'year' => $year]) }}"
                                class="btn {{ $viewMode == 'bulan' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm fw-bold">Bulan</a>
                            <a href="{{ route('siswa.lms.kalender', ['mode' => 'tahun', 'year' => $year]) }}"
                                class="btn {{ $viewMode == 'tahun' ? 'btn-primary' : 'btn-outline-primary' }} btn-sm fw-bold">Tahun</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @php
                            $prevParams = [];
                            if ($viewMode == 'minggu')
                                $prevParams = ['mode' => 'minggu', 'date' => $prevWeekDate];
                            elseif ($viewMode == 'tahun')
                                $prevParams = ['mode' => 'tahun', 'year' => $prevYear];
                            else
                                $prevParams = ['mode' => 'bulan', 'month' => $prevMonth['month'], 'year' => $prevMonth['year']];
                        @endphp
                        <a href="{{ route('siswa.lms.kalender', $prevParams) }}"
                            class="btn btn-outline-primary btn-sm rounded-circle shadow-sm"
                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chevron-left"></i>
                        </a>

                        <span class="month-title text-center" style="min-width: 150px;">
                            @if($viewMode == 'minggu')
                                {{ \Carbon\Carbon::parse($startOfWeek)->translatedFormat('d M') }} -
                                {{ \Carbon\Carbon::parse($endOfWeek)->translatedFormat('d M Y') }}
                            @elseif($viewMode == 'tahun')
                                Tahun {{ $year }}
                            @else
                                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
                            @endif
                        </span>

                        @php
                            $nextParams = [];
                            if ($viewMode == 'minggu')
                                $nextParams = ['mode' => 'minggu', 'date' => $nextWeekDate];
                            elseif ($viewMode == 'tahun')
                                $nextParams = ['mode' => 'tahun', 'year' => $nextYear];
                            else
                                $nextParams = ['mode' => 'bulan', 'month' => $nextMonth['month'], 'year' => $nextMonth['year']];
                        @endphp
                        <a href="{{ route('siswa.lms.kalender', $nextParams) }}"
                            class="btn btn-outline-primary btn-sm rounded-circle shadow-sm"
                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Calendar Table --}}
                @if($viewMode == 'minggu')
                    {{-- WEEK VIEW (VISUAL GRID - 06:00 to 18:00 Focus, but show full day logic if needed) --}}
                    {{-- Assuming standard school hours 07:00 - 16:00, but let's do 06:00 - 18:00 for optimal view --}}
                    <div class="card border-0 shadow-none">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" style="border-radius: 8px; overflow: hidden; table-layout: fixed;">
                                <thead class="bg-primary text-white text-center">
                                    <tr>
                                        <th width="80" class="align-middle py-3">Waktu</th>
                                        @foreach($weekDays as $day)
                                            <th class="py-3" style="{{ $day->isToday() ? 'background-color: #FFF9C4; color: #333;' : '' }}">
                                                <div style="font-size: 1.1em;">{{ $day->translatedFormat('D') }}</div>
                                                <div style="font-weight: 400; font-size: 0.9em;">{{ $day->format('d/m') }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- All Day Row --}}
                                    <tr>
                                        <td class="text-center align-middle bg-light fw-bold text-muted small">All Day</td>
                                        @foreach($weekDays as $day)
                                            <td class="p-1 align-top {{ $day->isToday() ? 'bg-warning bg-opacity-10' : '' }}" style="height: 50px; {{ $day->isToday() ? 'background-color: #FFF9C4;' : '' }}">
                                                @foreach($eventsMinggu as $event)
                                                    @php
                                                        $start = \Carbon\Carbon::parse($event->tanggal_mulai);
                                                        $end = $event->tanggal_selesai ? \Carbon\Carbon::parse($event->tanggal_selesai) : $start;
                                                        // Logic for All Day: If no specific time or duration > 24h
                                                        $isAllDay = !$event->waktu_mulai; 
                                                    @endphp
                                                    @if($isAllDay && $start->lte($day) && $end->gte($day))
                                                        <div class="event event-{{ $event->jenis_kegiatan }} mb-1"
                                                            onclick="window.location.href='{{ route('siswa.lms.kalender.detail', ['tanggal' => $day->format('Y-m-d')]) }}'"
                                                            title="{{ $event->nama_kegiatan }}">
                                                            <span class="event-text small">{{ Str::limit($event->nama_kegiatan, 15) }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>

                                    {{-- Time Grid (06:00 - 18:00 covers most school activities) --}}
                                    @for($hour = 6; $hour <= 18; $hour++)
                                        <tr>
                                            <td class="text-center align-middle text-muted small fw-bold" style="height: 60px;">
                                                {{ sprintf('%02d:00', $hour) }}
                                            </td>
                                            @foreach($weekDays as $day)
                                                <td class="p-1 position-relative border-start-0 border-end {{ $day->isToday() ? 'bg-warning bg-opacity-10' : '' }}" style="vertical-align: top; {{ $day->isToday() ? 'background-color: #FFF9C4;' : '' }}">
                                                     <!-- Check for events starting in this hour -->
                                                     @foreach($eventsMinggu as $event)
                                                        @php
                                                            if (!$event->waktu_mulai) continue;
                                                            $start = \Carbon\Carbon::parse($event->tanggal_mulai);
                                                            $startTime = \Carbon\Carbon::parse($event->waktu_mulai);
                                                            
                                                            // Check if event belongs to this day
                                                            if (!$start->isSameDay($day)) continue;

                                                            // Check if event starts in this hour
                                                            if ($startTime->hour != $hour) continue;
                                                        @endphp
                                                        
                                                        <div class="event event-{{ $event->jenis_kegiatan }} p-1 mb-1 shadow-sm"
                                                            style="font-size: 0.8em; border-radius: 4px; cursor: pointer;"
                                                            onclick="window.location.href='{{ route('siswa.lms.kalender.detail', ['tanggal' => $day->format('Y-m-d')]) }}'"
                                                            title="{{ $event->nama_kegiatan }} ({{ $startTime->format('H:i') }})">
                                                            <div class="text-truncate fw-bold">{{ $event->nama_kegiatan }}</div>
                                                        </div>
                                                     @endforeach
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                @elseif($viewMode == 'tahun')
                    {{-- YEAR VIEW (GRID) --}}
                    <div class="row g-3">
                        @for($m = 1; $m <= 12; $m++)
                            @php 
                                $tempDate = \Carbon\Carbon::createFromDate($year, $m, 1);
                                $monthName = $tempDate->translatedFormat('F'); // Localized Month Name
                                $monthEventsCount = $eventsTahun->filter(function($e) use ($year, $m) {
                                                                    $start = \Carbon\Carbon::parse($e->tanggal_mulai);
                                                                    $end = $e->tanggal_selesai ? \Carbon\Carbon::parse($e->tanggal_selesai) : $start;
                                                                    return $start->month == $m || $end->month == $m; 
                                                                 })->count();
                            @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('siswa.lms.kalender', ['mode' => 'bulan', 'month' => $m, 'year' => $year]) }}" class="card h-100 shadow-sm text-decoration-none border hover-scale" style="transition: transform 0.2s;">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3 text-primary">
                                            <i class="far fa-calendar-alt fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">{{ $monthName }}</h5>
                                        <div class="small text-muted">{{ $year }}</div>
                                        
                                        <div class="mt-3">
                                            @if($monthEventsCount > 0)
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success">
                                                    <i class="fas fa-check-circle me-1"></i> {{ $monthEventsCount }} Kegiatan
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted rounded-pill px-3 py-2 border">
                                                    Kosong
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endfor
                    </div>

                    @else
                        {{-- MONTH VIEW (DEFAULT GRID) --}}
                        <table class="calendar-table">
                            <thead>
                                <tr>
                                    <th>Min</th>
                                    <th>Sen</th>
                                    <th>Sel</th>
                                    <th>Rab</th>
                                    <th>Kam</th>
                                    <th>Jum</th>
                                    <th>Sab</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $chunks = array_chunk($calendarDays, 7);
                                @endphp

                                @foreach($chunks as $week)
                                    <tr>
                                        @foreach($week as $day)
                                            <td
                                                class="{{ $day['isOtherMonth'] ? 'other-month' : '' }} {{ $day['isToday'] ? 'today' : '' }}">
                                                <div class="date-number">{{ $day['day'] }}</div>
                                                @foreach($day['events']->take(3) as $event)
                                                    <div class="event event-{{ $event->jenis_kegiatan }}"
                                                        data-event="{{ strtolower($event->nama_kegiatan) }}"
                                                        onclick="window.location.href='{{ route('siswa.lms.kalender.detail', ['tanggal' => $day['fullDate']]) }}'"
                                                        title="{{ $event->nama_kegiatan }}">
                                                        <span class="event-dot"></span>
                                                        <span class="event-text">{{ Str::limit($event->nama_kegiatan, 15) }}</span>
                                                    </div>
                                                @endforeach
                                                @if($day['events']->count() > 3)
                                                    <div class="event event-lainnya"
                                                        onclick="window.location.href='{{ route('siswa.lms.kalender.detail', ['tanggal' => $day['fullDate']]) }}'">
                                                        <span class="event-dot"></span>
                                                        <span>+{{ $day['events']->count() - 3 }} lainnya</span>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                        {{-- Fill empty cells if week is incomplete --}}
                                        @for($i = count($week); $i < 7; $i++)
                                            <td class="other-month"></td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Resizable Handle --}}
                <div class="resizer" id="dragMe"></div>

                {{-- Sidebar: Event List --}}
                <div class="legend" id="sidebarLegend">
                    <div class="legend-card">
                        <div class="legend-header">
                            <i class="fas fa-info-circle me-2"></i> Keterangan
                        </div>
                        <div class="legend-body">
                            @forelse($events as $event)
                                <div class="event-item-sidebar"
                                    onclick="window.location.href='{{ route('siswa.lms.kalender.detail', ['tanggal' => \Carbon\Carbon::parse($event->tanggal_mulai)->format('Y-m-d')]) }}'">
                                    <div class="date-badge-wrapper">
                                        <div class="large-color-dot dot-{{ $event->jenis_kegiatan }}"></div>
                                        <span
                                            class="d-date-small">{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('d M') }}</span>
                                    </div>
                                    <div class="event-info">
                                        <div class="event-title" title="{{ $event->nama_kegiatan }}">
                                            {{ Str::limit($event->nama_kegiatan, 35) }}
                                        </div>
                                        <div class="event-meta">
                                            <span class="meta-day"><i class="far fa-calendar"></i>
                                                {{ \Carbon\Carbon::parse($event->tanggal_mulai)->locale('id')->isoFormat('dddd') }}</span>
                                            @if($event->waktu_mulai)
                                                <span class="meta-time"><i class="far fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="far fa-calendar-times fa-2x mb-2"></i>
                                    <p class="small m-0">Tidak ada kegiatan bulan ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Resizer Styles */
            .resizer {
                background-color: #94a3b8;
                cursor: col-resize;
                height: auto;
                min-height: 100%;
                width: 4px;
                border-radius: 2px;
                margin: 0 8px;
                transition: background-color 0.2s;
                flex-shrink: 0;
                user-select: none;
                opacity: 0.7;
            }

            .resizer:hover,
            .resizing {
                background-color: #475569;
                opacity: 1;
            }

            .main-content {
                display: flex;
                align-items: stretch;
                /* Stretch height */
                gap: 10px;
                /* Reduces gap, controlled by resizer now */
            }

            .calendar-section {
                flex: 1;
                /* Takes remaining space */
                min-width: 300px;
            }

            .legend {
                width: 280px;
                /* Default width */
                min-width: 200px;
                flex-shrink: 0;
            }

            /* Sidebar Event List Styles */
            .legend-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                border: 1px solid #e5e7eb;
            }

            .legend-header {
                background: #f8fafc;
                padding: 12px 15px;
                border-bottom: 1px solid #e5e7eb;
                font-weight: 700;
                color: #1a4d8f;
                font-size: 14px;
            }

            .legend-body {
                max-height: 400px;
                overflow-y: auto;
                padding: 0;
            }

            .legend-body::-webkit-scrollbar {
                width: 4px;
            }

            .legend-body::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 4px;
            }

            .event-item-sidebar {
                display: flex;
                gap: 12px;
                padding: 12px 15px;
                border-bottom: 1px solid #f3f4f6;
                cursor: pointer;
                transition: background 0.2s;
                position: relative;
            }

            .event-item-sidebar:hover {
                background: #f8fafc;
            }

            .event-item-sidebar:last-child {
                border-bottom: none;
            }

            .date-badge-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 40px;
                flex-shrink: 0;
                gap: 4px;
            }

            .large-color-dot {
                width: 18px;
                height: 18px;
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .d-date-small {
                font-size: 10px;
                font-weight: 600;
                color: #64748b;
                text-align: center;
            }

            /* Dot Colors reuse */
            .dot-libur {
                background: #ff4444;
            }

            .dot-ujian {
                background: #ff69b4;
            }

            .dot-kegiatan,
            .dot-acara_sekolah {
                background: #ff9800;
            }

            .dot-outing,
            .dot-field_trip {
                background: #00838f;
            }

            .dot-live_in {
                background: #6610f2;
            }

            .dot-hokfest {
                background: #fd7e14;
            }

            .dot-pts {
                background: #ffc107;
            }

            .dot-pas {
                background: #dc3545;
            }

            .dot-lainnya {
                background: #007bff;
            }

            .event-info {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .event-title {
                font-weight: 600;
                font-size: 13px;
                color: #334155;
                margin-bottom: 4px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .event-meta {
                display: flex;
                gap: 10px;
                font-size: 11px;
                color: #64748b;
            }

            .event-type-dot {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                width: 8px;
                height: 8px;
                border-radius: 50%;
            }

            .badge-dot {
                font-size: 9px;
                padding: 3px 6px;
                border-radius: 4px;
                color: white;
                cursor: help;
            }
        </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Search functionality
            const searchInput = document.getElementById('searchEvent');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase();
                    const events = document.querySelectorAll('.event');
                    const cells = document.querySelectorAll('.calendar-table td');

                    // Reset all cells
                    cells.forEach(td => {
                        if (!td.classList.contains('today')) {
                            td.style.background = '';
                        }
                    });

                    events.forEach(event => {
                        const text = event.getAttribute('data-event') || event.textContent.toLowerCase();
                        const td = event.closest('td');

                        if (searchTerm === '') {
                            event.style.display = 'flex';
                        } else if (text.includes(searchTerm)) {
                            event.style.display = 'flex';
                            if (td && !td.classList.contains('today')) {
                                td.style.background = '#fffacd';
                            }
                        } else {
                            event.style.display = 'none';
                        }
                    });
                });
            }

            // Resizable Sidebar Logic
            const resizer = document.getElementById('dragMe');
            const leftSide = resizer.previousElementSibling;
            const rightSide = resizer.nextElementSibling;
            const container = resizer.parentNode;

            // The current position of mouse
            let x = 0;
            let leftWidth = 0;
            let rightWidth = 0;

            const mouseDownHandler = function (e) {
                // Get the current mouse position
                x = e.clientX;

                // Calculate current widths (not used directly but good for init)
                const leftRect = leftSide.getBoundingClientRect();
                const rightRect = rightSide.getBoundingClientRect();

                leftWidth = leftRect.width;
                rightWidth = rightRect.width;

                document.addEventListener('mousemove', mouseMoveHandler);
                document.addEventListener('mouseup', mouseUpHandler);
                resizer.classList.add('resizing');

                // Disable text selection during drag
                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'col-resize';
            };

            const mouseMoveHandler = function (e) {
                // How far the mouse has been moved
                const dx = e.clientX - x;

                // New width for sidebar (right side)
                // Since flex container, increasing sidebar decreases main content
                const newRightWidth = rightWidth - dx; // Dragging left increases width

                // Min width constraints
                if (newRightWidth > 150 && newRightWidth < 600) {
                    rightSide.style.width = `${newRightWidth}px`;
                }
            };

            const mouseUpHandler = function () {
                document.removeEventListener('mousemove', mouseMoveHandler);
                document.removeEventListener('mouseup', mouseUpHandler);
                resizer.classList.remove('resizing');
                document.body.style.removeProperty('user-select');
                document.body.style.removeProperty('cursor');
            };

            resizer.addEventListener('mousedown', mouseDownHandler);
        });
    </script>
@endpush