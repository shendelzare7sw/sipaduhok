@extends('layouts.sneat')

@section('title', 'Dashboard Bendahara')
@section('page-title', 'Overview Keuangan')
@section('page-subtitle', 'Pantau aktivitas keuangan dan pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Card Styling */
    .dashboard-card {
        background: var(--surface-color, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        overflow: hidden;
    }
    
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.04);
    }

    .card-header-clean {
        background: transparent;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-clean {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-main, #1e293b);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-title-icon {
        color: var(--primary-color, #4361ee);
    }

    /* Stat Cards */
    .stat-widget {
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: rgba(67, 97, 238, 0.08);
        color: var(--primary-color, #4361ee);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-details {
        flex-grow: 1;
        overflow: hidden;
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-main, #1e293b);
        line-height: 1.2;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--text-muted, #64748b);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-footer {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed var(--border-color, #e2e8f0);
        font-size: 0.8rem;
        color: var(--secondary-color, #64748b);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Quick Links Grid */
    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1.5rem;
    }

    .quick-link-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.25rem 1rem;
        border-radius: 10px;
        border: 1px solid var(--border-color, #e2e8f0);
        background: var(--surface-color, #ffffff);
        color: var(--text-main, #1e293b);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
        gap: 0.75rem;
    }

    .quick-link-item:hover {
        background: var(--background-color, #f8fafc);
        border-color: var(--primary-color, #4361ee);
        color: var(--primary-color, #4361ee);
    }

    .quick-link-item i {
        font-size: 1.5rem;
        color: var(--secondary-color, #64748b);
        transition: color 0.2s ease;
    }

    .quick-link-item:hover i {
        color: var(--primary-color, #4361ee);
    }

    .quick-link-text {
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1.3;
    }

    /* Table Styling */
    .table thead th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    .currency-text {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
    }

    /* Alerts */
    .alert-card {
        padding: 1.25rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .alert-card-warning {
        background-color: #fffbeb;
        border: 1px solid #fde68a;
        border-left: 4px solid #f59e0b;
    }
    .alert-card-danger {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        border-left: 4px solid #ef4444;
    }
</style>
@endsection

@section('content')

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <!-- Total Tagihan -->
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value" title="Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Tagihan</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #3b82f6; background: #eff6ff;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
                <div class="stat-footer px-4 pb-3">
                    <span>TA: {{ $tahunAjaran->nama_tahun_ajaran ?? '2025/2026' }}</span>
                    <i class="fas fa-calendar-alt text-muted opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Total Terbayar -->
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value" title="Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}">Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Terbayar</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #10b981; background: #ecfdf5;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
                <div class="stat-footer px-4 pb-3">
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Tervalidasi</span>
                    <i class="fas fa-shield-alt text-muted opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Bulan Ini -->
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value" title="Rp {{ number_format($pembayaranBulanIni ?? 0, 0, ',', '.') }}">Rp {{ number_format($pembayaranBulanIni ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Terbayar Bulan Ini</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #f59e0b; background: #fffbeb;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="stat-footer px-4 pb-3">
                    <span>{{ now()->translatedFormat('F Y') }}</span>
                    <i class="fas fa-clock text-muted opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Menunggu Validasi -->
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ number_format($pembayaranPending ?? 0) }}</div>
                        <div class="stat-label">Menunggu Validasi</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #8b5cf6; background: #f5f3ff;">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
                <div class="stat-footer px-4 pb-3">
                    <span class="text-warning">Segera Konfirmasi</span>
                    <i class="fas fa-exclamation-circle text-muted opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="row g-4 mb-4">
        
        <!-- Left Column (Data Siswa Table) -->
        <div class="col-lg-8 d-flex flex-column gap-4">
            
            <div class="dashboard-card flex-grow-1">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-exclamation-triangle card-title-icon text-warning"></i> Prioritas Penagihan (Belum Lunas/Terlambat)
                    </h5>
                    <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-sm btn-outline-primary shadow-sm" style="font-weight: 500;">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($siswaRecent) && $siswaRecent->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <p>Belum ada data siswa terbaru.</p>
                        </div>
                    @else
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">NO</th>
                                        <th>IDENTITAS SISWA</th>
                                        <th>KELAS</th>
                                        <th class="text-end">TOTAL TAGIHAN</th>
                                        <th class="text-end">SISA</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswaRecent as $index => $siswa)
                                        <tr>
                                            <td class="text-center fw-medium text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $siswa->nama_lengkap }}</div>
                                                <small class="text-muted">{{ $siswa->nisn }}</small>
                                            </td>
                                            <td class="fw-medium text-primary text-uppercase small">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                            <td class="currency-text text-end">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td>
                                            <td class="currency-text text-end {{ $siswa->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if($siswa->sisa_tagihan <= 0)
                                                    <span class="badge bg-label-success px-2 py-1"><i class="fas fa-check me-1"></i> LUNAS</span>
                                                @else
                                                    <span class="badge bg-label-danger px-2 py-1"><i class="fas fa-times me-1"></i> BELUM LUNAS</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('bendahara.tagihan.show', $siswa->id) }}" class="btn btn-sm btn-icon btn-outline-info" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column (Links & Concerns) -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            
            <!-- Issues / Concerns -->
            <div>
                <div class="alert-card alert-card-warning shadow-sm">
                    <div style="font-size: 2rem; color: #f59e0b;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div>
                        <div class="text-uppercase" style="font-size: 0.75rem; font-weight: 700; color: #d97706; letter-spacing: 0.5px;">Tagihan Belum Lunas</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: #92400e;">{{ number_format($tagihanBelumLunas ?? 0) }} Siswa</div>
                    </div>
                </div>
                
                <div class="alert-card alert-card-danger shadow-sm mb-0">
                    <div style="font-size: 2rem; color: #ef4444;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase" style="font-size: 0.75rem; font-weight: 700; color: #b91c1c; letter-spacing: 0.5px;">Tagihan Terlambat</div>
                            <div style="font-size: 1.25rem; font-weight: 700; color: #7f1d1d;">{{ number_format($tagihanTerlambat ?? 0) }} Siswa</div>
                        </div>
                        <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="btn btn-sm btn-danger px-2 py-1 rounded shadow-sm">Detail</a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="dashboard-card flex-grow-1">
                <div class="card-header-clean border-bottom">
                    <h5 class="card-title-clean">
                        <i class="fas fa-bolt card-title-icon text-warning"></i> Menu Akses Cepat
                    </h5>
                </div>
                
                <div class="quick-links-grid">
                    <a href="{{ route('bendahara.pembayaran.index', ['status' => 'pending']) }}" class="quick-link-item">
                        <div class="position-relative">
                            <i class="fas fa-check-double text-primary"></i>
                            @if(($pembayaranPending ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.65rem; padding: 0.35em 0.55em; transform: translate(-30%, -30%) !important;">
                                {{ $pembayaranPending > 99 ? '99+' : $pembayaranPending }}
                            </span>
                            @endif
                        </div>
                        <span class="quick-link-text">Validasi<br>Pembayaran</span>
                    </a>
                    <a href="{{ route('bendahara.tagihan.bulk-create') }}" class="quick-link-item">
                        <i class="fas fa-plus-circle text-success"></i>
                        <span class="quick-link-text">Buat Tagihan<br>Massal</span>
                    </a>
                    <a href="{{ route('bendahara.validasi-akses.index') }}" class="quick-link-item">
                        <i class="fas fa-id-card text-warning"></i>
                        <span class="quick-link-text">Validasi<br>Akses</span>
                    </a>
                    <a href="{{ route('bendahara.promotion.validation.index') }}" class="quick-link-item">
                        <i class="fas fa-handshake text-secondary"></i>
                        <span class="quick-link-text">Validasi<br>Dispensasi</span>
                    </a>
                    <a href="{{ route('bendahara.tagihan.index') }}" class="quick-link-item">
                        <i class="fas fa-file-invoice-dollar text-primary"></i>
                        <span class="quick-link-text">Kelola<br>Tagihan</span>
                    </a>
                    <a href="{{ route('bendahara.laporan.index') }}" class="quick-link-item">
                        <i class="fas fa-print text-info"></i>
                        <span class="quick-link-text">Cetak<br>Laporan</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
<script>
    // Initialize tooltips if needed
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
