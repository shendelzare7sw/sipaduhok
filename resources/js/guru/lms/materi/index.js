document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-materi-page');

    if (!page) {
        return;
    }

    page.querySelectorAll('[data-auto-submit]').forEach((input) => {
        input.addEventListener('change', () => {
            input.form?.submit();
        });
    });

    const modalElement = page.querySelector('#deleteModal');
    const deleteForm = page.querySelector('#deleteForm');
    const hapusTerkaitCheck = page.querySelector('#hapusTerkaitCheck');
    const deleteModal = modalElement && window.bootstrap
        ? new window.bootstrap.Modal(modalElement)
        : null;

    page.querySelectorAll('[data-delete-url]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!deleteForm || !deleteModal) {
                return;
            }

            deleteForm.action = button.dataset.deleteUrl || '';

            if (hapusTerkaitCheck) {
                hapusTerkaitCheck.checked = false;
            }

            deleteModal.show();
        });
    });
});
