document.addEventListener('DOMContentLoaded', () => {
    const uploadContainer = document.querySelector('[data-upload-trigger]');
    const fileInput = document.getElementById('fileInput');
    const imgPreview = document.getElementById('previewImg');
    const placeholder = document.getElementById('uploadPlaceholder');
    const previewBox = document.getElementById('imagePreviewBox');

    if (!uploadContainer || !fileInput || !imgPreview || !placeholder || !previewBox) {
        return;
    }

    uploadContainer.addEventListener('click', () => {
        fileInput.click();
    });

    uploadContainer.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];

        if (!file) {
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB.');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();

        reader.addEventListener('load', (readerEvent) => {
            imgPreview.src = readerEvent.target.result;
            placeholder.classList.add('is-hidden');
            previewBox.classList.add('is-visible');
        });

        reader.readAsDataURL(file);
    });
});
