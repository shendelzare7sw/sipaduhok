@extends('layouts.sneat')

@section('title', 'Dashboard Orang Tua')
@section('page-title', 'Dashboard Orang Tua')
@section('page-subtitle', 'Monitoring pendidikan & keuangan anak')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    :root {
        --ot-primary: #4361ee;
        --ot-success: #10b981;
        --ot-warning: #f59e0b;
        --ot-danger: #ef4444;
        --ot-info: #06b6d4;
        --ot-purple: #8b5cf6;
        --ot-surface: #ffffff;
        --ot-bg: #f8fafc;
        --ot-border: #e2e8f0;
        --ot-text: #1e293b;
        --ot-muted: #64748b;
        --ot-radius: 12px;
    }
    .ot-card {
        background: var(--ot-surface);
        border: 1px solid var(--ot-border);
        border-radius: var(--ot-radius);
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .ot-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .ot-card-header {
        background: transparent;
        border-bottom: 1px solid var(--ot-border);
        padding: 1.15rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ot-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--ot-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Child Card */
    .child-card {
        padding: 1.5rem;
    }
    .child-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .child-avatar {
        width: 48px; height: 48px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        color: white;
        flex-shrink: 0;
        background: linear-gradient(135deg, #4361ee, #3b82f6);
        overflow: hidden;
    }
    .child-avatar img {
        width: 100%; height: 100%; object-fit: cover;
    }
    .child-info { flex: 1; min-width: 0; }
    .child-name {
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--ot-text);
        margin-bottom: 0.1rem;
        word-break: break-word;
        line-height: 1.3;
    }
    .child-meta {
        font-size: 0.78rem;
        color: var(--ot-muted);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Financial Summary */
    .finance-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.6rem;
        margin-bottom: 1rem;
    }
    .finance-item {
        text-align: center;
        padding: 0.75rem 0.5rem;
        border-radius: 8px;
        background: var(--ot-bg);
        border: 1px solid var(--ot-border);
    }
    .finance-label {
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--ot-muted);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 0.2rem;
    }
    .finance-value {
        font-size: 0.85rem;
        font-weight: 700;
    }

    /* Action Buttons */
    .child-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    .child-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.6rem 0.75rem;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--ot-border);
        background: var(--ot-surface);
        color: var(--ot-text);
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .child-action-btn:hover {
        border-color: var(--ot-primary);
        color: var(--ot-primary);
        background: #f0f4ff;
    }
    .child-action-btn.btn-primary-action {
        background: var(--ot-primary);
        color: white;
        border-color: var(--ot-primary);
    }
    .child-action-btn.btn-primary-action:hover {
        background: #3451d1;
        color: white;
    }

    /* Summary Section */
    .summary-stat {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
    }
    .summary-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .summary-label { font-size: 0.75rem; color: var(--ot-muted); margin-bottom: 0.15rem; }
    .summary-value { font-size: 1rem; font-weight: 700; color: var(--ot-text); }

    /* Responsive */
    @media (max-width: 768px) {
        .child-card { padding: 1.15rem; }
        .child-avatar { width: 42px; height: 42px; font-size: 1rem; }
        .child-name { font-size: 0.95rem; }
        .finance-item { padding: 0.6rem 0.35rem; }
        .finance-label { font-size: 0.6rem; }
        .finance-value { font-size: 0.78rem; }
        .child-action-btn { font-size: 0.72rem; padding: 0.5rem 0.5rem; }
        .ot-card-header { padding: 1rem 1.15rem; }
        .ot-card-title { font-size: 0.9rem; }
        .summary-stat { padding: 0.85rem 1rem; }
        .summary-value { font-size: 0.9rem; }
    }
    @media (max-width: 480px) {
        .child-actions { grid-template-columns: repeat(2, 1fr); }
        .finance-grid { grid-template-columns: repeat(3, 1fr); gap: 0.4rem; }
    }
</style>
@endsection

