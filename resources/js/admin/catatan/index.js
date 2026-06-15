/* Catatan delete behavior extracted from the former shared loader. */
document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('catatanDeleteConfig');
    const basePath = configEl?.dataset.basePath || '/catatan';
    const titleEl = document.getElementById('deleteCatatanJudul');
    const confirmBtn = document.getElementById('confirmDeleteCatatanBtn');
    const form = document.getElementById('deleteCatatanForm');
    const modalEl = document.getElementById('deleteCatatanModal');

    if (!titleEl || !confirmBtn || !form || !modalEl || typeof bootstrap === 'undefined') {
        return;
    }

    const modal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('[data-catatan-delete]').forEach((button) => {
        button.addEventListener('click', () => {
            titleEl.textContent = button.dataset.catatanTitle || '';
            form.action = `${basePath}/${button.dataset.catatanId}`;
            modal.show();
        });
    });

    confirmBtn.addEventListener('click', () => {
        modal.hide();
        form.submit();
    });
});
