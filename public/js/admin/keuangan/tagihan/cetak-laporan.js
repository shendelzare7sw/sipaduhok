document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[data-print-page]')?.addEventListener('click', () => {
        window.print();
    });

    document.querySelector('[data-close-page]')?.addEventListener('click', () => {
        window.close();
    });
});
