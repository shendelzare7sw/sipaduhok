/* Page asset: wali-kelas/presensi/riwayat. */
document.addEventListener('DOMContentLoaded', function () {
    function ensureWaliModalCloseButtons() {
        document.querySelectorAll('.modal .modal-header').forEach(function (header) {
            if (header.querySelector('.btn-close, [data-bs-dismiss="modal"][aria-label="Close"], [data-bs-dismiss="modal"][aria-label="Tutup"]')) {
                return;
            }

            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = 'btn-close';
            closeButton.setAttribute('data-bs-dismiss', 'modal');
            closeButton.setAttribute('aria-label', 'Close');

            const coloredHeader = header.classList.contains('text-white') ||
                ['bg-primary', 'bg-secondary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-dark']
                    .some(function (className) {
                        return header.classList.contains(className);
                    });

            if (coloredHeader) {
                closeButton.classList.add('btn-close-white');
            }

            header.appendChild(closeButton);
        });
    }

    ensureWaliModalCloseButtons();
    document.addEventListener('shown.bs.modal', ensureWaliModalCloseButtons);

    document.querySelectorAll('table.wk-card-table').forEach(function (table) {
        const labels = Array.from(table.querySelectorAll('thead th')).map(function (th) {
            return th.textContent.replace(/\s+/g, ' ').trim();
        });

        table.querySelectorAll('tbody tr').forEach(function (row) {
            Array.from(row.children).forEach(function (cell, index) {
                if (cell.tagName !== 'TD' || cell.hasAttribute('data-label')) {
                    return;
                }

                cell.setAttribute('data-label', labels[index] || '');
            });
        });
    });

    document.querySelectorAll('.js-status-validasi').forEach(function (select) {
        select.addEventListener('change', function () {
            const statusSelect = this.closest('.modal-body')?.querySelector('select[name="status"]');
            if (!statusSelect) return;

            if (this.value === 'ditolak') {
                statusSelect.value = 'alpha';
            } else if (this.value === 'disetujui' && statusSelect.value === 'alpha') {
                statusSelect.value = 'izin';
            }
        });
    });
});
