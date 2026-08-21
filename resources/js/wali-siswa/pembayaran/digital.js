document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-payment-submit]').forEach((form) => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('button[type="submit"]');

      if (!button || button.disabled) {
        return;
      }

      button.disabled = true;
      button.dataset.originalHtml = button.innerHTML;
      button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    });
  });
});
