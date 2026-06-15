@extends('layouts.lms')

@section('title', 'Detail Kalender')
@section('page-title', 'Detail Kegiatan')
@section('page-subtitle', $tanggal->format('d F Y'))

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/kalender/detail.css'])
@endpush

@section('content')
<div class="siswa-lms-kalender-detail-page">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('siswa.lms.kalender') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Kalender
        </a>
    </div>

    <!-- Date Header -->
    <div class="content-card date-header-card">
        <div class="date-header-inner">
            <h2 class="date-day">{{ $tanggal->format('d') }}</h2>
            <h4 class="date-month">{{ $tanggal->locale('id')->isoFormat('MMMM YYYY') }}</h4>
            <p class="date-weekday">{{ $tanggal->locale('id')->isoFormat('dddd') }}</p>
        </div>
    </div>

    <!-- Events Section -->
    @if($events->count() > 0)
        <h5 class="section-heading">
            <i class="fas fa-calendar-star"></i> Kegiatan Hari Ini ({{ $events->count() }})
        </h5>

        @foreach($events as $event)
            <div class="event-card {{ in_array($event->jenis_kegiatan, ['pts', 'pas']) ? 'event-card--exam' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="event-title">
                            {{ $event->nama_kegiatan }}
                        </h5>
                        <span class="event-badge badge-{{ $event->jenis_kegiatan }}">
                            {{ $event->jenis_label }}
                        </span>
                    </div>
                    @if($event->waktu_mulai)
                        <div class="event-time">
                            <i class="fas fa-clock"></i>
                            {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }}
                            @if($event->waktu_selesai)
                                - {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('H:i') }}
                            @endif
                        </div>
                    @endif
                </div>

                @if($event->keterangan)
                    <div class="event-note">
                        <small class="event-note-label">Keterangan:</small>
                        <p class="event-note-text">
                            {{ $event->keterangan }}
                        </p>
                    </div>
                @endif

                <!-- Duration Info -->
                @if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai)
                    <div class="event-duration">
                        <i class="fas fa-calendar-alt"></i>
                        Berlangsung {{ $event->tanggal_mulai->format('d M') }} - {{ $event->tanggal_selesai->format('d M Y') }}
                        ({{ $event->durasi }} hari)
                    </div>
                @endif

                <!-- Lampiran Surat -->
                @if($event->lampiran_surat)
                    <div class="mt-2">
                        @if(filter_var($event->lampiran_surat, FILTER_VALIDATE_URL))
                            <a href="{{ $event->lampiran_surat }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-external-link-alt"></i> Lihat Surat/Link
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $event->lampiran_surat) }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i> Download Surat
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="content-card empty-state">
            <i class="fas fa-calendar-times fa-3x mb-3"></i>
            <h5>Tidak Ada Kegiatan</h5>
            <p>Tidak ada kegiatan terjadwal pada tanggal ini</p>
        </div>
    @endif

    <!-- Jadwal Pelajaran Section -->
    @if($jadwalPelajaran->count() > 0)
        <h5 class="section-heading section-heading--spaced">
            <i class="fas fa-book"></i> Jadwal Pelajaran
        </h5>

        <div class="jadwal-card">
            @foreach($jadwalPelajaran as $jadwal)
                <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="mapel-item">
                    <div class="mapel-content">
                        <strong class="mapel-name">
                            {{ $jadwal->mataPelajaran->nama_mapel }}
                        </strong>
                        <small class="mapel-teacher">
                            <i class="fas fa-user-tie"></i> {{ $jadwal->guru->nama_lengkap }}
                        </small>
                    </div>
                    <div class="mapel-time">
                        <i class="fas fa-clock"></i>
                        {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}
                    </div>
                </a>
            @endforeach
        </div>

        <div class="alert alert-info" role="alert">
            <i class="fas fa-lightbulb"></i>
            <strong>Tips:</strong> Klik pada mata pelajaran untuk melihat materi, tugas, dan ujian
        </div>
    @else
        @if($isWeekday)
            <div class="content-card empty-state empty-state--compact">
                <i class="fas fa-chalkboard fa-2x mb-3"></i>
                <h6>Tidak Ada Jadwal Pelajaran</h6>
                <p class="empty-text">Tidak ada mata pelajaran terjadwal pada hari ini</p>
            </div>
        @endif
    @endif

    <!-- Navigation -->
    <div class="d-flex justify-content-between mt-4">
        @if($prevDate)
            <a href="{{ route('siswa.lms.kalender.detail', ['tanggal' => $prevDate->format('Y-m-d')]) }}"
                class="btn btn-secondary">
                <i class="fas fa-chevron-left"></i> {{ $prevDate->format('d M') }}
            </a>
        @else
            <div></div>
        @endif

        @if($nextDate)
            <a href="{{ route('siswa.lms.kalender.detail', ['tanggal' => $nextDate->format('Y-m-d')]) }}"
                class="btn btn-secondary">
                {{ $nextDate->format('d M') }} <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <div></div>
        @endif
    </div>
</div>

@endsection
