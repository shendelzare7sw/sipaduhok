@extends('layouts.sneat')

@section('title', 'Dashboard Bendahara')
@section('page-title', 'Overview Keuangan')
@section('page-subtitle', 'Pantau aktivitas keuangan dan pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* ===================== BASE TOKENS ===================== */
    :root {
        --dash-primary: #4361ee;
        --dash-success: #10b981;
        --dash-warning: #f59e0b;
        --dash-danger: #ef4444;
        --dash-purple: #8b5cf6;
        --dash-surface: #ffffff;
        --dash-bg: #f8fafc;
        --dash-border: #e2e8f0;
        --dash-text: #1e293b;
        --dash-muted: #64748b;
        --dash-radius: 12px;
    }

    /* ===================== CARD BASE ===================== */
    .dash-card {
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        overflow: hidden;
    }
    .dash-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .dash-card-header {
        background: transparent;
        border-bottom: 1px solid var(--dash-border);
        padding: 1.15rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dash-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dash-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* ===================== STAT CARDS ===================== */
    .stat-widget {
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
    }
    .stat-icon-box {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .stat-details { flex-grow: 1; min-width: 0; }
    .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--dash-text);
        line-height: 1.2;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--dash-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-footer {
        margin: 0 1.5rem;
        padding: 0.85rem 0;
        border-top: 1px dashed var(--dash-border);
        font-size: 0.8rem;
        color: var(--dash-muted);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* ===================== ALERT CARDS ===================== */
    .alert-insight {
        padding: 1.15rem 1.25rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .alert-insight:last-child { margin-bottom: 0; }
    .alert-insight:hover { opacity: 0.9; transform: translateX(2px); }
    .alert-insight-warning {
        background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b;
    }
    .alert-insight-danger {
        background: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #ef4444;
    }
    .alert-insight-success {
        background: #ecfdf5; border: 1px solid #a7f3d0; border-left: 4px solid #10b981;
    }

    /* ===================== TAB NAVIGATION ===================== */
    .action-tabs {
        display: flex;
        border-bottom: 2px solid var(--dash-border);
        padding: 0 1.5rem;
        gap: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .action-tabs::-webkit-scrollbar { display: none; }
    .action-tab {
        padding: 0.85rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--dash-muted);
        border: none;
        background: transparent;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .action-tab:hover { color: var(--dash-primary); }
    .action-tab.active {
        color: var(--dash-primary);
        border-bottom-color: var(--dash-primary);
    }
    .action-tab .tab-badge {
        font-size: 0.7rem;
        padding: 0.15em 0.55em;
        border-radius: 50px;
        font-weight: 700;
        line-height: 1.3;
    }
    .action-tab .badge-danger { background: #fee2e2; color: #dc2626; }
    .action-tab .badge-success { background: #d1fae5; color: #059669; }
    .action-tab .badge-warning { background: #fef3c7; color: #d97706; }

    /* ===================== TAB CONTENT ===================== */
    .tab-panel { display: none; }
    .tab-panel.active { display: flex; flex-direction: column; flex: 1; }
    .tab-panel-footer {
        margin-top: auto;
        text-align: center;
        padding: 0.85rem 1.5rem;
        border-top: 1px solid var(--dash-border);
    }
    .tab-panel-footer a {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--dash-primary);
        text-decoration: none;
    }
    .tab-panel-footer a:hover { text-decoration: underline; }

    /* ===================== LIST ITEMS (Card Style) ===================== */
    .action-list { padding: 0; margin: 0; list-style: none; }
    .action-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--dash-border);
        transition: background 0.15s ease;
    }
    .action-item:last-child { border-bottom: none; }
    .action-item:hover { background: #f8fafc; }
    .action-item-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .action-item-body { flex: 1; min-width: 0; }
    .action-item-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--dash-text);
        margin-bottom: 0.15rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .action-item-sub {
        font-size: 0.78rem;
        color: var(--dash-muted);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .action-item-amount {
        font-weight: 700;
        font-size: 0.95rem;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .action-item-actions {
        display: flex;
        gap: 0.4rem;
        flex-shrink: 0;
    }
    .action-item-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.35rem;
        flex-shrink: 0;
    }

    /* ===================== EMPTY STATE ===================== */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: var(--dash-muted);
    }
    .empty-state i { font-size: 2.5rem; opacity: 0.3; margin-bottom: 1rem; }
    .empty-state-title { font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; }
    .empty-state-desc { font-size: 0.85rem; }

    /* ===================== QUICK LINKS ===================== */
    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.85rem;
        padding: 1.25rem;
    }
    .quick-link-item {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 1.15rem 0.75rem;
        border-radius: 10px;
        border: 1px solid var(--dash-border);
        background: var(--dash-surface);
        color: var(--dash-text);
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
        gap: 0.65rem;
    }
    .quick-link-item:hover {
        background: var(--dash-bg);
        border-color: var(--dash-primary);
        color: var(--dash-primary);
    }
    .quick-link-item i {
        font-size: 1.4rem;
        color: var(--dash-muted);
        transition: color 0.2s ease;
    }
    .quick-link-item:hover i { color: var(--dash-primary); }
    .quick-link-text {
        font-size: 0.8rem; font-weight: 600; line-height: 1.3;
    }

    /* ===================== MOBILE RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .stat-value { font-size: 1.1rem; }
        .stat-widget { padding: 1.15rem; gap: 0.85rem; }
        .stat-icon-box { width: 40px; height: 40px; font-size: 1.1rem; }
        .stat-footer { margin: 0 1.15rem; font-size: 0.75rem; }

        .action-tabs { padding: 0 1rem; gap: 0; }
        .action-tab { padding: 0.7rem 0.75rem; font-size: 0.78rem; }
        .action-tab i { font-size: 0.75rem; }
        .action-tab .tab-badge { font-size: 0.6rem; padding: 0.1em 0.45em; }

        .action-item { padding: 0.85rem 1.15rem; gap: 0.75rem; }
        .action-item-icon { width: 36px; height: 36px; font-size: 0.9rem; }
        .action-item-title { font-size: 0.85rem; }
        .action-item-sub { font-size: 0.72rem; }
        .action-item-amount { font-size: 0.85rem; }

        .quick-links-grid { padding: 1rem; gap: 0.65rem; }
        .quick-link-item { padding: 1rem 0.5rem; }
        .quick-link-item i { font-size: 1.2rem; }
        .quick-link-text { font-size: 0.75rem; }

        .dash-card-header { padding: 1rem 1.15rem; }
        .dash-card-title { font-size: 0.9rem; }

        .action-item-right { align-items: flex-end; }
    }

    @media (max-width: 480px) {
        .stat-value { font-size: 1rem; }
        .stat-label { font-size: 0.72rem; }

        .action-tabs { padding: 0 0.75rem; }
        .action-tab { padding: 0.65rem 0.6rem; font-size: 0.72rem; gap: 0.3rem; }
        .action-tab i { display: none; } /* Hide icons on very small to save space */

        .action-item {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .action-item-body { flex-basis: calc(100% - 52px); }
        .action-item-right {
            flex-basis: 100%;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding-left: 52px;
        }
    }
</style>
@endsection

@section('content')

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <!-- Total Tagihan -->
        <div class="col-6 col-xl-3">
            <div class="dash-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value" title="Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Tagihan</div>
                    </div>
                    <div class="stat-icon-box" style="color: #3b82f6; background: #eff6ff;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>TA: {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</span>
                    <i class="fas fa-calendar-alt opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Total Terbayar -->
        <div class="col-6 col-xl-3">
            <div class="dash-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value" title="Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}">Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Terbayar</div>
                    </div>
                    <div class="stat-icon-box" style="color: #10b981; background: #ecfdf5;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Tervalidasi</span>
                    <i class="fas fa-shield-alt opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Kas Masuk Hari Ini -->
        <div class="col-6 col-xl-3">
            <div class="dash-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value" title="Rp {{ number_format($kasHariIni ?? 0, 0, ',', '.') }}">Rp {{ number_format($kasHariIni ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Kas Masuk Hari Ini</div>
                    </div>
                    <div class="stat-icon-box" style="color: #f59e0b; background: #fffbeb;">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>{{ now()->translatedFormat('d F Y') }}</span>
                    <i class="fas fa-clock opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Menunggu Validasi -->
        <div class="col-6 col-xl-3">
            <div class="dash-card" style="{{ ($pembayaranPending ?? 0) > 0 ? 'border-color: #fde68a;' : '' }}">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ number_format($pembayaranPending ?? 0) }}</div>
                        <div class="stat-label">Menunggu Validasi</div>
                    </div>
                    <div class="stat-icon-box" style="color: #8b5cf6; background: #f5f3ff;">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    @if(($pembayaranPending ?? 0) > 0)
                        <span class="text-warning fw-semibold"><i class="fas fa-exclamation-circle me-1"></i>Perlu Tindakan</span>
                    @else
                        <span class="text-success"><i class="fas fa-check me-1"></i>Semua Clear</span>
                    @endif
                    <i class="fas fa-bell opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="row g-4 mb-4">
        
        <!-- Left Column: Action Center (Tabs) -->
        <div class="col-lg-8 d-flex">
            <div class="dash-card d-flex flex-column w-100" style="height: auto;">

                <!-- Tab Navigation -->
                <div class="action-tabs">
                    <button class="action-tab active" data-tab="tab-validasi">
                        <i class="fas fa-clipboard-check"></i>
                        Perlu Validasi
                        @if(($pembayaranPending ?? 0) > 0)
                            <span class="tab-badge badge-danger">{{ $pembayaranPending > 99 ? '99+' : $pembayaranPending }}</span>
                        @endif
                    </button>
                    <button class="action-tab" data-tab="tab-transaksi">
                        <i class="fas fa-exchange-alt"></i>
                        Transaksi Terbaru
                        @if(isset($transaksiTerbaru) && $transaksiTerbaru->count() > 0)
                            <span class="tab-badge badge-success">{{ $transaksiTerbaru->count() }}</span>
                        @endif
                    </button>
                    <button class="action-tab" data-tab="tab-dispensasi">
                        <i class="fas fa-handshake"></i>
                        Dispensasi
                        @if(isset($dispensasiPending) && $dispensasiPending->count() > 0)
                            <span class="tab-badge badge-warning">{{ $dispensasiPending->count() }}</span>
                        @endif
                    </button>
                </div>

                <!-- Tab 1: Perlu Validasi -->
                <div class="tab-panel active" id="tab-validasi">
                    @if(isset($pendingPembayaran) && $pendingPembayaran->count() > 0)
                        <ul class="action-list">
                            @foreach($pendingPembayaran as $item)
                                <li class="action-item">
                                    <div class="action-item-icon" style="background: #fef3c7; color: #d97706;">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="action-item-body">
                                        <div class="action-item-title">{{ $item->siswa->nama_lengkap ?? 'Siswa' }}</div>
                                        <div class="action-item-sub">
                                            <span><i class="fas fa-hashtag me-1"></i>{{ $item->kode_pembayaran }}</span>
                                            <span>·</span>
                                            <span>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</span>
                                            <span>·</span>
                                            <span>{{ $item->metode_pembayaran ?? 'Transfer' }}</span>
                                        </div>
                                    </div>
                                    <div class="action-item-right">
                                        <div class="action-item-amount text-warning">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</div>
                                        <a href="{{ route('bendahara.pembayaran.show', $item->id) }}" class="btn btn-sm btn-outline-primary px-3" style="font-size: 0.75rem; font-weight: 600; border-radius: 6px;">
                                            <i class="fas fa-eye me-1"></i>Review
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        @if(($pembayaranPending ?? 0) > 7)
                            <div class="tab-panel-footer">
                                <a href="{{ route('bendahara.pembayaran.index', ['status' => 'pending']) }}">
                                    Lihat Semua {{ $pembayaranPending }} Pembayaran Pending <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-check-circle d-block text-success"></i>
                            <div class="empty-state-title text-success">Semua Tervalidasi!</div>
                            <div class="empty-state-desc">Tidak ada pembayaran yang menunggu validasi saat ini. Kerja bagus!</div>
                        </div>
                    @endif
                </div>

                <!-- Tab 2: Transaksi Terbaru -->
                <div class="tab-panel" id="tab-transaksi">
                    @if(isset($transaksiTerbaru) && $transaksiTerbaru->count() > 0)
                        <ul class="action-list">
                            @foreach($transaksiTerbaru as $item)
                                <li class="action-item">
                                    <div class="action-item-icon" style="background: #d1fae5; color: #059669;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="action-item-body">
                                        <div class="action-item-title">{{ $item->siswa->nama_lengkap ?? 'Siswa' }}</div>
                                        <div class="action-item-sub">
                                            <span>{{ ucwords(str_replace('_', ' ', $item->tagihan->jenis_tagihan ?? '-')) }}</span>
                                            <span>·</span>
                                            <span>{{ $item->tanggal_validasi ? $item->tanggal_validasi->diffForHumans() : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="action-item-right">
                                        <div class="action-item-amount text-success">+Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</div>
                                        <span class="badge bg-label-success px-2 py-1" style="font-size: 0.68rem;">LUNAS</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-panel-footer">
                            <a href="{{ route('bendahara.pembayaran.index', ['status' => 'disetujui']) }}">
                                Lihat Semua Riwayat Transaksi <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-inbox d-block"></i>
                            <div class="empty-state-title">Belum Ada Transaksi</div>
                            <div class="empty-state-desc">Transaksi yang sudah divalidasi akan muncul di sini.</div>
                        </div>
                    @endif
                </div>

                <!-- Tab 3: Dispensasi -->
                <div class="tab-panel" id="tab-dispensasi">
                    @if(isset($dispensasiPending) && $dispensasiPending->count() > 0)
                        <ul class="action-list">
                            @foreach($dispensasiPending as $item)
                                <li class="action-item">
                                    <div class="action-item-icon" style="background: #fef3c7; color: #b45309;">
                                        <i class="fas fa-user-clock"></i>
                                    </div>
                                    <div class="action-item-body">
                                        <div class="action-item-title">{{ $item->nama_lengkap }}</div>
                                        <div class="action-item-sub">
                                            <span>{{ $item->nama_kelas ?? '-' }}</span>
                                            <span>·</span>
                                            <span>NISN: {{ $item->nisn ?? '-' }}</span>
                                            <span>·</span>
                                            <span>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="action-item-right">
                                        <span class="badge bg-label-warning px-2 py-1" style="font-size: 0.68rem;">MENUNGGU</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-panel-footer">
                            <a href="{{ route('bendahara.promotion.validation.index') }}">
                                Kelola Dispensasi <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-handshake d-block text-success"></i>
                            <div class="empty-state-title">Tidak Ada Permintaan</div>
                            <div class="empty-state-desc">Belum ada pengajuan dispensasi kenaikan kelas yang menunggu.</div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Right Column: Insights & Quick Links -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            
            <!-- Insight Alerts -->
            <div>
                <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="alert-insight alert-insight-warning shadow-sm" style="text-decoration: none;">
                    <div style="font-size: 1.75rem; color: #f59e0b;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: #d97706; letter-spacing: 0.5px;">Siswa Belum Lunas</div>
                        <div style="font-size: 1.15rem; font-weight: 700; color: #92400e;">{{ number_format($tagihanBelumLunas ?? 0) }} Siswa</div>
                    </div>
                    <i class="fas fa-chevron-right" style="color: #d97706; opacity: 0.5;"></i>
                </a>
                
                <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="alert-insight alert-insight-danger shadow-sm" style="text-decoration: none;">
                    <div style="font-size: 1.75rem; color: #ef4444;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: #b91c1c; letter-spacing: 0.5px;">Tagihan Terlambat</div>
                        <div style="font-size: 1.15rem; font-weight: 700; color: #7f1d1d;">{{ number_format($tagihanTerlambat ?? 0) }} Siswa</div>
                    </div>
                    <i class="fas fa-chevron-right" style="color: #ef4444; opacity: 0.5;"></i>
                </a>

                <a href="{{ route('bendahara.pembayaran.index') }}" class="alert-insight alert-insight-success shadow-sm" style="text-decoration: none;">
                    <div style="font-size: 1.75rem; color: #10b981;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: #047857; letter-spacing: 0.5px;">Terbayar Bulan Ini</div>
                        <div style="font-size: 1.15rem; font-weight: 700; color: #064e3b;">Rp {{ number_format($pembayaranBulanIni ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <i class="fas fa-chevron-right" style="color: #10b981; opacity: 0.5;"></i>
                </a>
            </div>

            <!-- Quick Links -->
            <div class="dash-card flex-grow-1">
                <div class="dash-card-header">
                    <h5 class="dash-card-title">
                        <i class="fas fa-bolt text-warning"></i> Akses Cepat
                    </h5>
                </div>
                
                <div class="quick-links-grid">
                    <a href="{{ route('bendahara.pembayaran.index', ['status' => 'pending']) }}" class="quick-link-item">
                        <div class="position-relative">
                            <i class="fas fa-check-double text-primary"></i>
                            @if(($pembayaranPending ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.6rem; padding: 0.3em 0.5em; transform: translate(-30%, -30%) !important;">
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
    document.addEventListener('DOMContentLoaded', function () {
        // Tab switching logic
        const tabs = document.querySelectorAll('.action-tab');
        const panels = document.querySelectorAll('.tab-panel');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                const targetId = this.getAttribute('data-tab');

                // Deactivate all tabs & panels
                tabs.forEach(t => t.classList.remove('active'));
                panels.forEach(p => p.classList.remove('active'));

                // Activate clicked tab & target panel
                this.classList.add('active');
                const targetPanel = document.getElementById(targetId);
                if (targetPanel) targetPanel.classList.add('active');
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
