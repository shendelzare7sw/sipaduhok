document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tab-key]').forEach((button) => {
        button.addEventListener('shown.bs.tab', (event) => {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', event.target.dataset.tabKey);
            window.history.replaceState({}, '', url);
            const hidden = document.querySelector('input[name="tab"]');
            if (hidden) {
                hidden.value = event.target.dataset.tabKey;
            }
        });
    });
});
