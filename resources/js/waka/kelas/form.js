const getSelectedOption = (select) => select?.options[select.selectedIndex] || null;

const kelasSuggestions = {
    KB: ['KB1', 'KB2', 'KB3'],
    TKA: ['TKA1', 'TKA2', 'TKA3'],
    TKB: ['TKB1', 'TKB2', 'TKB3'],
    SD: ['1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B', '5A', '5B', '6A', '6B'],
    SMP: ['7A', '7B', '8A', '8B', '9A', '9B'],
    SMA: ['10A', '10B', '11A', '11B', '12A', '12B'],
};

const updatePreviewKode = () => {
    const cabangSelect = document.getElementById('cabang_id');
    const tahunAjaranSelect = document.getElementById('tahun_ajaran_id');
    const jenjangSelect = document.getElementById('jenjang');
    const namaKelasInput = document.getElementById('nama_kelas');
    const previewKode = document.getElementById('previewKode');

    if (!cabangSelect || !tahunAjaranSelect || !jenjangSelect || !namaKelasInput || !previewKode) {
        return;
    }

    const cabangOption = getSelectedOption(cabangSelect);
    const tahunOption = getSelectedOption(tahunAjaranSelect);
    const jenjang = jenjangSelect.value;
    const namaKelas = namaKelasInput.value;

    if (cabangOption?.dataset.kode && jenjang && namaKelas && tahunOption?.dataset.tahun) {
        previewKode.textContent = `${cabangOption.dataset.kode}-${jenjang}-${namaKelas.toUpperCase().replace(/\s+/g, '')}-${tahunOption.dataset.tahun}`;
        return;
    }

    if (previewKode.dataset.keepInitial !== 'true') {
        previewKode.textContent = '-';
    }
};

const updateNamaKelasSuggestions = () => {
    const jenjangSelect = document.getElementById('jenjang');
    const namaKelasInput = document.getElementById('nama_kelas');
    const suggestionsContainer = document.getElementById('namaKelasSuggestions');

    if (!jenjangSelect || !namaKelasInput || !suggestionsContainer) {
        return;
    }

    suggestionsContainer.innerHTML = '';

    (kelasSuggestions[jenjangSelect.value] || []).forEach((suggestion) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'nama-kelas-suggestion';
        button.textContent = suggestion;
        button.addEventListener('click', () => {
            namaKelasInput.value = suggestion;
            updatePreviewKode();
        });
        suggestionsContainer.appendChild(button);
    });
};

const createWaliDisplay = (name, cabang) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'wali-info';

    const avatar = document.createElement('div');
    avatar.className = 'wali-avatar';
    avatar.textContent = name.substring(0, 2);

    const details = document.createElement('div');
    details.className = 'wali-details';

    const displayName = document.createElement('div');
    displayName.className = 'wali-name';
    displayName.textContent = name;

    const role = document.createElement('div');
    role.className = 'wali-role';
    role.textContent = cabang || '-';

    details.append(displayName, role);
    wrapper.append(avatar, details);

    return wrapper;
};

const createWaliPlaceholder = () => {
    const placeholder = document.createElement('div');
    placeholder.className = 'wali-placeholder';

    const icon = document.createElement('i');
    icon.className = 'fas fa-user-plus';

    placeholder.append(icon, document.createTextNode(' Klik untuk memilih wali kelas'));

    return placeholder;
};

const setWaliKelas = (id, name, cabang) => {
    const input = document.getElementById('wali_kelas_id');
    const display = document.querySelector('[data-open-wali-modal]');

    if (input) {
        input.value = id;
    }

    if (display) {
        display.innerHTML = '';
        display.appendChild(createWaliDisplay(name, cabang));
    }
};

const clearWaliKelas = () => {
    const input = document.getElementById('wali_kelas_id');
    const display = document.querySelector('[data-open-wali-modal]');

    if (input) {
        input.value = '';
    }

    if (display) {
        display.innerHTML = '';
        display.appendChild(createWaliPlaceholder());
    }
};

const filterWaliKelas = () => {
    const searchInput = document.getElementById('searchWaliKelas');
    const searchTerm = searchInput?.value.toLowerCase().trim() || '';

    document.querySelectorAll('.wali-option-item').forEach((option) => {
        const name = option.dataset.name || '';
        option.classList.toggle('d-none', searchTerm !== '' && !name.includes(searchTerm));
    });
};

document.addEventListener('DOMContentLoaded', () => {
    const cabangSelect = document.getElementById('cabang_id');
    const tahunAjaranSelect = document.getElementById('tahun_ajaran_id');
    const jenjangSelect = document.getElementById('jenjang');
    const namaKelasInput = document.getElementById('nama_kelas');

    [cabangSelect, tahunAjaranSelect, jenjangSelect, namaKelasInput].forEach((element) => {
        element?.addEventListener(element === namaKelasInput ? 'input' : 'change', updatePreviewKode);
    });

    jenjangSelect?.addEventListener('change', updateNamaKelasSuggestions);

    document.querySelector('[data-open-wali-modal]')?.addEventListener('click', () => {
        new bootstrap.Modal(document.getElementById('waliKelasModal')).show();
    });

    document.getElementById('searchWaliKelas')?.addEventListener('input', filterWaliKelas);

    document.querySelectorAll('.wali-option-item').forEach((option) => {
        option.addEventListener('click', () => {
            setWaliKelas(option.dataset.id, option.dataset.displayName || '', option.dataset.cabangName || '-');
            bootstrap.Modal.getInstance(document.getElementById('waliKelasModal'))?.hide();
        });
    });

    document.getElementById('btnRemoveWaliKelas')?.addEventListener('click', () => {
        bootstrap.Modal.getInstance(document.getElementById('waliKelasModal'))?.hide();

        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('confirmRemoveWaliModal')).show();
        }, 300);
    });

    document.getElementById('confirmRemoveBtn')?.addEventListener('click', () => {
        clearWaliKelas();
        bootstrap.Modal.getInstance(document.getElementById('confirmRemoveWaliModal'))?.hide();
    });

    document.getElementById('waliKelasModal')?.addEventListener('shown.bs.modal', () => {
        const searchInput = document.getElementById('searchWaliKelas');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        filterWaliKelas();
    });

    updateNamaKelasSuggestions();
    updatePreviewKode();
});
