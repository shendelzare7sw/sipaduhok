function getJadwalConfig() {
    const configEl = document.getElementById('jp-config');

    if (!configEl) {
        return window.JP_CONFIG || {};
    }

    return {
        bulkUpdateStatusUrl: configEl.dataset.bulkUpdateStatusUrl || '',
        bulkDeleteUrl: configEl.dataset.bulkDeleteUrl || '',
        csrfToken: configEl.dataset.csrfToken || '',
        currentTahunAjaranId: configEl.dataset.currentTahunAjaranId || '',
        exportExcelBaseUrl: configEl.dataset.exportExcelBaseUrl || '',
        printBaseUrl: configEl.dataset.printBaseUrl || '',
    };
}

function setVisibility(element, visible) {
    if (!element) return;
    element.classList.toggle('d-none', !visible);
}

function filterGuruOptions(jadwalId) {
    const searchInput = document.getElementById('searchGuru' + jadwalId);
    if (!searchInput) return;
    const searchTerm = searchInput.value.toLowerCase().trim();
    const guruList = document.querySelectorAll('#guruList' + jadwalId + ' .guru-opt-item[data-name]');

    guruList.forEach(item => {
        const name = item.getAttribute('data-name') || '';
        if (searchTerm === '' || name.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

function selectGuruForJadwal(jadwalId, guruId, guruName) {
    const input = document.getElementById('guruIdBaru' + jadwalId);
    const display = document.getElementById('selectedGuruDisplay' + jadwalId);
    const nameSpan = document.getElementById('selectedGuruName' + jadwalId);

    if (input) input.value = guruId;

    if (guruId === '' || (guruName && guruName.includes('Kosongkan'))) {
        setVisibility(display, false);
    } else {
        setVisibility(display, true);
        if (nameSpan) nameSpan.textContent = guruName;
    }
}

function clearGuruSelection(jadwalId) {
    const input = document.getElementById('guruIdBaru' + jadwalId);
    const display = document.getElementById('selectedGuruDisplay' + jadwalId);
    if (input) input.value = '';
    setVisibility(display, false);
}

function showToast(icon, title) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: icon,
            title: title,
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    } else {
        alert(title);
    }
}

// Toggle select all (desktop thead checkbox)
function toggleSelectAllJadwal() {
    const selectAll = document.getElementById('select-all-jadwal');
    if (!selectAll) return;
    const checkboxes = document.querySelectorAll('.jadwal-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    const mobileSelectAll = document.getElementById('mobile-select-all-jadwal');
    if (mobileSelectAll) mobileSelectAll.checked = selectAll.checked;
    updateBulkButtons();
}

// Toggle select all (mobile "Pilih Semua" checkbox)
function mobileToggleSelectAll() {
    const mobileSelectAll = document.getElementById('mobile-select-all-jadwal');
    if (!mobileSelectAll) return;
    const checkboxes = document.querySelectorAll('.jadwal-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = mobileSelectAll.checked;
    });
    const desktopSelectAll = document.getElementById('select-all-jadwal');
    if (desktopSelectAll) desktopSelectAll.checked = mobileSelectAll.checked;
    updateBulkButtons();
}

// Update bulk action buttons visibility + card highlight
function updateBulkButtons() {
    const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
    const count = checkboxes.length;
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkStatusBtn = document.getElementById('bulkStatusBtn');
    const selectedCount = document.getElementById('selectedCount');
    const selectedInfo = document.getElementById('selectedInfo');

    setVisibility(bulkDeleteBtn, count > 0);
    setVisibility(bulkStatusBtn, count > 0);
    if (selectedCount) selectedCount.textContent = count;
    setVisibility(selectedInfo, count > 0);

    // Sync desktop select-all checkbox state
    const selectAll = document.getElementById('select-all-jadwal');
    const allCheckboxes = document.querySelectorAll('.jadwal-checkbox');
    const allChecked = allCheckboxes.length > 0 && count === allCheckboxes.length;
    
    if (selectAll) selectAll.checked = allChecked;
    const mobileSelectAll = document.getElementById('mobile-select-all-jadwal');
    if (mobileSelectAll) mobileSelectAll.checked = allChecked;

    // Highlight selected cards on mobile
    allCheckboxes.forEach(cb => {
        const tr = cb.closest('tr');
        if (tr) tr.classList.toggle('card-selected', cb.checked);
    });
}

// Bulk delete function
function bulkDelete() {
    const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        showToast('warning', 'Pilih minimal 1 jadwal untuk dihapus');
        return;
    }

    // Set count and IDs
    const bulkDeleteCount = document.getElementById('bulkDeleteCount');
    const bulkDeleteIds = document.getElementById('bulk-delete-ids');
    
    if (bulkDeleteCount) bulkDeleteCount.textContent = ids.length;
    if (bulkDeleteIds) bulkDeleteIds.value = JSON.stringify(ids);

    // Build item list in modal
    const listEl = document.getElementById('bulkDeleteList');
    if (listEl) {
        listEl.innerHTML = Array.from(checkboxes).map(cb => `
            <div class="jp-flex-center-gap jp-schedule-info">
                <div class="jp-icon-box jp-icon-box-danger">
                    <i class="fas fa-book text-danger"></i>
                </div>
                <div class="jp-min-w-0 flex-grow-1">
                    <div class="fw-semibold text-truncate">${cb.dataset.mapel || 'Jadwal'}</div>
                    <div class="text-muted small">
                        <i class="fas fa-school me-1"></i>${cb.dataset.kelas || '-'}
                        &nbsp;&middot;&nbsp;
                        <i class="fas fa-calendar-day me-1"></i>${cb.dataset.hari || '-'}, ${cb.dataset.jam || '-'}
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Show modal
    const modalEl = document.getElementById('bulkDeleteModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

// Bulk update status function - shows modal
function bulkUpdateStatus() {
    const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        showToast('warning', 'Pilih minimal 1 jadwal untuk diubah statusnya');
        return;
    }

    // Set count in modal
    const bulkStatusCount = document.getElementById('bulkStatusCount');
    if (bulkStatusCount) bulkStatusCount.textContent = ids.length;

    // Store IDs temporarily in a data attribute
    const bulkStatusModal = document.getElementById('bulkStatusModal');
    if (bulkStatusModal) {
        bulkStatusModal.setAttribute('data-selected-ids', JSON.stringify(ids));
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(bulkStatusModal);
            modal.show();
        }
    }
}

// Submit bulk status update after modal confirmation
function submitBulkStatus() {
    const config = getJadwalConfig();
    const modalEl = document.getElementById('bulkStatusModal');
    if (!modalEl) return;
    
    const idsJson = modalEl.getAttribute('data-selected-ids');

    if (!idsJson) {
        showToast('error', 'Data tidak valid');
        return;
    }

    const statusRadio = document.querySelector('input[name="status_choice"]:checked');
    if (!statusRadio) {
        showToast('warning', 'Pilih status terlebih dahulu');
        return;
    }

    const status = statusRadio.value;

    // Create and submit form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = config.bulkUpdateStatusUrl || '';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = config.csrfToken || '';
    form.appendChild(csrfInput);

    const idsInput = document.createElement('input');
    idsInput.type = 'hidden';
    idsInput.name = 'jadwal_ids';
    idsInput.value = idsJson;
    form.appendChild(idsInput);

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    form.appendChild(statusInput);

    document.body.appendChild(form);
    form.submit();
}

function filterPrintKelas() {
    const cabangId = document.getElementById('printCabangId').value;
    const kelasSelect = document.getElementById('printKelasId');
    if (!kelasSelect) return;
    const options = kelasSelect.querySelectorAll('option[data-cabang]');

    kelasSelect.value = "";
    kelasSelect.disabled = cabangId === "";

    options.forEach(opt => {
        if (cabangId === "" || opt.getAttribute('data-cabang') == cabangId) {
            opt.classList.remove('jp-hidden-option');
        } else {
            opt.classList.add('jp-hidden-option');
            if (opt.selected) kelasSelect.value = "";
        }
    });
}

function submitCetakKelas(type) {
    const config = getJadwalConfig();
    const kelasId = document.getElementById('printKelasId').value;
    if (!kelasId) {
        showToast('warning', 'Silakan pilih kelas terlebih dahulu!');
        return;
    }

    let url = "";
    const tahunAjaranParam = "?tahun_ajaran_id=" + (config.currentTahunAjaranId || "");

    if (type === 'excel') {
        url = (config.exportExcelBaseUrl || "") + "/" + kelasId + "/export-excel" + tahunAjaranParam;
    } else {
        url = (config.printBaseUrl || "") + "/" + kelasId + "/print" + tahunAjaranParam;
    }
    
    window.open(url, '_blank');
    
    const modalEl = document.getElementById('cetakKelasModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => field.form?.submit());
    });

    document.querySelector('[data-toggle-select-all-jadwal]')?.addEventListener('change', toggleSelectAllJadwal);
    document.querySelector('[data-mobile-toggle-select-all]')?.addEventListener('change', mobileToggleSelectAll);

    document.querySelectorAll('.jadwal-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', updateBulkButtons);
    });

    document.querySelector('[data-bulk-delete-trigger]')?.addEventListener('click', bulkDelete);
    document.querySelector('[data-bulk-status-trigger]')?.addEventListener('click', bulkUpdateStatus);
    document.querySelector('[data-submit-bulk-status]')?.addEventListener('click', submitBulkStatus);

    document.querySelectorAll('[data-guru-search]').forEach((input) => {
        input.addEventListener('input', () => filterGuruOptions(input.dataset.jadwalId));
    });

    document.querySelectorAll('[data-clear-guru]').forEach((button) => {
        button.addEventListener('click', () => clearGuruSelection(button.dataset.jadwalId));
    });

    document.querySelectorAll('[data-select-guru]').forEach((item) => {
        item.addEventListener('click', () => {
            selectGuruForJadwal(item.dataset.jadwalId, item.dataset.guruId || '', item.dataset.guruName || '');
        });
    });

    document.querySelector('[data-filter-print-kelas]')?.addEventListener('change', filterPrintKelas);

    document.querySelectorAll('[data-submit-cetak-kelas]').forEach((button) => {
        button.addEventListener('click', () => submitCetakKelas(button.dataset.submitCetakKelas));
    });
});
