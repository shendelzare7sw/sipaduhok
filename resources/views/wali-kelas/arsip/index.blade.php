@extends('layouts.sneat')

@section('title', 'Arsip Kelas Saya')
@section('page-title', 'Arsip Kelas Saya')
@section('page-subtitle', 'Akses read-only ke kelas yang pernah Anda walikan (lintas tahun ajaran)')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-kelas/arsip/index.css', 'resources/js/wali-kelas/arsip/index.js'])
@endsection

@section('content')
<div class="container-xxl">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat ringkas --}}
    <div class="arsip-stat-row">
        <div class="arsip-stat-card">
            <div class="icon-wrap icon-wrap-purple">
                <i class="fas fa-archive"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalKelas }}</div>
                <div class="stat-label">Total Kelas</div>
            </div>
        </div>
        <div class="arsip-stat-card">
            <div class="icon-wrap icon-wrap-blue">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <div class="stat-value">{{ $kelasGrouped->count() }}</div>
                <div class="stat-label">Tahun Ajaran</div>
            </div>
        </div>
    </div>

    @if($totalKelas === 0)
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h5 class="fw-bold">Belum Ada Arsip</h5>
            <p class="mb-0">Anda belum pernah diassign sebagai wali kelas pada kelas manapun.</p>
        </div>
    @else
        @foreach($kelasGrouped as $taName => $kelasList)
            <div class="ta-section">
                <div class="ta-header">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Tahun Ajaran {{ $taName }}</span>
                    @if($kelasList->first()?->tahunAjaran?->is_active)
                        <span class="badge-aktif">AKTIF</span>
                    @endif
                </div>

                <div class="kelas-grid">
                    @foreach($kelasList as $kelas)
                        <a href="{{ route('wali.arsip.show', $kelas->id) }}" class="kelas-card">
                            <div class="kelas-name">
                                <i class="fas fa-chalkboard-teacher text-purple-icon"></i>
                                {{ $kelas->nama_kelas }}
                            </div>
                            <div class="kelas-meta">
                                {{ $kelas->jenjang }} ·
                                {{ $kelas->cabang->nama_cabang ?? '-' }}
                            </div>

                            <div class="stat-row">
                                <div class="stat-mini">
                                    <div class="num">{{ $kelas->siswa_count }}</div>
                                    <div class="lbl">Siswa</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="num text-success-count">{{ $kelas->rapor_terbit }}</div>
                                    <div class="lbl">Rapor Terbit</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="num text-warning-count">{{ $kelas->rapor_total }}</div>
                                    <div class="lbl">Rapor Total</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
