@extends('layouts.lms')

@section('title', 'Materi - ' . $materi->judul_materi)
@section('page-title', $materi->mataPelajaran->nama_mapel)
@section('page-subtitle', 'Detail Materi Pembelajaran')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
<style>
    .materi-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .materi-header {
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }
    .materi-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 15px;
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #666;
        font-size: 14px;
    }
    .meta-item i {
        color: #165fac;
    }
    .file-preview {
        background: #f9fafb;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        margin: 25px 0;
    }
    .file-icon {
        font-size: 64px;
        color: #165fac;
        margin-bottom: 15px;
    }
    .download-btn {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    .download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
        color: white;
    }
    .content-section {
        background: white;
        padding: 25px;
        border-radius: 8px;
        border-left: 4px solid #165fac;
        margin: 20px 0;
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
        <h2 style="color: #165fac; margin: 0 0 10px 0;">
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
        <h4 style="color: #165fac; margin-bottom: 15px;">
            <i class="fas fa-align-left"></i> Deskripsi Materi
        </h4>
        <p style="color: #333; line-height: 1.8; margin: 0;">
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
            <h4 style="color: #1a1a1a; margin-bottom: 10px;">Link Materi</h4>
            <p style="color: #666; margin-bottom: 20px;">Link eksternal ke materi pembelajaran</p>
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
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
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
    <ul style="margin-bottom: 0; padding-left: 20px;">
        <li>Baca materi dengan seksama sebelum mengerjakan tugas</li>
        <li>Catat hal-hal penting untuk memudahkan belajar</li>
        <li>Jika ada yang tidak dipahami, tanyakan di forum diskusi</li>
        <li>Download materi untuk dipelajari secara offline</li>
    </ul>
</div>

@endsection