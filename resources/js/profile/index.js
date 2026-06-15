document.addEventListener('DOMContentLoaded', () => {
    const uploadInput = document.getElementById('foto_profil');
    const uploadForm = document.getElementById('uploadForm');

    uploadInput?.addEventListener('change', () => {
        if (uploadInput.files.length > 0) {
            uploadForm?.requestSubmit();
        }
    });
});
