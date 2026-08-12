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

@push('styles')
    @vite(['resources/css/siswa/lms/jadwal.css'])
@endpush

@section('content')
<div class="siswa-lms-jadwal-page">
    {{-- Print Button --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('siswa.lms.jadwal.print') }}" target="_blank" class="btn btn-primary btn-sm">
            <i class="fas fa-print me-1"></i> Cetak Jadwal
        </a>
    </div>

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
                            <th class="schedule-time-heading">Jam</th>
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
                                            $cellClass = $isBreak ? 'break-cell' : 'subject-cell';
                                        @endphp

                                        <td rowspan="{{ $rowspan }}" colspan="{{ $colspan }}" class="{{ $cellClass }}">
                                            @if($isBreak)
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-coffee me-2"></i> {{ $cell['data']->nama_istirahat ?? 'Istirahat' }}
                                                </div>
                                            @else
                                                @foreach($cell['data'] as $jadwal)
                                                    <div class="mb-2 last:mb-0">
                                                        @php($canOpenMapel = $siswa->canAccessMapel($jadwal->mataPelajaran))
                                                        @if($canOpenMapel)
                                                            <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="text-decoration-none text-dark d-block">
                                                                <strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong>
                                                            </a>
                                                        @else
                                                            <div class="text-dark d-block">
                                                                <strong>{{ $jadwal->mataPelajaran->nama_mapel }}</strong>
                                                            </div>
                                                        @endif
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
                        @if($siswa->canAccessMapel($jadwal->mataPelajaran))
                            <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="today-link">
                                {{ $jadwal->mataPelajaran->nama_mapel }}
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <div class="today-link text-muted pe-none">
                                {{ $jadwal->mataPelajaran->nama_mapel }}
                            </div>
                        @endif
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
                            @if($siswa->canAccessMapel($mapel))
                                <a href="{{ route('siswa.lms.mapel.show', $mapel->id) }}" class="subject-item"
                                    data-name="{{ strtolower($mapel->nama_mapel) }}">
                                    {{ $mapel->nama_mapel }}
                                    <i class="fas fa-chevron-right ms-1 subject-item-icon"></i>
                                </a>
                            @else
                                <div class="subject-item text-muted"
                                    data-name="{{ strtolower($mapel->nama_mapel) }}">
                                    {{ $mapel->nama_mapel }}
                                </div>
                            @endif
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
</div>
@endsection

@push('scripts')
    @vite(['resources/js/siswa/lms/jadwal.js'])
@endpush
