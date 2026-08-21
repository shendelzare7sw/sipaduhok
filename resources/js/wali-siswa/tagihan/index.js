const formatRupiah = (num) => `Rp ${num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')}`;

const getCheckedValue = (radios) => {
  const selectedRadio = Array.from(radios).find((radio) => radio.checked);

  return selectedRadio ? selectedRadio.value : null;
};

const copyText = (text, button) => {
  if (navigator.clipboard && window.isSecureContext) {
    return navigator.clipboard.writeText(text).then(() => true).catch(() => false);
  }

  const tempInput = document.createElement('input');
  tempInput.type = 'text';
  tempInput.value = text;
  tempInput.className = 'rekening-copy-helper';

  const container = button.closest('.modal-body') || document.body;
  container.appendChild(tempInput);
  tempInput.select();
  tempInput.setSelectionRange(0, 99999);

  let success = false;

  try {
    success = document.execCommand('copy');
  } catch (error) {
    console.error('Copy failed:', error);
  }

  tempInput.remove();

  return Promise.resolve(success);
};

const showCopyFeedback = (button) => {
  const originalHtml = button.innerHTML;

  button.innerHTML = '<i class="fas fa-check"></i>';
  button.classList.remove('btn-outline-primary');
  button.classList.add('btn-success');

  setTimeout(() => {
    button.innerHTML = originalHtml;
    button.classList.remove('btn-success');
    button.classList.add('btn-outline-primary');
  }, 2000);
};

