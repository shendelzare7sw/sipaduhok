document.addEventListener('DOMContentLoaded', () => {
    const kodeInput = document.getElementById('kode_cabang');
    const namaInput = document.getElementById('nama_cabang');
    const alamatInput = document.getElementById('alamat');
    const teleponInput = document.getElementById('telepon');

    const previewKode = document.getElementById('previewKode');
    const previewNama = document.getElementById('previewNama');
    const previewAlamat = document.getElementById('previewAlamat');
    const previewTelepon = document.getElementById('previewTelepon');

    if (!kodeInput || !namaInput || !alamatInput || !teleponInput) {
        return;
    }

    function setPreview(previewElement, value) {
        if (!previewElement) {
            return;
        }

        previewElement.textContent = value || '-';
        previewElement.classList.toggle('empty', !value);
    }

    function updatePreview() {
        setPreview(previewKode, kodeInput.value.toUpperCase());
        setPreview(previewNama, namaInput.value);
        setPreview(previewAlamat, alamatInput.value);
        setPreview(previewTelepon, teleponInput.value);
    }

    kodeInput.addEventListener('input', () => {
        kodeInput.value = kodeInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        updatePreview();
    });

    [namaInput, alamatInput, teleponInput].forEach((input) => {
        input.addEventListener('input', updatePreview);
    });

    updatePreview();
});
