document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.siswa-lms-mapel-show-page .date-group-header').forEach((header) => {
        header.addEventListener('click', () => {
            const content = header.nextElementSibling;
            const icon = header.querySelector('.date-group-icon');

            content?.classList.toggle('show');
            icon?.classList.toggle('open');
        });
    });
});
