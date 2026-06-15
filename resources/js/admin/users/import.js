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

        uploadArea.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            const file = fileInput.files?.[0];
            if (!file) return;

            fileName.textContent = file.name;
            fileSize.textContent = `${(file.size / 1024).toFixed(2)} KB`;
            fileSelected.classList.add('show');
            uploadArea.classList.add('is-hidden');
            submitButton.disabled = false;
        });

        clearButton?.addEventListener('click', () => {
            fileInput.value = '';
            fileSelected.classList.remove('show');
            uploadArea.classList.remove('is-hidden');
            submitButton.disabled = true;
        });
    });
});
