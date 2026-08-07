document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.user-import').forEach((container) => {
        const uploadArea = container.querySelector('[data-upload-area]');
        const fileInput = container.querySelector('[data-file-input]');
        const fileSelected = container.querySelector('[data-file-selected]');
        const fileName = container.querySelector('[data-file-name]');
        const fileSize = container.querySelector('[data-file-size]');
        const submitButton = container.querySelector('[data-submit-import]');
        const clearButton = container.querySelector('[data-clear-file]');

        if (!uploadArea || !fileInput || !fileSelected || !fileName || !fileSize || !submitButton) {
            return;
        }

        const showFile = (file) => {
            if (!file) return;

            fileName.textContent = file.name;
            fileSize.textContent = `${(file.size / 1024).toFixed(2)} KB`;
            fileSelected.classList.add('show');
            uploadArea.classList.add('is-hidden');
            submitButton.disabled = false;
        };

        uploadArea.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => showFile(fileInput.files?.[0]));

        clearButton?.addEventListener('click', () => {
            fileInput.value = '';
            fileSelected.classList.remove('show');
            uploadArea.classList.remove('is-hidden');
            submitButton.disabled = true;
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            uploadArea.addEventListener(eventName, (event) => {
                event.preventDefault();
                uploadArea.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            uploadArea.addEventListener(eventName, (event) => {
                event.preventDefault();
                uploadArea.classList.remove('is-dragging');
            });
        });

        uploadArea.addEventListener('drop', (event) => {
            const file = event.dataTransfer.files?.[0];
            if (!file) return;

            fileInput.files = event.dataTransfer.files;
            showFile(file);
        });
    });
});
