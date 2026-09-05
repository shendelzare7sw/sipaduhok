@extends('layouts.app')

@section('title', 'Monitoring Wali Kelas')
@section('page-title', 'Monitoring Data Wali Kelas')
@section('page-subtitle', 'Fokus pada wali kelas di cabang yang Anda kelola')


@section('styles')
    @vite(['resources/css/waka/monitoring/wali-kelas.css', 'resources/js/waka/monitoring/wali-kelas.js'])
@endsection

@section('content')
@php
    $routeBase = 'waka.monitoring';
    $scopeLabel = auth()->user()->cabang->nama_cabang ?? 'Cabang saya';
    $showCabangFilter = isset($cabangs);
    $visibleRows = $waliKelas->getCollection();
    $totalSiswaPage = $visibleRows->sum('total_siswa');
    $raporSelesaiPage = $visibleRows->sum('rapor_selesai');
    $avgProgressPage = $visibleRows->count() > 0 ? round($visibleRows->avg('progress_rapor'), 1) : 0;
@endphp

<div class="container-xxl flex-grow-1 container-p-y monitoring-page">
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon primary"><i class="fas fa-user-tie"></i></div>
                <span>Wali Kelas</span>
                <strong>{{ $waliKelas->total() }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon info"><i class="fas fa-school"></i></div>
                <span>Kelas Halaman Ini</span>
                <strong>{{ $visibleRows->sum('kelas_count') }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon success"><i class="fas fa-users"></i></div>
                <span>Siswa Terpantau</span>
                <strong>{{ $totalSiswaPage }}</strong>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="summary-card">
                <div class="summary-icon warning"><i class="fas fa-file-alt"></i></div>
                <span>Rapor Selesai</span>
                <strong>{{ $raporSelesaiPage }}</strong>
                <span class="meta-text">Rata-rata {{ number_format($avgProgressPage, 1) }}%</span>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="content-card-header">
            <div>
                <h5 class="mb-1">Progress Rapor per Wali Kelas</h5>
                <p class="text-muted mb-0">Progress dihitung dari rapor berstatus diterbitkan dibanding total siswa.</p>
            </div>
            <form action="{{ route($routeBase . '.wali-kelas') }}" method="GET" class="filter-toolbar">
                <input type="text" name="search" class="form-control" placeholder="Cari wali kelas..." value="{{ request('search') }}">

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
                    <a href="{{ route($routeBase . '.wali-kelas') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="content-card-body">
            @if($waliKelas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-clean align-middle">
                        <thead>
                            <tr>
                                <th>Wali Kelas</th>
                                <th>Kelas dan Cabang</th>
                                <th>Tahun Ajaran</th>
                                <th class="text-center">Siswa</th>
                                <th class="text-center">Rapor</th>
                                <th class="monitoring-progress-column">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($waliKelas as $wali)
                                @php
                                    $progress = $wali->progress_rapor ?? 0;
                                    $progressClass = $progress >= 75 ? 'high' : ($progress >= 50 ? 'medium' : 'low');
                                    $kelasNames = $wali->kelas_names ?: ($wali->kelas_info->nama_kelas ?? '-');
                                    $cabangNames = $wali->cabang_names ?: ($wali->kelas_info->cabang->nama_cabang ?? '-');
                                    $tahunNames = $wali->tahun_ajaran_names ?: ($wali->kelas_info->tahunAjaran->nama_tahun_ajaran ?? '-');
                                @endphp
                                <tr>
                                    <td data-label="Wali Kelas" class="mobile-primary-cell">
                                        <span class="entity-title">{{ $wali->nama_lengkap }}</span>
                                        <span class="entity-subtitle">{{ $wali->nip ?? 'NIP belum diisi' }}</span>
                                        <details class="mobile-row-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">Detail monitoring</span>
                                                    <span class="mobile-summary-meta">{{ \Illuminate\Support\Str::limit($kelasNames, 58) }}</span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-row-details-body">
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Kelas</div>
                                                    <span class="soft-badge primary assignment-badge">{{ $kelasNames }}</span>
                                                    <span class="entity-subtitle">{{ $cabangNames }}</span>
                                                </div>
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Tahun Ajaran</div>
                                                    <span class="entity-title">{{ $tahunNames }}</span>
                                                </div>
                                                <div class="mobile-detail-section">
                                                    <div class="mobile-detail-title">Rekap Rapor</div>
                                                    <div class="metric-grid">
                                                        <div class="metric-chip">
                                                            <span>Siswa</span>
                                                            <strong>{{ $wali->total_siswa ?? 0 }}</strong>
                                                        </div>
                                                        <div class="metric-chip">
                                                            <span>Rapor Selesai</span>
                                                            <strong class="text-success">{{ $wali->rapor_selesai ?? 0 }}</strong>
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
                                                            <span>
                                                                @if($progress >= 100)
                                                                    Selesai
                                                                @elseif($progress >= 75)
                                                                    Hampir selesai
                                                                @elseif($progress >= 50)
                                                                    Proses
                                                                @else
                                                                    Perlu tindak lanjut
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Kelas" class="complex-cell desktop-detail-cell">
                                        <details class="mobile-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">{{ \Illuminate\Support\Str::limit($kelasNames, 58) }}</span>
                                                    <span class="mobile-summary-meta">{{ $cabangNames }}</span>
                                                </span>
                                                <span class="mobile-summary-link">Selengkapnya</span>
                                            </summary>
                                            <div class="mobile-details-body">
                                                <span class="soft-badge primary assignment-badge">{{ $kelasNames }}</span>
                                                <span class="entity-subtitle">{{ $cabangNames }}</span>
                                            </div>
                                        </details>
                                    </td>
                                    <td data-label="Tahun Ajaran" class="desktop-detail-cell">{{ $tahunNames }}</td>
                                    <td data-label="Siswa" class="text-center desktop-detail-cell">
                                        <strong>{{ $wali->total_siswa ?? 0 }}</strong>
                                    </td>
                                    <td data-label="Rapor" class="text-center desktop-detail-cell">
                                        <strong class="text-success">{{ $wali->rapor_selesai ?? 0 }}</strong>
                                    </td>
                                    <td data-label="Progress" class="complex-cell desktop-detail-cell">
                                        <details class="mobile-details">
                                            <summary>
                                                <span>
                                                    <span class="mobile-summary-main">{{ number_format($progress, 1) }}%</span>
                                                    <span class="mobile-summary-meta">
                                                        @if($progress >= 100)
                                                            Selesai
                                                        @elseif($progress >= 75)
                                                            Hampir selesai
                                                        @elseif($progress >= 50)
                                                            Proses
                                                        @else
                                                            Perlu tindak lanjut
                                                        @endif
                                                    </span>
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
                                                        <span>
                                                            @if($progress >= 100)
                                                                Selesai
                                                            @elseif($progress >= 75)
                                                                Hampir selesai
                                                            @elseif($progress >= 50)
                                                                Proses
                                                            @else
                                                                Perlu tindak lanjut
                                                            @endif
                                                        </span>
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

                <div class="mt-4">{{ $waliKelas->withQueryString()->links() }}</div>
            @else
                <div class="empty-state">
                    <i class="fas fa-user-tie"></i>
                    <h6>Tidak ada data wali kelas</h6>
                    <p>Data muncul setelah wali kelas ditugaskan pada kelas aktif.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="info-panel">
        <div class="info-icon info"><i class="fas fa-info-circle"></i></div>
        <div>
            <h6>Catatan Monitoring</h6>
            <p>Halaman ini membaca penugasan wali kelas lama dan penugasan baru dari menu wali kelas, sehingga kelas yang dikelola lewat sistem terbaru tetap ikut terpantau.</p>
        </div>
    </div>
</div>
@endsection
