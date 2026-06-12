@extends('layouts.lms')

@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')
@section('page-subtitle', 'Lihat jadwal kegiatan dan agenda sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/kalender.css'])
@endpush

@section('content')
<div class="siswa-lms-kalender-page">
<div class="card-custom">
    <div class="card-header-custom">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-calendar3 me-2"></i>Kalender Akademik Tahun Ini
        </h6>
    </div>

    @if($kalenderTahunan->isEmpty())
    <div class="empty-state">
        <i class="bi bi-calendar-x empty-state-icon"></i>
        <h3 class="empty-state-title">Belum Ada Kegiatan</h3>
        <p class="empty-state-description">Kalender akademik untuk tahun ini belum tersedia.</p>
    </div>
    @else
    <div class="kalender-outer">
        @foreach($kalenderTahunan as $bulan => $kegiatanList)
            @php
                $bulanNama = \Carbon\Carbon::parse($bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            @endphp

            <div class="kalender-month-block">
                <h5 class="kalender-month-title">
                    <i class="bi bi-calendar-event me-2"></i>{{ $bulanNama }}
                </h5>

                <div class="kalender-events-grid">
                    @foreach($kegiatanList as $kegiatan)
                    <div class="kalender-event-card">
                        <div class="kalender-event-row">
                            <div class="kalender-event-info">
                                <h6 class="kalender-event-name">
                                    {{ $kegiatan->nama_kegiatan }}
                                </h6>
                                <div class="kalender-event-meta">
                                    <span>
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $kegiatan->tanggal_mulai->format('d M Y') }}
                                        @if($kegiatan->tanggal_selesai && $kegiatan->tanggal_selesai != $kegiatan->tanggal_mulai)
                                            - {{ $kegiatan->tanggal_selesai->format('d M Y') }}
                                        @endif
                                    </span>
                                    @if($kegiatan->waktu_mulai)
                                    <span>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $kegiatan->waktu_mulai }}
                                        @if($kegiatan->waktu_selesai)
                                            - {{ $kegiatan->waktu_selesai }}
                                        @endif
                                    </span>
                                    @endif
                                </div>
                                @if($kegiatan->keterangan)
                                <p class="kalender-event-desc">
                                    {{ $kegiatan->keterangan }}
                                </p>
                                @endif
                            </div>
                            <span class="badge bg-info text-dark kalender-event-badge">
                                {{ $kegiatan->getJenisLabelAttribute() }}
                            </span>
                        </div>

                        @if($kegiatan->lampiran_surat)
                        <div class="kalender-event-attachment">
                            <a href="{{ asset('storage/' . $kegiatan->lampiran_surat) }}"
                               target="_blank"
                               class="btn btn-sm btn-primary-custom">
                                <i class="bi bi-file-pdf me-1"></i>Lihat Surat
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
</div>
@endsection
