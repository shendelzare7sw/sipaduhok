document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-materi-edit-page');
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

    // Ekstensi yang diizinkan per tipe — auto-filter di file picker (sinkron dgn backend).
    const acceptForType = {
        pdf: '.pdf',
        ppt: '.ppt,.pptx',
        doc: '.doc,.docx',
        video: '.mp4,.avi,.mov,.mkv,.webm',
    };

    const toggleFileInput = (resetFile = false) => {
        const selectedType = tipeFile.value;
        const isLink = selectedType === 'link';

        fileInputContainer.hidden = isLink;
        linkInputContainer.hidden = !isLink;
        fileMateri.required = false;
        urlMateri.required = isLink;

        // Batasi format di file explorer/manager sesuai tipe yang dipilih.
        if (!isLink && acceptForType[selectedType]) {
            fileMateri.setAttribute('accept', acceptForType[selectedType]);
        } else {
            fileMateri.removeAttribute('accept');
        }

        if (resetFile) {
            fileMateri.value = '';
        }

        if (fileRequired) {
            fileRequired.textContent = isLink ? '' : '*';
        }
    };

    tipeFile.addEventListener('change', () => toggleFileInput(true));
    toggleFileInput(false);
});
