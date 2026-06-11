const rowCheckboxes = () => Array.from(document.querySelectorAll('[data-row-checkbox]'));

const getSelectedRows = () => rowCheckboxes().filter((checkbox) => checkbox.checked);

const updateToolbar = () => {
    const selected = getSelectedRows();
    const selectedCount = document.getElementById('selectedCount');
    const resetToolbar = document.getElementById('resetToolbar');
    const allRows = rowCheckboxes();
    const isAllSelected = allRows.length > 0 && selected.length === allRows.length;
    const isIndeterminate = selected.length > 0 && selected.length < allRows.length;

    if (selectedCount) {
        selectedCount.textContent = selected.length;
    }

    resetToolbar?.classList.toggle('show', selected.length > 0);

    document.querySelectorAll('[data-select-all-tagihan]').forEach((selectAll) => {
        selectAll.checked = isAllSelected;
        selectAll.indeterminate = isIndeterminate;
    });

    allRows.forEach((checkbox) => {
        checkbox.closest('tr')?.classList.toggle('selected-row', checkbox.checked);
    });
};

const setAllRows = (checked) => {
    rowCheckboxes().forEach((checkbox) => {
        checkbox.checked = checked;
    });

    updateToolbar();
};

const clearSelection = () => {
    setAllRows(false);
};

const buildResetList = () => {
    const resetIds = document.getElementById('resetSiswaIds');
    const resetList = document.getElementById('resetSiswaList');
    const selected = getSelectedRows();
    const ids = [];

    if (!resetIds || !resetList) {
        return;
    }

    resetList.replaceChildren();

    selected.forEach((checkbox, index) => {
        ids.push(checkbox.value);

        const row = checkbox.closest('tr');
        const name = row?.querySelector('.student-name')?.textContent.trim() || `Siswa #${checkbox.value}`;
        const item = document.createElement('div');
        const icon = document.createElement('i');
        const label = document.createElement('span');

        item.className = `d-flex align-items-center py-1 ${index > 0 ? 'border-top' : ''}`;
        icon.className = 'fas fa-user-minus text-danger me-2';
        label.textContent = `${index + 1}. ${name}`;

        item.append(icon, label);
        resetList.appendChild(item);
    });

    resetIds.value = JSON.stringify(ids);
};

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const resetModal = document.getElementById('resetTagihanModal');

    searchInput?.addEventListener('input', () => {
        clearSearch?.classList.toggle('show', searchInput.value.length > 0);
    });

    clearSearch?.addEventListener('click', () => {
        if (!searchInput) {
            return;
        }

        searchInput.value = '';
        clearSearch.classList.remove('show');
        searchInput.focus();
    });

    document.querySelectorAll('[data-auto-submit]').forEach((input) => {
        input.addEventListener('change', () => input.form?.submit());
    });

    document.querySelectorAll('[data-select-all-tagihan]').forEach((selectAll) => {
        selectAll.addEventListener('change', () => setAllRows(selectAll.checked));
    });

    rowCheckboxes().forEach((checkbox) => {
        checkbox.addEventListener('change', updateToolbar);
    });

    document.querySelector('[data-clear-selection]')?.addEventListener('click', clearSelection);
    document.querySelector('[data-execute-reset]')?.addEventListener('click', () => {
        document.getElementById('formResetTagihan')?.submit();
    });

    resetModal?.addEventListener('show.bs.modal', buildResetList);
    updateToolbar();
});
