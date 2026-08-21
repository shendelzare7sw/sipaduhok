@extends('layouts.sneat')

@section('title', 'Riwayat Pembayaran')
@section('page-title', 'Riwayat Pembayaran')
@section('page-subtitle', 'Lihat riwayat transaksi pembayaran')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@push('styles')
    @vite(['resources/css/siswa/sia/pembayaran/riwayat.css'])
@endpush

@section('content')
<div class="payment-history-page">

<!-- Back Button & Filter -->
<div class="content-card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('siswa.sia.pembayaran.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Tagihan
            </a>
        </div>
        <div>
            <h3 class="payment-history-heading">
                <i class="fas fa-history"></i> Riwayat Pembayaran
            </h3>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="content-card mb-4">
    <form method="GET" action="{{ route('siswa.sia.pembayaran.riwayat') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Status Validasi</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Metode Pembayaran</label>
            <select name="metode" class="form-select">
                <option value="">Semua Metode</option>
                <option value="tunai" {{ request('metode') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="transfer" {{ request('metode') === 'transfer' ? 'selected' : '' }}>Direct Transfer</option>
                <option value="paywuz" {{ request('metode') === 'paywuz' ? 'selected' : '' }}>Digital</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('siswa.sia.pembayaran.riwayat') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Summary Box -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="content-card summary-card summary-success">
            <small class="summary-muted">Total Disetujui</small>
            <h3 class="summary-value">
                {{ $riwayatPembayaran->where('status_validasi', 'disetujui')->count() }}
            </h3>
            <small class="summary-muted">Transaksi</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="content-card summary-card summary-warning">
            <small class="summary-muted">Menunggu Validasi</small>
            <h3 class="summary-value">
                {{ $riwayatPembayaran->where('status_validasi', 'pending')->count() }}
            </h3>
            <small class="summary-muted">Transaksi</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="content-card summary-card summary-danger">
            <small class="summary-muted">Ditolak</small>
            <h3 class="summary-value">
                {{ $riwayatPembayaran->where('status_validasi', 'ditolak')->count() }}
            </h3>
            <small class="summary-muted">Transaksi</small>
        </div>
    </div>
</div>

<!-- Timeline Riwayat -->
<h4 class="section-title">
    <i class="fas fa-list"></i> Daftar Transaksi
</h4>

@forelse($riwayatPembayaran as $pembayaran)
<div class="timeline-item">
    <div class="row align-items-center">
        <div class="col-md-7">
            <!-- Header -->
            <div class="d-flex align-items-center gap-2 mb-2">
                <h5 class="transaction-title">
                    {{ $pembayaran->tagihan->jenis_tagihan }}
                </h5>
                <span class="status-badge status-{{ $pembayaran->status_validasi }}">
                    @if($pembayaran->status_validasi === 'pending')
                        <i class="fas fa-clock"></i> Pending
                    @elseif($pembayaran->status_validasi === 'disetujui')
                        <i class="fas fa-check-circle"></i> Disetujui
                    @else
                        <i class="fas fa-times-circle"></i> Ditolak
                    @endif
                </span>
                <span class="metode-badge">
                    {{ $pembayaran->metode_pembayaran === 'transfer' ? 'DIRECT TRANSFER' : strtoupper($pembayaran->metode_pembayaran) }}
                </span>
            </div>

            <!-- Detail -->
            <div class="transaction-detail">
                <div><i class="fas fa-barcode"></i> Kode: <strong>{{ $pembayaran->kode_pembayaran }}</strong></div>
                <div><i class="fas fa-calendar"></i> Tanggal: {{ $pembayaran->tanggal_bayar->format('d F Y') }}</div>
                <div><i class="fas fa-money-bill-wave"></i> Jumlah: <strong class="text-primary-strong">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</strong></div>

                @if($pembayaran->status_validasi === 'disetujui' && $pembayaran->tanggal_validasi)
                <div class="validation-info">
                    <i class="fas fa-check"></i> Divalidasi: {{ $pembayaran->tanggal_validasi->format('d F Y, H:i') }}
                    @if($pembayaran->validator)
                        oleh {{ $pembayaran->validator->name }}
                    @endif
                </div>
                @endif

                @if($pembayaran->catatan)
                <div class="note-box">
                    <small><strong>Catatan:</strong> {{ $pembayaran->catatan }}</small>
                </div>
                @endif
            </div>
        </div>

        <div class="col-md-5 text-end">
            <!-- Bukti Pembayaran -->
            @if($pembayaran->bukti_pembayaran)
            <div class="proof-actions">
                <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}"
                   target="_blank"
                   class="btn btn-info btn-sm">
                    <i class="fas fa-image"></i> Lihat Bukti
                </a>
            </div>
            @endif

            <!-- Cetak Bukti -->
            @if($pembayaran->status_validasi === 'disetujui')
            <a href="{{ route('siswa.sia.pembayaran.cetak', $pembayaran->id) }}"
               class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Cetak Bukti
            </a>
            @endif

            <!-- Status Info -->
            @if($pembayaran->status_validasi === 'pending')
            <div class="alert alert-warning status-alert">
                <i class="fas fa-hourglass-half"></i> Menunggu validasi bendahara
            </div>
            @elseif($pembayaran->status_validasi === 'ditolak')
            <div class="alert alert-danger status-alert">
                <i class="fas fa-exclamation-circle"></i> Pembayaran ditolak. Hubungi bendahara.
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="content-card">
    <div class="empty-state-payment">
        <i class="fas fa-receipt fa-3x mb-3"></i>
        <h4>Belum Ada Riwayat Pembayaran</h4>
        <p>Riwayat pembayaran akan muncul setelah Anda melakukan transaksi</p>
        <a href="{{ route('siswa.sia.pembayaran.index') }}" class="btn btn-primary mt-2">
            <i class="fas fa-credit-card"></i> Lihat Tagihan
        </a>
    </div>
</div>
@endforelse

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $riwayatPembayaran->links() }}
</div>

<!-- Info Box -->
<div class="alert alert-info mt-4" role="alert">
    <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi</h5>
    <ul class="info-list">
        <li><strong>Pending:</strong> Pembayaran menunggu validasi dari bendahara</li>
        <li><strong>Disetujui:</strong> Pembayaran telah divalidasi dan tercatat di sistem</li>
        <li><strong>Ditolak:</strong> Pembayaran tidak valid, silakan hubungi bendahara</li>
        <li>Bukti pembayaran yang disetujui dapat dicetak untuk arsip</li>
    </ul>
</div>

</div>
@endsection
