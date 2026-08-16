import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const fireAlert = (options) => Swal.fire(options);

const visibleSiswaCheckboxes = () => Array.from(document.querySelectorAll('[data-siswa-checkbox]'))
    .filter((checkbox) => !checkbox.closest('.siswa-row')?.classList.contains('is-hidden'));

const visibleKelasCheckboxes = () => Array.from(document.querySelectorAll('.kelas-checkbox-item'))
    .filter((item) => !item.classList.contains('is-hidden'))
    .map((item) => item.querySelector('.kelas-checkbox'))
    .filter(Boolean);

const updateSelectedCount = () => {
    const selectedCount = document.getElementById('selectedCount');
    const checked = document.querySelectorAll('[data-siswa-checkbox]:checked').length;

    if (selectedCount) {
        selectedCount.textContent = `${checked} siswa dipilih`;
    }
};

const updateSelectAllSiswaUI = () => {
    const checkboxes = visibleSiswaCheckboxes();
    const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

    document.querySelectorAll('[data-select-all-siswa]').forEach((selectAll) => {
        selectAll.disabled = checkboxes.length === 0;
        selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    });

    updateSelectedCount();
};

const setSiswaSelection = (checked) => {
    visibleSiswaCheckboxes().forEach((checkbox) => {
        checkbox.checked = checked;
    });

    document.querySelectorAll('[data-select-all-siswa]').forEach((selectAll) => {
        selectAll.checked = checked;
        selectAll.indeterminate = false;
    });

    updateSelectedCount();
};

const filterSiswa = () => {
    const filterCabang = document.getElementById('filterCabang');
    const filterJenjang = document.getElementById('filterJenjang');
    const filterKelas = document.getElementById('filterKelas');
    const searchInput = document.getElementById('searchSiswa');

    if (!filterCabang || !filterJenjang || !filterKelas || !searchInput) {
        return;
    }

    const cabangId = filterCabang.value;
    const jenjangValue = filterJenjang.value;
    const kelasId = filterKelas.value;
    const searchTerm = searchInput.value.toLowerCase();

    document.querySelectorAll('.siswa-row').forEach((row) => {
        const matchesCabang = !cabangId || row.dataset.cabang === cabangId;
        const matchesJenjang = !jenjangValue || row.dataset.jenjang === jenjangValue;
        const matchesKelas = !kelasId || row.dataset.kelas === kelasId;
        const matchesSearch = !searchTerm || (row.dataset.search || '').includes(searchTerm);

        row.classList.toggle('is-hidden', !(matchesCabang && matchesJenjang && matchesKelas && matchesSearch));
    });

    updateSelectAllSiswaUI();
};

const resetOptionVisibility = (select) => {
    select?.querySelectorAll('option').forEach((option) => {
        option.hidden = false;
    });
};

const initSiswaFilters = () => {
    const filterCabang = document.getElementById('filterCabang');
    const filterJenjang = document.getElementById('filterJenjang');
    const filterKelas = document.getElementById('filterKelas');
    const searchInput = document.getElementById('searchSiswa');

    if (!filterCabang || !filterJenjang || !filterKelas || !searchInput) {
        return;
    }

    filterCabang.addEventListener('change', () => {
        const selectedCabang = filterCabang.value;
        filterJenjang.value = '';
        filterKelas.value = '';
        resetOptionVisibility(filterJenjang);
        resetOptionVisibility(filterKelas);

        if (!selectedCabang) {
            filterJenjang.disabled = true;
            filterKelas.disabled = true;
            filterSiswa();
            return;
        }

        const availableJenjang = new Set();
        filterKelas.querySelectorAll('option').forEach((option) => {
            if (option.value && option.dataset.cabang === selectedCabang) {
                availableJenjang.add(option.dataset.jenjang);
            }
        });

        filterJenjang.querySelectorAll('option').forEach((option) => {
            option.hidden = Boolean(option.value) && !availableJenjang.has(option.value);
        });

        filterKelas.querySelectorAll('option').forEach((option) => {
            option.hidden = Boolean(option.value) && option.dataset.cabang !== selectedCabang;
        });

        filterJenjang.disabled = false;
        filterKelas.disabled = false;
        filterSiswa();
    });

    filterJenjang.addEventListener('change', () => {
        const selectedCabang = filterCabang.value;
        const selectedJenjang = filterJenjang.value;

        filterKelas.value = '';

        filterKelas.querySelectorAll('option').forEach((option) => {
            const matchesCabang = !option.value || option.dataset.cabang === selectedCabang;
            const matchesJenjang = !selectedJenjang || option.dataset.jenjang === selectedJenjang;
            option.hidden = !(matchesCabang && matchesJenjang);
        });

        filterSiswa();
    });

    filterKelas.addEventListener('change', filterSiswa);
    searchInput.addEventListener('input', filterSiswa);
};

