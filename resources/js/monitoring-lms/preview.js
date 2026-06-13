document.addEventListener('DOMContentLoaded', () => {
    const previewImages = document.querySelectorAll('.preview-content img');
    const modalElement = document.getElementById('imagePreviewModal');
    const modalImage = document.getElementById('imagePreviewSource');

    if (!modalElement || !modalImage) {
        return;
    }

    const modal = new bootstrap.Modal(modalElement);

    previewImages.forEach((image) => {
        image.addEventListener('click', () => {
            modalImage.src = image.src;
            modal.show();
        });
    });
});
