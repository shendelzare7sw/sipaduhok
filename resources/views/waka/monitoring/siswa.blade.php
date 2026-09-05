@extends('layouts.app')

@section('title', 'Monitoring Siswa')
@section('page-title', 'Monitoring Data Siswa')
@section('page-subtitle', 'Fokus pada siswa di cabang yang Anda kelola')


@section('styles')
    @vite(['resources/css/waka/monitoring/siswa.css', 'resources/js/waka/monitoring/siswa.js'])
@endsection

@section('content')
@php
    $routeBase = 'waka.monitoring';
    $scopeLabel = auth()->user()->cabang->nama_cabang ?? 'Cabang saya';
    $showCabangFilter = isset($cabangs);
    $visibleRows = $siswa->getCollection();
    $avgTugasPage = $visibleRows->count() > 0 ? round($visibleRows->avg('progress_tugas'), 1) : 0;
    $avgUjianPage = $visibleRows->count() > 0 ? round($visibleRows->avg('progress_ujian'), 1) : 0;
    $sisaTagihanPage = $visibleRows->sum('sisa_tagihan');
@endphp

<div class="container-xxl flex-grow-1 container-p-y monitoring-page">
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon primary"><i class="fas fa-user-graduate"></i></div>
                <span>Siswa Aktif</span>
                <strong>{{ $siswa->total() }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon success"><i class="fas fa-tasks"></i></div>
                <span>Rata-rata Tugas</span>
                <strong>{{ number_format($avgTugasPage, 1) }}%</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon info"><i class="fas fa-file-alt"></i></div>
                <span>Rata-rata Ujian</span>
                <strong>{{ number_format($avgUjianPage, 1) }}%</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon warning"><i class="fas fa-wallet"></i></div>
                <span>Sisa Tagihan</span>
                <strong>Rp {{ number_format($sisaTagihanPage, 0, ',', '.') }}</strong>
                <span class="meta-text">Halaman ini</span>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h5 class="mb-1">Aktivitas Siswa</h5>
                <p class="text-muted mb-0">Progress tugas memakai status dinilai; progress ujian memakai status selesai atau dinilai.</p>
            </div>
            <form action="{{ route($routeBase . '.siswa') }}" method="GET" class="filter-toolbar">
                <input type="text" name="search" class="form-control" placeholder="Cari siswa..." value="{{ request('search') }}">

                @if($showCabangFilter)
                    <select name="cabang_id" class="form-select" data-monitoring-auto-submit>
                        <option value="">Semua Cabang</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                @endif

                <select name="kelas_id" class="form-select" data-monitoring-auto-submit>
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
                @if(request()->anyFilled($showCabangFilter ? ['search', 'cabang_id', 'kelas_id'] : ['search', 'kelas_id']))
                    <a href="{{ route($routeBase . '.siswa') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="content-card-body">
            @if($siswa->count() > 0)
                <div class="table-responsive">
                    <table class="table table-clean align-middle">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Progress LMS</th>
                                <th>Keuangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $s)
                                @php
                                    $taskProgress = $s->progress_tugas ?? 0;
                                    $examProgress = $s->progress_ujian ?? 0;
                                    $taskClass = $taskProgress >= 75 ? 'high' : ($taskProgress >= 50 ? 'medium' : 'low');
                                    $examClass = $examProgress >= 75 ? 'high' : ($examProgress >= 50 ? 'medium' : 'low');
                                @endphp
                                <tr>
                                    <td data-label="Siswa" class="mobile-primary-cell">
                                        <span class="entity-title">{{ $s->nama_lengkap }}</span>
                                        <span class="entity-subtitle">NISN: {{ $s->nisn ?? '-' }}</span>
                                        <details class="mobile-row-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">Detail monitoring</span>
                                                    <span class="mobile-summary-meta">Tugas {{ number_format($taskProgress, 0) }}%, Ujian {{ number_format($examProgress, 0) }}%</span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-row-details-body">
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Kelas</div>
                                                    @if($s->kelas)
                                                        <span class="soft-badge primary">{{ $s->kelas->nama_kelas }}</span>
                                                        <span class="entity-subtitle">{{ $s->kelas->cabang->nama_cabang ?? '-' }}</span>
                                                    @else
                                                        <span class="soft-badge warning">Belum ada kelas</span>
                                                    @endif
                                                </div>
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Progress LMS</div>
                                                    <div class="metric-grid">
                                                        <div class="metric-chip">
                                                            <span>Tugas Dinilai</span>
                                                            <strong>{{ $s->tugas_selesai ?? 0 }}/{{ $s->total_tugas ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip">
                                                            <span>Dikumpulkan</span>
                                                            <strong>{{ $s->tugas_dikumpulkan ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip">
                                                            <span>Ujian Selesai</span>
                                                            <strong>{{ $s->ujian_selesai ?? 0 }}/{{ $s->total_ujian ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip">
                                                            <span>Status LMS</span>
                                                            <strong>{{ $taskProgress >= 75 && $examProgress >= 75 ? 'Aman' : 'Pantau' }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="progress-wrap mt-2">
                                                        <div class="progress-caption">
                                                            <span>Tugas {{ number_format($taskProgress, 0) }}%</span>
                                                            <span>Ujian {{ number_format($examProgress, 0) }}%</span>
                                                        </div>
                                                        <div class="progress-track mb-1">
                                                            <div class="progress-fill {{ $taskClass }}" data-monitoring-progress="{{ min($taskProgress, 100) }}"></div>
                                                        </div>
                                                        <div class="progress-track">
                                                            <div class="progress-fill {{ $examClass }}" data-monitoring-progress="{{ min($examProgress, 100) }}"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Keuangan</div>
                                                    <div class="metric-grid">
                                                        <div class="metric-chip money-chip">
                                                            <span>Total Tagihan</span>
                                                            <strong>Rp {{ number_format($s->total_tagihan ?? 0, 0, ',', '.') }}</strong>
                                                        </div>
                                                        <div class="metric-chip money-chip">
                                                            <span>Terbayar</span>
                                                            <strong class="text-success">Rp {{ number_format($s->total_bayar ?? 0, 0, ',', '.') }}</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Status</div>
                                                    @if(($s->status_bayar ?? 'belum_lunas') === 'lunas')
                                                        <span class="soft-badge success"><i class="fas fa-check-circle me-1"></i> Lunas</span>
                                                    @else
                                                        <span class="soft-badge warning"><i class="fas fa-exclamation-triangle me-1"></i> Sisa Rp {{ number_format($s->sisa_tagihan ?? 0, 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Kelas" class="desktop-detail-cell">
                                        @if($s->kelas)
                                            <span class="soft-badge primary">{{ $s->kelas->nama_kelas }}</span>
                                            <span class="entity-subtitle">{{ $s->kelas->cabang->nama_cabang ?? '-' }}</span>
                                        @else
                                            <span class="soft-badge warning">Belum ada kelas</span>
                                        @endif
                                    </td>
                                    <td data-label="Progress LMS" class="complex-cell desktop-detail-cell">
                                        <details class="mobile-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">{{ $taskProgress >= 75 && $examProgress >= 75 ? 'Aman' : 'Pantau' }}</span>
                                                    <span class="mobile-summary-meta">Tugas {{ number_format($taskProgress, 0) }}%, Ujian {{ number_format($examProgress, 0) }}%</span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-details-body">
                                                <div class="metric-grid">
                                                    <div class="metric-chip">
                                                        <span>Tugas Dinilai</span>
                                                        <strong>{{ $s->tugas_selesai ?? 0 }}/{{ $s->total_tugas ?? 0 }}</strong>
                                                    </div>
                                                    <div class="metric-chip">
                                                        <span>Dikumpulkan</span>
                                                        <strong>{{ $s->tugas_dikumpulkan ?? 0 }}</strong>
                                                    </div>
                                                    <div class="metric-chip">
                                                        <span>Ujian Selesai</span>
                                                        <strong>{{ $s->ujian_selesai ?? 0 }}/{{ $s->total_ujian ?? 0 }}</strong>
                                                    </div>
                                                    <div class="metric-chip">
                                                        <span>Status LMS</span>
                                                        <strong>{{ $taskProgress >= 75 && $examProgress >= 75 ? 'Aman' : 'Pantau' }}</strong>
                                                    </div>
                                                </div>
                                                <div class="progress-wrap mt-2">
                                                    <div class="progress-caption">
                                                        <span>Tugas {{ number_format($taskProgress, 0) }}%</span>
                                                        <span>Ujian {{ number_format($examProgress, 0) }}%</span>
                                                    </div>
                                                    <div class="progress-track mb-1">
                                                        <div class="progress-fill {{ $taskClass }}" data-monitoring-progress="{{ min($taskProgress, 100) }}"></div>
                                                    </div>
                                                    <div class="progress-track">
                                                        <div class="progress-fill {{ $examClass }}" data-monitoring-progress="{{ min($examProgress, 100) }}"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Keuangan" class="complex-cell desktop-detail-cell">
                                        <details class="mobile-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">{{ ($s->status_bayar ?? 'belum_lunas') === 'lunas' ? 'Lunas' : 'Sisa Rp ' . number_format($s->sisa_tagihan ?? 0, 0, ',', '.') }}</span>
                                                    <span class="mobile-summary-meta">Terbayar Rp {{ number_format($s->total_bayar ?? 0, 0, ',', '.') }}</span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-details-body">
                                                <div class="metric-grid">
                                                    <div class="metric-chip money-chip">
                                                        <span>Total Tagihan</span>
                                                        <strong>Rp {{ number_format($s->total_tagihan ?? 0, 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div class="metric-chip money-chip">
                                                        <span>Terbayar</span>
                                                        <strong class="text-success">Rp {{ number_format($s->total_bayar ?? 0, 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Status" class="desktop-detail-cell">
                                        @if(($s->status_bayar ?? 'belum_lunas') === 'lunas')
                                            <span class="soft-badge success"><i class="fas fa-check-circle me-1"></i> Lunas</span>
                                        @else
                                            <span class="soft-badge warning"><i class="fas fa-exclamation-triangle me-1"></i> Sisa Rp {{ number_format($s->sisa_tagihan ?? 0, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $siswa->withQueryString()->links() }}</div>
            @else
                <div class="empty-state">
                    <i class="fas fa-user-graduate"></i>
                    <h6>Tidak ada data siswa</h6>
                    <p>Coba ubah filter pencarian atau pastikan siswa aktif sudah terdaftar di kelas.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="info-panel">
        <div class="info-icon primary"><i class="fas fa-info-circle"></i></div>
        <div>
            <h6>Catatan Data LMS</h6>
            <p>Tugas dihitung dari penugasan kelas dan jawaban siswa. Ujian dihitung dari ujian kelas dan hasil siswa, sehingga indikator lebih selaras dengan halaman LMS siswa dan guru.</p>
        </div>
    </div>
</div>
@endsection
