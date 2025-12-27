@extends('layouts.lms')

@section('title', $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Materi, Tugas, dan Ujian')
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
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .item-list {
        border-bottom: 1px solid #e5e7eb;
        padding: 16px 0;
    }
    .item-list:last-child {
        border-bottom: none;
    }
    .badge-new {
        background: #fef3c7;
        color: #92400e;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
</style>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" style="margin-bottom: 20px;">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('siswa.lms.dashboard') }}">Dashboard LMS</a></li>
        <li class="breadcrumb-item active">{{ $mataPelajaran->nama_mapel }}</li>
    </ol>
</nav>

<!-- Header Info -->
<div class="section-card" style="background: linear-gradient(135deg, #165fac, #0d3f7a); color: white;">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 style="margin: 0 0 8px 0;">
                <i class="fas fa-book"></i> {{ $mataPelajaran->nama_mapel }}
            </h2>
            <p style="margin: 0; opacity: 0.9;">
                Kode Mapel: {{ $mataPelajaran->kode_mapel }} | Jenjang: {{ $mataPelajaran->jenjang }}
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 8px; display: inline-block;">
                <i class="fas fa-graduation-cap fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<!-- MATERI -->
<div class="section-card">
    <h3 style="color: #165fac; margin-bottom: 20px;">
        <i class="fas fa-file-alt"></i> Materi Pembelajaran
    </h3>

    @forelse($materiList as $materi)
    <div class="item-list">
        <div class="d-flex justify-content-between align-items-start">
            <div style="flex: 1;">
                <h5 style="margin: 0 0 8px 0; color: #1a1a1a;">
                    {{ $materi->judul_materi }}
                    @if($materi->created_at->diffInDays(now()) < 3)
                        <span class="badge-new">BARU</span>
                    @endif
                </h5>
                <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">
                    {{ Str::limit($materi->deskripsi, 150) }}
                </p>
                <small style="color: #999;">
                    <i class="fas fa-calendar"></i> {{ $materi->tanggal_upload->format('d M Y') }}
                    | <i class="fas fa-file"></i> {{ strtoupper($materi->tipe_file ?? 'File') }}
                </small>
            </div>
            <div>
                <a href="{{ route('siswa.lms.mapel.materi', [$mataPelajaran->id, $materi->id]) }}" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat
                </a>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 32px; color: #999;">
        <i class="fas fa-folder-open fa-2x mb-2"></i>
        <p style="margin: 0;">Belum ada materi yang tersedia</p>
    </div>
    @endforelse
</div>

<!-- TUGAS & LATIHAN -->
<div class="section-card">
    <h3 style="color: #165fac; margin-bottom: 20px;">
        <i class="fas fa-tasks"></i> Tugas & Latihan
    </h3>

    @forelse($tugasList as $tugas)
    <div class="item-list">
        <div class="d-flex justify-content-between align-items-start">
            <div style="flex: 1;">
                <h5 style="margin: 0 0 8px 0; color: #1a1a1a;">
                    {{ $tugas->judul_tugas }}
                    @if($tugas->tanggal_deadline->isFuture())
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Ditutup</span>
                    @endif
                </h5>
                <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">
                    {{ Str::limit($tugas->deskripsi, 150) }}
                </p>
                <small style="color: #999;">
                    <i class="fas fa-clock"></i> 
                    Deadline: {{ $tugas->tanggal_deadline->format('d M Y, H:i') }}
                    @if($tugas->tanggal_deadline->isFuture())
                        <span style="color: #10b981;">({{ $tugas->tanggal_deadline->diffForHumans() }})</span>
                    @else
                        <span style="color: #dc2626;">(Sudah Lewat)</span>
                    @endif
                </small>
            </div>
            <div>
                <a href="{{ route('siswa.lms.mapel.tugas.show', [$mataPelajaran->id, $tugas->id]) }}" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-pencil-alt"></i> Kerjakan
                </a>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 32px; color: #999;">
        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
        <p style="margin: 0;">Belum ada tugas yang tersedia</p>
    </div>
    @endforelse
</div>

<!-- UJIAN -->
<div class="section-card">
    <h3 style="color: #165fac; margin-bottom: 20px;">
        <i class="fas fa-file-signature"></i> Ujian
    </h3>

    @forelse($ujianList as $ujian)
    <div class="item-list">
        <div class="d-flex justify-content-between align-items-start">
            <div style="flex: 1;">
                <h5 style="margin: 0 0 8px 0; color: #1a1a1a;">
                    {{ $ujian->judul_ujian }}
                    <span class="badge" style="background: #8b5cf6; color: white;">
                        {{ strtoupper(str_replace('_', ' ', $ujian->tipe_ujian)) }}
                    </span>
                </h5>
                <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">
                    {{ $ujian->deskripsi ?? 'Ujian ' . $ujian->tipe_ujian }}
                </p>
                <small style="color: #999;">
                    <i class="fas fa-calendar-alt"></i> 
                    {{ $ujian->tanggal_mulai->format('d M Y, H:i') }} - {{ $ujian->tanggal_selesai->format('H:i') }}
                    | <i class="fas fa-stopwatch"></i> Durasi: {{ $ujian->durasi_menit }} menit
                </small>
            </div>
            <div>
                @if($ujian->isOngoing())
                    <a href="{{ route('siswa.lms.mapel.ujian.show', [$mataPelajaran->id, $ujian->id]) }}" 
                       class="btn btn-danger btn-sm">
                        <i class="fas fa-play"></i> Mulai Ujian
                    </a>
                @elseif($ujian->tanggal_mulai->isFuture())
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-lock"></i> Belum Dimulai
                    </button>
                @else
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-check"></i> Selesai
                    </button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 32px; color: #999;">
        <i class="fas fa-pen-square fa-2x mb-2"></i>
        <p style="margin: 0;">Belum ada ujian yang tersedia</p>
    </div>
    @endforelse
</div>

<!-- Forum Diskusi (Placeholder) -->
<div class="section-card">
    <h3 style="color: #165fac; margin-bottom: 16px;">
        <i class="fas fa-comments"></i> Forum Diskusi
    </h3>
    <div style="text-align: center; padding: 24px; background: #f9fafb; border-radius: 8px;">
        <i class="fas fa-tools fa-2x mb-2" style="color: #999;"></i>
        <p style="margin: 0; color: #666;">Fitur forum diskusi sedang dalam pengembangan</p>
    </div>
</div>

@endsection