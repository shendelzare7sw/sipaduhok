import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const fireAlert = (options) => Swal.fire(options);

const escapeHtml = (value) => String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

const getStudentCheckboxes = () => Array.from(document.querySelectorAll('.student-checkbox'));

const getVisibleStudentCheckboxes = () => Array.from(document.querySelectorAll('.siswa-row'))
    .filter((row) => !row.classList.contains('is-hidden'))
    .map((row) => row.querySelector('.student-checkbox'))
    .filter(Boolean);

const syncSelectAllCheckboxes = (checked) => {
    document.querySelectorAll('.student-select-all').forEach((checkbox) => {
        checkbox.checked = checked;
        checkbox.indeterminate = false;
    });
};

const updateSelectAllState = () => {
    const visibleCheckboxes = getVisibleStudentCheckboxes();
    const checkedVisible = visibleCheckboxes.filter((checkbox) => checkbox.checked);
    const allVisibleChecked = visibleCheckboxes.length > 0 && checkedVisible.length === visibleCheckboxes.length;
    const someVisibleChecked = checkedVisible.length > 0 && !allVisibleChecked;

    document.querySelectorAll('.student-select-all').forEach((checkbox) => {
        checkbox.checked = allVisibleChecked;
        checkbox.indeterminate = someVisibleChecked;
    });
};

const updateSelectedCount = () => {
    const checkedCount = getStudentCheckboxes().filter((checkbox) => checkbox.checked).length;
    const selectedCount = document.getElementById('selectedCount');
    const previewCount = document.getElementById('previewCount');

    if (selectedCount) {
        selectedCount.textContent = `${checkedCount} siswa dipilih`;
    }

    if (previewCount) {
        previewCount.textContent = checkedCount;
    }

    updateSelectAllState();
};

const setOptionVisibility = (option, isVisible) => {
    option.hidden = !isVisible;

    if (option.value !== '') {
        option.disabled = !isVisible;
    }
};

const filterSiswa = () => {
    const cabangId = document.getElementById('filterCabang')?.value || '';
    const jenjangVal = document.getElementById('filterJenjang')?.value || '';
    const kelasId = document.getElementById('filterKelas')?.value || '';
    const searchTerm = (document.getElementById('searchSiswa')?.value || '').toLowerCase();

    document.querySelectorAll('.siswa-row').forEach((row) => {
        const matchesCabang = !cabangId || row.dataset.cabang === cabangId;
        const matchesJenjang = !jenjangVal || row.dataset.jenjang === jenjangVal;
        const matchesKelas = !kelasId || row.dataset.kelas === kelasId;
        const matchesSearch = !searchTerm || row.dataset.search?.includes(searchTerm);

        row.classList.toggle('is-hidden', !(matchesCabang && matchesJenjang && matchesKelas && matchesSearch));
    });

    updateSelectAllState();
};

const handleCabangChange = (event) => {
    const filterJenjang = document.getElementById('filterJenjang');
    const filterKelas = document.getElementById('filterKelas');
    const selectedCabang = event.target.value;

    if (!filterJenjang || !filterKelas) {
        return;
    }

    filterJenjang.value = '';
    filterKelas.value = '';

    if (!selectedCabang) {
        filterJenjang.disabled = true;
        filterKelas.disabled = true;

        filterJenjang.querySelectorAll('option').forEach((option) => setOptionVisibility(option, true));
        filterKelas.querySelectorAll('option').forEach((option) => setOptionVisibility(option, true));
        filterSiswa();
        return;
    }

    const availableJenjang = new Set();
    filterKelas.querySelectorAll('option').forEach((option) => {
        if (option.value && option.dataset.cabang === selectedCabang) {
            availableJenjang.add(option.dataset.jenjang);
        }
    });

    filterJenjang.disabled = false;
    filterJenjang.querySelectorAll('option').forEach((option) => {
        setOptionVisibility(option, option.value === '' || availableJenjang.has(option.value));
    });

    filterKelas.disabled = false;
    filterKelas.querySelectorAll('option').forEach((option) => {
        setOptionVisibility(option, option.value === '' || option.dataset.cabang === selectedCabang);
    });

    filterSiswa();
};

const handleJenjangChange = (event) => {
    const filterCabang = document.getElementById('filterCabang');
    const filterKelas = document.getElementById('filterKelas');
    const selectedCabang = filterCabang?.value || '';
    const selectedJenjang = event.target.value;

    if (!filterKelas) {
        return;
    }

    filterKelas.value = '';
    filterKelas.disabled = !selectedCabang;
    filterKelas.querySelectorAll('option').forEach((option) => {
        const matchesCabang = option.value === '' || option.dataset.cabang === selectedCabang;
        const matchesJenjang = !selectedJenjang || option.dataset.jenjang === selectedJenjang;
        setOptionVisibility(option, option.value === '' || (matchesCabang && matchesJenjang));
    });

    filterSiswa();
};

