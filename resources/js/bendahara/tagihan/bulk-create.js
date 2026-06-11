let customFieldCounter = 0;

const fireAlert = (options) => {
    if (window.Swal?.fire) {
        return window.Swal.fire(options);
    }

    if (options.showCancelButton) {
        return Promise.resolve({
            isConfirmed: window.confirm(options.text || options.title || ''),
        });
    }

    window.alert(options.text || options.title || '');
    return Promise.resolve({ isConfirmed: false });
};

const formatCurrency = (value) => {
    const numericValue = String(value).replace(/\D/g, '').replace(/^0+/, '') || '0';
    return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const bindCurrencyInput = (input) => {
    input.addEventListener('input', () => {
        input.value = formatCurrency(input.value);
    });

    input.addEventListener('paste', (event) => {
        event.preventDefault();
        const pastedText = (event.clipboardData || window.clipboardData).getData('text');
        input.value = formatCurrency(pastedText);
    });
};

const createField = (name, className, attributes = {}) => {
    const input = document.createElement('input');
    input.name = name;
    input.className = className;

    Object.entries(attributes).forEach(([key, value]) => {
        input.setAttribute(key, value);
    });

    return input;
};

const createRemoveButton = () => {
    const button = document.createElement('button');
    const icon = document.createElement('i');

    button.type = 'button';
    button.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-field-button';
    button.dataset.removeField = '';
    icon.className = 'fas fa-times';
    button.appendChild(icon);

    return button;
};

const addTagihanField = () => {
    const container = document.getElementById('tagihan-fields-container');
    const globalDate = document.getElementById('globalJatuhTempo')?.value;
    const defaultDate = document.querySelector('[data-add-tagihan-field]')?.dataset.defaultDate || '';

    if (!container) {
        return;
    }

    customFieldCounter += 1;

    const item = document.createElement('div');
    const card = document.createElement('div');
    const label = document.createElement('label');
    const nameInput = createField(
        `custom_jenis_tagihan[${customFieldCounter}]`,
        'form-control form-control-sm mb-2',
        {
            type: 'text',
            placeholder: 'Nama Jenis Tagihan (contoh: Les Tambahan)',
            required: 'required',
        },
    );
    const inputGroup = document.createElement('div');
    const inputGroupText = document.createElement('span');
    const amountInput = createField(
        `custom_tagihan[${customFieldCounter}]`,
        'form-control currency-input',
        { type: 'text', placeholder: '0' },
    );
    const dateWrapper = document.createElement('div');
    const dateLabel = document.createElement('label');
    const dateInput = createField(
        `custom_tanggal_jatuh_tempo[${customFieldCounter}]`,
        'form-control form-control-sm jatuh-tempo-input',
        {
            type: 'date',
            value: globalDate || defaultDate,
        },
    );
    const note = document.createElement('small');
    const noteIcon = document.createElement('i');

    item.className = 'col-md-6 col-lg-4 tagihan-field-item';
    item.dataset.type = 'custom';
    card.className = 'p-3 bg-light rounded shadow-sm position-relative border border-primary';
    label.className = 'form-label fw-bold small mb-2';
    inputGroup.className = 'input-group mb-2';
    inputGroupText.className = 'input-group-text bg-white';
    inputGroupText.textContent = 'Rp';
    dateLabel.className = 'form-label small mb-1';
    dateLabel.textContent = 'Jatuh Tempo';
    note.className = 'text-muted d-block mt-1';
    noteIcon.className = 'fas fa-info-circle me-1';

    label.appendChild(nameInput);
    inputGroup.append(inputGroupText, amountInput);
    dateWrapper.append(dateLabel, dateInput);
    note.append(noteIcon, document.createTextNode('Jenis tagihan custom'));
    card.append(label, inputGroup, dateWrapper, createRemoveButton(), note);
    item.appendChild(card);
    container.appendChild(item);
    bindCurrencyInput(amountInput);
};

const removeTagihanField = (button) => {
    const field = button.closest('.tagihan-field-item');

    fireAlert({
        title: 'Hapus Field?',
        text: 'Anda yakin ingin menghapus jenis tagihan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#8592a3',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed && field) {
            field.remove();
            fireAlert({
                title: 'Terhapus!',
                text: 'Field tagihan telah dihapus.',
                icon: 'success',
            });
        }
    });
};

