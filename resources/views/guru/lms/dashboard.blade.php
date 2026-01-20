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
            <div class="stat-label">Pertemuan</div>
            <div class="stat-value" style="color: #10b981;">{{ $pertemuans->count() ?? 0 }}</div>
            <div style="position: absolute; right: 20px; top: 20px; font-size: 40px; opacity: 0.1;">
                <i class="fas fa-calendar-alt"></i>
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

    {{-- Main Action: Buat Pertemuan --}}
    <div class="card-custom"
        style="margin-bottom: 24px; background: linear-gradient(135deg, #165fac 0%, #1e40af 100%); color: white;">
        <div style="padding: 32px; text-align: center;">
            <i class="fas fa-calendar-plus fa-3x mb-3" style="opacity: 0.8;"></i>
            <h4 class="fw-bold mb-2">Buat Pertemuan Baru</h4>
            <p class="mb-4" style="opacity: 0.9;">Kelola materi, tugas, kuis, dan meeting dalam satu pertemuan</p>
            <a href="{{ route('guru.lms.pertemuan.create', [$kelas->id, $mapel->id]) }}"
                class="btn btn-light btn-lg px-5 py-3 fw-bold" style="border-radius: 12px; color: #165fac;">
                <i class="fas fa-plus-circle me-2"></i>Buat Pertemuan
            </a>
        </div>
    </div>

    {{-- Daftar Pertemuan --}}
    <div class="card-custom">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-list me-2"></i>Daftar Pertemuan
            </h6>
            <span class="badge bg-primary">{{ $pertemuans->count() ?? 0 }} Pertemuan</span>
        </div>
        <div style="padding: 0;">
            @forelse($pertemuans ?? [] as $pertemuan)
                <a href="{{ route('guru.lms.pertemuan.show', [$kelas->id, $mapel->id, $pertemuan->id]) }}"
                    class="pertemuan-item">
                    <div class="pertemuan-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="pertemuan-info">
                        <h6 class="mb-1 fw-bold">Pekan {{ $pertemuan->pekan ?? 'N/A' }} - {{ $pertemuan->judul }}</h6>
                        <div class="text-muted small">
                            <i class="far fa-calendar me-1"></i>{{ $pertemuan->tanggal->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                    <div class="pertemuan-stats">
                        <span class="badge bg-success me-1" title="Materi">
                            <i class="fas fa-file-alt"></i> {{ $pertemuan->materi->count() }}
                        </span>
                        <span class="badge bg-warning me-1" title="Tugas">
                            <i class="fas fa-tasks"></i> {{ $pertemuan->tugas->count() }}
                        </span>
                        <span class="badge bg-danger me-1" title="Ujian">
                            <i class="fas fa-question-circle"></i> {{ $pertemuan->ujian->count() }}
                        </span>
                        <span class="badge bg-info" title="Forum">
                            <i class="fas fa-comments"></i> {{ $pertemuan->forumDiskusi->count() }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </a>
            @empty
                <div style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-calendar-times"
                        style="font-size: 48px; color: #dee2e6; display: block; margin-bottom: 16px;"></i>
                    <p class="text-muted mb-3">Belum ada pertemuan</p>
                    <a href="{{ route('guru.lms.pertemuan.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Buat Pertemuan Pertama
                    </a>
                </div>
            @endforelse
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
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                position: relative;
                overflow: hidden;
                transition: transform 0.2s, box-shadow 0.2s;
                text-align: center;
            }

            .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
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

            /* Pertemuan Item */
            .pertemuan-item {
                display: flex;
                align-items: center;
                padding: 20px 24px;
                border-bottom: 1px solid #f0f0f0;
                text-decoration: none;
                color: inherit;
                transition: background 0.2s;
                gap: 16px;
            }

            .pertemuan-item:hover {
                background: #f8f9fa;
                text-decoration: none;
                color: inherit;
            }

            .pertemuan-item:last-child {
                border-bottom: none;
            }

            .pertemuan-icon {
                width: 48px;
                height: 48px;
                background: #e3f2fd;
                color: #2196F3;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                flex-shrink: 0;
            }

            .pertemuan-info {
                flex: 1;
                min-width: 0;
            }

            .pertemuan-stats {
                display: flex;
                gap: 4px;
                flex-shrink: 0;
            }

            .pertemuan-stats .badge {
                font-size: 11px;
                padding: 4px 8px;
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

                .pertemuan-item {
                    flex-wrap: wrap;
                    padding: 16px;
                }

                .pertemuan-stats {
                    width: 100%;
                    margin-top: 8px;
                }
            }
        </style>
    @endpush
@endsection