const setVisibleStudentsChecked = (checked) => {
    getVisibleStudentCheckboxes().forEach((checkbox) => {
        checkbox.checked = checked;
    });

    syncSelectAllCheckboxes(checked);
    updateSelectedCount();
};

const updateSelectAllRowVisibility = () => {
    document.querySelector('.select-all-row')?.classList.toggle('mobile-visible', window.innerWidth <= 768);
};

const prepareCurrencyInputs = (form) => {
    if (window.CurrencyFormatter?.prepareFormData) {
        window.CurrencyFormatter.prepareFormData(form);
        return;
    }

    form.querySelectorAll('.currency-input').forEach((input) => {
        input.value = input.value.replace(/\./g, '') || '0';
    });
};

const handleSubmitClick = () => {
    const form = document.getElementById('customTagihanForm');
    const checked = getStudentCheckboxes().filter((checkbox) => checkbox.checked);
    const jenisTagihanInput = document.getElementById('jenis_tagihan');
    const jumlahInput = document.getElementById('jumlah');
    const jenisTagihan = jenisTagihanInput?.value.trim() || '';
    const jumlah = jumlahInput?.value.trim() || '';
    const jumlahRaw = Number(String(jumlah).replace(/\./g, '').replace(/\D/g, '')) || 0;

    if (!form) {
        return;
    }

    if (checked.length === 0) {
        fireAlert({
            icon: 'warning',
            title: 'Belum ada siswa!',
            text: 'Silakan pilih minimal satu siswa dari daftar.',
            confirmButtonText: 'Oke',
            confirmButtonColor: '#696cff',
            customClass: {
                confirmButton: 'btn btn-primary btn-confirm-swal',
            },
            buttonsStyling: false,
        });
        return;
    }

    if (!jenisTagihan) {
        fireAlert({
            icon: 'warning',
            title: 'Jenis Tagihan Kosong',
            text: 'Silakan isi jenis tagihan terlebih dahulu.',
            confirmButtonText: 'Oke',
            confirmButtonColor: '#696cff',
            customClass: {
                confirmButton: 'btn btn-primary btn-confirm-swal',
            },
            buttonsStyling: false,
        });
        jenisTagihanInput?.focus();
        return;
    }

    if (jumlahRaw <= 0) {
        fireAlert({
            icon: 'warning',
            title: 'Nominal Kosong',
            text: 'Silakan isi jumlah tagihan dengan benar.',
            confirmButtonText: 'Oke',
            confirmButtonColor: '#696cff',
            customClass: {
                confirmButton: 'btn btn-primary btn-confirm-swal',
            },
            buttonsStyling: false,
        });
        jumlahInput?.focus();
        return;
    }

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    fireAlert({
        title: 'Konfirmasi Tagihan',
        html: `<div class="text-center mt-3">
            <p class="mb-2">Anda akan membuat tagihan "<strong>${escapeHtml(jenisTagihan)}</strong>"</p>
            <p class="mb-2">dengan nominal <strong class="text-primary fs-5">Rp ${escapeHtml(jumlah)}</strong></p>
            <p class="mb-3">untuk <strong class="text-success">${checked.length}</strong> siswa.</p>
            <div class="alert alert-warning py-2 mb-0 mt-4 confirmation-note">
                <i class="fas fa-exclamation-triangle me-1"></i> Proses ini akan langsung tersimpan.
            </div>
        </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save me-1"></i> Ya, Simpan',
        cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
        confirmButtonColor: '#696cff',
        cancelButtonColor: '#8592a3',
        customClass: {
            confirmButton: 'btn btn-primary me-2',
            cancelButton: 'btn btn-secondary',
        },
        buttonsStyling: false,
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            prepareCurrencyInputs(form);
            form.submit();
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('filterCabang')?.addEventListener('change', handleCabangChange);
    document.getElementById('filterJenjang')?.addEventListener('change', handleJenjangChange);
    document.getElementById('filterKelas')?.addEventListener('change', filterSiswa);
    document.getElementById('searchSiswa')?.addEventListener('input', filterSiswa);
    document.querySelector('[data-submit-custom-tagihan]')?.addEventListener('click', handleSubmitClick);

    document.querySelectorAll('.student-select-all').forEach((checkbox) => {
        checkbox.addEventListener('change', (event) => {
            setVisibleStudentsChecked(event.target.checked);
        });
    });

    getStudentCheckboxes().forEach((checkbox) => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    updateSelectAllRowVisibility();
    updateSelectedCount();
    filterSiswa();
});

window.addEventListener('resize', updateSelectAllRowVisibility);
