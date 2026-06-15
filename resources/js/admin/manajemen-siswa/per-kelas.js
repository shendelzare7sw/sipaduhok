document.addEventListener('DOMContentLoaded', () => {
    const siswaName = document.getElementById('siswaName');
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    let deleteFormId = null;

    document.querySelectorAll('[data-remove-siswa]').forEach((button) => {
        button.addEventListener('click', () => {
            deleteFormId = button.dataset.formId;
            if (siswaName) {
                siswaName.textContent = button.dataset.siswaName || '';
            }
            new bootstrap.Modal(deleteModal).show();
        });
    });

    confirmDeleteBtn?.addEventListener('click', () => {
        if (deleteFormId) {
            document.getElementById(`deleteForm${deleteFormId}`)?.submit();
        }
    });
});
