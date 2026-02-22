@extends('layouts.sneat')

@section('title', 'Pembayaran Digital - Midtrans')
@section('page-title', 'Pembayaran Digital')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-credit-card fa-4x text-primary mb-3"></i>
                            <h4 class="fw-bold">Pembayaran Digital</h4>
                            <p class="text-muted">Anda akan diarahkan ke halaman pembayaran Midtrans</p>
                        </div>

                        <div class="mb-4 text-start">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Rincian Pembayaran</h6>
                            <div class="bg-light p-3 rounded mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <span class="text-muted">Siswa:</span>
                                    <strong>{{ $pembayaran->siswa->nama_lengkap }}</strong>
                                </div>

                                <div class="mb-2">
                                    <span class="text-muted small d-block mb-1">Item Tagihan:</span>
                                    @foreach($allPayments as $item)
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="small">
                                                {{ $item->tagihan->getLabelJenis($item->tagihan->jenis_tagihan) }}
                                                <div class="text-muted fst-italic" style="font-size: 0.85em;">
                                                    {{ $item->tagihan->nama_tagihan }}</div>
                                            </span>
                                            <span class="fw-bold small">Rp
                                                {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                <span class="text-muted fs-5">Total Bayar:</span>
                                <strong class="text-primary fs-4">Rp {{ number_format($totalBayar, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                Tersedia berbagai metode pembayaran: Virtual Account, E-Wallet (GoPay, ShopeePay), QRIS, dan
                                Kartu Kredit
                            </small>
                        </div>

                        <button id="pay-button" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-arrow-right me-2"></i>
                            Lanjutkan Pembayaran
                        </button>

                        <div class="mt-3">
                            <a href="{{ route('orang-tua.tagihan.anak', $pembayaran->siswa_id) }}"
                                class="btn btn-link text-muted">
                                <i class="fas fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                        </div>

                        <div id="snap-container" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Payment Cancelled -->
        <div class="modal fade" id="paymentCancelledModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning bg-opacity-10 border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Pembayaran Dibatalkan
                        </h5>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-times-circle fa-4x text-warning mb-3"></i>
                        <p class="mb-2 fw-bold">Anda menutup halaman pembayaran</p>
                        <p class="text-muted small mb-0">
                            Pembayaran Anda belum selesai. Silakan coba lagi jika ingin melanjutkan pembayaran.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-primary" onclick="retryPayment()">
                            <i class="fas fa-redo me-1"></i>
                            Coba Lagi
                        </button>
                        <a href="{{ route('orang-tua.tagihan.anak', $pembayaran->siswa_id) }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali ke Tagihan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Payment Error -->
        <div class="modal fade" id="paymentErrorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger bg-opacity-10 border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle text-danger me-2"></i>
                            Pembayaran Gagal
                        </h5>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                        <p class="mb-2 fw-bold" id="errorMessage">Pembayaran gagal diproses</p>
                        <p class="text-muted small mb-0">
                            Mohon periksa kembali metode pembayaran Anda atau coba metode lain.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-primary" onclick="retryPayment()">
                            <i class="fas fa-redo me-1"></i>
                            Coba Lagi
                        </button>
                        <a href="{{ route('orang-tua.tagihan.anak', $pembayaran->siswa_id) }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali ke Tagihan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script
        src="https://app.{{ \App\Models\InfoPembayaran::getInstance()->midtrans_is_production ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
        data-client-key="{{ $clientKey }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payButton = document.getElementById('pay-button');
            const snapToken = @json($snapToken);

            payButton.addEventListener('click', function () {
                // Trigger snap payment
                window.snap.pay(snapToken, {
                    onSuccess: function (result) {
                        console.log('Payment success:', result);
                        window.location.href = '{{ route("orang-tua.pembayaran.snap.finish") }}?order_id=' + result.order_id + '&status_code=' + result.status_code + '&transaction_status=' + result.transaction_status;
                    },
                    onPending: function (result) {
                        console.log('Payment pending:', result);
                        window.location.href = '{{ route("orang-tua.pembayaran.snap.finish") }}?order_id=' + result.order_id + '&status_code=' + result.status_code + '&transaction_status=' + result.transaction_status;
                    },
                    onError: function (result) {
                        console.log('Payment error:', result);
                        // Show error modal instead of alert
                        document.getElementById('errorMessage').textContent = 'Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan');
                        const errorModal = new bootstrap.Modal(document.getElementById('paymentErrorModal'));
                        errorModal.show();
                    },
                    onClose: function () {
                        console.log('Customer closed the popup without finishing the payment');
                        // Show cancelled modal instead of alert
                        const cancelModal = new bootstrap.Modal(document.getElementById('paymentCancelledModal'));
                        cancelModal.show();
                    }
                });
            });

            // Auto-trigger payment on page load (optional)
            // Uncomment if you want to auto-open Snap popup
            // setTimeout(function() {
            //     payButton.click();
            // }, 500);
        });

        // Function to retry payment after cancellation or error
        function retryPayment() {
            // Hide any open modals
            const cancelModal = bootstrap.Modal.getInstance(document.getElementById('paymentCancelledModal'));
            const errorModal = bootstrap.Modal.getInstance(document.getElementById('paymentErrorModal'));

            if (cancelModal) cancelModal.hide();
            if (errorModal) errorModal.hide();

            // Re-trigger payment
            document.getElementById('pay-button').click();
        }
    </script>
@endsection