const updateTargetType = () => {
    const isKelas = document.getElementById('target_kelas')?.checked ?? true;

    document.getElementById('kelasSelection')?.classList.toggle('is-hidden', !isKelas);
    document.getElementById('siswaSelection')?.classList.toggle('is-hidden', isKelas);
};

const updateJumlahBulan = () => {
    const jumlahBulanSelect = document.getElementById('jumlah_bulan');
    const bulanMulai = document.getElementById('bulan_mulai');
    const totalBulanText = document.getElementById('totalBulanText');
    const bulanRangeInfo = document.getElementById('bulanRangeInfo');
    const startIndex = Math.max(0, bulanMulai?.selectedIndex || 0);
    const remainingMonths = Math.max(1, (bulanMulai?.options.length || 1) - startIndex);

    jumlahBulanSelect?.querySelectorAll('option').forEach((option) => {
        option.disabled = parseInt(option.value, 10) > remainingMonths;
    });

    if (parseInt(jumlahBulanSelect?.value || '1', 10) > remainingMonths) {
        jumlahBulanSelect.value = String(remainingMonths);
    }

    const jumlahBulan = jumlahBulanSelect?.value || '1';
    const endIndex = Math.min((bulanMulai?.options.length || 1) - 1, startIndex + parseInt(jumlahBulan, 10) - 1);

    if (totalBulanText) {
        totalBulanText.textContent = jumlahBulan;
    }

    if (bulanRangeInfo) {
        const startLabel = bulanMulai?.options[startIndex]?.textContent.trim() || '-';
        const endLabel = bulanMulai?.options[endIndex]?.textContent.trim() || '-';
        bulanRangeInfo.innerHTML = `<i class="fas fa-calendar me-1"></i>${startLabel} - ${endLabel}`;
    }
};

const updateTipeSpp = () => {
    const isSetahun = document.getElementById('spp_setahun')?.checked ?? true;
    const jumlahBulanSection = document.getElementById('jumlahBulanSection');
    const totalBulanText = document.getElementById('totalBulanText');

    jumlahBulanSection?.classList.toggle('is-hidden', isSetahun);

    if (isSetahun) {
        const bulanMulai = document.getElementById('bulan_mulai');
        if (bulanMulai) {
            bulanMulai.selectedIndex = 0;
        }
        if (totalBulanText) {
            totalBulanText.textContent = String(document.getElementById('bulan_mulai')?.options.length || 12);
        }
        return;
    }

    updateJumlahBulan();
};

const updateSelectAllRowVisibility = () => {
    const selectAllRow = document.querySelector('.select-all-row');

    if (!selectAllRow) {
        return;
    }

    selectAllRow.classList.toggle('mobile-visible', window.innerWidth <= 768);
};

const filterKelasItems = () => {
    const filterCabangClass = document.getElementById('filterCabangClass');
    const filterJenjangClass = document.getElementById('filterJenjangClass');
    const selectedCabang = filterCabangClass?.value;
    const selectedJenjang = filterJenjangClass?.value;

    document.querySelectorAll('.kelas-checkbox-item').forEach((item) => {
        const matchesCabang = !selectedCabang || item.dataset.cabang === selectedCabang;
        const matchesJenjang = !selectedJenjang || item.dataset.jenjang === selectedJenjang;

        item.classList.toggle('is-hidden', !(matchesCabang && matchesJenjang));
    });

    updateSelectAllKelasUI();
};

const updateSelectedKelasCount = () => {
    const kelasSelectedCount = document.getElementById('kelasSelectedCount');
    const checked = document.querySelectorAll('.kelas-checkbox:checked').length;

    if (kelasSelectedCount) {
        kelasSelectedCount.textContent = `${checked} kelas dipilih`;
    }
};

const updateSelectAllKelasUI = () => {
    const selectAll = document.getElementById('selectAllClass');
    const checkboxes = visibleKelasCheckboxes();
    const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

    if (selectAll) {
        selectAll.disabled = checkboxes.length === 0;
        selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    }

    updateSelectedKelasCount();
};

