const formatFileSize = (size) => `${(size / 1024).toFixed(2)} KB`;

const setSelectedFile = (file) => {
    const uploadArea = document.getElementById('uploadArea');
    const fileSelected = document.getElementById('fileSelected');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');

    if (!uploadArea || !fileSelected || !fileName || !fileSize || !submitBtn) {
        return;
    }

    if (file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileSelected.classList.add('show');
        uploadArea.classList.add('d-none');
        submitBtn.disabled = false;
        return;
    }

    fileSelected.classList.remove('show');
    uploadArea.classList.remove('d-none');
    submitBtn.disabled = true;
};

document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    const clearFileButton = document.querySelector('[data-clear-file]');

    fileInput?.addEventListener('change', () => {
        setSelectedFile(fileInput.files[0] || null);
    });

    uploadArea?.addEventListener('click', () => {
        fileInput?.click();
    });

    clearFileButton?.addEventListener('click', () => {
        if (fileInput) {
            fileInput.value = '';
        }
        setSelectedFile(null);
    });

    uploadArea?.addEventListener('dragover', (event) => {
        event.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea?.addEventListener('dragleave', (event) => {
        event.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea?.addEventListener('drop', (event) => {
        event.preventDefault();
        uploadArea.classList.remove('dragover');

        if (event.dataTransfer.files.length && fileInput) {
            fileInput.files = event.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
});
