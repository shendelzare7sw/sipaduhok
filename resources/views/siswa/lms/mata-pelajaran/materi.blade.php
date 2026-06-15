@extends('layouts.lms')

@section('title', 'Materi - ' . $materi->judul_materi)
@section('page-title', $materi->mataPelajaran->nama_mapel)
@section('page-subtitle', 'Detail Materi Pembelajaran')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/mata-pelajaran/materi.css'])
@endpush

@section('content')
<div class="siswa-lms-materi-page">
<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="page-breadcrumb-item">
        <a href="{{ route('siswa.lms.dashboard') }}">
            <i class="fas fa-home"></i> Dashboard LMS
        </a>
    </div>
    <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
    <div class="page-breadcrumb-item">
        <a href="{{ route('siswa.lms.mapel.show', $materi->mata_pelajaran_id) }}">
            <i class="fas fa-book"></i> {{ $materi->mataPelajaran->nama_mapel }}
        </a>
    </div>
    <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
    <div class="page-breadcrumb-item active">
        <i class="fas fa-file-alt"></i> {{ Str::limit($materi->judul_materi, 30) }}
    </div>
</div>

<div class="materi-card">
    <!-- Header -->
    <div class="materi-header">
        <h2 class="materi-title">
            <i class="fas fa-book-open"></i> {{ $materi->judul_materi }}
        </h2>
        
        <div class="materi-meta">
            <div class="meta-item">
                <i class="fas fa-user-tie"></i>
                <span><strong>Guru:</strong> {{ $materi->guru->nama_lengkap }}</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-calendar"></i>
                <span><strong>Tanggal:</strong> {{ $materi->tanggal_upload->format('d F Y') }}</span>
            </div>
            @if($materi->tipe_file)
            <div class="meta-item">
                <i class="fas fa-file"></i>
                <span><strong>Tipe:</strong> {{ strtoupper($materi->tipe_file) }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Deskripsi -->
    @if($materi->deskripsi)
    <div class="content-section">
        <h4 class="content-title">
            <i class="fas fa-align-left"></i> Deskripsi Materi
        </h4>
        <p class="content-text">
            {{ $materi->deskripsi }}
        </p>
    </div>
    @endif

    <!-- File Preview/Download -->
    @if($materi->file_materi)
    <div class="file-preview">
        @if($materi->tipe_file === 'link')
            <div class="file-icon">
                <i class="fas fa-link"></i>
            </div>
            <h4 class="file-link-title">Link Materi</h4>
            <p class="file-link-description">Link eksternal ke materi pembelajaran</p>
            <a href="{{ $materi->file_materi }}" target="_blank" class="download-btn">
                <i class="fas fa-external-link-alt"></i> Buka Link
            </a>
        @else
            <x-file-preview :path="$materi->file_materi" label="Lihat Materi" />
        @endif
    </div>
    @else
    <div class="alert alert-warning" role="alert">
        <i class="fas fa-exclamation-triangle"></i> Belum ada file materi yang diunggah
    </div>
    @endif

    <!-- Navigation -->
    <div class="materi-actions">
        <a href="{{ route('siswa.lms.mapel.show', $materi->mata_pelajaran_id) }}" 
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Mata Pelajaran
        </a>
    </div>
</div>

<!-- Info Box -->
<div class="alert alert-info mt-4" role="alert">
    <h5 class="alert-heading">
        <i class="fas fa-lightbulb"></i> Tips Belajar
    </h5>
    <ul class="tips-list">
        <li>Baca materi dengan seksama sebelum mengerjakan tugas</li>
        <li>Catat hal-hal penting untuk memudahkan belajar</li>
        <li>Jika ada yang tidak dipahami, tanyakan di forum diskusi</li>
        <li>Download materi untuk dipelajari secara offline</li>
    </ul>
</div>
</div>
@endsection
