document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-print-button]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });

    const fotoInput = document.getElementById('fotoInput');
    fotoInput?.addEventListener('change', (event) => {
        const file = event.target.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.addEventListener('load', (readerEvent) => {
            const img = document.getElementById('kartuFoto');
            const placeholder = document.getElementById('fotoPlaceholder');

            if (!img) {
                return;
            }

            img.src = readerEvent.target.result;
            img.classList.remove('is-hidden');
            placeholder?.classList.add('is-hidden');
        });
        reader.readAsDataURL(file);
    });
});
