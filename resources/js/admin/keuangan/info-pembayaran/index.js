const setPanelState = (type) => {
  const viewPanel = document.getElementById(`${type}-view`);
  const editPanel = document.getElementById(`${type}-edit`);
  if (!viewPanel || !editPanel) return;

  const editing = !editPanel.classList.contains('is-visible');
  viewPanel.classList.toggle('is-hidden', editing);
  editPanel.classList.toggle('is-visible', editing);
};

const updateModeDisplay = () => {
  const environment = document.getElementById('paywuzEnvironment');
  const label = document.getElementById('modeLabel');
  const description = document.getElementById('modeDescription');
  const card = document.getElementById('modeSelectionCard');
  if (!environment || !label || !description || !card) return;

  const production = environment.value === 'production';
  label.textContent = production ? 'Mode Production' : 'Mode Sandbox';
  description.textContent = production
    ? 'Transaksi nyata dengan uang sungguhan.'
    : 'Simulasi pembayaran untuk pengujian.';
  card.classList.toggle('is-production', production);
  card.classList.toggle('is-sandbox', !production);
};

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-toggle-edit]').forEach((button) => {
    button.addEventListener('click', () => setPanelState(button.dataset.type));
  });
  document.querySelectorAll('[data-submit-on-change]').forEach((input) => {
    input.addEventListener('change', () => input.form?.submit());
  });
  document.querySelectorAll('[data-copy-value]').forEach((button) => {
    button.addEventListener('click', async () => {
      await navigator.clipboard.writeText(button.dataset.copyValue || '');
      const icon = button.querySelector('i');
      icon?.classList.replace('fa-copy', 'fa-check');
      setTimeout(() => icon?.classList.replace('fa-check', 'fa-copy'), 1500);
    });
  });
  document.getElementById('paywuzEnvironment')?.addEventListener('change', updateModeDisplay);
  updateModeDisplay();
});
