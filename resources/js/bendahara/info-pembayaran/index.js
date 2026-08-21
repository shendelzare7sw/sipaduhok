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

const copyToClipboard = async (value) => {
  if (navigator.clipboard && window.isSecureContext) {
    try {
      await navigator.clipboard.writeText(value);
      return;
    } catch (error) {
      console.warn('Clipboard API ditolak, mencoba fallback browser.', error);
    }
  }

  const input = document.createElement('textarea');
  input.value = value;
  input.setAttribute('readonly', '');
  input.style.position = 'fixed';
  input.style.opacity = '0';
  document.body.appendChild(input);
  input.select();

  const copied = document.execCommand('copy');
  input.remove();

  if (!copied) {
    throw new Error('Browser menolak akses clipboard.');
  }
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
      const icon = button.querySelector('i');

      try {
        await copyToClipboard(button.dataset.copyValue || '');
        icon?.classList.replace('fa-copy', 'fa-check');
        button.title = 'Berhasil disalin';
        setTimeout(() => {
          icon?.classList.replace('fa-check', 'fa-copy');
          button.title = 'Salin URL webhook';
        }, 1500);
      } catch (error) {
        console.error('Gagal menyalin URL webhook:', error);
        button.title = 'Gagal menyalin. Salin URL dari kolom di samping.';
      }
    });
  });
  document.getElementById('paywuzEnvironment')?.addEventListener('change', updateModeDisplay);
  updateModeDisplay();
});
