let pendingBulkType = '';

const getTicketChecks = () => Array.from(document.querySelectorAll('.ticket-check'));
const getCheckedTickets = () => getTicketChecks().filter((checkbox) => checkbox.checked);

const showToast = (message, type = 'success') => {
    const toast = document.createElement('div');
    const icon = type === 'success' ? 'bx-check-circle' : 'bx-error-circle';

    toast.className = `alert alert-${type} shadow-sm m-0 d-flex align-items-center recovery-toast`;
    toast.innerHTML = `<i class="bx ${icon} fs-4 me-2"></i> <div>${message}</div>`;
    document.body.appendChild(toast);

    window.setTimeout(() => {
        toast.style.opacity = '0';
        window.setTimeout(() => toast.remove(), 400);
    }, 2500);
};

const fallbackCopy = (text) => {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.className = 'position-fixed top-0 start-0';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();

    try {
        document.execCommand('copy') ? showToast('Link reset berhasil disalin!') : showToast('Gagal menyalin link.', 'danger');
    } catch (error) {
        showToast('Gagal menyalin link.', 'danger');
    }

    textarea.remove();
};

const copyToClipboard = (text) => {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text)
            .then(() => showToast('Link reset berhasil disalin!'))
            .catch(() => fallbackCopy(text));
        return;
    }

    fallbackCopy(text);
};

const updateBulkToolbar = () => {
    const checked = getCheckedTickets();
    const allChecks = getTicketChecks();
    const toolbar = document.getElementById('bulkToolbar');
    const countEl = document.getElementById('bulkCount');
    const allChecked = checked.length === allChecks.length && allChecks.length > 0;
    const someChecked = checked.length > 0 && !allChecked;

    document.querySelectorAll('[data-select-all]').forEach((checkbox) => {
        checkbox.checked = allChecked;
        checkbox.indeterminate = someChecked;
    });

    if (checked.length > 0) {
        toolbar?.classList.remove('d-none');
        toolbar?.classList.add('d-flex');
        if (countEl) {
            countEl.textContent = `${checked.length} tiket dipilih`;
        }
    } else {
        toolbar?.classList.add('d-none');
        toolbar?.classList.remove('d-flex');
    }
};

const toggleSelectAll = (checked) => {
    getTicketChecks().forEach((checkbox) => {
        checkbox.checked = checked;
    });

    updateBulkToolbar();
};

const bulkAction = (type) => {
    const checked = getCheckedTickets();

    if (checked.length === 0) {
        return;
    }

    pendingBulkType = type;
    const modal = new window.bootstrap.Modal(document.getElementById('bulkActionModal'));
    const title = document.getElementById('bulkModalTitle');
    const body = document.getElementById('bulkModalBody');
    const button = document.getElementById('confirmBulkBtn');

    if (type === 'resolve') {
        title.innerHTML = `<i class="bx bx-check-circle text-success me-2"></i>Setujui ${checked.length} Tiket`;
        body.textContent = `Yakin ingin menyelesaikan ${checked.length} tiket pemulihan yang dipilih?`;
        button.className = 'btn btn-success';
        button.innerHTML = '<i class="bx bx-check me-1"></i> Ya, Setujui Semua';
    } else {
        title.innerHTML = `<i class="bx bx-x-circle text-danger me-2"></i>Tolak ${checked.length} Tiket`;
        body.textContent = `Yakin ingin menolak ${checked.length} tiket pemulihan yang dipilih?`;
        button.className = 'btn btn-danger';
        button.innerHTML = '<i class="bx bx-x me-1"></i> Ya, Tolak Semua';
    }

    modal.show();
};

const submitBulkAction = () => {
    const checked = getCheckedTickets();
    const form = document.getElementById('bulkForm');
    const container = document.getElementById('bulkIdsContainer');
    const url = pendingBulkType === 'resolve' ? form?.dataset.resolveUrl : form?.dataset.rejectUrl;

    if (!form || !container || !url) {
        return;
    }

    form.action = url;
    container.replaceChildren();

    checked.forEach((checkbox) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = checkbox.dataset.id;
        container.appendChild(input);
    });

    form.submit();
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-select-all]').forEach((checkbox) => {
        checkbox.addEventListener('change', () => toggleSelectAll(checkbox.checked));
    });

    getTicketChecks().forEach((checkbox) => {
        checkbox.addEventListener('change', updateBulkToolbar);
    });

    document.querySelectorAll('[data-bulk-action]').forEach((button) => {
        button.addEventListener('click', () => bulkAction(button.dataset.bulkAction));
    });

    document.getElementById('confirmBulkBtn')?.addEventListener('click', submitBulkAction);

    document.querySelectorAll('[data-copy-link]').forEach((button) => {
        button.addEventListener('click', () => copyToClipboard(button.dataset.copyLink));
    });

    updateBulkToolbar();
});