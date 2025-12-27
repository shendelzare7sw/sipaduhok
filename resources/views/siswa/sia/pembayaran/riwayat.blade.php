@extends('layouts.sneat')

@section('title', 'Riwayat Pembayaran')
@section('page-title', 'Riwayat Pembayaran')
@section('page-subtitle', 'Lihat riwayat transaksi pembayaran')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@section('styles')
<style>
    .timeline-item {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        position: relative;
        border-left: 4px solid #165fac;
    }
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-disetujui { background: #d1fae5; color: #065f46; }
    .status-ditolak { background: #fee2e2; color: #991b1b; }
    .metode-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .content-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">

<!-- Back Button & Filter -->
<div class="content-card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('siswa.sia.pembayaran.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Tagihan
            </a>
        </div>
        <div>
            <h3 style="margin: 0; color: #165fac;">
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
                <option value="transfer" {{ request('metode') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="midtrans" {{ request('metode') === 'midtrans' ? 'selected' : '' }}>Midtrans</option>
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
        <div class="content-card" style="background: linear-gradient(135deg, #10b981, #059669); color: white; text-align: center;">
            <small style="opacity: 0.9;">Total Disetujui</small>
            <h3 style="margin: 8px 0;">
                {{ $riwayatPembayaran->where('status_validasi', 'disetujui')->count() }}
            </h3>
            <small style="opacity: 0.9;">Transaksi</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="content-card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-align: center;">
            <small style="opacity: 0.9;">Menunggu Validasi</small>
            <h3 style="margin: 8px 0;">
                {{ $riwayatPembayaran->where('status_validasi', 'pending')->count() }}
            </h3>
            <small style="opacity: 0.9;">Transaksi</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="content-card" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; text-align: center;">
            <small style="opacity: 0.9;">Ditolak</small>
            <h3 style="margin: 8px 0;">
                {{ $riwayatPembayaran->where('status_validasi', 'ditolak')->count() }}
            </h3>
            <small style="opacity: 0.9;">Transaksi</small>
        </div>
    </div>
</div>

<!-- Timeline Riwayat -->
<h4 style="margin-bottom: 20px; color: #1a1a1a;">
    <i class="fas fa-list"></i> Daftar Transaksi
</h4>

@forelse($riwayatPembayaran as $pembayaran)
<div class="timeline-item">
    <div class="row align-items-center">
        <div class="col-md-7">
            <!-- Header -->
            <div class="d-flex align-items-center gap-2 mb-2">
                <h5 style="margin: 0; color: #165fac;">
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
                    {{ strtoupper($pembayaran->metode_pembayaran) }}
                </span>
            </div>

            <!-- Detail -->
            <div style="color: #666; font-size: 14px; line-height: 1.8;">
                <div><i class="fas fa-barcode"></i> Kode: <strong>{{ $pembayaran->kode_pembayaran }}</strong></div>
                <div><i class="fas fa-calendar"></i> Tanggal: {{ $pembayaran->tanggal_bayar->format('d F Y') }}</div>
                <div><i class="fas fa-money-bill-wave"></i> Jumlah: <strong style="color: #165fac;">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</strong></div>

                @if($pembayaran->status_validasi === 'disetujui' && $pembayaran->tanggal_validasi)
                <div style="color: #10b981;">
                    <i class="fas fa-check"></i> Divalidasi: {{ $pembayaran->tanggal_validasi->format('d F Y, H:i') }}
                    @if($pembayaran->validator)
                        oleh {{ $pembayaran->validator->name }}
                    @endif
                </div>
                @endif

                @if($pembayaran->catatan)
                <div style="margin-top: 8px; padding: 8px; background: #f9fafb; border-radius: 6px;">
                    <small><strong>Catatan:</strong> {{ $pembayaran->catatan }}</small>
                </div>
                @endif
            </div>
        </div>

        <div class="col-md-5 text-end">
            <!-- Bukti Pembayaran -->
            @if($pembayaran->bukti_pembayaran)
            <div style="margin-bottom: 12px;">
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
            <div class="alert alert-warning" style="margin-top: 12px; padding: 8px; font-size: 12px;">
                <i class="fas fa-hourglass-half"></i> Menunggu validasi bendahara
            </div>
            @elseif($pembayaran->status_validasi === 'ditolak')
            <div class="alert alert-danger" style="margin-top: 12px; padding: 8px; font-size: 12px;">
                <i class="fas fa-exclamation-circle"></i> Pembayaran ditolak. Hubungi bendahara.
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="content-card">
    <div style="text-align: center; padding: 48px; color: #999;">
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
    <ul style="margin-bottom: 0; padding-left: 20px;">
        <li><strong>Pending:</strong> Pembayaran menunggu validasi dari bendahara</li>
        <li><strong>Disetujui:</strong> Pembayaran telah divalidasi dan tercatat di sistem</li>
        <li><strong>Ditolak:</strong> Pembayaran tidak valid, silakan hubungi bendahara</li>
        <li>Bukti pembayaran yang disetujui dapat dicetak untuk arsip</li>
    </ul>
</div>

</div>
@endsection
