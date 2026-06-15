document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-rapor-progress]').forEach((progressBar) => {
    const progress = Number.parseFloat(progressBar.dataset.raporProgress || '0');
    const clampedProgress = Math.min(Math.max(progress, 0), 100);

    progressBar.style.width = `${clampedProgress}%`;
  });
});
