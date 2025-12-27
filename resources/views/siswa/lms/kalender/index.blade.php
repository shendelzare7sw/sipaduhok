@extends('layouts.lms')

@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')
@section('page-subtitle', 'Jadwal kegiatan dan acara sekolah')

@section('content')
<style>
    .calendar-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: #e5e7eb;
        border: 1px solid #e5e7eb;
    }
    .calendar-day-header {
        background: #165fac;
        color: white;
        padding: 12px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
    }
    .calendar-day {
        background: white;
        min-height: 100px;
        padding: 8px;
        position: relative;
    }
    .calendar-day.other-month {
        background: #f9fafb;
        color: #9ca3af;
    }
    .calendar-day.today {
        background: #fef3c7;
    }
    .day-number {
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 5px;
    }
    .event-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }
    .event-item {
        font-size: 11px;
        padding: 3px 6px;
        margin-bottom: 3px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .event-item:hover {
        transform: scale(1.05);
    }
    .event-field_trip { background: #dbeafe; color: #1e40af; }
    .event-outing { background: #fef3c7; color: #92400e; }
    .event-live_in { background: #fce7f3; color: #9f1239; }
    .event-hokfest { background: #e0e7ff; color: #3730a3; }
    .event-pts { background: #fed7aa; color: #9a3412; }
    .event-pas { background: #fecaca; color: #991b1b; }
    .event-libur { background: #d1fae5; color: #065f46; }
    .event-ujian { background: #fecaca; color: #991b1b; }
    .event-acara_sekolah { background: #e0e7ff; color: #3730a3; }
</style>

<div class="calendar-container">
    <!-- Header -->
    <div class="calendar-header">
        <div>
            <h4 style="margin: 0; color: #165fac;">
                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
            </h4>
            <small style="color: #666;">Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('siswa.lms.kalender.index', ['month' => $prevMonth['month'], 'year' => $prevMonth['year']]) }}" 
               class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a href="{{ route('siswa.lms.kalender.index') }}" 
               class="btn btn-primary btn-sm">
                <i class="fas fa-calendar-day"></i> Hari Ini
            </a>
            <a href="{{ route('siswa.lms.kalender.index', ['month' => $nextMonth['month'], 'year' => $nextMonth['year']]) }}" 
               class="btn btn-secondary btn-sm">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid">
        <!-- Day Headers -->
        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
        <div class="calendar-day-header">{{ $day }}</div>
        @endforeach

        <!-- Calendar Days -->
        @foreach($calendarDays as $day)
        <div class="calendar-day {{ $day['isOtherMonth'] ? 'other-month' : '' }} {{ $day['isToday'] ? 'today' : '' }}">
            <div class="day-number">{{ $day['day'] }}</div>
            
            @foreach($day['events'] as $event)
            <div class="event-item event-{{ $event->jenis_kegiatan }}" 
                 onclick="window.location.href='{{ route('siswa.lms.kalender.detail', ['date' => $day['fullDate']]) }}'">
                <span class="event-dot" style="background: currentColor;"></span>
                {{ Str::limit($event->nama_kegiatan, 15) }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <!-- Legend -->
    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <h6 style="color: #165fac; margin-bottom: 12px;">
            <i class="fas fa-info-circle"></i> Keterangan
        </h6>
        <div class="row g-2">
            <div class="col-md-3 col-6">
                <small class="event-item event-field_trip d-block">Field Trip</small>
            </div>
            <div class="col-md-3 col-6">
                <small class="event-item event-outing d-block">Outing</small>
            </div>
            <div class="col-md-3 col-6">
                <small class="event-item event-live_in d-block">Live In</small>
            </div>
            <div class="col-md-3 col-6">
                <small class="event-item event-hokfest d-block">HOK Fest</small>
            </div>
            <div class="col-md-3 col-6">
                <small class="event-item event-pts d-block">PTS</small>
            </div>
            <div class="col-md-3 col-6">
                <small class="event-item event-pas d-block">PAS</small>
            </div>
            <div class="col-md-3 col-6">
                <small class="event-item event-libur d-block">Libur</small>
            </div>
            <div class="col-md-3 col-6">
                <small class="event-item event-ujian d-block">Ujian</small>
            </div>
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="alert alert-info mt-4" role="alert">
    <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips</h5>
    <ul style="margin-bottom: 0; padding-left: 20px;">
        <li>Klik pada event untuk melihat detail kegiatan</li>
        <li>Navigasi menggunakan tombol panah untuk berpindah bulan</li>
        <li>Hari ini ditandai dengan warna kuning</li>
    </ul>
</div>

@endsection