@extends('layouts.sneat')

@section('title', 'Admin - Pengaturan Kenaikan Kelas')
@section('page-title', 'Pengaturan Kenaikan Kelas')

@section('sidebar-menu')
    @if(auth()->user()->isWakilKepalaSekolah())
        @include('waka.partials.sneat-sidebar-menu')
    @elseif(auth()->user()->isAdmin())
        @include('admin.partials.sneat-sidebar-menu')
    @endif
@endsection

@section('content')
@php
    $routePrefix = request()->routeIs('waka.*') ? 'waka.promotion' : 'admin.akademik.promotion';
    $tahunLabel = $tahun->nama_tahun_ajaran ?? $tahun->nama ?? $tahun->tahun_ajaran ?? '-';
    $tanggalRapor = $setting && $setting->tanggal_pengambilan_rapor ? \Carbon\Carbon::parse($setting->tanggal_pengambilan_rapor)->format('Y-m-d') : '';
    $tanggalEksekusi = $setting && $setting->tanggal_eksekusi ? \Carbon\Carbon::parse($setting->tanggal_eksekusi)->format('Y-m-d') : '';
    $waktuEksekusi = $setting && $setting->tanggal_eksekusi ? \Carbon\Carbon::parse($setting->tanggal_eksekusi)->format('H:i') : '02:00';
    $minimalTuntas = $setting->persentase_minimal_tuntas ?? 70;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="promotion-page">
        <div class="page-panel mb-4">
            <div>
                <span class="panel-kicker">Akademik</span>
                <h4 class="panel-title">Pengaturan Kenaikan Kelas</h4>
                <p class="panel-subtitle mb-0">Tetapkan tanggal rapor, jadwal otomatis, dan ambang akademik untuk proses kenaikan kelas.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon primary"><i class="fas fa-calendar-alt"></i></div>
                    <span>Tahun Ajaran</span>
                    <strong>{{ $tahunLabel }}</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon success"><i class="fas fa-chart-line"></i></div>
                    <span>Minimal Tuntas</span>
                    <strong>{{ $minimalTuntas }}%</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon info"><i class="fas fa-file-alt"></i></div>
                    <span>Pembagian Rapor</span>
                    <strong>{{ $tanggalRapor ? \Carbon\Carbon::parse($tanggalRapor)->format('d/m/Y') : '-' }}</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon warning"><i class="fas fa-clock"></i></div>
                    <span>Eksekusi Otomatis</span>
                    <strong>{{ $tanggalEksekusi ? \Carbon\Carbon::parse($tanggalEksekusi)->format('d/m/Y') : 'Manual' }}</strong>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="content-card">
                    <div class="content-card-header">
                        <div>
                            <h5 class="mb-1">Konfigurasi Utama</h5>
                            <p class="text-muted mb-0">Pengaturan ini dipakai saat simulasi dan eksekusi kenaikan kelas.</p>
                        </div>
                    </div>
                    <div class="content-card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route($routePrefix . '.settings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">

                        <div class="form-block">
                            <label class="form-label">Tahun Ajaran Aktif</label>
                            <input type="text" class="form-control" value="{{ $tahunLabel }}" disabled>
                        </div>

                        <div class="form-block">
                            <label class="form-label">Tanggal Pembagian Rapor</label>
                            <input type="date" name="tanggal_pengambilan_rapor" class="form-control"
                                   value="{{ $tanggalRapor }}"
                                   required>
                            <div class="form-text">Tanggal resmi pembagian rapor kepada siswa.</div>
                        </div>

                        <div class="form-block">
                            <label class="form-label">Tanggal Eksekusi Kenaikan (Otomatis)</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="date" name="tanggal_eksekusi" class="form-control" 
                                           value="{{ $tanggalEksekusi }}">
                                    <div class="form-text">Tanggal eksekusi</div>
                                </div>
                                <div class="col-md-6">
                                    <input type="time" name="waktu_eksekusi" class="form-control" 
                                           value="{{ $waktuEksekusi }}">
                                    <div class="form-text">Waktu eksekusi (WIB)</div>
                                </div>
                            </div>
                            <div class="form-text mt-2">Jika diisi, sistem akan menjalankan job kenaikan otomatis pada tanggal & waktu ini. Kosongkan jika ingin eksekusi manual via tombol.</div>
                        </div>

                        <div class="form-block">
                            <label class="form-label">Persentase Minimal Tuntas (%)</label>
                            <div class="input-group">
                                <input type="number" name="persentase_minimal_tuntas" class="form-control" min="0" max="100" value="{{ $minimalTuntas }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Berapa % mata pelajaran yang harus tuntas (>= KKM) agar siswa dianggap layak naik kelas secara akademik.</div>
                        </div>

                        <div class="action-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="content-card h-100">
                    <div class="content-card-header">
                        <div>
                            <h5 class="mb-1">Informasi Sistem</h5>
                            <p class="text-muted mb-0">Syarat yang diperiksa saat siswa diproses.</p>
                        </div>
                    </div>
                    <div class="content-card-body">
                        <div class="info-list">
                            <div class="info-item">
                                <div class="info-icon success"><i class="fas fa-wallet"></i></div>
                                <div>
                                    <h6>Syarat Keuangan</h6>
                                    <p>Status tagihan harus lunas atau memiliki izin khusus dari ketua PKBM.</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon primary"><i class="fas fa-graduation-cap"></i></div>
                                <div>
                                    <h6>Syarat Akademik</h6>
                                    <p>Persentase mata pelajaran yang nilainya memenuhi KKM harus melewati ambang batas.</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon warning"><i class="fas fa-bullseye"></i></div>
                                <div>
                                    <h6>Pastikan KKM Siap</h6>
                                    <p>Lengkapi KKM semua mata pelajaran sebelum menjalankan eksekusi kenaikan kelas.</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route($routePrefix . '.kkm.index') }}" class="btn btn-outline-primary w-100 mt-3">
                            <i class="fas fa-sliders-h me-1"></i> Buka Pengaturan KKM
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.promotion-page {
    --primary: #4361ee;
    --success: #10b981;
    --warning: #f59e0b;
    --info: #06b6d4;
    --ink: #1f2937;
    --muted: #64748b;
    --line: #e2e8f0;
    --soft: #f8fafc;
}

