@extends('layouts.lms-guru')

@section('title', 'Beranda LMS')
@section('page-title', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)
@section('page-subtitle', 'Kelola pembelajaran untuk ' . $jumlahSiswa . ' siswa')

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    {{-- Quick Stats --}}
    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="stat-card" style="border-left: 4px solid #165fac;">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value" style="color: #165fac;">{{ $jumlahSiswa }}</div>
            <div style="position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1;">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid #10b981;">
            <div class="stat-label">Materi</div>
            <div class="stat-value" style="color: #10b981;">{{ $jumlahMateri }}</div>
            <div style="position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1;">
                <i class="fas fa-book"></i>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="stat-label">Tugas</div>
            <div class="stat-value" style="color: #ea580c;">{{ $jumlahTugas }}</div>
            <div style="position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1;">
                <i class="fas fa-tasks"></i>
            </div>
        </div>

        <div class="stat-card" style="border-left: 4px solid #dc2626;">
            <div class="stat-label">Perlu Koreksi</div>
            <div class="stat-value" style="color: #dc2626;">{{ $tugasBelumDikoreksi }}</div>
            <div style="position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card-custom" style="margin-bottom: 24px;">
        <div class="card-header-custom">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-bolt me-2"></i>Quick Actions
            </h6>
        </div>
        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <a href="{{ route('guru.lms.materi.create', [$kelas->id, $mapel->id]) }}" 
                   class="btn btn-primary d-flex align-items-center justify-content-center fw-bold" 
                   style="padding: 16px; gap: 10px; border-radius: 10px; transition: all 0.3s;"
                   onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 4px 12px rgba(22, 95, 172, 0.3)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <i class="fas fa-upload fa-lg"></i>
                    <span>Upload Materi</span>
                </a>
                
                <a href="{{ route('guru.lms.tugas.create', [$kelas->id, $mapel->id]) }}" 
                   class="btn btn-warning d-flex align-items-center justify-content-center fw-bold" 
                   style="padding: 16px; gap: 10px; border-radius: 10px; transition: all 0.3s;"
                   onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.3)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <i class="fas fa-plus-circle fa-lg"></i>
                    <span>Buat Tugas</span>
                </a>
                
                <a href="{{ route('guru.lms.ujian.create', [$kelas->id, $mapel->id]) }}" 
                   class="btn btn-danger d-flex align-items-center justify-content-center fw-bold" 
                   style="padding: 16px; gap: 10px; border-radius: 10px; transition: all 0.3s;"
                   onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <i class="fas fa-file-alt fa-lg"></i>
                    <span>Buat Ujian</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="content-grid">
        <div class="card-custom">
            <div class="card-header-custom">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-book me-2"></i>Materi Terbaru
                </h6>
            </div>
            <div style="padding: 24px;">
                @if($materiTerbaru->count() > 0)
                    <div style="display: grid; gap: 16px; margin-bottom: 20px;">
                        @foreach($materiTerbaru as $materi)
                        <div style="background: #f8f9fa; padding: 16px; border-radius: 10px; border-left: 4px solid #165fac; transition: all 0.2s;"
                             onmouseover="this.style.background='#e7f3ff'; this.style.transform='translateX(4px)'"
                             onmouseout="this.style.background='#f8f9fa'; this.style.transform='translateX(0)'">
                            <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 12px;">
                                <div style="flex: 1; min-width: 200px;">
                                    <h6 style="margin: 0 0 8px 0; font-weight: 600; color: #1a1a1a;">
                                        {{ $materi->judul_materi }}
                                    </h6>
                                    <small class="text-muted" style="font-size: 13px;">
                                        <i class="fas fa-calendar me-1"></i>{{ $materi->tanggal_upload->format('d M Y') }}
                                    </small>
                                </div>
                                <span class="badge bg-info" style="font-size: 11px; padding: 6px 12px;">
                                    {{ strtoupper($materi->tipe_file) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('guru.lms.materi.index', [$kelas->id, $mapel->id]) }}" 
                       class="btn btn-outline-primary w-100 fw-bold" 
                       style="border-radius: 10px; padding: 12px;">
                        Lihat Semua Materi
                    </a>
                @else
                    <div style="text-align: center; padding: 60px 20px;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #dee2e6; display: block; margin-bottom: 16px;"></i>
                        <p class="text-muted mb-0">Belum ada materi</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header-custom">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-tasks me-2"></i>Tugas Terbaru
                </h6>
            </div>
            <div style="padding: 24px;">
                @if($tugasTerbaru->count() > 0)
                    <div style="display: grid; gap: 16px; margin-bottom: 20px;">
                        @foreach($tugasTerbaru as $tugas)
                        <div style="background: #f8f9fa; padding: 16px; border-radius: 10px; border-left: 4px solid #f59e0b; transition: all 0.2s;"
                             onmouseover="this.style.background='#fef3c7'; this.style.transform='translateX(4px)'"
                             onmouseout="this.style.background='#f8f9fa'; this.style.transform='translateX(0)'">
                            <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 12px;">
                                <div style="flex: 1; min-width: 200px;">
                                    <h6 style="margin: 0 0 8px 0; font-weight: 600; color: #1a1a1a;">
                                        {{ $tugas->judul_tugas }}
                                    </h6>
                                    <small class="text-muted" style="font-size: 13px;">
                                        <i class="fas fa-clock me-1"></i>Deadline: {{ $tugas->tanggal_deadline->format('d M Y') }}
                                    </small>
                                </div>
                                @if($tugas->isOverdue())
                                    <span class="badge bg-danger" style="font-size: 11px; padding: 6px 12px;">Lewat</span>
                                @else
                                    <span class="badge bg-success" style="font-size: 11px; padding: 6px 12px;">Aktif</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}" 
                       class="btn btn-outline-primary w-100 fw-bold" 
                       style="border-radius: 10px; padding: 12px;">
                        Lihat Semua Tugas
                    </a>
                @else
                    <div style="text-align: center; padding: 60px 20px;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #dee2e6; display: block; margin-bottom: 16px;"></i>
                        <p class="text-muted mb-0">Belum ada tugas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

@push('styles')
<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        text-align: center;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    
    .stat-value {
        font-size: 48px;
        font-weight: 700;
        margin: 12px 0;
    }
    
    .stat-label {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }
    
    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    @media (min-width: 992px) {
        .content-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .stat-card {
            padding: 20px;
        }
        
        .stat-value {
            font-size: 36px;
        }
    }
</style>
@endpush
@endsection