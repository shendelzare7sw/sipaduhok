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

@push('styles')
    @vite(['resources/css/siswa/lms/kalender/index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/siswa/lms/kalender/index.js'])
@endpush

@section('content')
<div class="siswa-lms-kalender-index-page">
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
                            <a href="{{ route('siswa.lms.kalender', ['mode' => 'minggu', 'date' => now()->format('Y-m-d')]) }}"
                                class="btn-nav {{ $viewMode == 'minggu' ? 'btn-primary' : 'btn-outline-primary' }} btn btn-sm fw-bold">Minggu</a>
                            <a href="{{ route('siswa.lms.kalender', ['mode' => 'bulan', 'month' => $month, 'year' => $year]) }}"
                                class="btn-nav {{ $viewMode == 'bulan' ? 'btn-primary' : 'btn-outline-primary' }} btn btn-sm fw-bold">Bulan</a>
                            <a href="{{ route('siswa.lms.kalender', ['mode' => 'tahun', 'year' => $year]) }}"
                                class="btn-nav {{ $viewMode == 'tahun' ? 'btn-primary' : 'btn-outline-primary' }} btn btn-sm fw-bold">Tahun</a>
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
                            class="btn-nav btn btn-outline-primary btn-sm rounded-circle shadow-sm calendar-icon-button">
                            <i class="fas fa-chevron-left"></i>
                        </a>

                        <span class="month-title text-center">
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
                            class="btn-nav btn btn-outline-primary btn-sm rounded-circle shadow-sm calendar-icon-button">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Calendar Table --}}
                @if($viewMode == 'minggu')
                    {{-- WEEK VIEW --}}
                    <div class="card border-0 shadow-none">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 weekly-calendar-table">
                                <thead class="bg-primary text-white text-center">
                                    <tr>
                                        <th width="80" class="align-middle py-3">Waktu</th>
                                        @foreach($weekDays as $day)
                                            <th class="py-3 {{ $day->isToday() ? 'week-day-header--today' : '' }}">
                                                <div class="week-day-name">{{ $day->translatedFormat('D') }}</div>
                                                <div class="week-day-date">{{ $day->format('d/m') }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- All Day Row --}}
                                    <tr>
                                        <td class="text-center align-middle bg-light fw-bold text-muted small">All Day</td>
                                        @foreach($weekDays as $day)
                                            <td class="p-1 align-top week-cell week-cell--all-day {{ $day->isToday() ? 'week-cell--today' : '' }}">
                                                @foreach($eventsMinggu as $event)
                                                    @php
                                                        $start = \Carbon\Carbon::parse($event->tanggal_mulai);
                                                        $end = $event->tanggal_selesai ? \Carbon\Carbon::parse($event->tanggal_selesai) : $start;
                                                        $isAllDay = !$event->waktu_mulai;
                                                    @endphp
                                                    @if($isAllDay && $start->lte($day) && $end->gte($day))
                                                        <div class="event event-{{ $event->jenis_kegiatan }} mb-1"
                                                            data-detail-url="{{ route('siswa.lms.kalender.detail', ['tanggal' => $day->format('Y-m-d')]) }}"
                                                            title="{{ $event->nama_kegiatan }}">
                                                            <span
                                                                class="event-text small">{{ Str::limit($event->nama_kegiatan, 15) }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>

                                    {{-- Time Grid (06:00 - 18:00) --}}
                                    @for($hour = 6; $hour <= 18; $hour++)
                                        <tr>
                                            <td class="text-center align-middle text-muted small fw-bold week-time-cell">
                                                {{ sprintf('%02d:00', $hour) }}
                                            </td>
                                            @foreach($weekDays as $day)
                                                <td class="p-1 position-relative border-start-0 border-end week-cell {{ $day->isToday() ? 'week-cell--today' : '' }}">
                                                    @foreach($eventsMinggu as $event)
                                                        @php
                                                            if (!$event->waktu_mulai)
                                                                continue;
                                                            $start = \Carbon\Carbon::parse($event->tanggal_mulai);
                                                            $startTime = \Carbon\Carbon::parse($event->waktu_mulai);

                                                            if (!$start->isSameDay($day))
                                                                continue;
                                                            if ($startTime->hour != $hour)
                                                                continue;
                                                        @endphp

                                                        <div class="event event-{{ $event->jenis_kegiatan }} event--timed p-1 mb-1 shadow-sm"
                                                            data-detail-url="{{ route('siswa.lms.kalender.detail', ['tanggal' => $day->format('Y-m-d')]) }}"
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
                                $monthName = $tempDate->translatedFormat('F');
                                $monthEventsCount = $eventsTahun->filter(function ($e) use ($year, $m) {
                                    $start = \Carbon\Carbon::parse($e->tanggal_mulai);
                                    $end = $e->tanggal_selesai ? \Carbon\Carbon::parse($e->tanggal_selesai) : $start;
                                    return $start->month == $m || $end->month == $m;
                                })->count();
                            @endphp
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <a href="{{ route('siswa.lms.kalender', ['mode' => 'bulan', 'month' => $m, 'year' => $year]) }}"
                                    class="btn-nav card h-100 shadow-sm text-decoration-none border hover-scale calendar-month-card">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3 text-primary">
                                            <i class="far fa-calendar-alt fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">{{ $monthName }}</h5>
                                        <div class="small text-muted">{{ $year }}</div>

                                        <div class="mt-3">
                                            @if($monthEventsCount > 0)
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success">
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
                                                    data-detail-url="{{ route('siswa.lms.kalender.detail', ['tanggal' => $day['fullDate']]) }}"
                                                    title="{{ $event->nama_kegiatan }}">
                                                    <span class="event-dot"></span>
                                                    <span class="event-text">{{ Str::limit($event->nama_kegiatan, 15) }}</span>
                                                </div>
                                            @endforeach
                                            @if($day['events']->count() > 3)
                                                <div class="event event-lainnya"
                                                    data-detail-url="{{ route('siswa.lms.kalender.detail', ['tanggal' => $day['fullDate']]) }}">
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
                                data-detail-url="{{ route('siswa.lms.kalender.detail', ['tanggal' => \Carbon\Carbon::parse($event->tanggal_mulai)->format('Y-m-d')]) }}">
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
</div>
@endsection
