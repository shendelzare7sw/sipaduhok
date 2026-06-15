document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalKirimCatatan');
    const form = document.getElementById('formKirimCatatan');
    const textarea = document.getElementById('isi_catatan');
    const charCount = document.getElementById('charCount');
    const submitButton = document.getElementById('btnKirimCatatan');

    if (!modal || !form || !textarea || !charCount || !submitButton) {
        return;
    }

    textarea.addEventListener('input', () => {
        charCount.textContent = textarea.value.length;
    });

    document.querySelectorAll('[data-monitoring-catatan]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('catatanKontenType').value = button.dataset.kontenType;
            document.getElementById('catatanKontenId').value = button.dataset.kontenId;
            document.getElementById('catatanKontenLabel').textContent =
                `${button.dataset.kontenLabel || 'Konten'}: ${button.dataset.kontenJudul || '-'}`;
            textarea.value = '';
            charCount.textContent = '0';
            new bootstrap.Modal(modal).show();
        });
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                Accept: 'application/json',
            },
            body: new FormData(form),
        })
            .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    throw new Error(data.message || 'Gagal mengirim catatan');
                }

                bootstrap.Modal.getInstance(modal).hide();
                showFlash('success', data.message || 'Catatan berhasil dikirim.');
            })
            .catch((error) => {
                showFlash('danger', error.message || 'Terjadi kesalahan.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Catatan';
            });
    });
});

function showFlash(type, message) {
    const flash = document.createElement('div');
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    flash.className = `alert alert-${type} position-fixed monitoring-flash`;
    flash.innerHTML = `<i class="fas fa-${icon} me-2"></i>${message}`;
    document.body.appendChild(flash);
    setTimeout(() => flash.remove(), 3500);
}