document.addEventListener('DOMContentLoaded', () => {
  const page = document.querySelector('.wali-siswa-tagihan-index-page');

  if (!page) {
    return;
  }

  const checkAllGroups = page.querySelectorAll('.select-all-group');
  const itemCheckboxes = page.querySelectorAll('.item-checkbox');
  const footer = page.querySelector('#bulkPaymentFooter');
  const selectedCountSpan = page.querySelector('#selectedCount');
  const grandTotalDisplay = page.querySelector('#grandTotalDisplay');
  const btnPaySelected = page.querySelector('#btnPaySelected');
  const modalBulkPay = page.querySelector('#modalBulkPay');
  const bulkModal = modalBulkPay && window.bootstrap ? new window.bootstrap.Modal(modalBulkPay) : null;
  const methodRadios = page.querySelectorAll('input[name="metode_pembayaran"]');
  const infoSections = page.querySelectorAll('.method-info');
  const btnSubmit = page.querySelector('#btnSubmitBulk');
  const buktiInput = page.querySelector('#bulkBuktiInput');
  const paymentChannelInputs = page.querySelectorAll('.payment-channel-input');
  const paymentChannelOptions = page.querySelectorAll('[data-payment-channel-option]');
  const paymentChannelUnavailable = page.querySelector('#paymentChannelUnavailable');

  let totalBayar = 0;

  const updateTotal = () => {
    let count = 0;
    let total = 0;

    itemCheckboxes.forEach((checkbox) => {
      if (checkbox.checked) {
        count += 1;
        total += Number.parseInt(checkbox.dataset.amount || '0', 10) || 0;
      }
    });

    totalBayar = total;

    if (selectedCountSpan) {
      selectedCountSpan.textContent = count;
    }

    if (grandTotalDisplay) {
      grandTotalDisplay.textContent = formatRupiah(total);
    }

    footer?.classList.toggle('d-none', count === 0);
  };

  const appendHiddenInput = (container, name, value) => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    container.appendChild(input);
  };

  const appendSummaryItem = (summaryList, label, amount) => {
    const item = document.createElement('li');
    const title = document.createElement('span');
    const price = document.createElement('span');

    item.className = 'list-group-item d-flex justify-content-between align-items-center px-0';
    title.textContent = label;
    price.className = 'fw-semibold';
    price.textContent = `Rp ${amount.toLocaleString('id-ID')}`;

    item.append(title, price);
    summaryList.appendChild(item);
  };

  const openPaymentModal = () => {
    const summaryList = page.querySelector('#paymentSummaryList');
    const container = page.querySelector('#hiddenInputsContainer');
    const totalDisplay = page.querySelector('#modalTotalDisplay');
    const inputTotal = page.querySelector('#inputTotalBayar');

    if (!summaryList || !container || !totalDisplay || !inputTotal || !bulkModal) {
      return;
    }

    summaryList.replaceChildren();
    container.replaceChildren();

    let validItems = 0;

    itemCheckboxes.forEach((checkbox, index) => {
      if (!checkbox.checked) {
        return;
      }

      const amount = Number.parseInt(checkbox.dataset.amount || '0', 10) || 0;

      if (amount <= 0) {
        return;
      }

      validItems += 1;
      appendSummaryItem(summaryList, checkbox.dataset.label || 'Tagihan', amount);
      appendHiddenInput(container, `items[${index}][tagihan_id]`, checkbox.value);
      appendHiddenInput(container, `items[${index}][jumlah_bayar]`, amount);
    });

    if (validItems === 0) {
      alert('Silakan pilih tagihan yang akan dibayar.');
      return;
    }

    totalDisplay.textContent = formatRupiah(totalBayar);
    inputTotal.value = totalBayar;
    updatePaymentChannels();
    bulkModal.show();
  };

  const updatePaymentChannels = () => {
    let availableCount = 0;

    paymentChannelOptions.forEach((option) => {
      const input = option.querySelector('.payment-channel-input');
      const min = Number.parseInt(option.dataset.min || '1', 10);
      const max = Number.parseInt(option.dataset.max || `${Number.MAX_SAFE_INTEGER}`, 10);
      const available = totalBayar >= min && totalBayar <= max;

      option.classList.toggle('is-unavailable', !available);
      if (input) {
        input.disabled = !available || getCheckedValue(methodRadios) !== 'paywuz';
        if (!available) {
          input.checked = false;
        }
      }

      if (available) {
        availableCount += 1;
      }
    });

    paymentChannelUnavailable?.classList.toggle('d-none', availableCount > 0);
    return availableCount;
  };

  const handleMethodChange = () => {
    infoSections.forEach((section) => section.classList.add('d-none'));

    if (buktiInput) {
      buktiInput.required = false;
    }

    const selectedValue = getCheckedValue(methodRadios);

    paymentChannelInputs.forEach((input) => {
      input.required = false;
      input.disabled = true;
    });

    if (selectedValue === 'paywuz') {
      page.querySelector('#infoPaywuz')?.classList.remove('d-none');
      const availableCount = updatePaymentChannels();
      paymentChannelInputs.forEach((input) => {
        input.required = !input.disabled;
      });
      btnSubmit?.classList.toggle('d-none', availableCount === 0);
      return;
    }

    if (selectedValue === 'transfer') {
      page.querySelector('#infoTransfer')?.classList.remove('d-none');

      if (buktiInput) {
        buktiInput.required = true;
      }

      btnSubmit?.classList.remove('d-none');
    }
  };

  checkAllGroups.forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
      page.querySelectorAll(`.item-checkbox.group-${checkbox.dataset.group}`).forEach((target) => {
        target.checked = checkbox.checked;
      });

      updateTotal();
    });
  });

  itemCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
      const groupClass = Array.from(checkbox.classList).find((className) => className.startsWith('group-'));

      if (groupClass) {
        const group = groupClass.replace('group-', '');
        const selectAll = page.querySelector(`.select-all-group[data-group="${group}"]`);

        if (!checkbox.checked && selectAll) {
          selectAll.checked = false;
        }
      }

      updateTotal();
    });
  });

  btnPaySelected?.addEventListener('click', openPaymentModal);
  methodRadios.forEach((radio) => radio.addEventListener('change', handleMethodChange));

  page.querySelector('#formBulkPay')?.addEventListener('submit', (event) => {
    if (getCheckedValue(methodRadios) === 'paywuz' && !getCheckedValue(paymentChannelInputs)) {
      event.preventDefault();
      page.querySelector('#infoPaywuz')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      paymentChannelUnavailable?.classList.remove('d-none');
      paymentChannelUnavailable.textContent = 'Pilih salah satu kanal pembayaran yang tersedia.';
      return;
    }

    if (btnSubmit) {
      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    }
  });

  buktiInput?.addEventListener('change', () => {
    if (!buktiInput.files || !buktiInput.files[0]) {
      return;
    }

    const fileSize = buktiInput.files[0].size / 1024 / 1024;

    if (fileSize > 10) {
      alert(`Ukuran file terlalu besar! Maksimal 10MB. File Anda: ${fileSize.toFixed(2)}MB`);
      buktiInput.value = '';
    }
  });

  page.querySelectorAll('[data-copy-rekening]').forEach((button) => {
    button.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();

      copyText(button.dataset.copyText || '', button).then((success) => {
        if (success) {
          showCopyFeedback(button);
        }
      });
    });
  });

  handleMethodChange();
});
