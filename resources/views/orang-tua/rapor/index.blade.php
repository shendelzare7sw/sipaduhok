@extends('layouts.sneat')

@section('title', 'Rapor - ' . $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('orang-tua.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Rapor</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">Rapor</h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-2">|</span>
                <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <div>
            <a href="{{ route('orang-tua.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="avatar avatar-lg">
                        <div class="avatar-initial rounded-circle bg-label-primary">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <h5 class="mb-1">{{ $siswa->nama_lengkap }}</h5>
                    <div class="text-muted small">
                        <span class="me-3">
                            <i class="fas fa-id-card me-1"></i>NISN: {{ $siswa->nisn }}
                        </span>
                        <span class="me-3">
                            <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                        </span>
                        <span>
                            <i class="fas fa-building me-1"></i>{{ $siswa->cabang->nama_cabang ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Rapor -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2 text-primary"></i>
                Daftar Rapor
            </h5>
            <span class="badge bg-label-primary">{{ $rapor->count() }} Rapor</span>
        </div>
        <div class="card-body">
            @if(isset($locked) && $locked)
                <div class="alert alert-warning d-flex align-items-center mb-0">
                    <i class="fas fa-lock me-2"></i>
                    <div>
                        <strong>Akses Terkunci.</strong><br>
                        Rapor belum dapat dilihat karena belum divalidasi oleh Wali Kelas. 
                        Silakan hubungi Wali Kelas atau selesaikan administrasi jika diperlukan.
                    </div>
                </div>
            @elseif($rapor->isEmpty())
                <div class="alert alert-info d-flex align-items-center mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>Belum ada rapor yang tersedia untuk siswa ini.</div>
                </div>
            @else
                <div class="row">
                    @foreach($rapor as $r)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="avatar flex-shrink-0">
                                            <div class="avatar-initial rounded bg-label-success">
                                                <i class="fas fa-book-open"></i>
                                            </div>
                                        </div>
                                        <span class="badge bg-primary">Semester {{ $r->semester }}</span>
                                    </div>

                                    <h5 class="card-title mb-2">{{ $r->tahunAjaran->nama_tahun_ajaran ?? 'Tahun Ajaran' }}</h5>
                                    <p class="text-muted mb-3">
                                        <small>
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $r->tahunAjaran->tahun_mulai ?? '-' }} / {{ $r->tahunAjaran->tahun_selesai ?? '-' }}
                                        </small>
                                    </p>

                                    @if($r->nilai_rata_rata)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted fw-bold">Nilai Rata-rata</small>
                                                @php
                                                    $avg = $r->nilai_rata_rata;
                                                    $colorClass = $avg >= 85 ? 'text-success' : ($avg >= 70 ? 'text-primary' : ($avg >= 60 ? 'text-warning' : 'text-danger'));
                                                @endphp
                                                <strong class="{{ $colorClass }}">{{ number_format($r->nilai_rata_rata, 2) }}</strong>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                @php
                                                    $percentage = ($r->nilai_rata_rata / 100) * 100;
                                                    $barColor = $avg >= 85 ? 'bg-success' : ($avg >= 70 ? 'bg-primary' : ($avg >= 60 ? 'bg-warning' : 'bg-danger'));
                                                @endphp
                                                <div class="progress-bar {{ $barColor }}"
                                                     role="progressbar"
                                                     style="width: {{ $percentage }}%"
                                                     aria-valuenow="{{ $r->nilai_rata_rata }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="d-grid">
                                        <a href="{{ route('orang-tua.rapor.detail', $r->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
