const getFormConfig = () => {
    const config = document.getElementById('jadwalFormConfig');
    if (!config) {
        return { mapelList: [] };
    }

    try {
        return {
            mapelList: JSON.parse(config.dataset.mapelList || '[]'),
            currentMapelId: config.dataset.currentMapelId || '',
            currentMapelJenjang: config.dataset.currentMapelJenjang || '',
        };
    } catch {
        return { mapelList: [] };
    }
};

const setVisible = (element, visible, display = '') => {
    if (!element) return;
    element.classList.toggle('d-none', !visible);
    element.style.display = visible ? display : 'none';
};

const getSelectedKelasData = () => {
    const select = document.getElementById('kelasSelect');
    return Array.from(select?.selectedOptions || []).map((option) => ({
        id: option.value,
        name: option.text.trim(),
        jenjang: option.getAttribute('data-jenjang'),
    }));
};

const updateSelectedKelasUI = (data) => {
    const textPlaceholder = document.getElementById('selectedKelasText');
    const chipsContainer = document.getElementById('selectedKelasChips');

    if (!textPlaceholder || !chipsContainer) return;

    if (data.length === 0) {
        setVisible(textPlaceholder, true, 'block');
        setVisible(chipsContainer, false);
        chipsContainer.innerHTML = '';
        return;
    }

    setVisible(textPlaceholder, false);
    setVisible(chipsContainer, true, 'flex');
    chipsContainer.innerHTML = '';

    data.forEach((item) => {
        const chip = document.createElement('div');
        chip.className = 'badge bg-primary d-flex align-items-center p-2 chip-kelas';
        chip.innerHTML = `
            <i class="fas fa-school me-2"></i>
            ${item.name}
            <span class="ms-2 badge bg-white text-primary chip-jenjang">${item.jenjang}</span>
        `;
        chipsContainer.appendChild(chip);
    });
};

const filterIstirahatDisplay = () => {
    const kelasSelect = document.getElementById('kelasSelect');
    const hariSelect = document.getElementById('hariSelect');
    const istirahatDesc = document.getElementById('istirahatDesc');
    const istirahatSummary = document.getElementById('istirahatSummary');
    const jenjangGroups = document.querySelectorAll('.jenjang-group');

    if (!kelasSelect || !hariSelect || !istirahatDesc) return;

    const selectedKelasOption = kelasSelect.selectedOptions[0];
    const selectedJenjang = selectedKelasOption?.getAttribute('data-jenjang');
    const selectedHari = hariSelect.value;
    let visibleTotalCount = 0;

    if (selectedJenjang && selectedHari) {
        istirahatDesc.textContent = `Waktu istirahat untuk ${selectedJenjang} pada hari ${selectedHari}:`;

        jenjangGroups.forEach((group) => {
            const isCurrentJenjang = group.getAttribute('data-jenjang') === selectedJenjang;
            let visibleCount = 0;

            group.querySelectorAll('.col-md-6').forEach((item) => {
                const hariText = item.querySelector('small.text-muted:last-child')?.textContent || '';
                const visible = isCurrentJenjang && hariText.includes(selectedHari);
                setVisible(item, visible, 'block');
                if (visible) {
                    visibleCount++;
                    visibleTotalCount++;
                }
            });

            setVisible(group, isCurrentJenjang && visibleCount > 0, 'block');
        });

        if (istirahatSummary) {
            istirahatSummary.textContent = visibleTotalCount > 0
                ? `(${visibleTotalCount} istirahat pada ${selectedHari})`
                : `(Tidak ada istirahat pada ${selectedHari})`;
        }
        return;
    }

    if (selectedJenjang) {
        istirahatDesc.textContent = `Waktu istirahat untuk ${selectedJenjang}. Pilih hari untuk melihat istirahat yang lebih spesifik.`;

        jenjangGroups.forEach((group) => {
            const visible = group.getAttribute('data-jenjang') === selectedJenjang;
            setVisible(group, visible, 'block');

            if (visible) {
                group.querySelectorAll('.col-md-6').forEach((item) => {
                    setVisible(item, true, 'block');
                    visibleTotalCount++;
                });
            }
        });

        if (istirahatSummary) {
            istirahatSummary.textContent = `(${visibleTotalCount} istirahat untuk ${selectedJenjang})`;
        }
        return;
    }

    istirahatDesc.textContent = 'Berikut adalah waktu istirahat yang telah dikonfigurasi. Pilih kelas dan hari untuk melihat istirahat yang relevan.';
    jenjangGroups.forEach((group) => {
        setVisible(group, true, 'block');
        group.querySelectorAll('.col-md-6').forEach((item) => {
            setVisible(item, true, 'block');
            visibleTotalCount++;
        });
    });

    if (istirahatSummary) {
        istirahatSummary.textContent = `(${visibleTotalCount} jadwal istirahat)`;
    }
};