const updateSelectedKelas = () => {
    const selected = Array.from(document.querySelectorAll('.kelas-checkbox')).filter((checkbox) => checkbox.checked);
    const selectedBadgesContainer = document.getElementById('selectedKelasBadges');
    const kelasDropdownLabel = document.getElementById('kelasDropdownLabel');

    if (kelasDropdownLabel) {
        kelasDropdownLabel.innerHTML = `<i class="fas fa-school me-2"></i>Pilih Kelas (${selected.length} dipilih)`;
    }

    if (!selectedBadgesContainer) {
        return;
    }

    selectedBadgesContainer.replaceChildren();

    selected.forEach((checkbox) => {
        const badge = document.createElement('span');
        const label = document.createElement('span');
        const removeButton = document.createElement('button');

        badge.className = 'badge-kelas';
        label.textContent = `${checkbox.dataset.nama} (${checkbox.dataset.jenjang})`;
        removeButton.type = 'button';
        removeButton.className = 'remove-kelas';
        removeButton.dataset.removeKelas = checkbox.value;
        removeButton.textContent = 'x';

        badge.append(label, removeButton);
        selectedBadgesContainer.appendChild(badge);
    });
};

const filterKelasList = () => {
    const cabangId = document.getElementById('filterCabang')?.value;
    const jenjang = document.getElementById('filterJenjang')?.value;
    const emptyState = document.getElementById('emptyState');
    let visibleCount = 0;

    document.querySelectorAll('.kelas-checkbox-item').forEach((item) => {
        const matchesCabang = !cabangId || item.dataset.cabangId === cabangId;
        const matchesJenjang = !jenjang || item.dataset.jenjang === jenjang;
        const isVisible = matchesCabang && matchesJenjang;

        item.classList.toggle('is-hidden', !isVisible);
        if (isVisible) {
            visibleCount += 1;
        }
    });

    emptyState?.classList.toggle('is-hidden', visibleCount !== 0);
};

const setVisibleKelas = (checked) => {
    document.querySelectorAll('.kelas-checkbox-item').forEach((item) => {
        if (!item.classList.contains('is-hidden')) {
            const checkbox = item.querySelector('.kelas-checkbox');
            if (checkbox) {
                checkbox.checked = checked;
            }
        }
    });

    updateSelectedKelas();
};

const clearAllKelas = () => {
    document.querySelectorAll('.kelas-checkbox').forEach((checkbox) => {
        checkbox.checked = false;
    });

    updateSelectedKelas();
};

const handleSubmit = (event) => {
    const form = event.currentTarget;
    const selectedKelas = document.querySelectorAll('.kelas-checkbox:checked');

    if (selectedKelas.length === 0) {
        event.preventDefault();
        fireAlert({
            title: 'Perhatian!',
            text: 'Pilih minimal satu kelas untuk membuat tagihan',
            icon: 'warning',
            confirmButtonText: 'OK',
        });
        return;
    }

    form.querySelectorAll('.currency-input').forEach((input) => {
        input.value = input.value.replace(/\./g, '') || '0';
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[data-add-tagihan-field]')?.addEventListener('click', addTagihanField);
    document.querySelector('[data-clear-kelas-selection]')?.addEventListener('click', clearAllKelas);
    document.querySelector('[data-select-all-kelas]')?.addEventListener('click', () => setVisibleKelas(true));

    document.addEventListener('click', (event) => {
        const removeFieldButton = event.target.closest('[data-remove-field]');
        const removeKelasButton = event.target.closest('[data-remove-kelas]');

        if (removeFieldButton) {
            removeTagihanField(removeFieldButton);
        }

        if (removeKelasButton) {
            const checkbox = document.querySelector(`.kelas-checkbox[value="${removeKelasButton.dataset.removeKelas}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedKelas();
            }
        }
    });

    document.querySelectorAll('.kelas-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', updateSelectedKelas);
    });

    document.getElementById('filterCabang')?.addEventListener('change', filterKelasList);
    document.getElementById('filterJenjang')?.addEventListener('change', filterKelasList);
    document.querySelector('form')?.addEventListener('submit', handleSubmit);

    document.getElementById('globalJatuhTempo')?.addEventListener('change', (event) => {
        if (!event.target.value) {
            return;
        }

        document.querySelectorAll('.jatuh-tempo-input').forEach((input) => {
            input.value = event.target.value;
        });
    });

    updateSelectedKelas();
    filterKelasList();
});
