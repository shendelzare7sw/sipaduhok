const formatFileSize = (size) => `${(size / 1024).toFixed(2)} KB`;

document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('fileInput');
    const fileSelected = document.getElementById('fileSelected');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitButton = document.getElementById('submitBtn');
    const uploadArea = document.querySelector('[data-upload-trigger]');
    const clearFileButton = document.querySelector('[data-clear-file]');

    const setSelectedFile = (file) => {
        if (!file || !fileInput || !fileSelected || !fileName || !fileSize || !submitButton || !uploadArea) {
            return;
        }

        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileSelected.classList.add('show');
        uploadArea.classList.add('is-hidden');
        submitButton.disabled = false;
    };

    const clearFile = () => {
        if (!fileInput || !fileSelected || !submitButton || !uploadArea) {
            return;
        }

        fileInput.value = '';
        fileSelected.classList.remove('show');
        uploadArea.classList.remove('is-hidden', 'is-dragging');
        submitButton.disabled = true;
    };

    uploadArea?.addEventListener('click', () => fileInput?.click());
    uploadArea?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            fileInput?.click();
        }
    });

    uploadArea?.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadArea.classList.add('is-dragging');
    });

    uploadArea?.addEventListener('dragleave', () => {
        uploadArea.classList.remove('is-dragging');
    });

    uploadArea?.addEventListener('drop', (event) => {
        event.preventDefault();
        uploadArea.classList.remove('is-dragging');

        if (!fileInput || !event.dataTransfer?.files.length) {
            return;
        }

        fileInput.files = event.dataTransfer.files;
        setSelectedFile(event.dataTransfer.files[0]);
    });

    fileInput?.addEventListener('change', () => {
        if (fileInput.files.length) {
            setSelectedFile(fileInput.files[0]);
        }
    });

    clearFileButton?.addEventListener('click', clearFile);
});
