/* Page asset: wali-kelas/rapor/request-download. */
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
});

function showDownloadAction(action, message, btnLabel, btnClass, headerClass) {
    document.getElementById('downloadActionForm').action = action;
    document.getElementById('downloadModalMessage').textContent = message;
    document.getElementById('downloadModalTitle').innerHTML = '<i class="fas fa-question-circle me-2"></i>' + btnLabel;
    document.getElementById('downloadModalHeader').className = 'modal-header text-white ' + headerClass;
    document.getElementById('downloadModalIcon').className = 'fas fa-question-circle fa-3x mb-3 ' + (headerClass.includes('success') ? 'text-success' : 'text-danger');

    const submitBtn = document.getElementById('downloadSubmitBtn');
    submitBtn.className = 'btn fw-bold ' + btnClass;
    submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> ' + btnLabel;

    new bootstrap.Modal(document.getElementById('downloadActionModal')).show();
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-download-action').forEach(function (button) {
        button.addEventListener('click', function () {
            showDownloadAction(
                this.dataset.action || '',
                this.dataset.message || 'Apakah Anda yakin?',
                this.dataset.btnLabel || 'Konfirmasi',
                this.dataset.btnClass || 'btn-primary',
                this.dataset.headerClass || 'bg-primary'
            );
        });
    });
});
