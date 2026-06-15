document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-snap-token]');

    if (!page) {
        return;
    }

    const payButton = document.getElementById('pay-button');
    const errorMessage = document.getElementById('errorMessage');
    const snapToken = page.dataset.snapToken;
    const finishUrl = page.dataset.finishUrl;
    const snapUrl = page.dataset.midtransUrl;
    const clientKey = page.dataset.clientKey;

    const showModal = (modalId) => {
        const modalElement = document.getElementById(modalId);

        if (modalElement && window.bootstrap) {
            new bootstrap.Modal(modalElement).show();
        }
    };

    const hideModal = (modalId) => {
        const modalElement = document.getElementById(modalId);
        const modal = modalElement && window.bootstrap ? bootstrap.Modal.getInstance(modalElement) : null;

        if (modal) {
            modal.hide();
        }
    };

    const redirectToFinish = (result) => {
        const url = new URL(finishUrl, window.location.origin);
        url.searchParams.set('order_id', result.order_id || '');
        url.searchParams.set('status_code', result.status_code || '');
        url.searchParams.set('transaction_status', result.transaction_status || '');

        window.location.href = url.toString();
    };

    const showPaymentError = (message) => {
        if (errorMessage) {
            errorMessage.textContent = `Pembayaran gagal: ${message || 'Terjadi kesalahan'}`;
        }

        showModal('paymentErrorModal');
    };

    const loadSnapScript = () => new Promise((resolve, reject) => {
        if (window.snap) {
            resolve();
            return;
        }

        if (!snapUrl || !clientKey) {
            reject(new Error('Konfigurasi Midtrans belum lengkap.'));
            return;
        }

        const existingScript = document.querySelector(`script[src="${snapUrl}"]`);

        if (existingScript) {
            existingScript.addEventListener('load', resolve, { once: true });
            existingScript.addEventListener('error', () => reject(new Error('Gagal memuat Snap.js.')), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = snapUrl;
        script.dataset.clientKey = clientKey;
        script.addEventListener('load', resolve, { once: true });
        script.addEventListener('error', () => reject(new Error('Gagal memuat Snap.js.')), { once: true });
        document.head.appendChild(script);
    });

    const openPayment = () => {
        if (!window.snap) {
            showPaymentError('Snap.js belum siap. Silakan coba beberapa saat lagi.');
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: redirectToFinish,
            onPending: redirectToFinish,
            onError: (result) => {
                showPaymentError(result.status_message);
            },
            onClose: () => {
                showModal('paymentCancelledModal');
            },
        });
    };

    if (payButton) {
        payButton.disabled = true;
        payButton.addEventListener('click', openPayment);
    }

    document.querySelectorAll('[data-retry-payment]').forEach((button) => {
        button.addEventListener('click', () => {
            hideModal('paymentCancelledModal');
            hideModal('paymentErrorModal');
            openPayment();
        });
    });

    loadSnapScript()
        .then(() => {
            if (payButton) {
                payButton.disabled = false;
            }
        })
        .catch((error) => {
            if (payButton) {
                payButton.disabled = false;
            }

            showPaymentError(error.message);
        });
});
