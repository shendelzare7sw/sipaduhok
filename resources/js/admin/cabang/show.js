document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tab-target]').forEach((button) => {
        button.addEventListener('click', () => {
            showTab(button.dataset.tabTarget, button);
        });
    });
});

function showTab(tabName, activeButton) {
    document.querySelectorAll('.tab-content').forEach((tab) => {
        tab.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach((button) => {
        button.classList.remove('active');
    });

    document.getElementById(`tab-${tabName}`)?.classList.add('active');
    activeButton.classList.add('active');
}
