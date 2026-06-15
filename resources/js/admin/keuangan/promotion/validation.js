const getStudentCheckboxes = () => Array.from(document.querySelectorAll('.siswa-checkbox'));

const getUniqueCheckedValues = () => new Set(
    getStudentCheckboxes()
        .filter((checkbox) => checkbox.checked)
        .map((checkbox) => checkbox.value),
);

const getUniqueAvailableValues = () => new Set(getStudentCheckboxes().map((checkbox) => checkbox.value));

const setAllCheckboxes = (checked) => {
    getStudentCheckboxes().forEach((checkbox) => {
        checkbox.checked = checked;
    });
};

const updateSelectedCount = () => {
    const uniqueCheckedValues = getUniqueCheckedValues();
    const uniqueAllValues = getUniqueAvailableValues();
    const count = uniqueCheckedValues.size;
    const total = uniqueAllValues.size;
    const selectedCount = document.getElementById('selectedCount');
    const bulkButton = document.getElementById('btnBulkDispensasi');
    const selectAll = document.getElementById('select-all');
    const mobileSelectAll = document.getElementById('selectAllMobile');

    if (selectedCount) {
        selectedCount.textContent = count;
    }

    bulkButton?.classList.toggle('is-hidden', count === 0);

    [selectAll, mobileSelectAll].forEach((checkbox) => {
        if (!checkbox) {
            return;
        }

        checkbox.indeterminate = count > 0 && count < total;
        checkbox.checked = total > 0 && count === total;
    });
};

const syncCheckbox = (source) => {
    getStudentCheckboxes()
        .filter((checkbox) => checkbox.value === source.value && checkbox !== source)
        .forEach((checkbox) => {
            checkbox.checked = source.checked;
        });

    updateSelectedCount();
};

const hydrateBulkModal = () => {
    const container = document.getElementById('bulk-siswa-ids');
    const modalSelectedCount = document.getElementById('modalSelectedCount');
    const uniqueCheckedValues = getUniqueCheckedValues();

    if (!container) {
        return;
    }

    container.replaceChildren();

    if (modalSelectedCount) {
        modalSelectedCount.textContent = uniqueCheckedValues.size;
    }

    uniqueCheckedValues.forEach((value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'siswa_ids[]';
        input.value = value;
        container.appendChild(input);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('select-all')?.addEventListener('change', (event) => {
        setAllCheckboxes(event.target.checked);
        updateSelectedCount();
    });

    document.getElementById('selectAllMobile')?.addEventListener('change', (event) => {
        setAllCheckboxes(event.target.checked);
        updateSelectedCount();
    });

    getStudentCheckboxes().forEach((checkbox) => {
        checkbox.addEventListener('change', () => syncCheckbox(checkbox));
    });

    document.getElementById('modalBulkDispensasi')?.addEventListener('show.bs.modal', hydrateBulkModal);

    updateSelectedCount();
});
