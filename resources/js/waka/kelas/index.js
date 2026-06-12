const setKuotaWidths = () => {
    document.querySelectorAll('[data-kuota-width]').forEach((element) => {
        const width = Number(element.dataset.kuotaWidth || 0);
        element.style.width = `${Math.min(Math.max(width, 0), 100)}%`;
    });
};

const bindFilters = () => {
    document.querySelectorAll('[data-auto-submit]').forEach((select) => {
        select.addEventListener('change', () => {
            select.form?.submit();
        });
    });
};

const bindDeleteModal = () => {
    const deleteItemName = document.getElementById('deleteItemName');
    const deleteForm = document.getElementById('deleteForm');
    const deleteModal = document.getElementById('deleteModal');

    if (!deleteItemName || !deleteForm || !deleteModal) {
        return;
    }

    document.querySelectorAll('[data-delete-kelas]').forEach((button) => {
        button.addEventListener('click', () => {
            deleteItemName.textContent = button.dataset.deleteName || '';
            deleteForm.action = button.dataset.deleteUrl || '';
            new bootstrap.Modal(deleteModal).show();
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    setKuotaWidths();
    bindFilters();
    bindDeleteModal();
});
