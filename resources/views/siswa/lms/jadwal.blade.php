@extends('layouts.lms')

@php
    $title = 'Jadwal Pelajaran';
    $subtitle = 'Jadwal mingguan kelas ' . ($kelas->nama_kelas ?? '-');
@endphp

@section('title', $title)
@section('page-title', $title)
@section('page-subtitle', $subtitle)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    {{-- Print Button --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('siswa.lms.jadwal.print') }}" target="_blank" class="btn btn-primary btn-sm">
            <i class="fas fa-print me-1"></i> Cetak Jadwal
        </a>
    </div>

    <style>
        /* Weekly Schedule Table */
        .schedule-table-wrapper {
            overflow-x: auto;
            margin-bottom: 24px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 800px;
        }

        .schedule-table th {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #0d47a1;
        }

        .schedule-table td {
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
            min-width: 100px;
        }

        .schedule-table .time-cell {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 12px;
            white-space: nowrap;
        }

        .schedule-table .subject-cell {
            background: white;
            transition: all 0.2s;
        }

        .schedule-table .subject-cell:hover {
            background: #dbeafe;
        }

        .schedule-table .break-row td {
            background: #fef9c3 !important;
            color: #854d0e;
            font-weight: 600;
        }

        .schedule-table .break-row td.break-label {
            font-style: italic;
        }

        /* Section Cards */
        .section-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            height: 100%;
        }

        .section-header {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            color: white;
            padding: 14px 18px;
            font-weight: 700;
            font-size: 15px;
            border-bottom: none;
        }

        .section-body {
            padding: 16px;
        }

        /* Today Schedule Links */
        .today-link {
            display: block;
            padding: 10px 14px;
            color: #1565c0;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 6px;
            font-weight: 500;
            transition: all 0.2s;
            background: #f8fafc;
        }

        .today-link:hover {
            background: #dbeafe;
            color: #0d47a1;
            transform: translateX(4px);
        }

        .today-link i {
            margin-left: 8px;
            font-size: 11px;
        }

        /* Subject Grid */
        .subject-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .subject-item {
            display: inline-block;
            padding: 8px 14px;
            background: #f1f5f9;
            color: #1565c0;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .subject-item:hover {
            background: #dbeafe;
            color: #0d47a1;
        }

        /* Search Input */
        .search-input-wrapper {
            position: relative;
            margin-bottom: 12px;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 10px 14px 10px 36px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input-wrapper input:focus {
            border-color: #1565c0;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
        }

        /* Class Info Header */
        .class-info-header {
            background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .class-info-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 18px;
        }

        .class-info-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            opacity: 0.9;
        }

        .class-info-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>

    {{-- Class Info Header --}}
    <div class="class-info-header">
        <h5><i class="fas fa-calendar-alt me-2"></i>Jadwal Pelajaran {{ $kelas->nama_kelas }}</h5>
        <div class="class-info-meta">
            <span><i class="fas fa-graduation-cap"></i> {{ strtoupper($kelas->jenjang) }}</span>
            <span><i class="fas fa-calendar"></i> {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
            <span><i class="fas fa-user-tie"></i> Wali: {{ $kelas->waliKelas->nama_lengkap ?? '-' }}</span>
        </div>
    </div>

    {{-- Section 1: Weekly Schedule Table --}}
    <div class="card mb-4">
        <div class="card-body p-0">
            <div class="schedule-table-wrapper">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Jam</th>
                            @foreach($scheduleGrid['days'] as $day)
                                <th>{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scheduleGrid['rows'] as $row)
                            <tr>
                                <td class="time-cell">{{ $row['time_start'] }}</td>
                                @for($i = 0; $i < count($scheduleGrid['days']); $i++)
                                    @php 
                                        $day = $scheduleGrid['days'][$i];
                                        $cell = $row['days'][$day]; 
                                    @endphp

                                    @if($cell['type'] == 'taken')
                                        <!-- Spanned -->
                                    @elseif($cell['type'] == 'empty')
                                        <td></td>
                                    @else
                                        @php
                                            // Check horizontal merge (colspan) for Breaks
                                            $colspan = 1;
                                            if ($cell['type'] == 'break') {
                                                $breakName = $cell['data']->nama_istirahat ?? 'Istirahat';
                                                for ($j = $i + 1; $j < count($scheduleGrid['days']); $j++) {
                                                    $nextDay = $scheduleGrid['days'][$j];
                                                    $nextCell = $row['days'][$nextDay];
                                                    if ($nextCell['type'] == 'break' && ($nextCell['data']->nama_istirahat ?? '') == $breakName && $nextCell['data']->jam_mulai == $cell['data']->jam_mulai) {
                                                        $colspan++;
                                                    } else {
                                                        break;
                                                    }
                                                }
                                            }
                                            $i += ($colspan - 1);
                                            
                                            $rowspan = $cell['rowspan'] ?? 1;
                                            $isBreak = $cell['type'] == 'break';
                                            $cellClass = $isBreak ? 'break-row' : 'subject-cell';
                                            $style = $isBreak ? 'background: #fef9c3; color: #854d0e; font-weight: 600; font-style: italic;' : '';
                                        @endphp

                                        <td rowspan="{{ $rowspan }}" colspan="{{ $colspan }}" class="{{ $cellClass }}" style="{{ $style }}">
                                            @if($isBreak)
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-coffee me-2"></i> {{ $cell['data']->nama_istirahat ?? 'Istirahat' }}
                                                </div>
                                            @else
                                                @foreach($cell['data'] as $jadwal)
                                                    <div class="mb-2 last:mb-0">
                                                        <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="text-decoration-none text-dark d-block">
                                                            <strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong>
                                                        </a>
                                                        <div class="small text-muted">
                                                            {{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '(-)' }}
                                                        </div>
                                                        <div class="badge bg-light text-dark border mt-1">
                                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                        </div>

                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                    @endif
                                @endfor
                            </tr>
                        @endforeach
                        
                        @if(empty($scheduleGrid['rows']))
                             <tr>
                                <td colspan="{{ count($scheduleGrid['days']) + 1 }}" class="empty-state">
                                    Belum ada jadwal pelajaran
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Section 2 & 3: Today's Schedule + Subject List --}}
    <div class="row">
        {{-- Today's Schedule --}}
        <div class="col-md-4 mb-4">
            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-sun me-2"></i>Hari ini ({{ $hariIni }})
                </div>
                <div class="section-body">
                    @forelse($jadwalHariIni as $jadwal)
                        <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="today-link">
                            {{ $jadwal->mataPelajaran->nama_mapel }}
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                            Tidak ada jadwal hari ini
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Subject List with Search --}}
        <div class="col-md-8 mb-4">
            <div class="section-card">
                <div class="section-header">
                    <i class="fas fa-book me-2"></i>Mata Pelajaran Lengkap
                </div>
                <div class="section-body">
                    {{-- Search Input --}}
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchMapel" placeholder="Cari mata pelajaran...">
                    </div>

                    {{-- Subject Grid --}}
                    <div class="subject-grid" id="subjectGrid">
                        @forelse($mataPelajaranList as $mapel)
                            <a href="{{ route('siswa.lms.mapel.show', $mapel->id) }}" class="subject-item"
                                data-name="{{ strtolower($mapel->nama_mapel) }}">
                                {{ $mapel->nama_mapel }}
                                <i class="fas fa-chevron-right ms-1" style="font-size: 10px;"></i>
                            </a>
                        @empty
                            <div class="empty-state w-100">
                                Belum ada mata pelajaran
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="alert bg-white border-0 border-start border-5 border-info shadow-sm p-4">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-info-circle text-info me-2"></i>
            <h6 class="fw-bold mb-0">Informasi Penting</h6>
        </div>
        <ul class="mb-0 small text-muted">
            <li>Klik pada nama mata pelajaran untuk melihat materi dan mengumpulkan tugas.</li>
            <li>Jadwal ini merupakan jadwal rutin mingguan Anda.</li>
        </ul>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchMapel');
            const subjectItems = document.querySelectorAll('.subject-item');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();

                    subjectItems.forEach(function (item) {
                        const name = item.getAttribute('data-name');
                        if (name.includes(query)) {
                            item.style.display = 'inline-block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
@endpush