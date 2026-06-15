document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-ujian-edit-page');
    if (!page) {
        return;
    }

    const repeatToggle = page.querySelector('[data-repeat-toggle]');
    const repeatContainer = page.querySelector('[data-repeat-container]');

    if (!repeatToggle || !repeatContainer) {
        return;
    }

    const toggleRepeatLimit = () => {
        repeatContainer.hidden = !repeatToggle.checked;
    };

    repeatToggle.addEventListener('change', toggleRepeatLimit);
    toggleRepeatLimit();
});
