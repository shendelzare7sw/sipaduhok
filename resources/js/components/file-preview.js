(() => {
    document.addEventListener('shown.bs.modal', (event) => {
        const modal = event.target;

        if (!modal.classList.contains('file-preview-modal')) {
            return;
        }

        const iframe = modal.querySelector('iframe[data-src]');

        if (iframe && !iframe.getAttribute('src')) {
            iframe.setAttribute('src', iframe.getAttribute('data-src'));
        }
    });
})();
