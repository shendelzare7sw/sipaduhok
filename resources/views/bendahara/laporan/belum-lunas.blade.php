@extends('layouts.app')

@section('title', 'Laporan Siswa Belum Lunas')
@section('page-title', 'Laporan Siswa Belum Lunas')
@section('page-subtitle', 'Daftar siswa dengan tagihan belum lunas')


@section('styles')
<style>
    :root {
        --late-primary: #4361ee;
        --late-success: #10b981;
        --late-warning: #f59e0b;
        --late-danger: #ef4444;
        --late-info: #06b6d4;
        --late-purple: #8b5cf6;
        --late-surface: #ffffff;
        --late-bg: #f8fafc;
        --late-border: #e2e8f0;
        --late-text: #1e293b;
        --late-muted: #64748b;
        --late-radius: 12px;
    }

    .late-shell {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .stat-widget,
    .late-card {
        background: var(--late-surface);
        border: 1px solid var(--late-border);
        border-radius: var(--late-radius);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .stat-widget {
        padding: 1.35rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: transform 0.2s ease;
    }

    .stat-widget:hover { transform: translateY(-2px); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .stat-value {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--late-text);
        line-height: 1.25;
        word-break: break-word;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--late-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .stat-desc {
        font-size: 0.75rem;
        color: var(--late-muted);
        margin-top: 0.15rem;
    }

    .late-card {
        overflow: hidden;
    }

    .late-card-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--late-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .late-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--late-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .late-card-subtitle {
        color: var(--late-muted);
        font-size: 0.82rem;
        margin-top: 0.25rem;
    }

    .filter-wrapper {
        display: flex;
        gap: 0.75rem;
        align-items: end;
        flex-wrap: wrap;
        background: var(--late-bg);
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid var(--late-border);
    }

    .filter-field {
        flex: 1 1 260px;
    }

    .filter-field .form-label {
        color: var(--late-muted);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .filter-field .form-select {
        border-color: var(--late-border);
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .btn-soft {
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    .table-clean {
        margin: 0;
    }

    .table-clean th {
        background: var(--late-bg);
        border-bottom: 1px solid var(--late-border);
        color: var(--late-muted);
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem;
        white-space: nowrap;
    }

    .table-clean td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--late-border);
        color: var(--late-text);
        font-size: 0.88rem;
    }

    .table-clean tbody tr:hover {
        background: #f8fafc;
    }

    .table-clean tbody tr:last-child td {
        border-bottom: none;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .student-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.12);
        color: var(--late-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.78rem;
        flex-shrink: 0;
    }

    .student-name {
        color: var(--late-text);
        font-weight: 700;
        line-height: 1.25;
        word-break: break-word;
    }

    .student-meta {
        color: var(--late-muted);
        font-size: 0.76rem;
        margin-top: 0.15rem;
    }

    .currency-font {
        font-weight: 700;
        white-space: nowrap;
    }

    .progress-wrap {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        min-width: 140px;
    }

    .progress-track {
        width: 100%;
        height: 8px;
        background: var(--late-border);
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 999px;
    }

    .action-btns {
        display: flex;
        justify-content: center;
        gap: 0.35rem;
    }

    .action-btns .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .empty-state {
        min-height: 260px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--late-muted);
        padding: 2rem;
    }

    .empty-state i {
        color: #bbf7d0;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    }

    @media (max-width: 768px) {
        .stat-row { grid-template-columns: 1fr; }
        .late-card-header { align-items: stretch; }
        .late-card-header > div { width: 100%; }
        .filter-wrapper { flex-direction: column; align-items: stretch; }
        .filter-field {
            flex: 0 0 auto;
            width: 100%;
        }
        .filter-wrapper .btn { width: 100%; }
        .table-responsive { overflow-x: visible; }
        .table-clean thead { display: none; }
        .table-clean tbody tr {
            display: flex;
            flex-direction: column;
            border-bottom: 2px solid var(--late-border);
            background: #fff;
        }
        .table-clean tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            text-align: right;
            white-space: normal;
        }
        .table-clean tbody td::before {
            content: attr(data-label);
            color: var(--late-muted);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            flex-shrink: 0;
        }
        .table-clean tbody td.mobile-card-head {
            background: var(--late-bg);
            align-items: flex-start;
            text-align: left;
            order: -1;
        }
        .table-clean tbody td.mobile-card-head::before,
        .table-clean tbody td.mobile-card-actions::before {
            display: none;
        }
        .student-info {
            width: 100%;
            align-items: flex-start;
        }
        .progress-wrap { min-width: 0; width: 55%; }
        .mobile-card-actions {
            justify-content: flex-end !important;
            background: #f8fafc;
        }
        .action-btns { width: 100%; }
        .action-btns .btn { flex: 1; width: auto; }
    }
</style>
@endsection

@section('content')
@php
    $totalSisa = $siswaList->sum('sisa_tagihan');
    $totalTagihan = $siswaList->sum('total_tagihan');
    $totalBayar = $siswaList->sum('total_bayar');
@endphp

<div class="late-shell">
    <div class="late-card">
        <div class="late-card-header">
            <div>
                <h5 class="late-card-title"><i class="fas fa-filter" style="color: var(--late-primary);"></i> Filter Laporan</h5>
                <div class="late-card-subtitle">Saring laporan tunggakan berdasarkan kelas.</div>
            </div>
        </div>
        <form action="{{ route('bendahara.laporan.belum-lunas') }}" method="GET" class="filter-wrapper">
            <div class="filter-field">
                <label class="form-label">Kelas</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary btn-sm btn-soft px-3">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('bendahara.laporan.cetak-belum-lunas', request()->query()) }}" class="btn btn-success btn-sm btn-soft px-3" target="_blank">
                <i class="fas fa-print"></i> Cetak Laporan
            </a>
            <a href="{{ route('bendahara.laporan.belum-lunas') }}" class="btn btn-outline-danger btn-sm btn-soft px-3">
                <i class="fas fa-times"></i> Reset
            </a>
        </form>
    </div>

    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fff1f2; color: #ef4444;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <div class="stat-value">{{ $siswaList->count() }}</div>
                <div class="stat-label">Siswa Belum Lunas</div>
                <div class="stat-desc">Siswa dengan sisa tagihan</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div>
                <div class="stat-value">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
                <div class="stat-label">Total Sisa Tagihan</div>
                <div class="stat-desc">Piutang berjalan</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="stat-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                <div class="stat-label">Total Tagihan</div>
                <div class="stat-desc">Target keseluruhan</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-value">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
                <div class="stat-label">Total Terbayar</div>
                <div class="stat-desc">Sudah divalidasi</div>
            </div>
        </div>
    </div>

    <div class="late-card">
        <div class="late-card-header">
            <div>
                <h5 class="late-card-title"><i class="fas fa-list-ul" style="color: var(--late-primary);"></i> Daftar Rincian Tunggakan</h5>
                <div class="late-card-subtitle">Daftar siswa yang masih memiliki sisa tagihan pada tahun ajaran aktif.</div>
            </div>
            <span class="badge bg-danger px-3 py-2">{{ $siswaList->count() }} data ditemukan</span>
        </div>
        <div class="table-responsive">
            @if($siswaList->count() > 0)
                <table class="table table-clean align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">NISN</th>
                            <th>Kelas</th>
                            <th>Total Tagihan</th>
                            <th>Sudah Bayar</th>
                            <th>Sisa Tagihan</th>
                            <th width="150">Persentase</th>
                            <th class="text-center" width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaList as $index => $siswa)
                            @php
                                $persentase = $siswa->total_tagihan > 0 ? round(($siswa->total_bayar / $siswa->total_tagihan) * 100, 1) : 0;
                                $progColor = $persentase >= 75 ? '#10b981' : ($persentase >= 50 ? '#f59e0b' : '#ef4444');
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-muted" data-label="No">{{ $index + 1 }}</td>
                                <td class="mobile-card-head" data-label="Siswa">
                                    <div class="student-info">
                                        <div class="student-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
                                        <div style="min-width: 0;">
                                            <div class="student-name">{{ $siswa->nama_lengkap }}</div>
                                            <div class="student-meta">{{ $siswa->cabang->nama_cabang ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center" data-label="NISN">{{ $siswa->nisn }}</td>
                                <td data-label="Kelas">
                                    <span class="badge bg-label-primary px-2 py-1">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                </td>
                                <td class="currency-font" data-label="Total Tagihan">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td>
                                <td class="currency-font text-success" data-label="Sudah Bayar">Rp {{ number_format($siswa->total_bayar, 0, ',', '.') }}</td>
                                <td class="currency-font text-danger" data-label="Sisa Tagihan">Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}</td>
                                <td data-label="Persentase">
                                    <div class="progress-wrap">
                                        <div class="progress-track">
                                            <div class="progress-fill" style="width: {{ $persentase }}%; background: {{ $progColor }};"></div>
                                        </div>
                                        <span class="small fw-bold" style="color: {{ $progColor }};">{{ $persentase }}%</span>
                                    </div>
                                </td>
                                <td class="mobile-card-actions" data-label="Aksi">
                                    <div class="action-btns">
                                        <a href="{{ route('bendahara.tagihan.show', $siswa->id) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('bendahara.pembayaran.riwayat-siswa', $siswa->id) }}" class="btn btn-sm btn-success" title="Riwayat">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <a href="{{ route('bendahara.pembayaran.create', $siswa->id) }}" class="btn btn-sm btn-warning text-dark" title="Input Bayar">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="fas fa-check-double"></i>
                    <h6 class="mb-1">Semua tagihan telah lunas</h6>
                    <p class="small mb-0">Tidak ada siswa dengan sisa tagihan pada filter saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
