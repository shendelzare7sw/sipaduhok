@extends('layouts.sneat')

@section('title', 'Kelola Pembayaran')
@section('page-title', 'Kelola Pembayaran')
@section('page-subtitle', 'Daftar dan validasi pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Stat Card Vibrant */
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
    .stat-card:hover { transform: translateY(-5px); }
    .stat-content { position: relative; z-index: 2; }
    .stat-title { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 8px; }
    .stat-number { font-size: 28px; font-weight: 700; margin-bottom: 4px; line-height: 1.2; }
    .stat-label-sub { font-size: 13px; opacity: 0.8; }
    .stat-icon-bg { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 60px; opacity: 0.15; z-index: 1; }

    .bg-gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-green  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-red    { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    /* Table Styling */
    .table thead th {
        background-color: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #4e73df;
        border-bottom: 2px solid #e3e6f0;
        vertical-align: middle;
    }
    .currency-font { font-family: 'Nunito', sans-serif; font-weight: 700; }
    .badge-custom { padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: 11px; }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Statistik Cards --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-gradient-orange shadow">
                <div class="stat-content">
                    <div class="stat-title">Menunggu Validasi</div>
                    <div class="stat-number">{{ $stats['pending'] }}</div>
                    <div class="stat-label-sub">Transaksi Pending</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-history"></i></div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-gradient-green shadow">
                <div class="stat-content">
                    <div class="stat-title">Pembayaran Disetujui</div>
                    <div class="stat-number">{{ $stats['disetujui'] }}</div>
                    <div class="stat-label-sub">Tervalidasi Sistem</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-gradient-red shadow">
                <div class="stat-content">
                    <div class="stat-title">Pembayaran Ditolak</div>
                    <div class="stat-number">{{ $stats['ditolak'] }}</div>
                    <div class="stat-label-sub">Butuh Revisi Siswa</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Pencarian Pembayaran</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('bendahara.pembayaran.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="small fw-bold">STATUS</label>
                        <select name="status" class="form-select form-select-sm border-start border-primary border-3 shadow-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="disetujui" {{ ($filters['status'] ?? '') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ ($filters['status'] ?? '') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small fw-bold">METODE</label>
                        <select name="metode" class="form-select form-select-sm border-start border-primary border-3 shadow-sm">
                            <option value="">Semua Metode</option>
                            <option value="tunai" {{ ($filters['metode'] ?? '') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ ($filters['metode'] ?? '') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="midtrans" {{ ($filters['metode'] ?? '') == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small fw-bold">KELAS</label>
                        <select name="kelas_id" class="form-select form-select-sm border-start border-primary border-3 shadow-sm">
                            <option value="">Semua Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ ($filters['kelas_id'] ?? '') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small fw-bold">CARI SISWA/KODE</label>
                        <input type="text" name="search" class="form-control form-control-sm shadow-sm" placeholder="Nama/Kode..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small fw-bold">DARI TANGGAL</label>
                        <input type="date" name="tanggal_dari" class="form-control form-control-sm shadow-sm" value="{{ $filters['tanggal_dari'] ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small fw-bold">SAMPAI TANGGAL</label>
                        <input type="date" name="tanggal_sampai" class="form-control form-control-sm shadow-sm" value="{{ $filters['tanggal_sampai'] ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-2 text-end align-self-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm fw-bold">
                            <i class="fas fa-search me-1"></i> Cari Transaksi
                        </button>
                        <a href="{{ route('bendahara.pembayaran.index') }}" class="btn btn-light btn-sm border px-3 ms-2 fw-bold text-gray-800">
                            <i class="fas fa-sync me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Pembayaran --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Rincian Transaksi Masuk</h6>
        </div>
        <div class="card-body p-0">
            @if($pembayaranList->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                    <p>Tidak ada data pembayaran yang ditemukan.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">NO</th>
                                <th>KODE</th>
                                <th>IDENTITAS SISWA</th>
                                <th>JENIS TAGIHAN</th>
                                <th class="text-end">JUMLAH</th>
                                <th class="text-center">METODE</th>
                                <th>TANGGAL</th>
                                <th class="text-center">STATUS</th>
                                <th class="text-center" width="80">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayaranList as $index => $pembayaran)
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $pembayaranList->firstItem() + $index }}</td>
                                    <td class="align-middle"><code class="fw-bold text-primary small">{{ $pembayaran->kode_pembayaran }}</code></td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</div>
                                        <small class="text-muted fw-bold text-uppercase">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</small>
                                    </td>
                                    <td class="align-middle small fw-bold text-muted">
                                        {{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}
                                    </td>
                                    <td class="align-middle currency-font text-dark text-end">
                                        Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($pembayaran->metode_pembayaran === 'tunai')
                                            <span class="badge bg-primary badge-custom shadow-sm">TUNAI</span>
                                        @elseif($pembayaran->metode_pembayaran === 'transfer')
                                            <span class="badge bg-success badge-custom shadow-sm">TRANSFER</span>
                                        @else
                                            <span class="badge bg-info badge-custom shadow-sm">MIDTRANS</span>
                                        @endif
                                    </td>
                                    <td class="align-middle small fw-bold">{{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y') : '-' }}</td>
                                    <td class="align-middle text-center">
                                        @if($pembayaran->status_validasi === 'pending')
                                            <span class="badge bg-warning badge-custom text-white shadow-sm"><i class="fas fa-clock me-1"></i> PENDING</span>
                                        @elseif($pembayaran->status_validasi === 'disetujui')
                                            <span class="badge bg-success badge-custom shadow-sm"><i class="fas fa-check-circle me-1"></i> DISETUJUI</span>
                                        @else
                                            <span class="badge bg-danger badge-custom shadow-sm"><i class="fas fa-times-circle me-1"></i> DITOLAK</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="{{ route('bendahara.pembayaran.show', $pembayaran->id) }}" class="btn btn-info btn-sm rounded-circle shadow-sm" title="Validasi / Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light py-3 border-top">
                    <div class="d-flex justify-content-center">
                        {{ $pembayaranList->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
</div>
@endsection
