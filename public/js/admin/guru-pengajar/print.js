document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[data-print-button]')?.addEventListener('click', () => {
        window.print();
    });
});
