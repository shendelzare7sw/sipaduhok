const getManageConfig = () => {
    const config = document.getElementById('manageSiswaConfig');
    return {
        sisaKuota: Number(config?.dataset.sisaKuota || 0),
    };
};

const updateSelectedCount = () => {
    const { sisaKuota } = getManageConfig();
    const checked = document.querySelectorAll('.siswa-checkbox:checked').length;
    const selectedCount = document.getElementById('selectedCount');
    const addButton = document.getElementById('btnAddSiswa');

    if (selectedCount) {
        selectedCount.textContent = checked;
    }

    if (!addButton) {
        return;
    }

    addButton.disabled = checked === 0 || checked > sisaKuota;

    if (checked > sisaKuota) {
        addButton.textContent = 'Melebihi Kuota!';
        return;
    }

    addButton.innerHTML = '<i class="fas fa-plus"></i> Tambahkan ke Kelas';
};

const filterTable = (input, tableId) => {
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);

    if (!table) {
        return;
    }

    table.querySelectorAll('tbody tr').forEach((row) => {
        const nama = row.getAttribute('data-nama') || '';
        row.style.display = nama.includes(filter) ? '' : 'none';
    });
};

const bindCheckboxes = () => {
    const { sisaKuota } = getManageConfig();
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    const selectAll = document.getElementById('selectAll');

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            updateSelectedCount();
            if (selectAll) {
                selectAll.checked = document.querySelectorAll('.siswa-checkbox:checked').length === checkboxes.length;
            }
        });
    });

    selectAll?.addEventListener('change', () => {
        const maxSelect = Math.min(checkboxes.length, sisaKuota);
        checkboxes.forEach((checkbox, index) => {
            checkbox.checked = selectAll.checked && index < maxSelect;
        });
        updateSelectedCount();
    });
};

const bindSearch = () => {
    document.getElementById('searchInKelas')?.addEventListener('input', (event) => {
        filterTable(event.currentTarget, 'tableInKelas');
    });

    document.getElementById('searchAvailable')?.addEventListener('input', (event) => {
        filterTable(event.currentTarget, 'tableAvailable');
    });
};

const bindDeleteModal = () => {
    const siswaName = document.getElementById('siswaName');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const deleteModal = document.getElementById('deleteModal');
    let deleteFormId = null;

    document.querySelectorAll('[data-remove-siswa]').forEach((button) => {
        button.addEventListener('click', () => {
            deleteFormId = button.dataset.formId;
            if (siswaName) {
                siswaName.textContent = button.dataset.siswaName || '';
            }
            new bootstrap.Modal(deleteModal).show();
        });
    });

    confirmDeleteBtn?.addEventListener('click', () => {
        if (deleteFormId) {
            document.getElementById(`deleteForm${deleteFormId}`)?.submit();
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    bindCheckboxes();
    bindSearch();
    bindDeleteModal();
    updateSelectedCount();
});
