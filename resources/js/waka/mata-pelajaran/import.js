document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    const fileSelected = document.getElementById('fileSelected');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');

    const formatFileSize = (bytes) => {
        if (bytes === 0) {
            return '0 Bytes';
        }

        const base = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const index = Math.floor(Math.log(bytes) / Math.log(base));
        return `${parseFloat((bytes / Math.pow(base, index)).toFixed(2))} ${sizes[index]}`;
    };

    const updateSelectedFile = () => {
        if (!fileInput?.files.length) {
            return;
        }

        const file = fileInput.files[0];
        if (fileName) {
            fileName.textContent = file.name;
        }
        if (fileSize) {
            fileSize.textContent = formatFileSize(file.size);
        }
        fileSelected?.classList.add('show');
        uploadArea?.classList.add('d-none');
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    };

    fileInput?.addEventListener('change', updateSelectedFile);

    uploadArea?.addEventListener('click', () => fileInput?.click());
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

        if (event.dataTransfer.files.length > 0 && fileInput) {
            fileInput.files = event.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    document.querySelectorAll('[data-clear-file]').forEach((button) => {
        button.addEventListener('click', () => {
            if (fileInput) {
                fileInput.value = '';
            }
            fileSelected?.classList.remove('show');
            uploadArea?.classList.remove('d-none');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
        });
    });
});
