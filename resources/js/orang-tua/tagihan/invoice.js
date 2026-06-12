document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('[data-print-invoice]')?.addEventListener('click', () => {
    window.print();
  });

  document.querySelector('[data-close-invoice]')?.addEventListener('click', () => {
    window.close();
  });
});
