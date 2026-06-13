@extends('layouts.sneat')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran & Tagihan')
@section('page-subtitle', 'Kelola pembayaran sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@push('styles')
    @vite(['resources/css/siswa/sia/pembayaran/index.css'])
@endpush

@section('content')
<div class="payment-page">

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
                            <option value="transfer">Direct Transfer</option>
                            <option value="midtrans">Midtrans (Online)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Bukti Pembayaran</label>
                        <input type="file" name="bukti_pembayaran" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">Wajib untuk Tunai & Direct Transfer</small>
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
