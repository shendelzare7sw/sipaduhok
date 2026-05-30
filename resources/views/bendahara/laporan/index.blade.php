@extends('layouts.sneat')

@section('title', 'Laporan Pembayaran')
@section('page-title', 'Laporan Pembayaran Bulanan')
@section('page-subtitle', 'Rekap pembayaran periode ' . $bulanList[$bulan] . ' ' . $tahun)

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* ── Reuse dashboard card pattern ─────────────────── */
    .dashboard-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .dashboard-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.04); }

    .card-header-clean {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .card-title-clean {
        font-size: 1rem; font-weight: 600;
        color: var(--text-main); margin: 0;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .card-title-icon { color: var(--primary-color); }

    /* ── Stat Widget ──────────────────────────────────── */
    .stat-widget {
        padding: 1.4rem 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
    }
    .stat-icon-wrapper {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .stat-details { flex-grow: 1; }
    .stat-value {
        font-size: 1.5rem; font-weight: 700;
        color: var(--text-main); line-height: 1.2; margin-bottom: 0.2rem;
        word-break: break-word;
    }
    .stat-label {
        font-size: 0.78rem; font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .stat-footer {
        padding: 0.65rem 1.5rem 0.9rem;
        border-top: 1px dashed var(--border-color);
        font-size: 0.78rem;
        color: var(--text-muted);
        display: flex; justify-content: space-between; align-items: center;
    }

    /* ── Filter ───────────────────────────────────────── */
    .filter-row { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; }
    .filter-group { flex: 1; min-width: 140px; }
    .filter-group label {
        display: block; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px;
        color: var(--text-muted); margin-bottom: 4px;
    }
    .filter-divider { width: 100%; border-top: 1px dashed var(--border-color); margin: 8px 0; }
    .filter-cascade-hidden { display: none; }
    .active-filters { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .active-filter-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
        background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;
    }

    /* ── Chart ────────────────────────────────────────── */
    .chart-bar {
        background: var(--primary-color, #4361ee);
        border-radius: 4px 4px 0 0;
        transition: height 0.5s ease;
        min-height: 2px;
        opacity: 0.75;
    }
    .chart-bar:hover { opacity: 1; }

    /* ── Table ────────────────────────────────────────── */
    .table-clean { margin: 0; }
    .table-clean th {
        background: var(--background-color, #f8fafc);
        border-bottom: 1px solid var(--border-color);
        border-top: none;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.85rem 1.25rem;
    }
    .table-clean td {
        padding: 0.85rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        border-top: none;
        color: var(--text-main);
        font-size: 0.875rem;
    }
    .table-clean tbody tr:hover { background-color: var(--background-color, #f8fafc); }
    .table-clean tbody tr:last-child td { border-bottom: none; }
    .table-clean tfoot td {
        background: var(--background-color, #f8fafc);
        border-top: 2px solid var(--border-color);
        border-bottom: none;
        font-weight: 700;
        padding: 0.85rem 1.25rem;
    }

    /* ── Pagination ───────────────────────────────────── */
    .pagination-area {
        padding: 0.85rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex; justify-content: space-between;
        align-items: center; flex-wrap: wrap; gap: 0.5rem;
    }

    /* ── Responsive ───────────────────────────────────── */
    @media (max-width: 992px) {
        .stat-value { font-size: 1.2rem; }
    }
    @media (max-width: 768px) {
        .stat-value { font-size: 1.1rem; }
        .stat-widget { padding: 1.1rem; gap: 0.85rem; }
        .stat-icon-wrapper { width: 40px; height: 40px; font-size: 1rem; }
        .card-header-clean { flex-direction: column; align-items: stretch; }
        .filter-group { min-width: 100%; }

        .table-clean thead { display: none; }
        .table-clean tbody tr { display: flex; flex-direction: column; border-bottom: 2px solid var(--border-color); }
        .table-clean tbody td {
            display: flex; justify-content: space-between; align-items: center;
            border: none; border-bottom: 1px solid var(--border-color); gap: 0.75rem;
        }
        .table-clean tbody td::before {
            content: attr(data-label);
            font-weight: 600; font-size: 0.7rem; color: var(--text-muted);
            text-transform: uppercase; flex-shrink: 0;
        }
        .table-clean tbody td:last-child { border-bottom: none; }
        .pagination-area { flex-direction: column; align-items: center; }
    }
</style>
@endsection

@section('content')

    {{-- FILTER ─────────────────────────────────────────── --}}
    <div class="dashboard-card mb-4">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="fas fa-sliders-h card-title-icon"></i> Filter Laporan
            </h5>
        </div>
        <div class="p-4">
            <form action="{{ route('bendahara.laporan.index') }}" method="GET" id="filterForm">
                <div class="filter-row">
                    <div class="filter-group" style="max-width: 160px;">
                        <label><i class="fas fa-calendar-alt me-1"></i>Tahun</label>
                        <select name="tahun" class="form-select form-select-sm border-start border-primary border-3">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group" style="max-width: 180px;">
                        <label><i class="fas fa-calendar me-1"></i>Bulan</label>
                        <select name="bulan" class="form-select form-select-sm border-start border-primary border-3">
                            @foreach($bulanList as $key => $nama)
                                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group" style="max-width: 180px;">
                        <label><i class="fas fa-credit-card me-1"></i>Metode</label>
                        <select name="metode" class="form-select form-select-sm border-start border-primary border-3">
                            <option value="">Semua Metode</option>
                            <option value="tunai"    {{ request('metode') == 'tunai'    ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="midtrans" {{ request('metode') == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                        </select>
                    </div>
                </div>

                <div class="filter-divider"></div>

                <div class="filter-row">
                    <div class="filter-group" style="max-width: 200px;">
                        <label><i class="fas fa-building me-1"></i>Cabang</label>
                        <select name="cabang_id" id="cabangFilter" class="form-select form-select-sm border-start border-success border-3">
                            <option value="">Semua Cabang</option>
                            @foreach($cabangList as $cabang)
                                <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group {{ !request('cabang_id') ? 'filter-cascade-hidden' : '' }}" id="jenjangFilterContainer" style="max-width: 180px;">
                        <label><i class="fas fa-layer-group me-1"></i>Jenjang</label>
                        <select name="jenjang" id="jenjangFilter" class="form-select form-select-sm border-start border-success border-3">
                            <option value="">Semua Jenjang</option>
                            @foreach($jenjangList as $j)
                                <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group {{ !request('jenjang') ? 'filter-cascade-hidden' : '' }}" id="kelasFilterContainer" style="max-width: 200px;">
                        <label><i class="fas fa-door-open me-1"></i>Kelas</label>
                        <select name="kelas_id" id="kelasFilter" class="form-select form-select-sm border-start border-success border-3">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}"
                                        data-cabang="{{ $kelas->cabang_id }}"
                                        data-jenjang="{{ $kelas->jenjang }}"
                                        {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3 flex-wrap align-items-center">
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('bendahara.laporan.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                    <a href="{{ route('bendahara.laporan.cetak', request()->query()) }}" target="_blank"
                        class="btn btn-outline-success btn-sm px-3 ms-auto d-flex align-items-center gap-1">
                        <i class="fas fa-print"></i> Cetak
                    </a>
                </div>

                @if(request('cabang_id') || request('jenjang') || request('kelas_id') || request('metode'))
                <div class="active-filters">
                    <span style="font-size: 11px; color: var(--text-muted); font-weight: 600; line-height: 24px;">Filter aktif:</span>
                    @if(request('metode'))
                        <span class="active-filter-badge"><i class="fas fa-credit-card"></i> {{ ucfirst(request('metode')) }}</span>
                    @endif
                    @if(request('cabang_id'))
                        @php $cabangNama = $cabangList->firstWhere('id', request('cabang_id'))?->nama_cabang; @endphp
                        @if($cabangNama)
                            <span class="active-filter-badge" style="background:#f0fdf4;color:#16a34a;border-color:#86efac;">
                                <i class="fas fa-building"></i> {{ $cabangNama }}
                            </span>
                        @endif
                    @endif
                    @if(request('jenjang'))
                        <span class="active-filter-badge" style="background:#fefce8;color:#ca8a04;border-color:#fde68a;">
                            <i class="fas fa-layer-group"></i> {{ request('jenjang') }}
                        </span>
                    @endif
                    @if(request('kelas_id'))
                        @php $kelasNama = $kelasList->firstWhere('id', request('kelas_id'))?->nama_kelas; @endphp
                        @if($kelasNama)
                            <span class="active-filter-badge" style="background:#faf5ff;color:#7c3aed;border-color:#c4b5fd;">
                                <i class="fas fa-door-open"></i> {{ $kelasNama }}
                            </span>
                        @endif
                    @endif
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- STAT WIDGETS ─ white cards, colored icons ──────── --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Pembayaran</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #10b981; background: #ecfdf5;">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>{{ $bulanList[$bulan] }} {{ $tahun }}</span>
                    <i class="fas fa-chart-line opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $jumlahTransaksi }}</div>
                        <div class="stat-label">Jumlah Transaksi</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #3b82f6; background: #eff6ff;">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Transaksi disetujui</span>
                    <i class="fas fa-check opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Tunai</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #8b5cf6; background: #f5f3ff;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>{{ $jumlahTunai }} Transaksi Kasir</span>
                    <i class="fas fa-cash-register opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">Rp {{ number_format($totalNonTunai, 0, ',', '.') }}</div>
                        <div class="stat-label">Transfer & Digital</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #f59e0b; background: #fffbeb;">
                        <i class="fas fa-university"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>{{ $jumlahNonTunai }} Transaksi Daring</span>
                    <i class="fas fa-globe opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART ──────────────────────────────────────────── --}}
    <div class="dashboard-card mb-4">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="fas fa-chart-bar card-title-icon"></i> Grafik Arus Kas Harian
            </h5>
        </div>
        <div class="p-4">
            <div class="d-flex align-items-end justify-content-between" style="height: 160px; overflow-x: auto;">
                @foreach($pembayaranPerHari as $hari => $jumlah)
                    @php
                        $maxJumlah = $pembayaranPerHari->max() ?: 1;
                        $height = ($jumlah / $maxJumlah) * 100;
                    @endphp
                    <div style="flex:1; min-width:14px; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; padding:0 2px; height:100%;">
                        <div class="chart-bar w-100" style="height:{{ $height }}%;"
                             title="Tgl {{ $hari }}: Rp {{ number_format($jumlah, 0, ',', '.') }}"></div>
                        <span style="font-size:9px; font-weight:700; color:var(--text-muted); margin-top:5px;">{{ $hari }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- DETAIL TABLE ─────────────────────────────────── --}}
    <div class="dashboard-card mb-4">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="fas fa-receipt card-title-icon"></i> Rincian Transaksi Pembayaran
            </h5>
            <span class="badge bg-label-primary px-3 py-2 fw-bold" style="font-size: 0.75rem; border-radius: 20px;">
                {{ $pembayaran->total() }} Transaksi
            </span>
        </div>

        @if($pembayaran->count() > 0)
            <div class="table-responsive">
                <table class="table table-clean">
                    <thead>
                        <tr>
                            <th width="45" class="text-center">No</th>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Tagihan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Metode</th>
                            <th>Validator</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayaran as $index => $bayar)
                        <tr>
                            <td data-label="No" class="text-center fw-bold" style="color:var(--text-muted);">{{ $pembayaran->firstItem() + $index }}</td>
                            <td data-label="Tanggal" class="small fw-semibold">{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                            <td data-label="Kode"><code class="fw-bold text-primary small">{{ $bayar->kode_pembayaran }}</code></td>
                            <td data-label="Siswa">
                                <div class="fw-semibold">{{ $bayar->siswa->nama_lengkap ?? '-' }}</div>
                                <small style="color:var(--text-muted);">{{ $bayar->siswa->nisn ?? '-' }}</small>
                            </td>
                            <td data-label="Kelas" class="text-uppercase small fw-semibold">{{ $bayar->siswa->kelas->nama_kelas ?? '-' }}</td>
                            <td data-label="Jenis Tagihan" class="small fw-semibold" style="color:var(--text-muted);">
                                @if($bayar->tagihan)
                                    {{ $jenisTagihan[$bayar->tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan)) }}
                                @else -
                                @endif
                            </td>
                            <td data-label="Jumlah" class="text-end fw-bold">
                                Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td data-label="Metode" class="text-center">
                                @if($bayar->metode_pembayaran === 'tunai')
                                    <span class="badge bg-primary fw-bold" style="border-radius:20px;font-size:0.7rem;padding:0.3em 0.75em;">TUNAI</span>
                                @elseif($bayar->metode_pembayaran === 'transfer')
                                    <span class="badge bg-warning text-white fw-bold" style="border-radius:20px;font-size:0.7rem;padding:0.3em 0.75em;">TRANSFER</span>
                                @else
                                    <span class="badge bg-success fw-bold" style="border-radius:20px;font-size:0.7rem;padding:0.3em 0.75em;">MIDTRANS</span>
                                @endif
                            </td>
                            <td data-label="Validator" class="small fw-semibold">
                                <i class="fas fa-user-check me-1 text-success"></i>{{ $bayar->validator->name ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end" style="color:var(--text-muted);">Total Halaman Ini:</td>
                            <td class="text-end text-primary">Rp {{ number_format($pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="pagination-area">
                <span class="small" style="color:var(--text-muted);">
                    Menampilkan {{ $pembayaran->firstItem() ?? 0 }}–{{ $pembayaran->lastItem() ?? 0 }}
                    dari {{ $pembayaran->total() }} transaksi
                </span>
                <div>{{ $pembayaran->withQueryString()->links() }}</div>
            </div>
        @else
            <div class="text-center py-5" style="color:var(--text-muted);">
                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                <p class="mb-0">Tidak ada transaksi pada periode ini.</p>
            </div>
        @endif
    </div>

    {{-- METHOD SUMMARY ─ white stat widgets ─────────── --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper" style="color: #3b82f6; background: #eff6ff;">
                        <i class="fas fa-money-bill"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-label">Kasir (Tunai)</div>
                        <div class="stat-value" style="font-size:1.1rem;">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
                        <small style="color:var(--text-muted);">{{ $jumlahTunai }} Transaksi</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper" style="color: #d97706; background: #fefce8;">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-label">Manual Transfer</div>
                        <div class="stat-value" style="font-size:1.1rem;">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</div>
                        <small style="color:var(--text-muted);">{{ $jumlahTransfer }} Transaksi</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper" style="color: #16a34a; background: #f0fdf4;">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-label">Payment Gateway</div>
                        <div class="stat-value" style="font-size:1.1rem;">Rp {{ number_format($totalMidtrans, 0, ',', '.') }}</div>
                        <small style="color:var(--text-muted);">{{ $jumlahMidtrans }} Transaksi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cabangSelect = document.getElementById('cabangFilter');
    const jenjangSelect = document.getElementById('jenjangFilter');
    const jenjangContainer = document.getElementById('jenjangFilterContainer');
    const kelasSelect = document.getElementById('kelasFilter');
    const kelasContainer = document.getElementById('kelasFilterContainer');

    const allJenjangData = Array.from(jenjangSelect.options).map(o => ({ value: o.value, text: o.text }));
    const allKelasData = Array.from(kelasSelect.options).map(o => ({
        value: o.value, text: o.text,
        cabang: o.getAttribute('data-cabang'), jenjang: o.getAttribute('data-jenjang')
    }));

    function rebuildSelect(selectEl, options) {
        const current = selectEl.value;
        selectEl.innerHTML = '';
        options.forEach(function (opt) {
            const el = document.createElement('option');
            el.value = opt.value;
            el.textContent = opt.text;
            if (opt.cabang) el.setAttribute('data-cabang', opt.cabang);
            if (opt.jenjang) el.setAttribute('data-jenjang', opt.jenjang);
            if (opt.value && opt.value === current) el.selected = true;
            selectEl.appendChild(el);
        });
    }

    function updateFilters() {
        const selectedCabangId = cabangSelect.value;
        const selectedJenjang = jenjangSelect.value;

        if (selectedCabangId) {
            jenjangContainer.classList.remove('filter-cascade-hidden');
            const availableJenjangs = new Set();
            allKelasData.forEach(opt => {
                if (opt.value !== '' && opt.cabang == selectedCabangId) availableJenjangs.add(opt.jenjang);
            });
            const filteredJenjang = allJenjangData.filter(opt => opt.value === '' || availableJenjangs.has(opt.value));
            rebuildSelect(jenjangSelect, filteredJenjang);
            if (availableJenjangs.has(selectedJenjang)) jenjangSelect.value = selectedJenjang;
        } else {
            jenjangContainer.classList.add('filter-cascade-hidden');
            jenjangSelect.value = '';
        }

        const currentJenjang = jenjangSelect.value;
        if (currentJenjang) {
            kelasContainer.classList.remove('filter-cascade-hidden');
            const filteredKelas = allKelasData.filter(opt =>
                opt.value === '' || (opt.cabang == selectedCabangId && opt.jenjang == currentJenjang)
            );
            rebuildSelect(kelasSelect, filteredKelas);
        } else {
            kelasContainer.classList.add('filter-cascade-hidden');
            kelasSelect.value = '';
        }
    }

    cabangSelect.addEventListener('change', function () { jenjangSelect.value = ''; kelasSelect.value = ''; updateFilters(); });
    jenjangSelect.addEventListener('change', function () { kelasSelect.value = ''; updateFilters(); });
    updateFilters();
});
</script>
@endsection
