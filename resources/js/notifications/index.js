document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-notifications-page]');
    if (!root) return;

    const baseUrl = root.dataset.notificationsBaseUrl || '/notifications';
    const context = root.dataset.notificationsContext || '';
    const isLmsLayout = root.dataset.isLmsLayout === 'true';
    const filterForm = document.getElementById('filterForm');
    const bulkToolbar = document.getElementById('bulkToolbar');
    const bulkCount = document.getElementById('bulkCount');
    const selectAll = document.getElementById('selectAllCb');
    const bulkActionForm = document.getElementById('bulkActionForm');
    const bulkActionInput = document.getElementById('bulkActionInput');
    const bulkIdsContainer = document.getElementById('bulkIdsContainer');
    const bulkDeleteModal = document.getElementById('bulkDeleteModal');
    const bulkDeleteMsg = document.getElementById('bulkDeleteMsg');
    const confirmBulkDeleteBtn = document.getElementById('confirmBulkDeleteBtn');
    let searchTimer;
    let pendingDeleteAction = null;
    let modalInstance = null;

    if (bulkDeleteModal && bulkDeleteModal.parentElement !== document.body) {
        document.body.appendChild(bulkDeleteModal);
    }

    if (bulkDeleteModal) {
        bulkDeleteModal.addEventListener('show.bs.modal', () => {
            bulkDeleteModal.classList.add('modal-flex');
            if (isLmsLayout) updateLmsPadding();
        });

        bulkDeleteModal.addEventListener('hidden.bs.modal', () => {
            bulkDeleteModal.classList.remove('modal-flex');
            bulkDeleteModal.style.paddingLeft = '';
            pendingDeleteAction = null;
        });
    }

    if (isLmsLayout && bulkDeleteModal) {
        const bodyObserver = new MutationObserver(() => {
            if (bulkDeleteModal.classList.contains('modal-flex')) updateLmsPadding();
        });
        bodyObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });

        window.addEventListener('resize', () => {
            if (bulkDeleteModal.classList.contains('modal-flex')) updateLmsPadding();
        });
    }

    document.querySelectorAll('[data-filter-search]').forEach((input) => {
        input.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => filterForm?.submit(), 500);
        });
    });

    document.querySelectorAll('[data-auto-submit]').forEach((input) => {
        input.addEventListener('change', () => input.form?.submit());
    });

    selectAll?.addEventListener('change', () => {
        document.querySelectorAll('.notif-check').forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
        updateBulkToolbar();
    });

    document.querySelectorAll('.notif-check').forEach((checkbox) => {
        checkbox.addEventListener('change', updateBulkToolbar);
    });

    document.querySelectorAll('[data-bulk-action]').forEach((button) => {
        button.addEventListener('click', () => bulkAction(button.dataset.bulkAction));
    });

    root.addEventListener('click', (event) => {
        if (event.target.closest('[data-stop-notification-click]')) return;
        const row = event.target.closest('[data-notification-row]');
        if (!row) return;
        handleNotificationClick(row);
    });

    confirmBulkDeleteBtn?.addEventListener('click', () => {
        if (!pendingDeleteAction) return;
        modalInstance?.hide();
        submitBulkAction(pendingDeleteAction);
    });

    function updateLmsPadding() {
        const isMobile = window.innerWidth <= 768;
        const collapsed = document.body.classList.contains('sidebar-collapsed');
        if (isMobile || collapsed) {
            bulkDeleteModal.style.paddingLeft = '';
            return;
        }

        const sidebarWidth = parseFloat(
            getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width')
        ) || 280;
        bulkDeleteModal.style.paddingLeft = `${sidebarWidth}px`;
    }

    function handleNotificationClick(row) {
        const id = row.dataset.notificationId;
        const type = row.dataset.notificationType;

        if (!id) return;

        if (type === 'catatan') {
            const showUrl = new URL(`${baseUrl}/${encodeURIComponent(id)}`, window.location.origin);
            if (context) showUrl.searchParams.set('ctx', context);
            window.location.href = showUrl.toString();
            return;
        }

        fetch(`${baseUrl}/${encodeURIComponent(id)}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
        }).catch(() => {});

        row.classList.remove('unread');
        row.querySelector('.unread-dot')?.remove();

        const link = row.dataset.link;
        if (link && link.trim() !== '') {
            window.location.href = link;
        }
    }

    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.notif-check:checked');
        if (!bulkToolbar || !bulkCount) return;

        if (checked.length > 0) {
            bulkToolbar.classList.add('visible');
            bulkCount.textContent = `${checked.length} dipilih`;
        } else {
            bulkToolbar.classList.remove('visible');
            if (selectAll) selectAll.checked = false;
        }
    }

    function bulkAction(action) {
        const checked = document.querySelectorAll('.notif-check:checked');
        if (checked.length === 0) return;

        if (action === 'delete') {
            if (bulkDeleteMsg) {
                bulkDeleteMsg.textContent = `Yakin ingin menghapus ${checked.length} notifikasi yang dipilih?`;
            }
            if (bulkDeleteModal && window.bootstrap?.Modal) {
                pendingDeleteAction = 'delete';
                modalInstance = window.bootstrap.Modal.getOrCreateInstance(bulkDeleteModal);
                modalInstance.show();
            } else {
                submitBulkAction('delete');
            }
            return;
        }

        submitBulkAction(action);
    }

    function submitBulkAction(action) {
        const checked = document.querySelectorAll('.notif-check:checked');
        if (!bulkActionForm || !bulkActionInput || !bulkIdsContainer) return;

        bulkActionInput.value = action;
        bulkIdsContainer.innerHTML = '';
        checked.forEach((checkbox) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = checkbox.dataset.id;
            bulkIdsContainer.appendChild(input);
        });
        bulkActionForm.submit();
    }
});
