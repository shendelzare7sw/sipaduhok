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
        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Total Siswa</div>
                <div class="stat-value" style="color: #1f2937;">{{ $jumlahSiswa }}</div>
            </div>
            <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="fas fa-users" style="opacity: 0.9;"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Materi</div>
                <div class="stat-value" style="color: #1f2937;">{{ $jumlahMateri }}</div>
            </div>
            <div class="stat-icon" style="background: #dcfce7; color: #16a34a;">
                <i class="fas fa-book" style="opacity: 0.9;"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Tugas</div>
                <div class="stat-value" style="color: #1f2937;">{{ $jumlahTugas }}</div>
            </div>
            <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                <i class="fas fa-tasks" style="opacity: 0.9;"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-content">
                <div class="stat-label">Perlu Koreksi</div>
                <div class="stat-value" style="color: #1f2937;">{{ $tugasBelumDikoreksi }}</div>
            </div>
            <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
                <i class="fas fa-exclamation-circle" style="opacity: 0.9;"></i>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card-custom premium-section" style="margin-bottom: 24px; border: none; background: transparent; box-shadow: none;">
        <div class="d-flex align-items-center mb-4">
            <div class="title-icon-wrapper" style="background: linear-gradient(135deg, #165fac, #0d3f7a); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 12px; box-shadow: 0 4px 10px rgba(22, 95, 172, 0.3);">
                <i class="fas fa-bolt text-white" style="font-size: 18px;"></i>
            </div>
            <h5 class="mb-0 fw-bold" style="color: #1f2937;">Quick Actions</h5>
        </div>
        
        <div class="premium-actions-grid">
            <a href="{{ route('guru.lms.materi.create', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-materi">
                <div class="icon-circle">
                    <i class="fas fa-book"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Materi</span>
                    <span class="card-subtitle">Kelola bahan ajar</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.tugas.create', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-tugas">
                <div class="icon-circle">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Tugas</span>
                    <span class="card-subtitle">Buat tugas baru</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-latihan">
                <div class="icon-circle">
                    <i class="fas fa-pencil-ruler"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Latihan</span>
                    <span class="card-subtitle">Buat soal latihan</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.ujian.create', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-ujian">
                <div class="icon-circle">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Ujian</span>
                    <span class="card-subtitle">Selenggarakan ujian</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.forum.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-forum">
                <div class="icon-circle">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Forum</span>
                    <span class="card-subtitle">Ruang diskusi siswa</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.meeting.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-meeting">
                <div class="icon-circle">
                    <i class="fas fa-video"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Virtual Class</span>
                    <span class="card-subtitle">Jadwalkan meeting</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>

            <a href="{{ route('guru.lms.nilai.index', [$kelas->id, $mapel->id]) }}" class="premium-action-card card-nilai">
                <div class="icon-circle">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-details">
                    <span class="card-title">Nilai Siswa</span>
                    <span class="card-subtitle">Rekap penilaian</span>
                </div>
                <i class="fas fa-chevron-right arrow-icon"></i>
            </a>
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
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 12px;">
                                <div style="flex: 1; min-width: 200px;">
                                    <h6 class="activity-title">
                                        {{ $materi->judul_materi }}
                                    </h6>
                                    <p class="activity-meta">
                                        <i class="fas fa-calendar me-1"></i>
                                        <span>{{ $materi->tanggal_upload->format('d M Y') }}</span>
                                    </p>
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
                        <div class="activity-item tugas">
                            <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 12px;">
                                <div style="flex: 1; min-width: 200px;">
                                    <h6 class="activity-title">
                                        {{ $tugas->judul_tugas }}
                                    </h6>
                                    <p class="activity-meta">
                                        <i class="fas fa-clock me-1"></i>
                                        <span>Deadline: {{ $tugas->tanggal_deadline->format('d M Y') }}</span>
                                    </p>
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
    /* Stats Grid Minimalist Professional */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        border: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        will-change: transform;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        border-color: #e5e7eb;
    }

    .stat-content {
        text-align: left;
    }
    
    .stat-label {
        font-size: 13px;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        transition: transform 0.3s;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.05);
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
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            min-height: 110px;
        }

        .stat-icon {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            font-size: 18px;
            border-radius: 12px;
        }

        .stat-label {
            font-size: 11px;
            margin-bottom: 4px;
            padding-right: 48px; /* space for absolute icon */
        }
        
        .stat-value {
            font-size: 26px;
            margin-top: auto;
        }
    }

    /* Premium Quick Actions CSS */
    .premium-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }

    .premium-action-card {
        display: flex;
        align-items: center;
        padding: 16px;
        border-radius: 16px;
        background: white;
        text-decoration: none;
        color: #1f2937;
        border: 1px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        will-change: transform;
    }

    .premium-action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.8) 50%, rgba(255,255,255,0) 100%);
        transform: translateX(-100%);
        transition: transform 0.6s;
        z-index: 1;
    }

    .premium-action-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -5px rgba(0, 0, 0, 0.04);
        text-decoration: none;
        color: #1f2937;
    }

    .premium-action-card:hover::before {
        transform: translateX(100%);
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-right: 16px;
        z-index: 2;
        flex-shrink: 0;
    }

    .card-details {
        display: flex;
        flex-direction: column;
        z-index: 2;
        flex: 1;
        min-width: 0;
    }

    .card-title {
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-subtitle {
        font-size: 11px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .arrow-icon {
        font-size: 12px;
        color: #9ca3af;
        transition: transform 0.3s;
        z-index: 2;
        flex-shrink: 0;
    }

    .premium-action-card:hover .arrow-icon {
        transform: translateX(4px);
        color: #1f2937;
    }

    /* Action Card Colors */
    .card-materi .icon-circle { background: #e0f2fe; color: #0284c7; }
    .card-materi:hover { border-color: #bae6fd; border-left: 3px solid #0ea5e9; }

    .card-tugas .icon-circle { background: #fef3c7; color: #d97706; }
    .card-tugas:hover { border-color: #fde68a; border-left: 3px solid #f59e0b; }

    .card-latihan .icon-circle { background: #e0e7ff; color: #4338ca; }
    .card-latihan:hover { border-color: #c7d2fe; border-left: 3px solid #6366f1; }

    .card-ujian .icon-circle { background: #fee2e2; color: #dc2626; }
    .card-ujian:hover { border-color: #fecaca; border-left: 3px solid #ef4444; }

    .card-forum .icon-circle { background: #dcfce7; color: #16a34a; }
    .card-forum:hover { border-color: #bbf7d0; border-left: 3px solid #22c55e; }

    .card-meeting .icon-circle { background: #f3f4f6; color: #4b5563; }
    .card-meeting:hover { border-color: #e5e7eb; border-left: 3px solid #6b7280; }

    .card-nilai .icon-circle { background: #fce7f3; color: #db2777; }
    .card-nilai:hover { border-color: #fbcfe8; border-left: 3px solid #ec4899; }

    /* Recent Activity Styles */
    .activity-item {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 10px;
        border-left: 4px solid #165fac;
        transition: all 0.2s;
    }

    .activity-item:hover {
        background: #e7f3ff;
        transform: translateX(4px);
    }

    .activity-item.tugas {
        border-left-color: #f59e0b;
    }

    .activity-item.tugas:hover {
        background: #fef3c7;
    }

    .activity-title {
        margin: 0 0 8px 0;
        font-weight: 600;
        color: #1a1a1a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .activity-meta {
        font-size: 13px;
        margin-bottom: 0;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .premium-actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .card-title, .card-subtitle {
            font-size: 13px;
        }
    }
</style>
@endpush
@endsection