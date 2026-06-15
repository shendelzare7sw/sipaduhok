document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[data-print-page]')?.addEventListener('click', () => {
        window.print();
    });

    document.querySelector('[data-history-back]')?.addEventListener('click', () => {
        window.history.back();
    });
});