@section('content')

    @if(isset($message))
        <div class="ot-card" style="border-left: 4px solid var(--ot-warning);">
            <div class="text-center" style="padding: 3rem 1.5rem;">
                <i class="fas fa-info-circle d-block" style="font-size: 2.5rem; color: #fde68a; margin-bottom: 1rem;"></i>
                <h5 class="fw-bold" style="color: var(--ot-text);">Belum Ada Data Anak</h5>
                <p style="color: var(--ot-muted); font-size: 0.9rem;">{{ $message }}</p>
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
                    $gradients = ['linear-gradient(135deg, #4361ee, #3b82f6)', 'linear-gradient(135deg, #8b5cf6, #7c3aed)', 'linear-gradient(135deg, #06b6d4, #0891b2)', 'linear-gradient(135deg, #10b981, #059669)'];
                @endphp
                <div class="col-12 col-lg-6">
                    <div class="ot-card">
                        <div class="child-card">
                            <!-- Child Header -->
                            <div class="child-header">
                                <div class="child-avatar" style="background: {{ $gradients[$loop->index % 4] }};">
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
                                            <span class="badge bg-dark" style="font-size: 0.6rem;">ALUMNI</span>
                                        @endif
                                    </div>
                                </div>
                                @if($child->status !== 'lulus')
                                    @if($childSummary['sisa_tagihan'] <= 0)
                                        <span class="badge bg-success" style="font-size: 0.68rem;"><i class="fas fa-check me-1"></i>Lunas</span>
                                    @elseif($persentaseBayar >= 50)
                                        <span class="badge bg-warning" style="font-size: 0.68rem;">Sebagian</span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 0.68rem;">Belum Lunas</span>
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
                                    <span style="font-size: 0.72rem; color: var(--ot-muted);">Progress Pembayaran</span>
                                    <span class="fw-bold {{ $persentaseBayar >= 100 ? 'text-success' : ($persentaseBayar >= 50 ? 'text-warning' : 'text-danger') }}" style="font-size: 0.75rem;">{{ number_format($persentaseBayar, 0) }}%</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 3px;">
                                    <div class="progress-bar {{ $persentaseBayar >= 100 ? 'bg-success' : ($persentaseBayar >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                         role="progressbar" style="width: {{ min($persentaseBayar, 100) }}%; border-radius: 3px;"></div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="child-actions">
                                <a href="{{ route('orang-tua.tagihan.anak', $child->id) }}" class="child-action-btn btn-primary-action">
                                    <i class="fas fa-wallet"></i> Tagihan
                                </a>
                                <a href="{{ route('orang-tua.rapor.anak', $child->id) }}" class="child-action-btn">
                                    <i class="fas fa-file-alt"></i> Rapor
                                </a>
                                @if($child->status !== 'lulus')
                                    <a href="{{ route('orang-tua.presensi.ajukan-izin', $child->id) }}" class="child-action-btn">
                                        <i class="fas fa-paper-plane"></i> Ajukan Izin
                                    </a>
                                @else
                                    <span class="child-action-btn" style="opacity: 0.5; cursor: default;">
                                        <i class="fas fa-graduation-cap"></i> Lulus
                                    </span>
                                @endif
                                <a href="{{ route('orang-tua.presensi.riwayat-izin', $child->id) }}" class="child-action-btn">
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
                                <div class="summary-icon" style="background: #eff6ff; color: #3b82f6;">
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
                                <div class="summary-icon" style="background: #ecfdf5; color: #10b981;">
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
                                <div class="summary-icon" style="background: {{ $totalSemuaSisa > 0 ? '#fef2f2' : '#ecfdf5' }}; color: {{ $totalSemuaSisa > 0 ? '#ef4444' : '#10b981' }};">
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
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.78rem; color: var(--ot-muted);">
                            <span>Progress Keseluruhan</span>
                            <span class="fw-bold {{ $totalPersentase >= 100 ? 'text-success' : ($totalPersentase >= 50 ? 'text-warning' : 'text-danger') }}">{{ number_format($totalPersentase, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 3px;">
                            <div class="progress-bar {{ $totalPersentase >= 100 ? 'bg-success' : ($totalPersentase >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar" style="width: {{ min($totalPersentase, 100) }}%; border-radius: 3px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @endif

@endsection
