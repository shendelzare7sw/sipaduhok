document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');

    if (searchInput && clearSearch) {
        searchInput.addEventListener('input', () => {
            clearSearch.classList.toggle('show', searchInput.value.length > 0);
        });

        clearSearch.addEventListener('click', () => {
            searchInput.value = '';
            clearSearch.classList.remove('show');
            searchInput.focus();
        });
    }

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
        if (window.bootstrap?.Tooltip) {
            new window.bootstrap.Tooltip(element);
        }
    });
});
