@extends('layouts.sneat')

@section('title', 'Kelola Pembayaran')
@section('page-title', 'Kelola Pembayaran')
@section('page-subtitle', 'Daftar dan validasi pembayaran siswa')

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

    /* ── Stat Widget (sama persis dgn dashboard) ──────── */
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
        font-size: 1.75rem; font-weight: 700;
        color: var(--text-main); line-height: 1.2; margin-bottom: 0.2rem;
    }
    .stat-label {
        font-size: 0.8rem; font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .stat-footer {
        padding: 0.75rem 1.5rem 1rem;
        border-top: 1px dashed var(--border-color);
        font-size: 0.8rem;
        color: var(--text-muted);
        display: flex; justify-content: space-between; align-items: center;
    }

    /* ── Filter Area ──────────────────────────────────── */
    .filter-area {
        background: var(--background-color, #f8fafc);
        padding: 0.85rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    /* ── Search ───────────────────────────────────────── */
    .search-wrap { position: relative; }
    .search-wrap input {
        padding: 0.42rem 2rem 0.42rem 2.1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.85rem;
        width: 230px;
        background: var(--surface-color);
        color: var(--text-main);
        transition: all 0.2s;
    }
    .search-wrap input:focus {
        outline: none; border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(67,97,238,0.1);
    }
    .search-wrap .si { position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; font-size: 0.75rem; }
    .search-wrap .cl-btn {
        position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%);
        background: none; border: none; color: var(--text-muted); cursor: pointer; display: none; font-size: 0.75rem;
    }
    .search-wrap .cl-btn.show { display: block; }

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

    /* ── Badges ───────────────────────────────────────── */
    .badge-pill { padding: 0.3em 0.75em; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }

    /* ── Pagination ───────────────────────────────────── */
    .pagination-area {
        padding: 0.85rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    /* ── Responsive ───────────────────────────────────── */
    @media (max-width: 768px) {
        .stat-value { font-size: 1.35rem; }
        .stat-widget { padding: 1.1rem; gap: 0.85rem; }
        .stat-icon-wrapper { width: 40px; height: 40px; font-size: 1.1rem; }
        .card-header-clean { flex-direction: column; align-items: stretch; }
        .filter-area { flex-direction: column; align-items: stretch; }
        .search-wrap input { width: 100%; }
        .dropdown, .dropdown-toggle { width: 100%; }

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
        .table-clean tbody td:last-child { border-bottom: none; justify-content: flex-end; }
        .table-clean tbody td:last-child::before { display: none; }
        .pagination-area { flex-direction: column; align-items: center; }
    }
</style>
@endsection

@section('content')

    {{-- Stat Widgets ─ white cards, colored icons ─────── --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="stat-widget">
                    <div class="stat-details">
                        <div class="stat-value">{{ $stats['pending'] }}</div>
                        <div class="stat-label">Menunggu Validasi</div>
                    </div>
                    <div class="stat-icon-wrapper" style="color: #f59e0b; background: #fffbeb;">
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
                    <div class="stat-icon-wrapper" style="color: #10b981; background: #ecfdf5;">
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
                    <div class="stat-icon-wrapper" style="color: #ef4444; background: #fef2f2;">
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

    {{-- Main Table Card ──────────────────────────────── --}}
    <div class="dashboard-card">
        <div class="card-header-clean">
            <div>
                <h5 class="card-title-clean">
                    <i class="fas fa-list card-title-icon"></i> Rincian Transaksi Masuk
                </h5>
                <small style="color: var(--text-muted);">Total: {{ $pembayaranList->total() }} transaksi</small>
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
                <div class="dropdown-menu p-3 shadow border-0" aria-labelledby="filterDropdown" style="min-width: 300px; z-index: 9999;">
                    <h6 class="text-uppercase small fw-bold text-primary mb-2 pb-2" style="border-bottom: 1px solid #e5e7eb;">Opsi Filter</h6>
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
                            <option value="transfer" {{ ($filters['metode'] ?? '') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="midtrans" {{ ($filters['metode'] ?? '') == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
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
            <div class="text-center py-5" style="color: var(--text-muted);">
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
                            <td data-label="No" class="text-center fw-bold" style="color: var(--text-muted);">
                                {{ $pembayaranList->firstItem() + $index }}
                            </td>
                            <td data-label="Kode">
                                <code class="fw-bold text-primary small">{{ $pembayaran->kode_pembayaran }}</code>
                                @if($pembayaran->order_id && $pembayaran->group_transactions_count > 1)
                                    <div class="mt-1">
                                        <span class="badge"
                                            style="background: #e0f2fe; color: #0284c7; font-size: 10px; border: 1px solid #bae6fd;"
                                            data-bs-toggle="tooltip"
                                            title="Bagian dari transaksi gabungan ({{ $pembayaran->group_transactions_count }} tagihan)">
                                            <i class="fas fa-layer-group me-1"></i> Gabungan ({{ $pembayaran->group_transactions_count }})
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td data-label="Siswa">
                                <div class="fw-semibold" style="color: var(--text-main);">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</div>
                                <small class="fw-bold text-uppercase" style="color: var(--text-muted);">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</small>
                            </td>
                            <td data-label="Jenis Tagihan" class="small fw-semibold" style="color: var(--text-muted);">
                                {{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}
                            </td>
                            <td data-label="Jumlah" class="text-end fw-bold" style="color: var(--text-main);">
                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td data-label="Metode" class="text-center">
                                @if($pembayaran->metode_pembayaran === 'tunai')
                                    <span class="badge bg-primary badge-pill">TUNAI</span>
                                @elseif($pembayaran->metode_pembayaran === 'transfer')
                                    <span class="badge bg-success badge-pill">TRANSFER</span>
                                @else
                                    <span class="badge bg-info badge-pill">MIDTRANS</span>
                                @endif
                            </td>
                            <td data-label="Tanggal" class="small fw-semibold" style="color: var(--text-main);">
                                {{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y') : '-' }}
                            </td>
                            <td data-label="Status" class="text-center">
                                @if($pembayaran->status_validasi === 'pending')
                                    @if($pembayaran->metode_pembayaran === 'midtrans')
                                        @php $isExpired = $pembayaran->created_at < now()->subHours(24); @endphp
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
                                    class="btn btn-sm btn-info text-white" title="Detail / Validasi"
                                    style="border-radius: 6px; padding: 0.3rem 0.6rem;">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-area">
                <span class="small" style="color: var(--text-muted);">
                    Menampilkan {{ $pembayaranList->firstItem() ?? 0 }}–{{ $pembayaranList->lastItem() ?? 0 }}
                    dari {{ $pembayaranList->total() }} transaksi
                </span>
                <div>{{ $pembayaranList->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>

@endsection

@section('scripts')
<script>
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearSearch.classList.toggle('show', this.value.length > 0);
        });
    }
    if (clearSearch) {
        clearSearch.addEventListener('click', function () {
            searchInput.value = '';
            clearSearch.classList.remove('show');
            searchInput.focus();
        });
    }
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
</script>
@endsection
