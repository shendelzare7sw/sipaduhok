document.addEventListener('DOMContentLoaded', () => {
    const jenisKegiatanSelect = document.getElementById('jenis_kegiatan');
    const customGroup = document.getElementById('customJenisKegiatanGroup');
    const customInput = document.getElementById('custom_jenis_kegiatan');

    if (!jenisKegiatanSelect || !customGroup || !customInput) {
        return;
    }

    const toggleCustomInput = () => {
        const shouldShow = jenisKegiatanSelect.value === 'lainnya' || customInput.value.trim() !== '';

        customGroup.classList.toggle('is-visible', shouldShow);
        customInput.required = shouldShow;

        if (!shouldShow) {
            customInput.value = '';
        }
    };

    if (customInput.value.trim() !== '') {
        jenisKegiatanSelect.value = 'lainnya';
    }

    toggleCustomInput();
    jenisKegiatanSelect.addEventListener('change', toggleCustomInput);
});
