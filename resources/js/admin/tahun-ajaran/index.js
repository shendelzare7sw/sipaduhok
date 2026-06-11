const bindStatusFilter = () => {
    document.querySelector('[data-auto-submit]')?.addEventListener('change', (event) => {
        event.currentTarget.form?.submit();
    });
};

const bindDeleteModal = () => {
    const itemName = document.getElementById('deleteItemName');
    const form = document.getElementById('deleteForm');
    const modal = document.getElementById('deleteModal');

    if (!itemName || !form || !modal) {
        return;
    }

    document.querySelectorAll('[data-delete-ta]').forEach((button) => {
        button.addEventListener('click', () => {
            itemName.textContent = button.dataset.deleteName || '';
            form.action = button.dataset.deleteUrl || '';
            new bootstrap.Modal(modal).show();
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    bindStatusFilter();
    bindDeleteModal();
});
