@extends('layouts.sneat')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran & Tagihan')
@section('page-subtitle', 'Kelola pembayaran sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@section('styles')
<style>
    .summary-container {
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card.bg-gradient-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .stat-card.bg-gradient-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stat-card.bg-gradient-orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stat-content {
        position: relative;
        z-index: 2;
    }

    .stat-label {
        font-size: 0.813rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
        margin-bottom: 0.5rem;
        color: white;
    }

    .stat-value {
        font-size: clamp(2rem, 4vw, 2.375rem);
        font-weight: 700;
        margin-bottom: 0.25rem;
        line-height: 1.2;
        color: white;
    }

    .stat-desc {
        font-size: 0.813rem;
        opacity: 0.8;
        color: white;
    }

    .stat-icon-bg {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: clamp(3.5rem, 8vw, 4.375rem);
        opacity: 0.15;
        z-index: 1;
        color: white;
    }

    .tagihan-card {
        background: white;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border-left: 4px solid #165fac;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .tagihan-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .tagihan-title {
        flex: 1;
        min-width: 200px;
    }

    .tagihan-title h4 {
        margin: 0 0 0.5rem 0;
        color: #1a1a1a;
        font-size: clamp(1rem, 2.5vw, 1.25rem);
    }

    .tagihan-title p {
        margin: 0;
        color: #666;
        font-size: clamp(0.813rem, 2vw, 0.875rem);
    }

    .status-lunas {
        background: #d1fae5;
        color: #065f46;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-belum {
        background: #fee2e2;
        color: #991b1b;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .amount-box {
        background: #f9fafb;
        padding: 0.75rem;
        border-radius: 6px;
        margin: 0.75rem 0;
    }

    .amount-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .amount-row span {
        color: #666;
        font-size: 0.875rem;
    }

    .amount-row strong {
        font-size: clamp(1rem, 3vw, 1.125rem);
        color: #165fac;
    }

    .btn-responsive {
        font-size: clamp(0.813rem, 2vw, 0.875rem);
        padding: 0.5rem 1rem;
        white-space: nowrap;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h3 {
        margin: 0;
        font-size: clamp(1.125rem, 3vw, 1.5rem);
    }

    .content-card {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
    }

    .empty-state {
        padding: 3rem 1rem;
        color: #999;
    }

    .empty-state i {
        font-size: clamp(2rem, 5vw, 3rem);
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        font-size: clamp(1rem, 3vw, 1.25rem);
        margin-bottom: 0.5rem;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .stat-card {
            padding: 1.25rem;
        }

        .stat-icon-bg {
            font-size: 3rem;
            right: 15px;
        }
    }

    @media (max-width: 768px) {
        .stat-card {
            padding: 1.25rem;
        }

        .stat-icon-bg {
            font-size: 2.5rem;
        }

        .tagihan-card {
            padding: 1rem;
        }

        .tagihan-header {
            flex-direction: column;
        }

        .tagihan-title {
            width: 100%;
        }

        .btn-responsive {
            width: 100%;
            margin-top: 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .stat-card {
            padding: 1rem;
        }

        .stat-icon-bg {
            font-size: 2rem;
            right: 10px;
        }

        .stat-label {
            font-size: 0.75rem;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-header .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">

<!-- Summary Stats Cards -->
<div class="summary-container">
    <div class="row g-3">
        <!-- Total Tagihan -->
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-label">Total Tagihan</div>
                    <div class="stat-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                    <div class="stat-desc">Total yang harus dibayar</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>

        <!-- Sudah Dibayar -->
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-label">Sudah Dibayar</div>
                    <div class="stat-value">Rp {{ number_format($totalBayar, 0, ',', '.') }}</div>
                    <div class="stat-desc">Pembayaran terverifikasi</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Sisa Tagihan -->
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-label">Sisa Tagihan</div>
                    <div class="stat-value">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</div>
                    <div class="stat-desc">{{ $sisaTagihan > 0 ? 'Belum lunas' : 'Semua lunas' }}</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tombol Riwayat -->
<div class="page-header">
    <h3><i class="fas fa-list"></i> Daftar Tagihan</h3>
    <a href="{{ route('siswa.sia.pembayaran.riwayat') }}" class="btn btn-info btn-responsive">
        <i class="fas fa-history"></i> Riwayat Pembayaran
    </a>
</div>

<!-- Daftar Tagihan -->
@forelse($tagihan as $item)
<div class="tagihan-card">
    <div class="tagihan-header">
        <div class="tagihan-title">
            <h4>{{ $item->jenis_tagihan }}</h4>
            <p>
                <i class="fas fa-calendar"></i> Jatuh Tempo: {{ $item->tanggal_jatuh_tempo->format('d F Y') }}
            </p>
        </div>
        <span class="status-{{ $item->status === 'sudah_bayar' ? 'lunas' : 'belum' }}">
            {{ $item->status === 'sudah_bayar' ? 'Lunas' : 'Belum Bayar' }}
        </span>
    </div>

    <div class="amount-box">
        <div class="amount-row">
            <span>Jumlah Tagihan:</span>
            <strong>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</strong>
        </div>
    </div>

    @if($item->status !== 'sudah_bayar')
    <button type="button"
            class="btn btn-primary btn-responsive"
            data-bs-toggle="modal"
            data-bs-target="#bayarModal{{ $item->id }}">
        <i class="fas fa-credit-card"></i> Bayar Sekarang
    </button>
    @else
    <button class="btn btn-success btn-responsive" disabled>
        <i class="fas fa-check-circle"></i> Sudah Lunas
    </button>
    @endif
</div>

<!-- Modal Pembayaran -->
<div class="modal fade" id="bayarModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pembayaran: {{ $item->jenis_tagihan }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('siswa.sia.pembayaran.bayar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="tagihan_id" value="{{ $item->id }}">

                    <div class="alert alert-info">
                        <strong>Total Tagihan:</strong> Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                        <input type="number"
                               name="jumlah_bayar"
                               class="form-control"
                               value="{{ $item->jumlah }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="metode_pembayaran" class="form-select" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="tunai">Tunai (Bayar di Sekolah)</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="midtrans">Midtrans (Online)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Bukti Pembayaran</label>
                        <input type="file" name="bukti_pembayaran" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Wajib untuk Tunai & Transfer</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@empty
<div class="content-card">
    <div class="empty-state">
        <i class="fas fa-money-bill-wave"></i>
        <h4>Tidak Ada Tagihan</h4>
        <p>Belum ada tagihan yang perlu dibayar</p>
    </div>
</div>
@endforelse

</div>
@endsection
