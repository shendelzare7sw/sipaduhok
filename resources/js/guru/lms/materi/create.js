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
        const isFile = Boolean(selectedType) && !isLink;

        fileInputContainer.hidden = !isFile;
        linkInputContainer.hidden = !isLink;
        fileMateri.required = isFile;
        urlMateri.required = isLink;

        // Batasi format di file explorer/manager sesuai tipe yang dipilih.
        if (isFile && acceptForType[selectedType]) {
            fileMateri.setAttribute('accept', acceptForType[selectedType]);
        } else {
            fileMateri.removeAttribute('accept');
        }

        // Kosongkan pilihan file saat tipe diganti agar tidak menyisakan file tak sesuai.
        if (resetFile) {
            fileMateri.value = '';
        }

        if (fileRequired) {
            fileRequired.textContent = isFile ? '*' : '';
        }
    };

    tipeFile.addEventListener('change', () => toggleFileInput(true));
    toggleFileInput(false);
});
