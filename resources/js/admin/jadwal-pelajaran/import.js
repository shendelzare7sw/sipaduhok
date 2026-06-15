const formatSize = (bytes) => `${(bytes / 1024).toFixed(2)} KB`;

document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('fileInput');
    const fileSelected = document.getElementById('fileSelected');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const uploadArea = document.querySelector('[data-upload-area]');
    const clearBtn = document.querySelector('[data-clear-file]');

    const showFile = (file) => {
        if (!file) return;

        fileName.textContent = file.name;
        fileSize.textContent = formatSize(file.size);
        fileSelected.classList.add('show');
        uploadArea.classList.add('d-none');
        submitBtn.disabled = false;
    };

    const clearFile = () => {
        fileInput.value = '';
        fileSelected.classList.remove('show');
        uploadArea.classList.remove('d-none');
        submitBtn.disabled = true;
    };

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            showFile(fileInput.files[0]);
        }
    });

    clearBtn.addEventListener('click', clearFile);

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
        const [file] = event.dataTransfer.files;
        if (!file) return;

        fileInput.files = event.dataTransfer.files;
        showFile(file);
    });
});
