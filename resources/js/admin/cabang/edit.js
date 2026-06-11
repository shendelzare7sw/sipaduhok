document.addEventListener('DOMContentLoaded', () => {
    const kodeInput = document.getElementById('kode_cabang');

    if (!kodeInput) {
        return;
    }

    kodeInput.addEventListener('input', () => {
        kodeInput.value = kodeInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
});
