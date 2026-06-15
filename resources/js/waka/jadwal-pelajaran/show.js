document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-schedule-href]').forEach((cell) => {
        cell.addEventListener('click', () => {
            window.location.href = cell.dataset.scheduleHref;
        });
    });
});
