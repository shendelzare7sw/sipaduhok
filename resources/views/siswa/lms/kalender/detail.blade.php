@extends('layouts.lms')

@section('title', 'Detail Kalender')
@section('page-title', 'Detail Kegiatan')
@section('page-subtitle', $tanggal->format('d F Y'))

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .event-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #165fac;
            transition: all 0.3s;
        }

        .event-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .event-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-field_trip {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-outing {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-live_in {
            background: #fce7f3;
            color: #9f1239;
        }

        .badge-hokfest {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-pts {
            background: #fed7aa;
            color: #9a3412;
        }

        .badge-pas {
            background: #fecaca;
            color: #991b1b;
        }

        .badge-libur {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-ujian {
            background: #fecaca;
            color: #991b1b;
        }

        .badge-acara_sekolah {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-lainnya {
            background: #f3f4f6;
            color: #6b7280;
        }

        .jadwal-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .mapel-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .mapel-item:hover {
            background: #f3f4f6;
            transform: translateX(4px);
        }
    </style>

    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('siswa.lms.kalender') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Kalender
        </a>
    </div>

    <!-- Date Header -->
    <div class="content-card"
        style="background: linear-gradient(135deg, #165fac, #0d3f7a); color: white; margin-bottom: 20px;">
        <div style="text-align: center; padding: 20px;">
            <h2 style="margin: 0; color: white;">{{ $tanggal->format('d') }}</h2>
            <h4 style="margin: 5px 0; color: white;">{{ $tanggal->locale('id')->isoFormat('MMMM YYYY') }}</h4>
            <p style="margin: 0; opacity: 0.9;">{{ $tanggal->locale('id')->isoFormat('dddd') }}</p>
        </div>
    </div>

    <!-- Events Section -->
    @if($events->count() > 0)
        <h5 style="margin-bottom: 15px; color: #1a1a1a;">
            <i class="fas fa-calendar-star"></i> Kegiatan Hari Ini ({{ $events->count() }})
        </h5>

        @foreach($events as $event)
            <div class="event-card"
                style="border-color: {{ $event->jenis_kegiatan === 'pts' || $event->jenis_kegiatan === 'pas' ? '#ef4444' : '#165fac' }};">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 style="margin: 0 0 8px 0; color: #165fac;">
                            {{ $event->nama_kegiatan }}
                        </h5>
                        <span class="event-badge badge-{{ $event->jenis_kegiatan }}">
                            {{ $event->jenis_label }}
                        </span>
                    </div>
                    @if($event->waktu_mulai)
                        <div style="text-align: right; color: #666;">
                            <i class="fas fa-clock"></i>
                            {{ \Carbon\Carbon::parse($event->waktu_mulai)->format('H:i') }}
                            @if($event->waktu_selesai)
                                - {{ \Carbon\Carbon::parse($event->waktu_selesai)->format('H:i') }}
                            @endif
                        </div>
                    @endif
                </div>

                @if($event->keterangan)
                    <div style="padding: 12px; background: #f9fafb; border-radius: 8px; margin-bottom: 12px;">
                        <small style="color: #666; font-weight: 500;">Keterangan:</small>
                        <p style="margin: 5px 0 0 0; color: #1a1a1a; line-height: 1.6;">
                            {{ $event->keterangan }}
                        </p>
                    </div>
                @endif

                <!-- Duration Info -->
                @if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai)
                    <div style="color: #666; font-size: 14px;">
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
        <div class="content-card" style="text-align: center; padding: 48px; color: #999;">
            <i class="fas fa-calendar-times fa-3x mb-3"></i>
            <h5>Tidak Ada Kegiatan</h5>
            <p>Tidak ada kegiatan terjadwal pada tanggal ini</p>
        </div>
    @endif

    <!-- Jadwal Pelajaran Section -->
    @if($jadwalPelajaran->count() > 0)
        <h5 style="margin: 30px 0 15px 0; color: #1a1a1a;">
            <i class="fas fa-book"></i> Jadwal Pelajaran
        </h5>

        <div class="jadwal-card">
            @foreach($jadwalPelajaran as $jadwal)
                <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="mapel-item"
                    style="text-decoration: none; color: inherit;">
                    <div style="flex: 1;">
                        <strong style="color: #165fac; display: block; margin-bottom: 4px;">
                            {{ $jadwal->mataPelajaran->nama_mapel }}
                        </strong>
                        <small style="color: #666;">
                            <i class="fas fa-user-tie"></i> {{ $jadwal->guru->nama_lengkap }}
                        </small>
                    </div>
                    <div style="text-align: right; color: #666;">
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
            <div class="content-card" style="text-align: center; padding: 32px; color: #999;">
                <i class="fas fa-chalkboard fa-2x mb-3"></i>
                <h6>Tidak Ada Jadwal Pelajaran</h6>
                <p style="margin: 0;">Tidak ada mata pelajaran terjadwal pada hari ini</p>
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

@endsection