@extends('layouts.sneat')

@section('title', 'Laporan Pembayaran')
@section('page-title', 'Laporan Pembayaran Bulanan')
@section('page-subtitle', 'Rekap pembayaran periode ' . $bulanList[$bulan] . ' ' . $tahun)

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/laporan/index.css'])
@endsection

@section('content')
    @php
        $metodeLabels = [
            'tunai' => 'Tunai',
            'transfer' => 'Direct Transfer',
            'paywuz' => 'Pembayaran Digital',
        ];
    @endphp

    <div class="dashboard-card mb-4">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="fas fa-sliders-h card-title-icon"></i> Filter Laporan
            </h5>
        </div>
        <div class="p-4">
            <form action="{{ route('bendahara.laporan.index') }}" method="GET" id="filterForm">
                <div class="filter-row">
                    <div class="filter-group filter-year">
                        <label><i class="fas fa-calendar-alt me-1"></i>Tahun</label>
                        <select name="tahun" class="form-select form-select-sm border-start border-primary border-3">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group filter-month">
                        <label><i class="fas fa-calendar me-1"></i>Bulan</label>
                        <select name="bulan" class="form-select form-select-sm border-start border-primary border-3">
                            @foreach($bulanList as $key => $nama)
                                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group filter-method">
                        <label><i class="fas fa-credit-card me-1"></i>Metode</label>
                        <select name="metode" class="form-select form-select-sm border-start border-primary border-3">
                            <option value="">Semua Metode</option>
                            <option value="tunai" {{ request('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>Direct Transfer</option>
                            <option value="paywuz" {{ request('metode') == 'paywuz' ? 'selected' : '' }}>Pembayaran Digital</option>
                        </select>
                    </div>
                </div>

                <div class="filter-divider"></div>

                <div class="filter-row">
                    <div class="filter-group filter-cabang">
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
                    <div class="filter-group filter-jenjang {{ !request('cabang_id') ? 'filter-cascade-hidden' : '' }}" id="jenjangFilterContainer">
                        <label><i class="fas fa-layer-group me-1"></i>Jenjang</label>
                        <select name="jenjang" id="jenjangFilter" class="form-select form-select-sm border-start border-success border-3">
                            <option value="">Semua Jenjang</option>
                            @foreach($jenjangList as $j)
                                <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group filter-kelas {{ !request('jenjang') ? 'filter-cascade-hidden' : '' }}" id="kelasFilterContainer">
                        <label><i class="fas fa-door-open me-1"></i>Kelas</label>
                        <select name="kelas_id" id="kelasFilter" class="form-select form-select-sm border-start border-success border-3">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option
                                    value="{{ $kelas->id }}"
                                    data-cabang="{{ $kelas->cabang_id }}"
                                    data-jenjang="{{ $kelas->jenjang }}"
                                    {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}
                                >
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
                    <a href="{{ route('bendahara.laporan.cetak', request()->query()) }}" target="_blank" class="btn btn-outline-success btn-sm px-3 ms-auto d-flex align-items-center gap-1">
                        <i class="fas fa-print"></i> Cetak
                    </a>
                </div>

                @if(request('cabang_id') || request('jenjang') || request('kelas_id') || request('metode'))
                    <div class="active-filters">
                        <span class="active-filter-label">Filter aktif:</span>
                        @if(request('metode'))
                            <span class="active-filter-badge"><i class="fas fa-credit-card"></i> {{ $metodeLabels[request('metode')] ?? ucfirst(request('metode')) }}</span>
                        @endif
                        @if(request('cabang_id'))
                            @php $cabangNama = $cabangList->firstWhere('id', request('cabang_id'))?->nama_cabang; @endphp
                            @if($cabangNama)
                                <span class="active-filter-badge active-filter-success">
                                    <i class="fas fa-building"></i> {{ $cabangNama }}
                                </span>
                            @endif
                        @endif
                        @if(request('jenjang'))
                            <span class="active-filter-badge active-filter-warning">
                                <i class="fas fa-layer-group"></i> {{ request('jenjang') }}
                            </span>
                        @endif
                        @if(request('kelas_id'))
                            @php $kelasNama = $kelasList->firstWhere('id', request('kelas_id'))?->nama_kelas; @endphp
                            @if($kelasNama)
                                <span class="active-filter-badge active-filter-purple">
                                    <i class="fas fa-door-open"></i> {{ $kelasNama }}
                                </span>
                            @endif
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</div>
                        <div class="stat-label">Total Pembayaran</div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-wallet">
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
                    <div class="stat-icon-wrapper stat-icon-transaction">
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
                    <div class="stat-icon-wrapper stat-icon-tunai">
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
                        <div class="stat-label">Direct Transfer & Digital</div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-gateway">
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

    <div class="dashboard-card mb-4">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="fas fa-chart-bar card-title-icon"></i> Grafik Arus Kas Harian
            </h5>
        </div>
        <div class="p-4">
            <div class="chart-scroll">
                @foreach($pembayaranPerHari as $hari => $jumlah)
                    @php
                        $maxJumlah = $pembayaranPerHari->max() ?: 1;
                        $height = round(($jumlah / $maxJumlah) * 100, 2);
                    @endphp
                    <div class="chart-item">
                        <div class="chart-bar w-100" data-chart-height="{{ $height }}" title="Tgl {{ $hari }}: Rp {{ number_format($jumlah, 0, ',', '.') }}"></div>
                        <span class="chart-day">{{ $hari }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="dashboard-card mb-4">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="fas fa-receipt card-title-icon"></i> Rincian Transaksi Pembayaran
            </h5>
            <span class="badge bg-label-primary px-3 py-2 fw-bold transaction-count-badge">
                {{ $pembayaran->total() }} Transaksi
            </span>
        </div>

        @if($pembayaran->count() > 0)
            <div class="table-responsive">
                <table class="table table-clean">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
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
                                <td data-label="No" class="text-center fw-bold muted-text">{{ $pembayaran->firstItem() + $index }}</td>
                                <td data-label="Tanggal" class="small fw-semibold">{{ $bayar->tanggal_bayar->format('d/m/Y') }}</td>
                                <td data-label="Kode"><code class="fw-bold text-primary small">{{ $bayar->kode_pembayaran }}</code></td>
                                <td data-label="Siswa">
                                    <div class="fw-semibold">{{ $bayar->siswa->nama_lengkap ?? '-' }}</div>
                                    <small class="muted-text">{{ $bayar->siswa->nisn ?? '-' }}</small>
                                </td>
                                <td data-label="Kelas" class="text-uppercase small fw-semibold">{{ $bayar->siswa->kelas->nama_kelas ?? '-' }}</td>
                                <td data-label="Jenis Tagihan" class="small fw-semibold muted-text">
                                    @if($bayar->tagihan)
                                        {{ $jenisTagihan[$bayar->tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $bayar->tagihan->jenis_tagihan)) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-label="Jumlah" class="text-end fw-bold">
                                    Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td data-label="Metode" class="text-center">
                                    @if($bayar->metode_pembayaran === 'tunai')
                                        <span class="badge bg-primary fw-bold method-badge">TUNAI</span>
                                    @elseif($bayar->metode_pembayaran === 'transfer')
                                        <span class="badge bg-warning text-white fw-bold method-badge">DIRECT TRANSFER</span>
                                    @else
                                        <span class="badge bg-success fw-bold method-badge">DIGITAL</span>
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
                            <td colspan="6" class="text-end page-total-label">Total Halaman Ini:</td>
                            <td class="text-end text-primary">Rp {{ number_format($pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="pagination-area">
                <span class="small muted-text">
                    Menampilkan {{ $pembayaran->firstItem() ?? 0 }}&ndash;{{ $pembayaran->lastItem() ?? 0 }}
                    dari {{ $pembayaran->total() }} transaksi
                </span>
                <div>{{ $pembayaran->withQueryString()->links() }}</div>
            </div>
        @else
            <div class="text-center py-5 empty-state-muted">
                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                <p class="mb-0">Tidak ada transaksi pada periode ini.</p>
            </div>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper stat-icon-cash">
                        <i class="fas fa-money-bill"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-label">Kasir (Tunai)</div>
                        <div class="stat-value stat-value-compact">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
                        <small class="muted-text">{{ $jumlahTunai }} Transaksi</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper stat-icon-transfer">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-label">Direct Transfer</div>
                        <div class="stat-value stat-value-compact">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</div>
                        <small class="muted-text">{{ $jumlahTransfer }} Transaksi</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper stat-icon-digital">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-label">Payment Gateway</div>
                        <div class="stat-value stat-value-compact">Rp {{ number_format($totalDigital, 0, ',', '.') }}</div>
                        <small class="muted-text">{{ $jumlahDigital }} Transaksi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/bendahara/laporan/index.js'])
@endsection
