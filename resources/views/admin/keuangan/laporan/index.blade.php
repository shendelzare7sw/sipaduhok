@extends('layouts.sneat')

@section('title', 'Laporan Pembayaran')
@section('page-title', 'Laporan Pembayaran Bulanan')
@section('page-subtitle', 'Rekap pembayaran periode ' . $bulanList[$bulan] . ' ' . $tahun)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* === STYLE STAT CARD VIBRANT === */
.stat-card {
    padding: 24px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
    border: none;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-title {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.stat-number {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 4px;
    line-height: 1.2;
}

.stat-desc {
    font-size: 13px;
    opacity: 0.8;
}

.stat-icon-bg {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 60px;
    opacity: 0.15;
    z-index: 1;
}

/* Gradients */
.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

/* Table & UI */
.table thead th {
    background: #f8f9fc;
    color: #4e73df;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e3e6f0;
}

.chart-bar {
    background: linear-gradient(to top, #4e73df, #224abe);
    border-radius: 4px 4px 0 0;
    transition: height 0.5s ease;
    min-height: 2px;
}

.currency-font { font-family: 'Nunito', sans-serif; font-weight: 700; }

/* Filter Section */
.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 140px;
}

.filter-group label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    margin-bottom: 4px;
}

.filter-divider {
    width: 100%;
    border-top: 1px dashed #e5e7eb;
    margin: 8px 0;
}

.filter-cascade-hidden {
    display: none;
}

.active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}

