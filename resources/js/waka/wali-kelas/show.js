const filterWali = () => {
    const searchInput = document.getElementById('searchWali');
    const term = (searchInput?.value || '').toLowerCase();

    document.querySelectorAll('.wali-option').forEach((option) => {
        const name = option.dataset.name || '';
        const matchName = !term || name.includes(term);

        option.classList.toggle('is-hidden', !matchName);
    });
};

const setAssignPanelOpen = (isOpen) => {
    document.getElementById('assignFormPanel')?.classList.toggle('is-open', isOpen);
    document.getElementById('toggleAssignBtn')?.classList.toggle('is-hidden', isOpen);
};

const refreshSelectedOption = () => {
    document.querySelectorAll('.wali-option').forEach((option) => {
        option.classList.toggle('is-selected', Boolean(option.querySelector('input:checked')));
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toggle-assign-panel]').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = document.getElementById('assignFormPanel');
            setAssignPanelOpen(!panel?.classList.contains('is-open'));
        });
    });

    document.getElementById('searchWali')?.addEventListener('input', filterWali);

    document.querySelectorAll('.wali-option').forEach((label) => {
        label.addEventListener('click', () => {
            refreshSelectedOption();
        });
    });

    refreshSelectedOption();
});
