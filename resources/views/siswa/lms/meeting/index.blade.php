@extends('layouts.lms')

@section('title', 'Kelas Virtual')
@section('page-title', 'Kelas Virtual (Meeting)')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .meeting-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s;
            background: #fff;
        }

        .meeting-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #d1d5db;
        }

        .meeting-item.active {
            border-left: 4px solid #10b981;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
    </style>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard LMS
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">
                <i class="fas fa-book"></i> {{ $mataPelajaran->nama_mapel }}
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item active">
            <i class="fas fa-video"></i> Kelas Virtual
        </div>
    </div>>

    <div class="section-card">
        <h3 style="color: #165fac; margin-bottom: 20px; display: flex; align-items: center;">
            <i class="fas fa-video me-2"></i> Daftar Kelas Virtual (Meeting)
        </h3>

        @forelse($meetings as $meeting)
            <div class="meeting-item {{ $meeting->is_active ? 'active' : '' }}">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info text-dark me-2">
                                <i class="fas fa-video me-1"></i> {{ ucfirst(str_replace('_', ' ', $meeting->platform)) }}
                            </span>
                            @if($meeting->waktu_mulai->isFuture())
                                <span class="status-badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Akan Datang</span>
                            @elseif($meeting->is_active)
                                <span class="status-badge bg-success text-white"><i class="fas fa-check-circle me-1"></i>Sedang
                                    Berlangsung</span>
                            @else
                                <span class="status-badge bg-secondary text-white">Selesai</span>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1" style="color: #1a1a1a;">
                            {{ $meeting->judul }}
                        </h5>
                        <div class="text-muted small mb-2">
                            <i class="far fa-calendar-alt me-1"></i> {{ $meeting->waktu_mulai->translatedFormat('l, d F Y') }}
                            <span class="mx-2">•</span>
                            <i class="far fa-clock me-1"></i> {{ $meeting->waktu_mulai->format('H:i') }} -
                            {{ $meeting->waktu_selesai ? $meeting->waktu_selesai->format('H:i') : 'Selesai' }}
                        </div>
                        @if($meeting->deskripsi)
                            <p class="mb-0 text-muted small" style="line-height: 1.5;">
                                {{ $meeting->deskripsi }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-4 text-md-end">
                        @if($meeting->is_active)
                            <a href="{{ $meeting->link_meeting }}" target="_blank" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-video me-2"></i>Masuk Meeting
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled>Link Kedaluwarsa</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-meeting-4450216-3726715.png" alt="Empty"
                    style="width: 150px; opacity: 0.5;">
                <p class="text-muted mt-3 mb-0">Belum ada jadwal meeting yang tersedia.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $meetings->links() }}
        </div>
    </div>
@endsection