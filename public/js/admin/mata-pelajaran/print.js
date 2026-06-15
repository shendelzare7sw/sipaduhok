document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-print-button]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });
});
