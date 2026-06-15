/* Page asset: wali-kelas/presensi/show-harian. */
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

    const btnEdit = document.getElementById('btnEditMode');
    const btnCancel = document.getElementById('btnCancelEdit');
    const editActions = document.getElementById('editActions');
    const viewEls = document.querySelectorAll('.view-mode');
    const editEls = document.querySelectorAll('.edit-mode');
    const editInputs = document.querySelectorAll('.edit-mode input, .edit-mode select');

    function toggleEditMode(on) {
        viewEls.forEach(function (el) {
            el.classList.toggle('d-none', on);
        });
        editEls.forEach(function (el) {
            el.classList.toggle('d-none', !on);
        });
        editInputs.forEach(function (el) {
            el.disabled = !on;
        });

        if (editActions) {
            editActions.classList.toggle('d-none', !on);
        }
        if (btnEdit) {
            btnEdit.classList.toggle('d-none', on);
        }
    }

    btnEdit?.addEventListener('click', function () {
        toggleEditMode(true);
    });
    btnCancel?.addEventListener('click', function () {
        toggleEditMode(false);
    });

    document.querySelectorAll('[data-print-page]').forEach(function (button) {
        button.addEventListener('click', function () {
            window.print();
        });
    });
});
