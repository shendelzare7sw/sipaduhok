const getRequestCheckboxes = () => Array.from(document.querySelectorAll('.req-checkbox'));

const updateSelectedCount = () => {
    const checked = getRequestCheckboxes().filter((checkbox) => checkbox.checked);
    const all = getRequestCheckboxes();
    const count = checked.length;
    const selectAll = document.getElementById('select-all');

    document.getElementById('approveCount').textContent = count;
    document.getElementById('rejectCount').textContent = count;

    document.getElementById('btnBulkApprove')?.classList.toggle('is-hidden', count === 0);
    document.getElementById('btnBulkReject')?.classList.toggle('is-hidden', count === 0);

    if (selectAll) {
        selectAll.indeterminate = count > 0 && count < all.length;
        selectAll.checked = all.length > 0 && count === all.length;
    }
};

const injectIds = (containerId, countId) => {
    const container = document.getElementById(containerId);
    const countTarget = document.getElementById(countId);
    const checked = getRequestCheckboxes().filter((checkbox) => checkbox.checked);

    if (!container || !countTarget) {
        return;
    }

    container.replaceChildren();
    countTarget.textContent = checked.length;

    checked.forEach((checkbox) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = checkbox.value;
        container.appendChild(input);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('select-all')?.addEventListener('change', (event) => {
        getRequestCheckboxes().forEach((checkbox) => {
            checkbox.checked = event.target.checked;
        });

        updateSelectedCount();
    });

    getRequestCheckboxes().forEach((checkbox) => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    document.getElementById('modalBulkApprove')?.addEventListener('show.bs.modal', () => {
        injectIds('bulk-approve-ids', 'modalApproveCount');
    });

    document.getElementById('modalBulkReject')?.addEventListener('show.bs.modal', () => {
        injectIds('bulk-reject-ids', 'modalRejectCount');
    });

    updateSelectedCount();
});