const filterGuruByCabang = () => {
    const kelasSelect = document.getElementById('kelasSelect');
    const selectedOptions = Array.from(kelasSelect?.selectedOptions || []);
    const uniqueBranches = [...new Set(selectedOptions.map((option) => option.getAttribute('data-cabang-id')))];
    const guruOptions = document.querySelectorAll('.guru-option-item');

    if (uniqueBranches.length === 0) {
        guruOptions.forEach((option) => {
            option.setAttribute('data-visible-branch', 'true');
            setVisible(option, true, 'flex');
        });
        return;
    }

    guruOptions.forEach((option) => {
        const guruCabang = option.getAttribute('data-cabang-id');
        const visible = !guruCabang || uniqueBranches.includes(guruCabang);
        option.setAttribute('data-visible-branch', visible ? 'true' : 'false');
        setVisible(option, visible, 'flex');
    });
};

const renderMapelSections = () => {
    const config = getFormConfig();
    const kelasSelect = document.getElementById('kelasSelect');
    const mapelSelect = document.getElementById('mapelSelect');
    const singleSection = document.getElementById('singleMapelSection');
    const multiContainer = document.getElementById('multiMapelContainer');
    const multiSections = document.getElementById('multiMapelSections');
    const isMultiJenjang = document.getElementById('isMultiJenjang');
    const selectedOptions = Array.from(kelasSelect?.selectedOptions || []);

    if (!mapelSelect || !singleSection || !multiContainer || !multiSections || !isMultiJenjang) return;

    if (selectedOptions.length === 0) {
        setVisible(singleSection, true);
        multiContainer.classList.remove('is-visible');
        isMultiJenjang.value = '0';
        mapelSelect.setAttribute('required', 'required');
        mapelSelect.setAttribute('name', 'mata_pelajaran_id');
        mapelSelect.value = '';
        Array.from(mapelSelect.options).forEach((option) => { option.hidden = false; });
        multiSections.innerHTML = '';
        return;
    }

    const jenjangMap = {};
    selectedOptions.forEach((option) => {
        const jenjang = option.getAttribute('data-jenjang');
        if (!jenjangMap[jenjang]) {
            jenjangMap[jenjang] = [];
        }
        jenjangMap[jenjang].push(option.text.trim());
    });

    const jenjangKeys = Object.keys(jenjangMap);

    if (jenjangKeys.length <= 1) {
        const jenjang = jenjangKeys[0] || null;
        setVisible(singleSection, true);
        multiContainer.classList.remove('is-visible');
        isMultiJenjang.value = '0';
        mapelSelect.setAttribute('required', 'required');
        mapelSelect.setAttribute('name', 'mata_pelajaran_id');
        multiSections.innerHTML = '';

        Array.from(mapelSelect.options).forEach((option) => {
            const mapelJenjang = option.getAttribute('data-jenjang');
            option.hidden = option.value !== '' && jenjang && mapelJenjang !== jenjang;
        });
        return;
    }

    setVisible(singleSection, false);
    multiContainer.classList.add('is-visible');
    isMultiJenjang.value = '1';
    mapelSelect.removeAttribute('required');
    mapelSelect.removeAttribute('name');
    multiSections.innerHTML = '';

    jenjangKeys.forEach((jenjang) => {
        const kelasNames = jenjangMap[jenjang].join(', ');
        const filteredMapel = config.mapelList.filter((mapel) => mapel.jenjang === jenjang);
        const section = document.createElement('div');
        section.className = 'mb-3 p-3 border rounded bg-white';

        const heading = document.createElement('div');
        heading.className = 'd-flex align-items-center gap-2 mb-2';
        heading.innerHTML = `<span class="badge bg-info">${jenjang}</span><small class="text-muted">Kelas: ${kelasNames}</small>`;

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = `mapel_per_jenjang[${jenjang}]`;

        const select = document.createElement('select');
        select.className = 'form-select';
        select.required = true;
        select.innerHTML = `<option value="">-- Pilih Mapel ${jenjang} --</option>`;

        filteredMapel.forEach((mapel) => {
            const option = document.createElement('option');
            option.value = mapel.id;
            option.textContent = `${mapel.nama} (${mapel.jenjang})`;

            if (config.currentMapelId && jenjang === config.currentMapelJenjang && String(mapel.id) === String(config.currentMapelId)) {
                option.selected = true;
                hidden.value = mapel.id;
            }

            select.appendChild(option);
        });

        select.addEventListener('change', () => {
            hidden.value = select.value;
        });

        section.append(heading, hidden, select);
        multiSections.appendChild(section);
    });
};