.page-panel,
.content-card,
.summary-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
}

.page-panel {
    padding: 20px;
}

.panel-kicker {
    color: var(--primary);
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    margin-bottom: 6px;
    text-transform: uppercase;
}

.panel-title {
    color: var(--ink);
    font-weight: 800;
    margin-bottom: 4px;
}

.panel-subtitle,
.summary-card span {
    color: var(--muted);
}

.summary-card {
    height: 100%;
    padding: 16px;
}

.summary-icon,
.info-icon {
    align-items: center;
    border-radius: 10px;
    display: inline-flex;
    height: 36px;
    justify-content: center;
    width: 36px;
}

.summary-icon {
    margin-bottom: 14px;
}

.summary-icon.primary,
.info-icon.primary { background: rgba(67, 97, 238, .12); color: var(--primary); }
.summary-icon.success,
.info-icon.success { background: rgba(16, 185, 129, .12); color: var(--success); }
.summary-icon.warning,
.info-icon.warning { background: rgba(245, 158, 11, .14); color: var(--warning); }
.summary-icon.info { background: rgba(6, 182, 212, .12); color: var(--info); }

.summary-card span {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .03em;
    text-transform: uppercase;
}

.summary-card strong {
    color: var(--ink);
    display: block;
    font-size: 18px;
    line-height: 1.25;
    margin-top: 5px;
}

.content-card {
    overflow: hidden;
}

.content-card-header {
    border-bottom: 1px solid var(--line);
    padding: 18px 20px;
}

.content-card-header h5,
.info-item h6 {
    color: var(--ink);
    font-weight: 800;
}

.content-card-body {
    padding: 20px;
}

.form-block {
    background: var(--soft);
    border: 1px solid #eef2f7;
    border-radius: 12px;
    margin-bottom: 16px;
    padding: 16px;
}

.form-label {
    color: #334155;
    font-weight: 800;
}

.action-footer {
    background: #fff;
    border-top: 1px solid var(--line);
    margin: 20px -20px -20px;
    padding: 16px 20px;
    text-align: right;
}

.info-list {
    display: grid;
    gap: 14px;
}

.info-item {
    align-items: flex-start;
    background: var(--soft);
    border: 1px solid #eef2f7;
    border-radius: 12px;
    display: flex;
    gap: 12px;
    padding: 14px;
}

.info-item p {
    color: var(--muted);
    margin-bottom: 0;
}

@media (max-width: 767.98px) {
    .page-panel,
    .content-card-body {
        padding: 16px;
    }

    .action-footer {
        margin: 16px -16px -16px;
    }

    .action-footer .btn {
        width: 100%;
    }
}
</style>
@endsection
