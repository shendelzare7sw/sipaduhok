document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-materi-create-page');
    if (!page) {
        return;
    }

    const tipeFile = page.querySelector('[data-file-type-toggle]');
    const fileInputContainer = page.querySelector('[data-file-input-container]');
    const linkInputContainer = page.querySelector('[data-link-input-container]');
    const fileMateri = page.querySelector('[data-file-input]');
    const urlMateri = page.querySelector('[data-url-input]');
    const fileRequired = page.querySelector('#fileRequired');

    if (!tipeFile || !fileInputContainer || !linkInputContainer || !fileMateri || !urlMateri) {
        return;
    }

    const toggleFileInput = () => {
        const selectedType = tipeFile.value;
        const isLink = selectedType === 'link';
        const isFile = Boolean(selectedType) && !isLink;

        fileInputContainer.hidden = !isFile;
        linkInputContainer.hidden = !isLink;
        fileMateri.required = isFile;
        urlMateri.required = isLink;

        if (fileRequired) {
            fileRequired.textContent = isFile ? '*' : '';
        }
    };

    tipeFile.addEventListener('change', toggleFileInput);
    toggleFileInput();
});
