@extends('layouts.app')

@section('title', 'Dashboard Alumni')
@section('page-title', 'Dashboard Alumni')
@section('page-subtitle', 'Akses arsip akademik Anda')

@section('sidebar-menu')
    {{-- Sidebar minimal alumni: hanya dashboard, riwayat, logout --}}
    <li class="menu-item active">
        <a href="{{ route('siswa.sia.dashboard') }}" class="menu-link">
            <i class="menu-icon fas fa-graduation-cap"></i>
            <div>Dashboard Alumni</div>
        </a>
    </li>
    <li class="menu-item">
        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
            @csrf
            <button type="submit" class="menu-link border-0 bg-transparent w-100 text-start"
                    data-confirm-submit="Yakin ingin logout?">
                <i class="menu-icon fas fa-sign-out-alt"></i>
                <div>Logout</div>
            </button>
        </form>
    </li>
@endsection

@push('styles')
    @vite(['resources/css/siswa/alumni/dashboard.css'])
@endpush

@push('scripts')
    @vite(['resources/js/siswa/alumni/dashboard.js'])
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="alumni-hero">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-graduation-cap fa-2x"></i>
            <div>
                <div class="greeting">Selamat, {{ $siswa->nama_lengkap }}!</div>
                <div class="subtext">
                    Anda telah dinyatakan <strong>LULUS</strong> dari PKBM.
                </div>
            </div>
        </div>
    </div>

    <div class="alumni-grid">
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="alumni-action-card w-100 border-0 text-start"
                    data-confirm-submit="Yakin ingin logout?">
                <div class="icon-circle icon-circle-danger"><i class="fas fa-sign-out-alt"></i></div>
                <div>
                    <div class="label">Logout</div>
                    <div class="desc">Keluar dari sistem</div>
                </div>
            </button>
        </form>
    </div>

    <div class="info-card">
        <h5 class="fw-bold mb-2"><i class="fas fa-id-card me-2 text-primary"></i>Profil Saya</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <img src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : asset('img/logo.png') }}"
                     alt="Foto" class="rounded shadow-sm alumni-photo">
            </div>
            <div class="col-md-9">
                <table class="table table-sm">
                    <tr><td class="text-muted alumni-profile-label">Nama Lengkap</td><td><strong>{{ $siswa->nama_lengkap }}</strong></td></tr>
                    <tr><td class="text-muted">NISN</td><td>{{ $siswa->nisn ?? '-' }}</td></tr>
                    <tr><td class="text-muted">NIS</td><td>{{ $siswa->nis ?? '-' }}</td></tr>
                    <tr><td class="text-muted">Jenis Kelamin</td><td>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                    <tr><td class="text-muted">Status</td><td><span class="badge bg-success">ALUMNI</span></td></tr>
                </table>
            </div>
        </div>

        @if($raporTerakhir)
            <h6 class="fw-bold mt-3 mb-1"><i class="fas fa-file-alt me-2 text-primary"></i>Rapor Terakhir</h6>
            <div class="rapor-summary">
                <div class="field">
                    <div class="label">Tahun Ajaran</div>
                    <div class="value">{{ $raporTerakhir->tahunAjaran?->nama_tahun_ajaran ?? '-' }}</div>
                </div>
                <div class="field">
                    <div class="label">Semester / Jenis</div>
                    <div class="value">Semester {{ $raporTerakhir->semester }} - {{ $raporTerakhir->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }}</div>
                </div>
                <div class="field">
                    <div class="label">Status</div>
                    <div class="value">
                        @if($raporTerakhir->status === 'diterbitkan')
                            <span class="badge bg-success">Diterbitkan</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($raporTerakhir->status) }}</span>
                        @endif
                    </div>
                </div>
                <div class="field">
                    <div class="label">Tanggal Terbit</div>
                    <div class="value">{{ $raporTerakhir->tanggal_terbit ? $raporTerakhir->tanggal_terbit->locale('id')->translatedFormat('d M Y') : '-' }}</div>
                </div>
            </div>
            <div class="text-muted small mt-2">
                <i class="fas fa-info-circle me-1"></i>
                Untuk akses rapor lengkap (download/cetak), silakan hubungi wali siswa atau wali Anda. Akun wali siswa tetap memiliki akses penuh.
            </div>
        @else
            <div class="alert alert-warning mt-3 mb-0">
                <i class="fas fa-exclamation-triangle me-1"></i>Belum ada rapor yang tercatat untuk Anda.
            </div>
        @endif
    </div>
</div>
@endsection
