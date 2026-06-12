@extends('layouts.lms')

@section('title', 'Kelas Virtual')
@section('page-title', 'Kelas Virtual (Meeting)')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/meeting/index.css'])
@endpush

@section('content')
<div class="siswa-lms-meeting-index-page">
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
    </div>

    <div class="section-card">
        <h3 class="section-title">
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
                        <h5 class="fw-bold mb-1 meeting-title">
                            {{ $meeting->judul }}
                        </h5>
                        <div class="text-muted small mb-2">
                            <i class="far fa-calendar-alt me-1"></i> {{ $meeting->waktu_mulai->translatedFormat('l, d F Y') }}
                            <span class="mx-2">-</span>
                            <i class="far fa-clock me-1"></i> {{ $meeting->waktu_mulai->format('H:i') }} -
                            {{ $meeting->waktu_selesai ? $meeting->waktu_selesai->format('H:i') : 'Selesai' }}
                        </div>
                        @if($meeting->deskripsi)
                            <p class="mb-0 text-muted small meeting-description">
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
                    class="empty-illustration">
                <p class="text-muted mt-3 mb-0">Belum ada jadwal meeting yang tersedia.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $meetings->links() }}
        </div>
    </div>
</div>
@endsection
