document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-tugas-create-page');
    if (!page) {
        return;
    }

    const repeatToggle = page.querySelector('[data-repeat-toggle]');
    const repeatContainer = page.querySelector('[data-repeat-container]');
    const repeatInput = page.querySelector('[data-repeat-input]');

    if (!repeatToggle || !repeatContainer || !repeatInput) {
        return;
    }

    const toggleRepeatLimit = () => {
        repeatContainer.hidden = !repeatToggle.checked;

        if (repeatToggle.checked && !repeatInput.value) {
            repeatInput.value = 2;
        }
    };

    repeatToggle.addEventListener('change', toggleRepeatLimit);
    toggleRepeatLimit();
});