const setKelasSelection = (checked) => {
    visibleKelasCheckboxes().forEach((checkbox) => {
        checkbox.checked = checked;
    });

    updateSelectAllKelasUI();
};

const confirmGenerate = () => {
    const targetType = document.querySelector('input[name="target_type"]:checked')?.value || 'kelas';
    const tipeSpp = document.querySelector('input[name="tipe_spp"]:checked')?.value || 'setahun';
    const jumlahInput = document.getElementById('jumlah_spp');
    const jumlahDisplay = jumlahInput?.value || '';
    const jumlahRaw = jumlahDisplay.replace(/\./g, '');
    let targetCount;

    if (targetType === 'kelas') {
        const checkedKelas = document.querySelectorAll('.kelas-checkbox:checked');

        if (checkedKelas.length === 0) {
            fireAlert({
                icon: 'warning',
                title: 'Pilih Kelas',
                text: 'Silakan pilih minimal satu kelas terlebih dahulu!',
                confirmButtonColor: '#696cff',
            });
            return;
        }

        targetCount = `${checkedKelas.length} kelas terpilih`;
    } else {
        const checkedSiswa = document.querySelectorAll('[data-siswa-checkbox]:checked');

        if (checkedSiswa.length === 0) {
            fireAlert({
                icon: 'warning',
                title: 'Belum ada siswa',
                text: 'Silakan pilih minimal satu siswa!',
                confirmButtonColor: '#696cff',
            });
            return;
        }

        targetCount = `${checkedSiswa.length} siswa terpilih`;
    }

    if (!jumlahRaw || jumlahRaw === '0') {
        fireAlert({
            icon: 'warning',
            title: 'Nominal Kosong',
            text: 'Silakan isi jumlah SPP per bulan!',
            confirmButtonColor: '#696cff',
        });
        return;
    }

    const totalBulan = tipeSpp === 'setahun'
        ? String(document.getElementById('bulan_mulai')?.options.length || 12)
        : document.getElementById('jumlah_bulan')?.value || '1';
    const infoText = tipeSpp === 'setahun'
        ? `Proses ini akan membuat tagihan untuk satu tahun ajaran penuh (${totalBulan} bulan).`
        : `Proses ini akan membuat tagihan untuk ${totalBulan} bulan.`;

    fireAlert({
        title: 'Konfirmasi Generate SPP',
        html: `Anda akan membuat <strong>${totalBulan} tagihan SPP</strong> dengan nominal <strong>Rp ${jumlahDisplay}</strong> untuk <strong>${targetCount}</strong>.<br><br><small class="text-muted">${infoText}</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#696cff',
        cancelButtonColor: '#8592a3',
        confirmButtonText: 'Ya, Generate SPP',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            if (jumlahInput) {
                jumlahInput.value = jumlahRaw || '0';
            }

            document.getElementById('generateSppForm')?.submit();
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initSiswaFilters();
    updateTargetType();
    updateTipeSpp();
    updateSelectAllRowVisibility();
    updateSelectAllSiswaUI();
    updateSelectAllKelasUI();
    updateJumlahBulan();

    document.querySelectorAll('input[name="target_type"]').forEach((radio) => {
        radio.addEventListener('change', updateTargetType);
    });

    document.querySelectorAll('input[name="tipe_spp"]').forEach((radio) => {
        radio.addEventListener('change', updateTipeSpp);
    });

    document.querySelectorAll('[data-select-all-siswa]').forEach((checkbox) => {
        checkbox.addEventListener('change', () => setSiswaSelection(checkbox.checked));
    });

    document.querySelectorAll('[data-siswa-checkbox]').forEach((checkbox) => {
        checkbox.addEventListener('change', updateSelectAllSiswaUI);
    });

    document.getElementById('selectAllClass')?.addEventListener('change', (event) => {
        setKelasSelection(event.currentTarget.checked);
    });

    document.querySelectorAll('.kelas-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', updateSelectAllKelasUI);
    });

    document.getElementById('filterCabangClass')?.addEventListener('change', filterKelasItems);
    document.getElementById('filterJenjangClass')?.addEventListener('change', filterKelasItems);
    document.getElementById('jumlah_bulan')?.addEventListener('change', updateJumlahBulan);
    document.getElementById('bulan_mulai')?.addEventListener('change', () => {
        if (!document.getElementById('spp_setahun')?.checked) {
            updateJumlahBulan();
        }
    });
    document.querySelector('[data-confirm-generate]')?.addEventListener('click', confirmGenerate);
});

window.addEventListener('resize', updateSelectAllRowVisibility);