.active-filter-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .stat-card {
        padding: 16px;
    }

    .stat-number {
        font-size: 16px;
    }

    .stat-title {
        font-size: 11px;
    }

    .stat-icon-bg {
        font-size: 40px;
        right: 12px;
    }

    .filter-group {
        min-width: 100%;
    }

    .filter-row {
        gap: 8px;
    }

    .d-flex.align-items-end.justify-content-between {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .chart-bar {
        min-width: 8px;
    }

    .table thead th {
        font-size: 10px;
        padding: 8px 6px;
    }

    .table td {
        font-size: 12px;
        padding: 8px 6px;
    }

    .currency-font {
        font-size: 12px;
    }

    .card-header.d-flex {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 8px;
    }
}
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- FILTER SECTION --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-sliders-h me-2"></i>Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.keuangan.laporan.index') }}" method="GET" id="filterForm">
                {{-- Row 1: Periode & Metode --}}
                <div class="filter-row">
                    <div class="filter-group" style="max-width: 160px;">
                        <label><i class="fas fa-calendar-alt me-1"></i>Tahun</label>
                        <select name="tahun" class="form-select form-select-sm border-start border-primary border-3 shadow-sm">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group" style="max-width: 180px;">
                        <label><i class="fas fa-calendar me-1"></i>Bulan</label>
                        <select name="bulan" class="form-select form-select-sm border-start border-primary border-3 shadow-sm">
                            @foreach($bulanList as $key => $nama)
                                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group" style="max-width: 180px;">
                        <label><i class="fas fa-credit-card me-1"></i>Metode</label>
                        <select name="metode" class="form-select form-select-sm border-start border-primary border-3 shadow-sm">
                            <option value="">Semua Metode</option>
                            <option value="tunai" {{ request('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="midtrans" {{ request('metode') == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                        </select>
                    </div>
                </div>

                <div class="filter-divider"></div>

                {{-- Row 2: Lokasi Cascading --}}
                <div class="filter-row">
                    <div class="filter-group" style="max-width: 200px;">
                        <label><i class="fas fa-building me-1"></i>Cabang</label>
                        <select name="cabang_id" id="cabangFilter" class="form-select form-select-sm border-start border-success border-3 shadow-sm">
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
                        <select name="jenjang" id="jenjangFilter" class="form-select form-select-sm border-start border-success border-3 shadow-sm">
                            <option value="">Semua Jenjang</option>
                            @foreach($jenjangList as $j)
                                <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group {{ !request('jenjang') ? 'filter-cascade-hidden' : '' }}" id="kelasFilterContainer" style="max-width: 200px;">
                        <label><i class="fas fa-door-open me-1"></i>Kelas</label>
                        <select name="kelas_id" id="kelasFilter" class="form-select form-select-sm border-start border-success border-3 shadow-sm">
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

                {{-- Buttons --}}
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm fw-bold">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('admin.keuangan.laporan.index') }}" class="btn btn-outline-secondary btn-sm px-3 fw-bold">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                    <a href="{{ route('admin.keuangan.laporan.cetak', request()->query()) }}" target="_blank" class="btn btn-success btn-sm px-3 shadow-sm fw-bold ms-auto">
                        <i class="fas fa-print me-1"></i> Cetak
                    </a>
                </div>

                {{-- Active Filter Badges --}}
                @if(request('cabang_id') || request('jenjang') || request('kelas_id') || request('metode'))
                <div class="active-filters">
                    <span style="font-size: 11px; color: #6b7280; font-weight: 600; line-height: 24px;">Filter aktif:</span>
                    @if(request('metode'))
                        <span class="active-filter-badge"><i class="fas fa-credit-card"></i> {{ ucfirst(request('metode')) }}</span>
                    @endif
                    @if(request('cabang_id'))
                        @php $cabangNama = $cabangList->firstWhere('id', request('cabang_id'))?->nama_cabang; @endphp
                        @if($cabangNama)
                            <span class="active-filter-badge" style="background: #f0fdf4; color: #16a34a; border-color: #86efac;">
                                <i class="fas fa-building"></i> {{ $cabangNama }}
                            </span>
                        @endif
                    @endif
                    @if(request('jenjang'))
                        <span class="active-filter-badge" style="background: #fefce8; color: #ca8a04; border-color: #fde68a;">
                            <i class="fas fa-layer-group"></i> {{ request('jenjang') }}
                        </span>
                    @endif
                    @if(request('kelas_id'))
                        @php $kelasNama = $kelasList->firstWhere('id', request('kelas_id'))?->nama_kelas; @endphp
                        @if($kelasNama)
                            <span class="active-filter-badge" style="background: #faf5ff; color: #7c3aed; border-color: #c4b5fd;">
                                <i class="fas fa-door-open"></i> {{ $kelasNama }}
                            </span>
                        @endif
                    @endif
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Total Pembayaran</div>
                    <div class="stat-number">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</div>
                    <div class="stat-desc">{{ $bulanList[$bulan] }} {{ $tahun }}</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-wallet"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Jumlah Transaksi</div>
                    <div class="stat-number">{{ $jumlahTransaksi }}</div>
                    <div class="stat-desc">Transaksi Disetujui</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-exchange-alt"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">Total Tunai</div>
                    <div class="stat-number">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
                    <div class="stat-desc">{{ $jumlahTunai }} Transaksi Kasir</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Transfer & Digital</div>
                    <div class="stat-number">Rp {{ number_format($totalNonTunai, 0, ',', '.') }}</div>
                    <div class="stat-desc">{{ $jumlahNonTunai }} Transaksi Online</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-university"></i></div>
            </div>
        </div>
    </div>

    {{-- CHART VISUAL --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-bar me-2 text-info"></i>Grafik Arus Kas Harian</h6>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-end justify-content-between" style="height: 180px; padding: 10px 0;">
                @foreach($pembayaranPerHari as $hari => $jumlah)
                    @php
                        $maxJumlah = $pembayaranPerHari->max() ?: 1;
                        $height = ($jumlah / $maxJumlah) * 100;
                    @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; padding: 0 2px; height: 100%;">
                        <div class="chart-bar w-100 shadow-sm" style="height: {{ $height }}%;" 
                             title="Tanggal {{ $hari }}: Rp {{ number_format($jumlah, 0, ',', '.') }}"></div>
                        <span style="font-size: 9px; font-weight: 700; color: #4e73df; margin-top: 5px;">{{ $hari }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- DETAIL TABLE --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-receipt me-2"></i>Rincian Transaksi Pembayaran</h6>
            <span class="badge bg-info px-3 py-2 fw-bold shadow-sm">{{ $pembayaran->total() }} TRANSAKSI</span>
        </div>
        <div class="card-body p-0">
            @if($pembayaran->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jenis Tagihan</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Validator</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayaran as $index => $bayar)
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $pembayaran->firstItem() + $index }}</td>
                                    <td class="align-middle small fw-bold">{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                                    <td class="align-middle"><code class="fw-bold text-primary">{{ $bayar->kode_pembayaran }}</code></td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $bayar->siswa->nama_lengkap ?? '-' }}</div>
                                        <small class="text-muted fw-bold">{{ $bayar->siswa->nisn ?? '-' }}</small>
                                    </td>
                                    <td class="align-middle text-uppercase small fw-bold">{{ $bayar->siswa->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="align-middle small fw-bold text-muted">
                                        @if($bayar->tagihan)
                                            {{ $jenisTagihan[$bayar->tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="align-middle currency-font text-dark">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td class="align-middle">
                                        @if($bayar->metode_pembayaran === 'tunai')
                                            <span class="badge bg-primary px-2 py-1 shadow-sm fw-bold">TUNAI</span>
                                        @elseif($bayar->metode_pembayaran === 'transfer')
                                            <span class="badge bg-warning px-2 py-1 shadow-sm fw-bold text-white">TRANSFER</span>
                                        @else
                                            <span class="badge bg-success px-2 py-1 shadow-sm fw-bold">MIDTRANS</span>
                                        @endif
                                    </td>
                                    <td class="align-middle small fw-bold text-gray-700">
                                        <i class="fas fa-user-check me-1 text-success"></i>{{ $bayar->validator->name ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light fw-bold text-dark border-top">
                            <tr>
                                <td colspan="6" class="text-end py-3 pe-4">TOTAL HALAMAN INI:</td>
                                <td class="currency-font text-primary">Rp {{ number_format($pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="px-4 py-3 bg-light border-top">
                    {{ $pembayaran->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 text-gray-200"></i>
                    <p>Tidak ada transaksi yang ditemukan pada periode ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- METHOD SUMMARY GRID --}}
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-start border-primary border-4 shadow h-100 py-2 bg-light">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Kasir (Tunai)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1">{{ $jumlahTunai }} Transaksi</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-money-bill fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-start border-warning border-4 shadow h-100 py-2 bg-light">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Manual Transfer</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1">{{ $jumlahTransfer }} Transaksi</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-university fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2 bg-light">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Payment Gateway</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rp {{ number_format($totalMidtrans, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1">{{ $jumlahMidtrans }} Transaksi</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-credit-card fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Cascading Filter JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cabangSelect = document.getElementById('cabangFilter');
    const jenjangSelect = document.getElementById('jenjangFilter');
    const jenjangContainer = document.getElementById('jenjangFilterContainer');
    const kelasSelect = document.getElementById('kelasFilter');
    const kelasContainer = document.getElementById('kelasFilterContainer');

    // Store original data
    const allJenjangData = Array.from(jenjangSelect.options).map(o => ({
        value: o.value, text: o.text
    }));
    const allKelasData = Array.from(kelasSelect.options).map(o => ({
        value: o.value,
        text: o.text,
        cabang: o.getAttribute('data-cabang'),
        jenjang: o.getAttribute('data-jenjang')
    }));

    function rebuildSelect(selectEl, options) {
        const current = selectEl.value;
        selectEl.innerHTML = '';
        options.forEach(function(opt) {
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

        // 1. Jenjang: filter berdasarkan cabang terpilih
        if (selectedCabangId) {
            jenjangContainer.classList.remove('filter-cascade-hidden');

            const availableJenjangs = new Set();
            allKelasData.forEach(function(opt) {
                if (opt.value !== '' && opt.cabang == selectedCabangId) {
                    availableJenjangs.add(opt.jenjang);
                }
            });

            const filteredJenjang = allJenjangData.filter(function(opt) {
                return opt.value === '' || availableJenjangs.has(opt.value);
            });
            rebuildSelect(jenjangSelect, filteredJenjang);

            if (availableJenjangs.has(selectedJenjang)) {
                jenjangSelect.value = selectedJenjang;
            }
        } else {
            jenjangContainer.classList.add('filter-cascade-hidden');
            jenjangSelect.value = '';
        }

        // 2. Kelas: filter berdasarkan cabang + jenjang terpilih
        const currentJenjang = jenjangSelect.value;
        if (currentJenjang) {
            kelasContainer.classList.remove('filter-cascade-hidden');

            const filteredKelas = allKelasData.filter(function(opt) {
                return opt.value === '' ||
                    (opt.cabang == selectedCabangId && opt.jenjang == currentJenjang);
            });
            rebuildSelect(kelasSelect, filteredKelas);
        } else {
            kelasContainer.classList.add('filter-cascade-hidden');
            kelasSelect.value = '';
        }
    }

    cabangSelect.addEventListener('change', function() {
        jenjangSelect.value = '';
        kelasSelect.value = '';
        updateFilters();
    });

    jenjangSelect.addEventListener('change', function() {
        kelasSelect.value = '';
        updateFilters();
    });

    // Initialize on load (for when filters are pre-selected via URL params)
    updateFilters();
});
</script>
@endsection