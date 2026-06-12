document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-ujian-soal-page');
    if (!page) {
        return;
    }

    const deleteForm = page.querySelector('#deleteForm');
    const deleteModalElement = page.querySelector('#deleteModal');

    if (!deleteForm || !deleteModalElement || !window.bootstrap) {
        return;
    }

    const deleteModal = new window.bootstrap.Modal(deleteModalElement);

    page.querySelectorAll('[data-delete-url]').forEach((button) => {
        button.addEventListener('click', () => {
            deleteForm.action = button.dataset.deleteUrl || '';
            deleteModal.show();
        });
    });
});
