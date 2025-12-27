@extends('layouts.lms')

@php
    $title = 'Jadwal Pelajaran';
    $subtitle = 'Jadwal mingguan kelas ' . ($siswa->kelas->nama_kelas ?? '-');
@endphp

@section('title', $title)
@section('page-title', $title)
@section('page-subtitle', $subtitle)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
<style>
    /* Gunakan warna yang sudah ada di sistem, jangan buat variabel baru yang bentrok */
    .day-section-card {
        background: white;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
        border: none;
    }

    .day-header-blue {
        /* Menggunakan warna biru standar dashboard Anda */
        background: linear-gradient(135deg, #1565c0, #0d47a1);
        color: white;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .day-header-blue h6 {
        margin: 0;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .jadwal-list-container {
        padding: 20px;
        background: #fff;
    }

    .jadwal-row-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 12px;
        border-left: 4px solid #1565c0;
        text-decoration: none;
        color: #333 !important;
        transition: all 0.2s ease-in-out;
    }

    .jadwal-row-item:hover {
        background: #eef2ff;
        transform: translateX(8px);
        border-left: 4px solid #0d47a1;
    }

    .time-box {
        background: white;
        color: #1565c0;
        padding: 8px 12px;
        border-radius: 8px;
        font-weight: 700;
        min-width: 100px;
        text-align: center;
        border: 1px solid rgba(21, 101, 192, 0.1);
    }

    .mapel-content {
        flex: 1;
    }

    .mapel-title {
        font-weight: 700;
        font-size: 1.05rem;
        display: block;
        margin-bottom: 2px;
    }

    .guru-subtitle {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .empty-schedule {
        text-align: center;
        padding: 60px;
        background: white;
        border-radius: 12px;
        color: #adb5bd;
    }

    .info-alert-custom {
        background: #fff;
        border: none;
        border-left: 5px solid #06b6d4;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
</style>

@if($jadwalMingguIni->isEmpty())
    <div class="empty-schedule shadow-sm">
        <i class="fas fa-calendar-alt fa-4x mb-3" style="opacity: 0.2;"></i>
        <h5 class="fw-bold">Belum Ada Jadwal Tersedia</h5>
        <p>Silakan hubungi admin atau wali kelas Anda.</p>
    </div>
@else
    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
        @if($jadwalMingguIni->has($hari))
        <div class="day-section-card shadow-sm">
            <div class="day-header-blue">
                <h6><i class="fas fa-calendar-day me-2"></i>{{ $hari }}</h6>
                <span class="badge bg-light text-primary rounded-pill">
                    {{ $jadwalMingguIni[$hari]->count() }} Mata Pelajaran
                </span>
            </div>

            <div class="jadwal-list-container">
                @foreach($jadwalMingguIni[$hari] as $jadwal)
                <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="jadwal-row-item">
                    <div class="time-box">
                        <i class="far fa-clock me-1"></i>
                        {{ date('H:i', strtotime($jadwal->jam_mulai)) }}
                    </div>
                    <div class="mapel-content">
                        <span class="mapel-title text-primary">{{ $jadwal->mataPelajaran->nama_mapel }}</span>
                        <span class="guru-subtitle">
                            <i class="fas fa-chalkboard-teacher me-1"></i> {{ $jadwal->guru->nama_lengkap ?? 'Guru Pengajar' }}
                        </span>
                    </div>
                    <div class="ms-auto text-primary">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
@endif

<div class="alert info-alert-custom p-4 mt-2">
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