const setGuru = (id, name, cabang) => {
    const input = document.getElementById('guru_id');
    const display = document.querySelector('.guru-display');

    if (input) {
        input.value = id;
    }

    if (display) {
        display.innerHTML = `
            <div class="guru-info">
                <div class="guru-avatar">${name.substring(0, 2)}</div>
                <div class="guru-details">
                    <div class="guru-name">${name}</div>
                    <div class="guru-role">${cabang}</div>
                </div>
            </div>
        `;
    }
};

const clearGuruSelection = () => {
    const input = document.getElementById('guru_id');
    const display = document.querySelector('.guru-display');

    if (input) {
        input.value = '';
    }

    if (display) {
        display.innerHTML = '<div class="form-placeholder"><i class="fas fa-chalkboard-teacher"></i> Klik untuk memilih guru pengajar</div>';
    }

    bootstrap.Modal.getInstance(document.getElementById('guruModal'))?.hide();
};

const filterGuruList = () => {
    const searchTerm = document.getElementById('searchGuru')?.value.toLowerCase().trim() || '';

    document.querySelectorAll('.guru-option-item').forEach((option) => {
        const name = option.getAttribute('data-name') || '';
        const visibleByBranch = option.getAttribute('data-visible-branch') !== 'false';
        setVisible(option, visibleByBranch && (searchTerm === '' || name.includes(searchTerm)), 'flex');
    });
};

const filterKelasList = () => {
    const cabangFilter = document.getElementById('filterCabang')?.value || '';
    const jenjangFilter = document.getElementById('filterJenjang')?.value || '';
    const searchText = document.getElementById('searchKelas')?.value.toLowerCase() || '';

    document.querySelectorAll('.kelas-item').forEach((item) => {
        const visible = (!cabangFilter || item.getAttribute('data-cabang-id') === cabangFilter)
            && (!jenjangFilter || item.getAttribute('data-jenjang') === jenjangFilter)
            && (!searchText || (item.getAttribute('data-name') || '').includes(searchText));

        setVisible(item, visible, 'block');
    });
};

const updateTempSelection = () => {
    const count = document.querySelectorAll('.kelas-checkbox:checked').length;
    const counter = document.getElementById('selectedCount');
    if (counter) {
        counter.textContent = count;
    }
};

const openKelasModal = () => {
    const select = document.getElementById('kelasSelect');
    const selectedValues = Array.from(select?.selectedOptions || []).map((option) => option.value);

    document.querySelectorAll('.kelas-checkbox').forEach((checkbox) => {
        checkbox.checked = selectedValues.includes(checkbox.value);
    });

    updateTempSelection();
    new bootstrap.Modal(document.getElementById('kelasModal')).show();
};

