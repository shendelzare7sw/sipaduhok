document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-submit-change]').forEach((input) => {
        input.addEventListener('change', () => {
            input.form?.submit();
        });
    });
});
