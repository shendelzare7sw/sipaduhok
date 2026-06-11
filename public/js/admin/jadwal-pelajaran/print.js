document.addEventListener('DOMContentLoaded', () => {
    if (document.body.dataset.autoPrint === '1') {
        window.print();
    }

    document.querySelector('[data-print-page]')?.addEventListener('click', () => window.print());
    document.querySelector('[data-close-page]')?.addEventListener('click', () => window.close());
});
