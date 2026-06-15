@extends('layouts.lms')

@section('title', 'Daftar Tugas')
@section('page-title', 'Daftar Tugas')
@section('page-subtitle', 'Semua tugas dari berbagai mata pelajaran')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/mata-pelajaran/tugas/index.css'])
@endpush

@section('content')
<div class="siswa-lms-tugas-index-page">
<!-- Summary Stats -->
<div class="row mb-4 summary-row">
    <div class="col-md-3">
        <div class="summary-box summary-box--pending">
            <h3 class="summary-count">{{ $tugasBelum }}</h3>
            <small class="summary-label">Belum Dikerjakan</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="summary-box summary-box--process">
            <h3 class="summary-count">{{ $tugasProses }}</h3>
            <small class="summary-label">Sedang Dikerjakan</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="summary-box summary-box--done">
            <h3 class="summary-count">{{ $tugasSelesai }}</h3>
            <small class="summary-label">Sudah Dinilai</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="summary-box summary-box--late">
            <h3 class="summary-count">{{ $tugasTerlambat }}</h3>
            <small class="summary-label">Terlambat</small>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="filter-card">
    <form method="GET" action="{{ route('siswa.lms.tugas.index') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Mata Pelajaran</label>
            <select name="mapel" class="form-control">
                <option value="">Semua Mata Pelajaran</option>
                @foreach($mataPelajaranList as $mapel)
                <option value="{{ $mapel->id }}" {{ request('mapel') == $mapel->id ? 'selected' : '' }}>
                    {{ $mapel->nama_mapel }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="belum_dikerjakan" {{ request('status') === 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
                <option value="dikerjakan" {{ request('status') === 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                <option value="dinilai" {{ request('status') === 'dinilai' ? 'selected' : '' }}>Dinilai</option>
                <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('siswa.lms.tugas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Daftar Tugas -->
<h4 class="section-heading">
    <i class="fas fa-tasks"></i> Daftar Tugas
</h4>

@forelse($tugasList as $tugas)
@php
    $now = now();
    $deadline = $tugas->tanggal_deadline;
    $isExpired = $now->gt($deadline);
    $hoursLeft = $now->diffInHours($deadline, false);
    
    // Get submission status
    $submission = $tugas->tugasSiswa->where('siswa_id', $siswa->id)->first();
    $status = $submission ? $submission->status : 'belum_dikerjakan';
    
    // Deadline badge
    if ($isExpired) {
        $deadlineClass = 'deadline-expired';
        $deadlineIcon = 'fa-times-circle';
        $deadlineText = 'Sudah Ditutup';
    } elseif ($hoursLeft < 24) {
        $deadlineClass = 'deadline-urgent';
        $deadlineIcon = 'fa-exclamation-circle';
        $deadlineText = 'Segera! (' . $hoursLeft . ' jam)';
    } elseif ($hoursLeft < 72) {
        $deadlineClass = 'deadline-warning';
        $deadlineIcon = 'fa-clock';
        $deadlineText = ceil($hoursLeft / 24) . ' hari lagi';
    } else {
        $deadlineClass = 'deadline-safe';
        $deadlineIcon = 'fa-check-circle';
        $deadlineText = ceil($hoursLeft / 24) . ' hari lagi';
    }
@endphp

<div class="tugas-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="d-flex align-items-center gap-2 mb-2">
                <h5 class="subject-title">
                    <i class="fas fa-book"></i> {{ $tugas->mataPelajaran->nama_mapel }}
                </h5>
                <span class="deadline-badge {{ $deadlineClass }}">
                    <i class="fas {{ $deadlineIcon }}"></i> {{ $deadlineText }}
                </span>
                <span class="status-badge status-{{ $status }}">
                    {{ ucwords(str_replace('_', ' ', $status)) }}
                </span>
            </div>

            <!-- Title -->
            <h6 class="assignment-title">
                {{ $tugas->judul_tugas }}
            </h6>

            <!-- Info -->
            <div class="assignment-meta">
                <div class="d-flex gap-3 flex-wrap">
                    <div>
                        <i class="fas fa-user-tie"></i> {{ $tugas->guru->nama_lengkap }}
                    </div>
                    <div>
                        <i class="fas fa-calendar"></i> {{ $tugas->tanggal_mulai->format('d M Y') }}
                    </div>
                    <div>
                        <i class="fas fa-calendar-times"></i> Tenggat: {{ $deadline->copy()->locale('id')->translatedFormat('d M Y, H:i') }}
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($tugas->deskripsi)
            <p class="assignment-description">
                {{ Str::limit($tugas->deskripsi, 120) }}
            </p>
            @endif

            <!-- Nilai jika sudah dinilai -->
            @if($submission && $submission->status === 'dinilai' && $submission->nilai !== null)
            <div class="alert alert-success submission-score-alert">
                <strong><i class="fas fa-star"></i> Nilai:</strong> {{ $submission->nilai }}
                @if($submission->feedback_guru)
                <br><small>{{ Str::limit($submission->feedback_guru, 80) }}</small>
                @endif
            </div>
            @endif
        </div>

        <div class="col-md-4 text-end">
            <!-- Action Buttons -->
            @if($submission && $submission->status === 'dinilai')
                <a href="{{ route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]) }}" 
                   class="btn btn-success">
                    <i class="fas fa-eye"></i> Lihat Detail
                </a>
            @elseif($isExpired && !$submission)
                <button class="btn btn-secondary" disabled>
                    <i class="fas fa-lock"></i> Ditutup
                </button>
            @elseif($submission)
                <a href="{{ route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]) }}" 
                   class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Jawaban
                </a>
            @else
                <a href="{{ route('siswa.lms.mapel.tugas.show', [$tugas->mata_pelajaran_id, $tugas->id]) }}" 
                   class="btn btn-primary">
                    <i class="fas fa-pencil-alt"></i> Kerjakan
                </a>
            @endif

            <!-- Link ke Mata Pelajaran -->
            <a href="{{ route('siswa.lms.mapel.show', $tugas->mata_pelajaran_id) }}" 
               class="btn btn-info btn-sm mt-2">
                <i class="fas fa-arrow-right"></i> Ke Mata Pelajaran
            </a>
        </div>
    </div>
</div>
@empty
<div class="tugas-card">
    <div class="empty-state">
        <i class="fas fa-tasks fa-3x mb-3"></i>
        <h4>Belum Ada Tugas</h4>
        <p>Belum ada tugas yang tersedia saat ini</p>
        <a href="{{ route('siswa.lms.dashboard') }}" class="btn btn-primary mt-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endforelse

<!-- Pagination -->
@if($tugasList->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $tugasList->links() }}
</div>
@endif

<!-- Info Box -->
<div class="alert alert-info mt-4" role="alert">
    <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi</h5>
    <ul class="info-list">
        <li><strong>Belum Dikerjakan:</strong> Tugas yang belum Anda kerjakan</li>
        <li><strong>Dikerjakan:</strong> Tugas yang sudah Anda submit, menunggu penilaian</li>
        <li><strong>Dinilai:</strong> Tugas yang sudah dinilai oleh guru</li>
        <li><strong>Terlambat:</strong> Tugas yang disubmit setelah deadline</li>
        <li>Tugas yang sudah dinilai tidak dapat diubah lagi</li>
    </ul>
</div>

</div>
@endsection