@extends('layouts.sneat')

@section('title', 'Pembayaran Tagihan')
@section('page-title', 'Pembayaran Tagihan')

@section('sidebar-menu')
    @include('wali-siswa.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-siswa/pembayaran/digital.css'])
@endsection

@section('content')
@php
    $isPending = $pembayaran->status_validasi === 'pending';
    $isPaid = $pembayaran->status_validasi === 'disetujui';
    $gatewayTotal = (int) ($pembayaran->gateway_total ?: $totalBayar);
    $fee = max(0, $gatewayTotal - $totalBayar);
    $expired = $pembayaran->payment_expires_at && $pembayaran->payment_expires_at->isPast();
@endphp

<div class="container-xxl flex-grow-1 container-p-y digital-payment-page">
    <div class="mb-4">
        <a href="{{ route('wali-siswa.tagihan.anak', $pembayaran->siswa_id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Tagihan
        </a>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="digital-payment-hero">
                    <div>
                        <span class="digital-payment-eyebrow">TAGIHAN SEKOLAH</span>
                        <h3 class="mb-2 text-white">{{ $isPaid ? 'Pembayaran Berhasil' : ($isPending ? 'Selesaikan Pembayaran' : 'Pembayaran Tidak Aktif') }}</h3>
                        <p class="mb-0">Nomor transaksi {{ $pembayaran->order_id }}</p>
                    </div>
                    <span class="badge rounded-pill px-3 py-2 {{ $isPaid ? 'bg-success' : ($isPending ? 'bg-warning text-dark' : 'bg-danger') }}">
                        {{ $isPaid ? 'Lunas' : ($isPending ? 'Menunggu Pembayaran' : 'Dibatalkan / Kedaluwarsa') }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Rincian Tagihan</h6>
                    <div class="digital-item-list mb-4">
                        @foreach($allPayments as $item)
                            <div class="digital-item-row">
                                <div>
                                    <div class="fw-semibold">{{ $item->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $item->tagihan->jenis_tagihan)) }}</div>
                                    <small class="text-muted">{{ $item->kode_pembayaran }}</small>
                                </div>
                                <strong>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                    </div>

                    <div class="digital-total-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah tagihan</span>
                            <strong>Rp {{ number_format($totalBayar, 0, ',', '.') }}</strong>
                        </div>
                        @if($fee > 0)
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Biaya kanal pembayaran</span>
                                <span>Rp {{ number_format($fee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
                            <span class="fw-bold">Total pembayaran</span>
                            <strong class="fs-4 text-primary">Rp {{ number_format($gatewayTotal, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    @if($isPending && $pembayaran->payment_url && !$expired)
                        <a href="{{ $pembayaran->payment_url }}" class="btn btn-primary btn-lg w-100 mt-4 digital-pay-button" rel="noopener">
                            <i class="fas fa-lock me-2"></i>Selesaikan Pembayaran
                        </a>
                        <p class="small text-muted text-center mt-3 mb-0">Anda dapat menutup halaman ini dan melanjutkan pembayaran kembali dari riwayat tagihan.</p>
                    @elseif($isPending)
                        <div class="alert alert-warning mt-4 mb-0">
                            <i class="fas fa-clock me-2"></i>{{ $pembayaran->gateway_error ?: 'Kanal pembayaran belum terbentuk. Silakan coba kembali atau pilih kanal lain.' }}
                        </div>
                        <form action="{{ route('wali-siswa.pembayaran.continue', $pembayaran) }}" method="POST" class="mt-3" data-payment-submit>
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-rotate me-2"></i>Coba Buat Kanal Lagi
                            </button>
                        </form>
                    @elseif($isPaid)
                        <div class="alert alert-success mt-4 mb-0">
                            <i class="fas fa-check-circle me-2"></i>Pembayaran telah dikonfirmasi otomatis dan tagihan siswa sudah diperbarui.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Informasi Transaksi</h6>
                    <dl class="digital-meta mb-0">
                        <div><dt>Siswa</dt><dd>{{ $pembayaran->siswa->nama_lengkap }}</dd></div>
                        <div><dt>Kanal</dt><dd>{{ $pembayaran->payment_channel_label }}</dd></div>
                        @if($pembayaran->payment_expires_at)
                            <div><dt>Berlaku hingga</dt><dd>{{ $pembayaran->payment_expires_at->format('d M Y H:i') }} WIB</dd></div>
                        @endif
                    </dl>
                </div>
            </div>

            <form action="{{ route('wali-siswa.pembayaran.sync', $pembayaran) }}" method="POST" class="d-grid gap-2">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt me-2"></i>Cek Status Pembayaran
                </button>
                <a href="{{ route('wali-siswa.pembayaran.invoice', $pembayaran) }}" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-file-invoice me-2"></i>Lihat Invoice
                </a>
            </form>

            @if($isPending && !$expired && count($paymentMethods) > 0)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-1">Ganti Kanal Pembayaran</h6>
                        <p class="small text-muted mb-3">Transaksi lama akan dibatalkan dengan aman sebelum kanal baru dibuat.</p>

                        <form action="{{ route('wali-siswa.pembayaran.change-method', $pembayaran) }}" method="POST" data-payment-submit>
                            @csrf
                            <div class="digital-channel-list">
                                @foreach($paymentMethods as $method)
                                    @php
                                        $id = 'changeMethod'.preg_replace('/[^A-Za-z0-9]/', '', $method['code']);
                                        $feeParts = [];
                                        if ($method['fee_percent_bps'] > 0) {
                                            $feeParts[] = number_format($method['fee_percent_bps'] / 100, 2, ',', '.').'%';
                                        }
                                        if ($method['fee_flat'] > 0) {
                                            $feeParts[] = 'Rp '.number_format($method['fee_flat'], 0, ',', '.');
                                        }
                                    @endphp
                                    <input type="radio" class="btn-check" name="payment_method"
                                        id="{{ $id }}" value="{{ $method['code'] }}"
                                        @checked($pembayaran->payment_type === $method['code']) required>
                                    <label class="digital-channel-choice" for="{{ $id }}">
                                        <span>
                                            <strong>{{ $method['name'] }}</strong>
                                            <small>{{ $feeParts ? 'Biaya '.implode(' + ', $feeParts) : 'Biaya ditampilkan saat memilih bank' }}</small>
                                        </span>
                                        <i class="fas fa-circle-check"></i>
                                    </label>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100 mt-3">
                                <i class="fas fa-arrows-rotate me-2"></i>Terapkan Kanal Pilihan
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/wali-siswa/pembayaran/digital.js'])
@endsection
