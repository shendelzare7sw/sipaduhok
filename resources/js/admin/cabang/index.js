document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => {
            field.form?.submit();
        });
    });

    document.querySelectorAll('[data-delete-id]').forEach((button) => {
        button.addEventListener('click', () => {
            const deleteName = document.getElementById('deleteItemName');
            const deleteForm = document.getElementById('deleteForm');
            const deleteModal = document.getElementById('deleteModal');

            if (!deleteName || !deleteForm || !deleteModal) {
                return;
            }

            deleteName.textContent = button.dataset.deleteName || '';
            const baseUrl = deleteForm.dataset.deleteBaseUrl || '';
            deleteForm.action = `${baseUrl.replace(/\/$/, '')}/${button.dataset.deleteId}`;

            bootstrap.Modal.getOrCreateInstance(deleteModal).show();
        });
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((tooltipTriggerEl) => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
