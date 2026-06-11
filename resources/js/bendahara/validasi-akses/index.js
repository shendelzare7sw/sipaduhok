document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-validasi-akses]');
    const csrfToken = shell?.dataset.csrfToken || '';
    const actionForm = document.createElement('form');
    const confirmModalElement = document.getElementById('confirmModal');
    const alertModalElement = document.getElementById('alertModal');
    const confirmModal = confirmModalElement ? new bootstrap.Modal(confirmModalElement) : null;
    const alertModal = alertModalElement ? new bootstrap.Modal(alertModalElement) : null;
    let confirmCallback = null;

    actionForm.id = 'action-form';
    actionForm.method = 'POST';
    actionForm.classList.add('d-none');

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    actionForm.appendChild(csrfInput);
    document.body.appendChild(actionForm);

    const showConfirmModal = (title, message, callback) => {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;
        confirmCallback = callback;
        confirmModal?.show();
    };

    const showAlertModal = (title, message) => {
        document.getElementById('alertTitle').textContent = title;
        document.getElementById('alertMessage').textContent = message;
        alertModal?.show();
    };

    const getSelectedCheckboxes = () => Array.from(document.querySelectorAll('.siswa-checkbox:checked'));

    const updateSelectedCount = () => {
        const selectedCount = document.getElementById('selectedCount');
        if (selectedCount) {
            selectedCount.textContent = getSelectedCheckboxes().length;
        }
    };

    const updateBtnValidasiRapor = () => {
        const checked = getSelectedCheckboxes();
        const hasEligible = checked.some((checkbox) => checkbox.dataset.ketuaApproved === '1');
        const btn = document.getElementById('btn-bulk-rapor');
        const icon = document.getElementById('btn-bulk-rapor-icon');
        const label = document.getElementById('btn-bulk-rapor-label');

        if (!btn || !icon || !label) {
            return;
        }

        if (hasEligible) {
            btn.className = 'btn btn-info btn-sm btn-soft text-white';
            btn.title = 'Validasi akses rapor siswa yang sudah di-approve Ketua';
            icon.className = 'fas fa-file-alt';
            label.classList.add('d-none');
        } else {
            btn.className = 'btn btn-outline-secondary btn-sm btn-soft';
            btn.title = 'Belum ada siswa yang di-approve Ketua';
            icon.className = 'fas fa-lock';
            label.classList.remove('d-none');
        }
    };

    const handleSelectionChange = () => {
        const selectAll = document.getElementById('select-all');
        const selectAllMobile = document.getElementById('select-all-mobile');
        const checkboxes = Array.from(document.querySelectorAll('.siswa-checkbox'));
        const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;
        const isAllChecked = checkboxes.length > 0 && checkedCount === checkboxes.length;
        const isSomeChecked = checkedCount > 0 && checkedCount < checkboxes.length;

        [selectAll, selectAllMobile].forEach((checkbox) => {
            if (checkbox) {
                checkbox.checked = isAllChecked;
                checkbox.indeterminate = isSomeChecked;
            }
        });

        updateSelectedCount();
        updateBtnValidasiRapor();
    };

    const setAllSelection = (checked) => {
        document.querySelectorAll('.siswa-checkbox').forEach((checkbox) => {
            checkbox.checked = checked;
        });
        handleSelectionChange();
    };

    const bulkValidasiUjian = () => {
        const checked = getSelectedCheckboxes();
        if (checked.length === 0) {
            showAlertModal('Data Belum Dipilih', 'Silakan pilih minimal satu siswa terlebih dahulu!');
            return;
        }

        showConfirmModal('Validasi Akses Ujian', `Validasi akses ujian untuk ${checked.length} siswa terpilih?`, () => {
            document.getElementById('bulk-action').value = 'ujian';
            document.getElementById('bulk-form').submit();
        });
    };

    const bulkValidasiRapor = () => {
        const checked = getSelectedCheckboxes();
        if (checked.length === 0) {
            showAlertModal('Data Belum Dipilih', 'Silakan pilih minimal satu siswa terlebih dahulu!');
            return;
        }

        const eligible = checked.filter((checkbox) => checkbox.dataset.ketuaApproved === '1');
        const notEligible = checked.length - eligible.length;

        if (eligible.length === 0) {
            showAlertModal('Tidak Bisa Validasi Rapor', 'Semua siswa terpilih belum di-approve Ketua PKBM. Validasi rapor hanya bisa dilakukan setelah Ketua menyetujui.');
            return;
        }

        let message = `Validasi akses rapor untuk ${eligible.length} siswa yang sudah di-approve Ketua?`;
        if (notEligible > 0) {
            message += ` (${notEligible} siswa dilewati karena belum di-approve Ketua)`;
        }

        showConfirmModal('Validasi Akses Rapor', message, () => {
            document.getElementById('bulk-action').value = 'rapor';
            document.getElementById('bulk-form').submit();
        });
    };

    document.querySelectorAll('[data-auto-submit]').forEach((select) => {
        select.addEventListener('change', () => select.form?.submit());
    });

    document.getElementById('select-all')?.addEventListener('change', (event) => setAllSelection(event.target.checked));
    document.getElementById('select-all-mobile')?.addEventListener('change', (event) => setAllSelection(event.target.checked));

    document.querySelectorAll('.siswa-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', handleSelectionChange);
    });

    document.querySelector('[data-bulk-ujian]')?.addEventListener('click', bulkValidasiUjian);
    document.querySelector('[data-bulk-rapor]')?.addEventListener('click', bulkValidasiRapor);

    document.querySelectorAll('[data-confirm-action]').forEach((button) => {
        button.addEventListener('click', () => {
            showConfirmModal('Konfirmasi Aksi', button.dataset.message || 'Apakah Anda yakin?', () => {
                actionForm.action = button.dataset.url;
                actionForm.submit();
            });
        });
    });

    document.querySelectorAll('[data-confirm-class-action]').forEach((button) => {
        button.addEventListener('click', () => {
            showConfirmModal(button.dataset.title || 'Konfirmasi Aksi', button.dataset.message || 'Apakah Anda yakin?', () => {
                document.getElementById(button.dataset.formId)?.submit();
            });
        });
    });

    document.getElementById('confirmBtn')?.addEventListener('click', () => {
        if (confirmCallback) {
            confirmCallback();
            confirmCallback = null;
        }
        confirmModal?.hide();
    });

    document.getElementById('dispensasiModal')?.addEventListener('show.bs.modal', () => {
        const container = document.getElementById('dispensasi-siswa-ids');
        const submitDispensasi = document.getElementById('submitDispensasi');
        const checked = getSelectedCheckboxes();

        if (!container || !submitDispensasi) {
            return;
        }

        container.innerHTML = '';
        if (checked.length === 0) {
            submitDispensasi.disabled = true;
            container.innerHTML = '<span class="text-danger small">Pilih siswa terlebih dahulu!</span>';
            return;
        }

        submitDispensasi.disabled = false;
        checked.forEach((checkbox) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'siswa_ids[]';
            input.value = checkbox.value;
            container.appendChild(input);
        });
    });

    handleSelectionChange();
});
