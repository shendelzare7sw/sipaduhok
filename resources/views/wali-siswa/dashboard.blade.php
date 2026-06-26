@extends('layouts.sneat')

@section('title', 'Dashboard Wali Siswa')
@section('page-title', 'Dashboard Wali Siswa')
@section('page-subtitle', 'Monitoring pendidikan & keuangan anak')

@section('sidebar-menu')
    @include('wali-siswa.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-siswa/dashboard.css'])
@endsection

@section('content')
<div class="wali-siswa-dashboard-page">

    @if(isset($message))
        <div class="ot-card empty-children-card">
            <div class="text-center empty-children-body">
                <i class="fas fa-info-circle d-block empty-children-icon"></i>
                <h5 class="fw-bold empty-children-title">Belum Ada Data Anak</h5>
                <p class="empty-children-message">{{ $message }}</p>
            </div>
        </div>
    @else

        <!-- Children Cards -->
        <div class="row g-4 mb-4">
            @foreach($children as $child)
                @php
                    $childSummary = $summary[$child->id] ?? [
                        'total_tagihan' => 0,
                        'total_bayar' => 0,
                        'sisa_tagihan' => 0
                    ];
                    $persentaseBayar = $childSummary['total_tagihan'] > 0
                        ? ($childSummary['total_bayar'] / $childSummary['total_tagihan']) * 100
                        : 0;
                    $gradientIndex = $loop->index % 4;
                @endphp
                <div class="col-12 col-lg-6">
                    <div class="ot-card">
                        <div class="child-card">
                            <!-- Child Header -->
                            <div class="child-header">
                                <div class="child-avatar child-avatar-gradient-{{ $gradientIndex }}">
                                    @if($child->user && $child->user->foto_profil)
                                        <img src="{{ asset('storage/' . $child->user->foto_profil) }}" alt="avatar">
                                    @elseif($child->foto)
                                        <img src="{{ asset('storage/' . $child->foto) }}" alt="avatar">
                                    @else
                                        {{ strtoupper(substr($child->nama_lengkap, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="child-info">
                                    <div class="child-name">{{ $child->nama_lengkap }}</div>
                                    <div class="child-meta">
                                        <span><i class="fas fa-school me-1"></i>{{ $child->kelas->nama_kelas ?? '-' }}</span>
                                        <span>·</span>
                                        <span>{{ $child->cabang->nama_cabang ?? '-' }}</span>
                                        @if($child->status === 'lulus')
                                            <span class="badge bg-dark badge-child-alumni">ALUMNI</span>
                                        @endif
                                    </div>
                                </div>
                                @if($child->status !== 'lulus')
                                    @if($childSummary['sisa_tagihan'] <= 0)
                                        <span class="badge bg-success badge-child-status"><i class="fas fa-check me-1"></i>Lunas</span>
                                    @elseif($persentaseBayar >= 50)
                                        <span class="badge bg-warning badge-child-status">Sebagian</span>
                                    @else
                                        <span class="badge bg-danger badge-child-status">Belum Lunas</span>
                                    @endif
                                @endif
                            </div>

                            <!-- Finance Summary -->
                            <div class="finance-grid">
                                <div class="finance-item">
                                    <div class="finance-label">Total Tagihan</div>
                                    <div class="finance-value text-primary">Rp {{ number_format($childSummary['total_tagihan'] / 1000, 0) }}K</div>
                                </div>
                                <div class="finance-item">
                                    <div class="finance-label">Dibayar</div>
                                    <div class="finance-value text-success">Rp {{ number_format($childSummary['total_bayar'] / 1000, 0) }}K</div>
                                </div>
                                <div class="finance-item">
                                    <div class="finance-label">Sisa</div>
                                    <div class="finance-value {{ $childSummary['sisa_tagihan'] > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($childSummary['sisa_tagihan'] / 1000, 0) }}K</div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="payment-progress-label">Progress Pembayaran</span>
                                    <span class="fw-bold payment-progress-value {{ $persentaseBayar >= 100 ? 'text-success' : ($persentaseBayar >= 50 ? 'text-warning' : 'text-danger') }}">{{ number_format($persentaseBayar, 0) }}%</span>
                                </div>
                                <div class="progress payment-progress">
                                    <div class="progress-bar {{ $persentaseBayar >= 100 ? 'bg-success' : ($persentaseBayar >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                         role="progressbar"
                                         data-progress="{{ min($persentaseBayar, 100) }}"
                                         aria-valuenow="{{ number_format(min($persentaseBayar, 100), 0, '.', '') }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="child-actions">
                                <a href="{{ route('wali-siswa.tagihan.anak', $child->id) }}" class="child-action-btn btn-primary-action">
                                    <i class="fas fa-wallet"></i> Tagihan
                                </a>
                                <a href="{{ route('wali-siswa.rapor.anak', $child->id) }}" class="child-action-btn">
                                    <i class="fas fa-file-alt"></i> Rapor
                                </a>
                                @if($child->status !== 'lulus')
                                    <a href="{{ route('wali-siswa.presensi.ajukan-izin', $child->id) }}" class="child-action-btn">
                                        <i class="fas fa-paper-plane"></i> Ajukan Izin
                                    </a>
                                @else
                                    <span class="child-action-btn child-action-disabled">
                                        <i class="fas fa-graduation-cap"></i> Lulus
                                    </span>
                                @endif
                                <a href="{{ route('wali-siswa.presensi.riwayat-izin', $child->id) }}" class="child-action-btn">
                                    <i class="fas fa-history"></i> Riwayat Izin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Overall Summary (if more than 1 child) -->
        @if($children->count() > 1)
            @php
                $totalSemuaTagihan = collect($summary)->sum('total_tagihan');
                $totalSemuaBayar = collect($summary)->sum('total_bayar');
                $totalSemuaSisa = collect($summary)->sum('sisa_tagihan');
                $totalPersentase = $totalSemuaTagihan > 0 ? ($totalSemuaBayar / $totalSemuaTagihan) * 100 : 0;
            @endphp
            <div class="ot-card">
                <div class="ot-card-header">
                    <h5 class="ot-card-title">
                        <i class="fas fa-chart-pie text-primary"></i> Ringkasan Keseluruhan ({{ $children->count() }} Anak)
                    </h5>
                </div>
                <div>
                    <div class="row g-0">
                        <div class="col-md-4 border-end">
                            <div class="summary-stat">
                                <div class="summary-icon summary-icon-primary">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Total Semua Tagihan</div>
                                    <div class="summary-value">Rp {{ number_format($totalSemuaTagihan, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 border-end">
                            <div class="summary-stat">
                                <div class="summary-icon summary-icon-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Total Sudah Dibayar</div>
                                    <div class="summary-value text-success">Rp {{ number_format($totalSemuaBayar, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-stat">
                                <div class="summary-icon {{ $totalSemuaSisa > 0 ? 'summary-icon-danger' : 'summary-icon-success' }}">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Total Sisa Tagihan</div>
                                    <div class="summary-value {{ $totalSemuaSisa > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($totalSemuaSisa, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 overall-progress-meta">
                            <span>Progress Keseluruhan</span>
                            <span class="fw-bold {{ $totalPersentase >= 100 ? 'text-success' : ($totalPersentase >= 50 ? 'text-warning' : 'text-danger') }}">{{ number_format($totalPersentase, 1) }}%</span>
                        </div>
                        <div class="progress overall-progress">
                            <div class="progress-bar {{ $totalPersentase >= 100 ? 'bg-success' : ($totalPersentase >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar"
                                 data-progress="{{ min($totalPersentase, 100) }}"
                                 aria-valuenow="{{ number_format(min($totalPersentase, 100), 0, '.', '') }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @endif
</div>

@endsection

@section('scripts')
    @vite(['resources/js/wali-siswa/dashboard.js'])
@endsection
