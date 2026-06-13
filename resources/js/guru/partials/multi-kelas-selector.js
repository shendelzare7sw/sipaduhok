document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-multi-kelas-selector]').forEach((selector) => {
        const toggle = selector.querySelector('[data-multi-kelas-toggle]');
        const body = selector.querySelector('[data-multi-kelas-body]');
        const classCheckboxes = () => selector.querySelectorAll('[data-kelas-lain-checkbox]');

        if (!toggle || !body) {
            return;
        }

        const syncBody = () => {
            body.classList.toggle('d-none', !toggle.checked);

            if (!toggle.checked) {
                classCheckboxes().forEach((checkbox) => {
                    checkbox.checked = false;
                });
            }
        };

        toggle.addEventListener('change', syncBody);

        selector.querySelectorAll('[data-toggle-all-kelas]').forEach((button) => {
            button.addEventListener('click', () => {
                const state = button.dataset.toggleAllKelas === 'true';
                classCheckboxes().forEach((checkbox) => {
                    checkbox.checked = state;
                });
            });
        });
    });
});
