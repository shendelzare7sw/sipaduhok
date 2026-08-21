@extends('layouts.sneat')

@section('title', 'Dashboard Bendahara')
@section('page-title', 'Overview Keuangan')
@section('page-subtitle', 'Pantau aktivitas keuangan dan pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/dashboard/bendahara.css'])
@endsection

@section('content')
<div class="bendahara-dashboard-page">

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
                    <div class="stat-icon-box stat-icon-primary">
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
                    <div class="stat-icon-box stat-icon-success">
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
                    <div class="stat-icon-box stat-icon-warning">
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
            <div class="dash-card {{ ($pembayaranPending ?? 0) > 0 ? 'dash-card-pending' : '' }}">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ number_format($pembayaranPending ?? 0) }}</div>
                        <div class="stat-label">Menunggu Validasi</div>
                    </div>
                    <div class="stat-icon-box stat-icon-purple">
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
            <div class="dash-card dashboard-action-card d-flex flex-column w-100">

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
                                    <div class="action-item-icon action-item-icon-warning">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="action-item-body">
                                        <div class="action-item-title">{{ $item->siswa->nama_lengkap ?? 'Siswa' }}</div>
                                        <div class="action-item-sub">
                                            <span><i class="fas fa-hashtag me-1"></i>{{ $item->kode_pembayaran }}</span>
                                            <span>&middot;</span>
                                            <span>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</span>
                                            <span>&middot;</span>
                                            <span>{{ $item->payment_channel_label }}</span>
                                        </div>
                                    </div>
                                    <div class="action-item-right">
                                        <div class="action-item-amount text-warning">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</div>
                                        <a href="{{ route('bendahara.pembayaran.show', $item->id) }}" class="btn btn-sm btn-outline-primary px-3 review-action-btn">
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
                                    <div class="action-item-icon action-item-icon-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="action-item-body">
                                        <div class="action-item-title">{{ $item->siswa->nama_lengkap ?? 'Siswa' }}</div>
                                        <div class="action-item-sub">
                                            <span>{{ ucwords(str_replace('_', ' ', $item->tagihan->jenis_tagihan ?? '-')) }}</span>
                                            <span>&middot;</span>
                                            <span>{{ $item->tanggal_validasi ? $item->tanggal_validasi->copy()->locale('id')->diffForHumans() : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="action-item-right">
                                        <div class="action-item-amount text-success">+Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</div>
                                        <span class="badge bg-label-success px-2 py-1 status-badge-sm">LUNAS</span>
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
                                    <div class="action-item-icon action-item-icon-amber">
                                        <i class="fas fa-user-clock"></i>
                                    </div>
                                    <div class="action-item-body">
                                        <div class="action-item-title">{{ $item->nama_lengkap }}</div>
                                        <div class="action-item-sub">
                                            <span>{{ $item->nama_kelas ?? '-' }}</span>
                                            <span>&middot;</span>
                                            <span>NISN: {{ $item->nisn ?? '-' }}</span>
                                            <span>&middot;</span>
                                            <span>{{ \Carbon\Carbon::parse($item->created_at)->locale('id')->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="action-item-right">
                                        <span class="badge bg-label-warning px-2 py-1 status-badge-sm">MENUNGGU</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-panel-footer">
                            <a href="{{ route('bendahara.kenaikan-kelas.validation.index') }}">
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
                <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="alert-insight alert-insight-warning shadow-sm">
                    <div class="alert-insight-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-uppercase alert-insight-label">Siswa Belum Lunas</div>
                        <div class="alert-insight-value">{{ number_format($tagihanBelumLunas ?? 0) }} Siswa</div>
                    </div>
                    <i class="fas fa-chevron-right alert-insight-chevron"></i>
                </a>
                
                <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="alert-insight alert-insight-danger shadow-sm">
                    <div class="alert-insight-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-uppercase alert-insight-label">Tagihan Terlambat</div>
                        <div class="alert-insight-value">{{ number_format($tagihanTerlambat ?? 0) }} Siswa</div>
                    </div>
                    <i class="fas fa-chevron-right alert-insight-chevron"></i>
                </a>

                <a href="{{ route('bendahara.pembayaran.index') }}" class="alert-insight alert-insight-success shadow-sm">
                    <div class="alert-insight-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-uppercase alert-insight-label">Terbayar Bulan Ini</div>
                        <div class="alert-insight-value">Rp {{ number_format($pembayaranBulanIni ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <i class="fas fa-chevron-right alert-insight-chevron"></i>
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
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm quick-link-badge">
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
                    <a href="{{ route('bendahara.kenaikan-kelas.validation.index') }}" class="quick-link-item">
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
</div>

@endsection

@section('scripts')
    @vite(['resources/js/dashboard/bendahara.js'])
@endsection