const confirmKelasSelection = () => {
    const select = document.getElementById('kelasSelect');
    const checkboxes = document.querySelectorAll('.kelas-checkbox:checked');
    const selectedData = [];

    Array.from(select.options).forEach((option) => { option.selected = false; });

    checkboxes.forEach((checkbox) => {
        const option = select.querySelector(`option[value="${checkbox.value}"]`);
        if (option) {
            option.selected = true;
        }

        selectedData.push({
            id: checkbox.value,
            name: checkbox.getAttribute('data-name'),
            jenjang: checkbox.getAttribute('data-jenjang'),
        });
    });

    updateSelectedKelasUI(selectedData);
    select.dispatchEvent(new Event('change'));
    bootstrap.Modal.getInstance(document.getElementById('kelasModal'))?.hide();
};

document.addEventListener('DOMContentLoaded', () => {
    const kelasSelect = document.getElementById('kelasSelect');
    const hariSelect = document.getElementById('hariSelect');
    const istirahatCollapse = document.getElementById('istirahatCollapse');
    const collapseBtn = document.querySelector('[data-bs-target="#istirahatCollapse"]');
    const tahunAjaranSelect = document.getElementById('tahunAjaranSelect');

    // Daftar kelas dirender di server berdasarkan tahun ajaran saat halaman dimuat.
    // Reload halaman saat tahun ajaran diganti agar daftar kelas tidak "nyangkut"
    // ke tahun ajaran lama (kelas dari tahun lain punya id berbeda meski nama sama).
    tahunAjaranSelect?.addEventListener('change', function () {
        const createUrl = this.dataset.createUrl;
        if (createUrl) {
            window.location.href = `${createUrl}?tahun_ajaran_id=${this.value}`;
        }
    });

    istirahatCollapse?.addEventListener('show.bs.collapse', () => collapseBtn?.classList.add('is-expanded'));
    istirahatCollapse?.addEventListener('hide.bs.collapse', () => collapseBtn?.classList.remove('is-expanded'));

    kelasSelect?.addEventListener('change', () => {
        renderMapelSections();
        filterIstirahatDisplay();
        filterGuruByCabang();
    });

    hariSelect?.addEventListener('change', filterIstirahatDisplay);

    document.querySelector('[data-open-kelas-modal]')?.addEventListener('click', openKelasModal);
    document.querySelector('[data-open-guru-modal]')?.addEventListener('click', () => {
        new bootstrap.Modal(document.getElementById('guruModal')).show();
    });

    document.getElementById('searchGuru')?.addEventListener('input', filterGuruList);
    document.getElementById('filterCabang')?.addEventListener('change', filterKelasList);
    document.getElementById('filterJenjang')?.addEventListener('change', filterKelasList);
    document.getElementById('searchKelas')?.addEventListener('input', filterKelasList);

    document.querySelectorAll('.guru-option-item').forEach((option) => {
        option.addEventListener('click', () => {
            setGuru(option.dataset.id, option.dataset.displayName || option.dataset.name, option.dataset.cabangName || '-');
            bootstrap.Modal.getInstance(document.getElementById('guruModal'))?.hide();
        });
    });

    document.querySelector('[data-clear-guru]')?.addEventListener('click', clearGuruSelection);
    document.querySelectorAll('.kelas-checkbox').forEach((checkbox) => checkbox.addEventListener('change', updateTempSelection));
    document.querySelector('[data-confirm-kelas]')?.addEventListener('click', confirmKelasSelection);

    document.getElementById('guruModal')?.addEventListener('shown.bs.modal', () => {
        const searchInput = document.getElementById('searchGuru');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }

        filterGuruByCabang();
        filterGuruList();
    });

    if (kelasSelect?.selectedOptions.length > 0) {
        updateSelectedKelasUI(getSelectedKelasData());
        kelasSelect.dispatchEvent(new Event('change'));
    }
});
