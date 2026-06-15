document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-progress]').forEach((bar) => {
        const progress = Number.parseFloat(bar.dataset.progress || '0');
        const clampedProgress = Math.min(Math.max(progress, 0), 100);

        bar.style.width = `${clampedProgress}%`;
    });
});
