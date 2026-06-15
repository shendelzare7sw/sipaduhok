(() => {
    const page = document.querySelector('.siswa-lms-jadwal-page');

    if (!page) {
        return;
    }

    const searchInput = page.querySelector('#searchMapel');
    const subjectItems = page.querySelectorAll('.subject-item');

    searchInput?.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();

        subjectItems.forEach((item) => {
            const name = item.dataset.name || '';
            item.classList.toggle('is-hidden', !name.includes(query));
        });
    });
})();
