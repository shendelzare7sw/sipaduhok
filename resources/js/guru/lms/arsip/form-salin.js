(() => {
    const page = document.querySelector('.guru-lms-arsip-salin-page');

    if (!page) {
        return;
    }

    const targets = Array.from(page.querySelectorAll('[data-target]'));
    const kelasInput = page.querySelector('[data-kelas-input]');
    const mapelInput = page.querySelector('[data-mapel-input]');

    const syncTarget = (label) => {
        const radio = label?.querySelector('input[type="radio"]');

        if (!radio || !kelasInput || !mapelInput) {
            return;
        }

        targets.forEach((target) => target.classList.remove('selected'));
        label.classList.add('selected');
        radio.checked = true;

        const [kelasId = '', mapelId = ''] = (radio.value || '|').split('|');
        kelasInput.value = kelasId;
        mapelInput.value = mapelId;
    };

    targets.forEach((label) => {
        const radio = label.querySelector('input[type="radio"]');

        label.addEventListener('click', () => syncTarget(label));
        radio?.addEventListener('change', () => syncTarget(label));
    });

    const initial = page.querySelector('[data-target] input[type="radio"]:checked');

    if (initial) {
        syncTarget(initial.closest('[data-target]'));
    }
})();
