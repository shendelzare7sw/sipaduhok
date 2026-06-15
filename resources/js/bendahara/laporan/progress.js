document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-progress-width]').forEach((bar) => {
        const width = Number.parseFloat(bar.dataset.progressWidth || '0');
        bar.style.width = `${Math.min(Math.max(width, 0), 100)}%`;
    });
});
