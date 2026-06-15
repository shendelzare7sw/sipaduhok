@extends('layouts.sneat')

@section('title', 'Monitoring Guru Pengajar')
@section('page-title', 'Monitoring Data Guru Pengajar')
@section('page-subtitle', 'Lihat aktivitas LMS, koreksi, dan progress nilai guru')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/ketua/monitoring/guru-pengajar.css', 'resources/js/ketua/monitoring/guru-pengajar.js'])
@endsection

@section('content')
@php
    $routeBase = 'ketua.monitoring';
    $scopeLabel = request('cabang_id') ? 'Cabang terfilter' : 'Semua cabang';
    $showCabangFilter = isset($cabangs);
    $visibleRows = $guruPengajar->getCollection();
    $avgProgressPage = $visibleRows->count() > 0 ? round($visibleRows->avg('progress_nilai'), 1) : 0;
    $totalKontenPage = $visibleRows->sum('materi_dibuat') + $visibleRows->sum('tugas_dibuat') + $visibleRows->sum('ujian_dibuat');
@endphp

<div class="container-xxl flex-grow-1 container-p-y monitoring-page">
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon primary"><i class="fas fa-chalkboard-teacher"></i></div>
                <span>Guru Pengajar</span>
                <strong>{{ $guruPengajar->total() }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon info"><i class="fas fa-layer-group"></i></div>
                <span>Kelas Mapel</span>
                <strong>{{ $visibleRows->sum('total_kelas_mapel') }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon success"><i class="fas fa-book-open"></i></div>
                <span>Konten LMS</span>
                <strong>{{ $totalKontenPage }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon warning"><i class="fas fa-clipboard-check"></i></div>
                <span>Rata-rata Nilai</span>
                <strong>{{ number_format($avgProgressPage, 1) }}%</strong>
                <span class="meta-text">{{ $visibleRows->sum('tugas_perlu_koreksi') }} tugas perlu koreksi</span>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h5 class="mb-1">Aktivitas Guru Pengajar</h5>
                <p class="text-muted mb-0">Data konten mengikuti LMS guru: materi, tugas, ujian, dan virtual class.</p>
            </div>
            <form action="{{ route($routeBase . '.guru-pengajar') }}" method="GET" class="filter-toolbar">
                <input type="text" name="search" class="form-control" placeholder="Cari guru..." value="{{ request('search') }}">

                @if($showCabangFilter)
                    <select name="cabang_id" class="form-select" data-monitoring-auto-submit>
                        <option value="">Semua Cabang</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled($showCabangFilter ? ['search', 'cabang_id'] : ['search']))
                    <a href="{{ route($routeBase . '.guru-pengajar') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="content-card-body">
            @if($guruPengajar->count() > 0)
                <div class="table-responsive">
                    <table class="table table-clean align-middle">
                        <thead>
                            <tr>
                                <th>Guru</th>
                                <th>Kelas dan Mapel</th>
                                <th>Konten LMS</th>
                                <th>Koreksi</th>
                                <th>Progress Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guruPengajar as $guru)
                                @php
                                    $assignments = $guru->visible_guru_kelas ?? $guru->guruKelas;
                                    $assignmentLabels = $assignments->map(fn($gk) => ($gk->kelas->nama_kelas ?? '-') . ' - ' . ($gk->mataPelajaran->nama_mapel ?? '-'));
                                    $assignmentPreview = $assignmentLabels->take(2)->implode(', ');
                                    $assignmentMoreCount = max($assignmentLabels->count() - 2, 0);
                                    $progress = $guru->progress_nilai ?? 0;
                                    $progressClass = $progress >= 75 ? 'high' : ($progress >= 50 ? 'medium' : 'low');
                                @endphp
                                <tr>
                                    <td data-label="Guru" class="mobile-primary-cell">
                                        <span class="entity-title">{{ $guru->nama_lengkap }}</span>
                                        <span class="entity-subtitle">{{ $guru->nip ?? 'NIP belum diisi' }}</span>
                                        <details class="mobile-row-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">Detail monitoring</span>
                                                    <span class="mobile-summary-meta">
                                                        {{ $assignmentPreview ?: 'Belum ada penugasan' }}
                                                        @if($assignmentMoreCount > 0)
                                                            , +{{ $assignmentMoreCount }} lainnya
                                                        @endif
                                                    </span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-row-details-body">
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Kelas Mapel</div>
                                                    <div class="assignment-list d-flex flex-wrap gap-1">
                                                        @forelse($assignments->take(4) as $gk)
                                                            <span class="soft-badge primary">
                                                                {{ $gk->kelas->nama_kelas ?? '-' }} - {{ $gk->mataPelajaran->nama_mapel ?? '-' }}
                                                            </span>
                                                        @empty
                                                            <span class="soft-badge warning">Belum ada penugasan</span>
                                                        @endforelse
                                                        @if($assignments->count() > 4)
                                                            <span class="soft-badge neutral">+{{ $assignments->count() - 4 }} lainnya</span>
                                                        @endif
                                                    </div>
                                                    <span class="entity-subtitle">{{ $assignments->first()?->kelas?->cabang?->nama_cabang ?? '-' }}</span>
                                                </div>
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Konten LMS</div>
                                                    <div class="metric-grid">
                                                        <div class="metric-chip">
                                                            <span>Materi</span>
                                                            <strong>{{ $guru->materi_dibuat ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip">
                                                            <span>Tugas</span>
                                                            <strong>{{ $guru->tugas_dibuat ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip">
                                                            <span>Ujian</span>
                                                            <strong>{{ $guru->ujian_dibuat ?? $guru->soal_ujian_dibuat ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip">
                                                            <span>Meeting</span>
                                                            <strong>{{ $guru->meeting_dibuat ?? 0 }}</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Koreksi</div>
                                                    <div class="metric-grid">
                                                        <div class="metric-chip">
                                                            <span>Sudah Dinilai</span>
                                                            <strong class="text-success">{{ $guru->nilai_sudah_diisi ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip">
                                                            <span>Belum Nilai</span>
                                                            <strong class="text-danger">{{ $guru->total_nilai_harus_diisi ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip full-width">
                                                            <span>Tugas Perlu Koreksi</span>
                                                            <strong class="{{ ($guru->tugas_perlu_koreksi ?? 0) > 0 ? 'text-warning' : 'text-success' }}">{{ $guru->tugas_perlu_koreksi ?? 0 }}</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Progress</div>
                                                    <div class="progress-wrap">
                                                        <div class="progress-track">
                                                            <div class="progress-fill {{ $progressClass }}" data-monitoring-progress="{{ min($progress, 100) }}"></div>
                                                        </div>
                                                        <div class="progress-caption">
                                                            <span>{{ number_format($progress, 1) }}%</span>
                                                            <span>{{ $progress >= 100 ? 'Lengkap' : 'Berjalan' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Kelas Mapel" class="complex-cell desktop-detail-cell">
                                        <details class="mobile-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">{{ $assignmentPreview ?: 'Belum ada penugasan' }}</span>
                                                    <span class="mobile-summary-meta">
                                                        @if($assignmentMoreCount > 0)
                                                            +{{ $assignmentMoreCount }} lainnya
                                                        @else
                                                            {{ $assignments->first()?->kelas?->cabang?->nama_cabang ?? '-' }}
                                                        @endif
                                                    </span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-details-body">
                                                <div class="assignment-list d-flex flex-wrap gap-1 justify-content-md-start justify-content-end">
                                                    @forelse($assignments->take(4) as $gk)
                                                        <span class="soft-badge primary">
                                                            {{ $gk->kelas->nama_kelas ?? '-' }} - {{ $gk->mataPelajaran->nama_mapel ?? '-' }}
                                                        </span>
                                                    @empty
                                                        <span class="soft-badge warning">Belum ada penugasan</span>
                                                    @endforelse
                                                    @if($assignments->count() > 4)
                                                        <span class="soft-badge neutral">+{{ $assignments->count() - 4 }} lainnya</span>
                                                    @endif
                                                </div>
                                                <span class="entity-subtitle">{{ $assignments->first()?->kelas?->cabang?->nama_cabang ?? '-' }}</span>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Konten LMS" class="complex-cell desktop-detail-cell">
                                        <details class="mobile-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">{{ ($guru->materi_dibuat ?? 0) + ($guru->tugas_dibuat ?? 0) + ($guru->ujian_dibuat ?? $guru->soal_ujian_dibuat ?? 0) }} konten LMS</span>
                                                    <span class="mobile-summary-meta">Materi {{ $guru->materi_dibuat ?? 0 }}, Tugas {{ $guru->tugas_dibuat ?? 0 }}, Ujian {{ $guru->ujian_dibuat ?? $guru->soal_ujian_dibuat ?? 0 }}</span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-details-body">
                                                <div class="metric-grid">
                                                    <div class="metric-chip">
                                                        <span>Materi</span>
                                                        <strong>{{ $guru->materi_dibuat ?? 0 }}</strong>
                                                    </div>
                                                    <div class="metric-chip">
                                                        <span>Tugas</span>
                                                        <strong>{{ $guru->tugas_dibuat ?? 0 }}</strong>
                                                    </div>
                                                    <div class="metric-chip">
                                                        <span>Ujian</span>
                                                        <strong>{{ $guru->ujian_dibuat ?? $guru->soal_ujian_dibuat ?? 0 }}</strong>
                                                    </div>
                                                    <div class="metric-chip">
                                                        <span>Meeting</span>
                                                        <strong>{{ $guru->meeting_dibuat ?? 0 }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Koreksi" class="complex-cell desktop-detail-cell">
                                        <details class="mobile-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">{{ $guru->nilai_sudah_diisi ?? 0 }} dinilai</span>
                                                    <span class="mobile-summary-meta">{{ $guru->total_nilai_harus_diisi ?? 0 }} belum nilai, {{ $guru->tugas_perlu_koreksi ?? 0 }} perlu koreksi</span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-details-body">
                                                <div class="metric-grid">
                                                    <div class="metric-chip">
                                                        <span>Sudah Dinilai</span>
                                                        <strong class="text-success">{{ $guru->nilai_sudah_diisi ?? 0 }}</strong>
                                                    </div>
                                                    <div class="metric-chip">
                                                        <span>Belum Nilai</span>
                                                        <strong class="text-danger">{{ $guru->total_nilai_harus_diisi ?? 0 }}</strong>
                                                    </div>
                                                    <div class="metric-chip full-width">
                                                        <span>Tugas Perlu Koreksi</span>
                                                        <strong class="{{ ($guru->tugas_perlu_koreksi ?? 0) > 0 ? 'text-warning' : 'text-success' }}">{{ $guru->tugas_perlu_koreksi ?? 0 }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Progress" class="complex-cell desktop-detail-cell">
                                        <details class="mobile-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">{{ number_format($progress, 1) }}%</span>
                                                    <span class="mobile-summary-meta">{{ $progress >= 100 ? 'Lengkap' : 'Berjalan' }}</span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-details-body">
                                                <div class="progress-wrap">
                                                    <div class="progress-track">
                                                        <div class="progress-fill {{ $progressClass }}" data-monitoring-progress="{{ min($progress, 100) }}"></div>
                                                    </div>
                                                    <div class="progress-caption">
                                                        <span>{{ number_format($progress, 1) }}%</span>
                                                        <span>{{ $progress >= 100 ? 'Lengkap' : 'Berjalan' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $guruPengajar->withQueryString()->links() }}</div>
            @else
                <div class="empty-state">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h6>Tidak ada data guru pengajar</h6>
                    <p>Data muncul setelah guru ditugaskan pada kelas dan mata pelajaran.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="info-panel">
        <div class="info-icon success"><i class="fas fa-chart-bar"></i></div>
        <div>
            <h6>Sinkron dengan LMS</h6>
            <p>Metrik konten mengambil data dari materi, tugas, ujian, dan meeting LMS guru. Tugas perlu koreksi dihitung dari jawaban siswa yang sudah dikumpulkan tetapi belum dinilai.</p>
        </div>
    </div>
</div>
@endsection
