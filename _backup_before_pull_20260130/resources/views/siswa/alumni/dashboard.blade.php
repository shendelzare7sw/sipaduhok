@extends('layouts.app')

@section('title', 'Alumni Dashboard')

@section('content')
<div class="row">
    <!-- Welcome Banner -->
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl me-3 bg-white rounded-circle p-1">
                        <img src="{{ $siswa->foto_profil ?? asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" width="100%">
                    </div>
                    <div>
                        <h4 class="text-white mb-0">Selamat, {{ $siswa->nama_lengkap }}! 🎓</h4>
                        <p class="mb-0 opacity-75">Anda telah lulus dari PKBM House of Knowledge.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile & Status -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Profil Alumni</h5>
            </div>
            <div class="card-body mt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">NISN:</span>
                    <span>{{ $siswa->nisn }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">Angkatan Lulus:</span>
                    <span>{{ $raporTerakhir->tahunAjaran->nama_tahun_ajaran ?? 'Unknown' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">Status:</span>
                    <span class="badge bg-success">LULUS / ALUMNI</span>
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('profile.index') }}" class="btn btn-outline-primary">
                        <i class="bx bx-user me-1"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic History / Documents -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Dokumen Akademik</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bx bx-info-circle me-2"></i>
                    <div>
                        dokumen ijazah asli dapat diambil di bagian Tata Usaha sekolah pada jam kerja.
                    </div>
                </div>

                <div class="list-group">
                    <a href="{{ route('siswa.sia.rapor.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-book-open fs-3 text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0">Riwayat Rapor</h6>
                                <small class="text-muted">Lihat nilai rapor selama masa studi</small>
                            </div>
                        </div>
                        <i class="bx bx-chevron-right"></i>
                    </a>
                    
                    @if($raporTerakhir)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-certification fs-3 text-warning me-3"></i>
                            <div>
                                <h6 class="mb-0">Transkrip Nilai Akhir</h6>
                                <small class="text-muted">Semester {{ $raporTerakhir->semester }} - {{ $raporTerakhir->tahunAjaran->nama_tahun_ajaran ?? '' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('siswa.sia.rapor.download', $raporTerakhir->id) }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-download"></i> Download
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
