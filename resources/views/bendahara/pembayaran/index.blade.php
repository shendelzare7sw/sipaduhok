@extends('layouts.sneat')

@section('title', 'Kelola Pembayaran')
@section('page-title', 'Kelola Pembayaran')
@section('page-subtitle', 'Daftar dan validasi pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/pembayaran/index.css'])
@endsection

@section('content')

    {{-- Stat Widgets --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $stats['pending'] }}</div>
                        <div class="stat-label">Menunggu Validasi</div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-warning">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Perlu tindakan segera</span>
                    <i class="fas fa-clock opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $stats['disetujui'] }}</div>
                        <div class="stat-label">Pembayaran Disetujui</div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Tervalidasi sistem</span>
                    <i class="fas fa-check opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $stats['ditolak'] }}</div>
                        <div class="stat-label">Pembayaran Ditolak</div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="stat-footer">
                    <span>Transaksi dibatalkan</span>
                    <i class="fas fa-ban opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="dashboard-card">
        <div class="card-header-clean">
            <div>
                <h5 class="card-title-clean">
                    <i class="fas fa-list card-title-icon"></i> Rincian Transaksi Masuk
                </h5>
                <small class="payment-total-text">Total: {{ $pembayaranList->total() }} transaksi</small>
            </div>
        </div>

        {{-- Filter + Search --}}
        <form action="{{ route('bendahara.pembayaran.index') }}" method="GET" id="filterForm" class="filter-area">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1"
                    type="button" id="filterDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-auto-close="outside" data-bs-display="static">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <div class="dropdown-menu p-3 shadow border-0 payment-filter-menu" aria-labelledby="filterDropdown">
                    <h6 class="text-uppercase small fw-bold text-primary mb-2 pb-2 payment-filter-title">Opsi Filter</h6>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Status Validasi</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="pending"   {{ ($filters['status'] ?? '') == 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="disetujui" {{ ($filters['status'] ?? '') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak"   {{ ($filters['status'] ?? '') == 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Metode Pembayaran</label>
                        <select name="metode" class="form-select form-select-sm">
                            <option value="">Semua Metode</option>
                            <option value="tunai"    {{ ($filters['metode'] ?? '') == 'tunai'    ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ ($filters['metode'] ?? '') == 'transfer' ? 'selected' : '' }}>Direct Transfer</option>
                            <option value="paywuz" {{ ($filters['metode'] ?? '') == 'paywuz' ? 'selected' : '' }}>Pembayaran Digital</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Kelas</label>
                        <select name="kelas_id" class="form-select form-select-sm">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ ($filters['kelas_id'] ?? '') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1">Dari Tanggal</label>
                        <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ $filters['tanggal_dari'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold mb-1">Sampai Tanggal</label>
                        <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ $filters['tanggal_sampai'] ?? '' }}">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                        <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </div>
            </div>

            <div class="search-wrap">
                <i class="fas fa-search si"></i>
                <input type="text" name="search" id="searchInput"
                    placeholder="Cari siswa/kode..."
                    value="{{ $filters['search'] ?? '' }}" autocomplete="off">
                <button type="button" class="cl-btn {{ ($filters['search'] ?? '') ? 'show' : '' }}" id="clearSearch" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </form>

        {{-- Table --}}
        @if($pembayaranList->isEmpty())
            <div class="text-center py-5 payment-empty-state">
                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                <p class="mb-0">Tidak ada data pembayaran yang ditemukan.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-clean">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">No</th>
                            <th>Kode</th>
                            <th>Identitas Siswa</th>
                            <th>Jenis Tagihan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Metode</th>
                            <th>Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="70">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayaranList as $index => $pembayaran)
                        <tr>
                            <td data-label="No" class="text-center fw-bold payment-muted-text">
                                {{ $pembayaranList->firstItem() + $index }}
                            </td>
                            <td data-label="Kode">
                                <code class="fw-bold text-primary small">{{ $pembayaran->kode_pembayaran }}</code>
                                @if($pembayaran->order_id && $pembayaran->group_transactions_count > 1)
                                    <div class="mt-1">
                                        <span class="badge group-badge"
                                            data-bs-toggle="tooltip"
                                            title="Bagian dari transaksi gabungan ({{ $pembayaran->group_transactions_count }} tagihan)">
                                            <i class="fas fa-layer-group me-1"></i> Gabungan ({{ $pembayaran->group_transactions_count }})
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td data-label="Siswa">
                                <div class="fw-semibold payment-main-text">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</div>
                                <small class="fw-bold text-uppercase payment-muted-text">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</small>
                            </td>
                            <td data-label="Jenis Tagihan" class="small fw-semibold payment-muted-text">
                                {{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}
                            </td>
                            <td data-label="Jumlah" class="text-end fw-bold payment-main-text">
                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td data-label="Metode" class="text-center">
                                @if($pembayaran->metode_pembayaran === 'tunai')
                                    <span class="badge bg-primary badge-pill">TUNAI</span>
                                @elseif($pembayaran->metode_pembayaran === 'transfer')
                                    <span class="badge bg-success badge-pill">DIRECT TRANSFER</span>
                                @else
                                    <span class="badge bg-info badge-pill">DIGITAL</span>
                                @endif
                            </td>
                            <td data-label="Tanggal" class="small fw-semibold payment-main-text">
                                {{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y') : '-' }}
                            </td>
                            <td data-label="Status" class="text-center">
                                @if($pembayaran->status_validasi === 'pending')
                                    @if($pembayaran->metode_pembayaran === 'paywuz')
                                        @php $isExpired = $pembayaran->payment_expires_at?->isPast() ?? false; @endphp
                                        @if($isExpired)
                                            <span class="badge bg-secondary badge-pill"><i class="fas fa-times-circle me-1"></i>Kadaluarsa</span>
                                        @else
                                            <span class="badge bg-info badge-pill"><i class="fas fa-hourglass-half me-1"></i>Menunggu Bayar</span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-white badge-pill"><i class="fas fa-clock me-1"></i>Menunggu Validasi</span>
                                    @endif
                                @elseif($pembayaran->status_validasi === 'disetujui')
                                    <span class="badge bg-success badge-pill"><i class="fas fa-check-circle me-1"></i>Disetujui</span>
                                @else
                                    <span class="badge bg-danger badge-pill"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                                @endif
                            </td>
                            <td data-label="Aksi" class="text-center">
                                <a href="{{ route('bendahara.pembayaran.show', $pembayaran->id) }}"
                                    class="btn btn-sm btn-info text-white payment-action-btn" title="Detail / Validasi">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-area">
                <span class="small payment-muted-text">
                    Menampilkan {{ $pembayaranList->firstItem() ?? 0 }} - {{ $pembayaranList->lastItem() ?? 0 }}
                    dari {{ $pembayaranList->total() }} transaksi
                </span>
                <div>{{ $pembayaranList->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>

@endsection

@section('scripts')
    @vite(['resources/js/bendahara/pembayaran/index.js'])
@endsection
