document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('gambar_flyer');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImage = document.getElementById('imagePreview');
    const previewLabel = document.getElementById('previewLabel');
    const sidePreviewCard = document.getElementById('sidePreviewCard');
    const sidePreviewImage = document.getElementById('sidePreviewImage');

    if (!fileInput || !previewContainer || !previewImage || !previewLabel || !sidePreviewCard || !sidePreviewImage) {
        return;
    }

    fileInput.addEventListener('change', () => {
        const file = fileInput.files?.[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.addEventListener('load', (event) => {
            previewImage.src = event.target.result;
            previewContainer.classList.add('is-visible');
            previewLabel.textContent = 'Preview gambar yang akan diupload';

            sidePreviewImage.src = event.target.result;
            sidePreviewCard.classList.add('is-visible');
        });

        reader.readAsDataURL(file);
    });
});
