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

document.querySelectorAll('[data-auto-submit]').forEach((input) => {
    input.addEventListener('change', () => input.form?.submit());
});
