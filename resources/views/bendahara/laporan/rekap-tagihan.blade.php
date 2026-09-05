@extends('layouts.app')

@section('title', 'Rekap Tagihan per Kelas')
@section('page-title', 'Rekap Tagihan per Kelas')
@section('page-subtitle', 'Rekap total tagihan dan pembayaran setiap kelas')


@section('styles')
<style>
    :root {
        --rekap-primary: #4361ee;
        --rekap-success: #10b981;
        --rekap-warning: #f59e0b;
        --rekap-danger: #ef4444;
        --rekap-info: #06b6d4;
        --rekap-purple: #8b5cf6;
        --rekap-surface: #ffffff;
        --rekap-bg: #f8fafc;
        --rekap-border: #e2e8f0;
        --rekap-text: #1e293b;
        --rekap-muted: #64748b;
        --rekap-radius: 12px;
    }

    .rekap-shell {
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
    .rekap-card,
    .level-card {
        background: var(--rekap-surface);
        border: 1px solid var(--rekap-border);
        border-radius: var(--rekap-radius);
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
        color: var(--rekap-text);
        line-height: 1.25;
        word-break: break-word;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--rekap-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .stat-desc {
        font-size: 0.75rem;
        color: var(--rekap-muted);
        margin-top: 0.15rem;
    }

    .year-banner {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: var(--rekap-radius);
        padding: 1.15rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .year-title {
        color: var(--rekap-text);
        font-weight: 700;
        margin: 0;
    }

    .year-subtitle {
        color: var(--rekap-muted);
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .rekap-card {
        overflow: hidden;
    }

    .rekap-card-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--rekap-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .rekap-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--rekap-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .rekap-card-subtitle {
        color: var(--rekap-muted);
        font-size: 0.82rem;
        margin-top: 0.25rem;
    }

    .table-clean {
        margin: 0;
    }

    .table-clean th {
        background: var(--rekap-bg);
        border-bottom: 1px solid var(--rekap-border);
        color: var(--rekap-muted);
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
        border-bottom: 1px solid var(--rekap-border);
        color: var(--rekap-text);
        font-size: 0.88rem;
    }

    .table-clean tbody tr:hover {
        background: #f8fafc;
    }

    .table-clean tbody tr:last-child td {
        border-bottom: none;
    }

    .class-info {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .class-name {
        color: var(--rekap-text);
        font-weight: 700;
    }

    .class-code {
        color: var(--rekap-muted);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .currency-font {
        font-weight: 700;
        white-space: nowrap;
    }

    .progress-wrap {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        min-width: 150px;
    }

    .progress-track {
        width: 100%;
        height: 8px;
        background: var(--rekap-border);
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 999px;
    }

    .level-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        padding: 1.25rem;
    }

    .level-card {
        padding: 1.1rem;
    }

    .level-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .level-name {
        color: var(--rekap-text);
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
    }

    .level-students {
        display: flex;
        align-items: baseline;
        gap: 0.35rem;
        margin-bottom: 1rem;
    }

    .level-students strong {
        color: var(--rekap-text);
        font-size: 1.6rem;
        line-height: 1;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        font-size: 0.82rem;
        margin-bottom: 0.45rem;
    }

    .empty-state {
        min-height: 240px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--rekap-muted);
        padding: 2rem;
    }

    .empty-state i {
        color: #cbd5e1;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 1200px) {
        .stat-row { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .level-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 768px) {
        .stat-row,
        .level-grid { grid-template-columns: 1fr; }
        .year-banner { align-items: stretch; }
        .year-banner .btn { width: 100%; justify-content: center; }
        .rekap-card-header { align-items: stretch; }
        .rekap-card-header > div { width: 100%; }
        .table-responsive { overflow-x: visible; }
        .table-clean thead { display: none; }
        .table-clean tfoot { display: none; }
        .table-clean tbody tr {
            display: flex;
            flex-direction: column;
            border-bottom: 2px solid var(--rekap-border);
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
            color: var(--rekap-muted);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            flex-shrink: 0;
        }
        .table-clean tbody td.mobile-card-head {
            background: var(--rekap-bg);
            align-items: flex-start;
            text-align: left;
            order: -1;
        }
        .table-clean tbody td.mobile-card-head::before {
            display: none;
        }
        .progress-wrap { min-width: 0; width: 55%; }
    }
</style>
@endsection

@section('content')
@php
    $lunasPercent = $grandTotal['tagihan'] > 0 ? round(($grandTotal['bayar'] / $grandTotal['tagihan']) * 100, 1) : 0;
    $perJenjang = $kelasList->groupBy('jenjang')->map(function ($items) {
        return [
            'jumlah_kelas' => $items->count(),
            'jumlah_siswa' => $items->sum('total_siswa'),
            'total_tagihan' => $items->sum('total_tagihan'),
            'total_bayar' => $items->sum('total_bayar'),
            'sisa_tagihan' => $items->sum('sisa_tagihan'),
        ];
    });
@endphp

<div class="rekap-shell">
    <div class="year-banner">
        <div>
            <h5 class="year-title">Tahun Ajaran: {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</h5>
            <div class="year-subtitle">
                Periode:
                {{ $tahunAjaran ? $tahunAjaran->tanggal_mulai->format('d/m/Y') . ' - ' . $tahunAjaran->tanggal_selesai->format('d/m/Y') : '-' }}
            </div>
        </div>
        <a href="{{ route('bendahara.laporan.cetak-rekap-tagihan') }}" class="btn btn-primary btn-sm" target="_blank">
            <i class="fas fa-print me-1"></i> Cetak Rekap
        </a>
    </div>

    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <div class="stat-value">Rp {{ number_format($grandTotal['tagihan'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Tagihan</div>
                <div class="stat-desc">Seluruh kelas</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-value">Rp {{ number_format($grandTotal['bayar'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Terbayar</div>
                <div class="stat-desc">Dana masuk tervalidasi</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #fff1f2; color: #ef4444;">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <div class="stat-value">Rp {{ number_format($grandTotal['sisa'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Sisa</div>
                <div class="stat-desc">Belum dibayar</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <div class="stat-value">{{ $lunasPercent }}%</div>
                <div class="stat-label">Persentase Lunas</div>
                <div class="stat-desc">Akumulasi pembayaran</div>
            </div>
        </div>
    </div>

    <div class="rekap-card">
        <div class="rekap-card-header">
            <div>
                <h5 class="rekap-card-title"><i class="fas fa-list-alt" style="color: var(--rekap-primary);"></i> Rincian Pembayaran Per Kelas</h5>
                <div class="rekap-card-subtitle">Pantau total tagihan, terbayar, sisa, dan progres lunas per kelas.</div>
            </div>
            <span class="badge bg-label-primary px-3 py-2">{{ $kelasList->count() }} kelas</span>
        </div>

        <div class="table-responsive">
            <table class="table table-clean align-middle">
                <thead>
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>Kelas</th>
                        <th>Jenjang</th>
                        <th class="text-center">Siswa</th>
                        <th>Total Tagihan</th>
                        <th>Total Terbayar</th>
                        <th>Sisa Tagihan</th>
                        <th width="170">Persentase</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelasList as $index => $kelas)
                        @php
                            $barColor = $kelas->persentase >= 75 ? '#10b981' : ($kelas->persentase >= 50 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <tr>
                            <td class="text-center fw-bold text-muted" data-label="No">{{ $index + 1 }}</td>
                            <td class="mobile-card-head" data-label="Kelas">
                                <div class="class-info">
                                    <span class="class-name">{{ $kelas->nama_kelas }}</span>
                                    <span class="class-code">{{ $kelas->kode_kelas }}</span>
                                </div>
                            </td>
                            <td data-label="Jenjang">
                                <span class="badge bg-label-primary px-2 py-1">{{ $kelas->jenjang }}</span>
                            </td>
                            <td class="text-center fw-bold" data-label="Siswa">{{ $kelas->total_siswa }}</td>
                            <td class="currency-font" data-label="Total Tagihan">Rp {{ number_format($kelas->total_tagihan, 0, ',', '.') }}</td>
                            <td class="currency-font text-success" data-label="Total Terbayar">Rp {{ number_format($kelas->total_bayar, 0, ',', '.') }}</td>
                            <td class="currency-font {{ $kelas->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}" data-label="Sisa Tagihan">
                                Rp {{ number_format($kelas->sisa_tagihan, 0, ',', '.') }}
                            </td>
                            <td data-label="Persentase">
                                <div class="progress-wrap">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: {{ $kelas->persentase }}%; background: {{ $barColor }};"></div>
                                    </div>
                                    <span class="small fw-bold" style="color: {{ $barColor }};">{{ $kelas->persentase }}%</span>
                                </div>
                            </td>
                            <td class="text-center" data-label="Status">
                                @if($kelas->sisa_tagihan <= 0)
                                    <span class="badge bg-success px-3 py-2">Lunas</span>
                                @elseif($kelas->persentase >= 75)
                                    <span class="badge bg-warning text-dark px-3 py-2">Hampir Lunas</span>
                                @elseif($kelas->persentase >= 50)
                                    <span class="badge bg-info px-3 py-2">Dalam Proses</span>
                                @else
                                    <span class="badge bg-danger px-3 py-2">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-database"></i>
                                    <h6 class="mb-1">Data kelas tidak tersedia</h6>
                                    <p class="small mb-0">Belum ada data rekap tagihan untuk tahun ajaran aktif.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Grand Total</td>
                        <td class="text-center fw-bold">{{ $kelasList->sum('total_siswa') }}</td>
                        <td class="currency-font">Rp {{ number_format($grandTotal['tagihan'], 0, ',', '.') }}</td>
                        <td class="currency-font text-success">Rp {{ number_format($grandTotal['bayar'], 0, ',', '.') }}</td>
                        <td class="currency-font text-danger">Rp {{ number_format($grandTotal['sisa'], 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="rekap-card">
        <div class="rekap-card-header">
            <div>
                <h5 class="rekap-card-title"><i class="fas fa-chart-bar" style="color: var(--rekap-warning);"></i> Statistik Pembayaran Per Jenjang</h5>
                <div class="rekap-card-subtitle">Ringkasan jumlah kelas, siswa, tagihan, dan persentase lunas per jenjang.</div>
            </div>
        </div>
        <div class="level-grid">
            @forelse($perJenjang as $jenjang => $data)
                @php
                    $persenJenjang = $data['total_tagihan'] > 0 ? round(($data['total_bayar'] / $data['total_tagihan']) * 100, 1) : 0;
                    $jBarColor = $persenJenjang >= 75 ? '#10b981' : ($persenJenjang >= 50 ? '#f59e0b' : '#ef4444');
                @endphp
                <div class="level-card">
                    <div class="level-head">
                        <h5 class="level-name">{{ $jenjang }}</h5>
                        <span class="badge bg-primary px-3">{{ $data['jumlah_kelas'] }} kelas</span>
                    </div>
                    <div class="level-students">
                        <strong>{{ $data['jumlah_siswa'] }}</strong>
                        <span class="text-muted small">siswa aktif</span>
                    </div>
                    <div class="summary-line">
                        <span class="text-muted">Total Tagihan</span>
                        <strong>Rp {{ number_format($data['total_tagihan'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="summary-line">
                        <span class="text-muted">Dana Terbayar</span>
                        <strong class="text-success">Rp {{ number_format($data['total_bayar'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="summary-line">
                        <span class="text-muted">Sisa Tagihan</span>
                        <strong class="text-danger">Rp {{ number_format($data['sisa_tagihan'], 0, ',', '.') }}</strong>
                    </div>
                    <div class="progress-track mt-3">
                        <div class="progress-fill" style="width: {{ $persenJenjang }}%; background: {{ $jBarColor }};"></div>
                    </div>
                    <div class="text-center small fw-bold mt-2" style="color: {{ $jBarColor }};">{{ $persenJenjang }}% lunas</div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-chart-bar"></i>
                    <h6 class="mb-1">Belum ada data jenjang</h6>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
