document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm-submit]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const message = button.dataset.confirmSubmit || 'Yakin melanjutkan?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
