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
        if (display) display.style.display = 'none';
    } else {
        if (display) display.style.display = 'block';
        if (nameSpan) nameSpan.textContent = guruName;
    }
}

function clearGuruSelection(jadwalId) {
    const input = document.getElementById('guruIdBaru' + jadwalId);
    const display = document.getElementById('selectedGuruDisplay' + jadwalId);
    if (input) input.value = '';
    if (display) display.style.display = 'none';
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

    if (bulkDeleteBtn) bulkDeleteBtn.style.display = count > 0 ? 'inline-block' : 'none';
    if (bulkStatusBtn) bulkStatusBtn.style.display = count > 0 ? 'inline-block' : 'none';
    if (selectedCount) selectedCount.textContent = count;
    if (selectedInfo) selectedInfo.style.display = count > 0 ? 'flex' : 'none';

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
            <div style="display:flex; align-items:center; gap:10px; background:#f8f9fa; border-radius:8px; padding:10px 12px;">
                <div style="width:34px; height:34px; background:#fff0f0; border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-book text-danger" style="font-size:13px;"></i>
                </div>
                <div style="min-width:0; flex:1;">
                    <div style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${cb.dataset.mapel || 'Jadwal'}</div>
                    <div style="font-size:12px; color:#6c757d;">
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
    const config = window.JP_CONFIG || {};
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
            opt.style.display = "";
        } else {
            opt.style.display = "none";
            if (opt.selected) kelasSelect.value = "";
        }
    });
}

function submitCetakKelas(type) {
    const config = window.JP_CONFIG || {};
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
