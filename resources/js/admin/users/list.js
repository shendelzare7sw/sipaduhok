document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');

    if (searchInput && clearSearch) {
        const toggleClearSearch = () => {
            clearSearch.classList.toggle('show', searchInput.value.length > 0);
        };

        searchInput.addEventListener('input', toggleClearSearch);
        clearSearch.addEventListener('click', () => {
            searchInput.value = '';
            clearSearch.classList.remove('show');
            searchInput.focus();
        });

        toggleClearSearch();
    }

    initializeSiswaCascadeFilters();
    initializeAutoSubmitFilters();
    initializeBulkDelete();
});

function initializeAutoSubmitFilters() {
    document.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => field.form?.submit());
    });
}

function initializeSiswaCascadeFilters() {
    const cabangSelect = document.getElementById('cabangSelect');
    const jenjangSelect = document.getElementById('jenjangSelect');
    const jenjangContainer = document.getElementById('jenjangFilterContainer');
    const kelasSelect = document.getElementById('kelasSelect');
    const kelasContainer = document.getElementById('kelasFilterContainer');

    if (!cabangSelect || !jenjangSelect || !jenjangContainer || !kelasSelect || !kelasContainer) {
        return;
    }

    const allJenjangData = Array.from(jenjangSelect.options).map((option) => ({
        value: option.value,
        text: option.text,
    }));

    const allKelasData = Array.from(kelasSelect.options).map((option) => ({
        value: option.value,
        text: option.text,
        cabang: option.getAttribute('data-cabang'),
        jenjang: option.getAttribute('data-jenjang'),
    }));

    const rebuildSelect = (selectEl, options) => {
        const current = selectEl.value;
        selectEl.innerHTML = '';

        options.forEach((option) => {
            const element = document.createElement('option');
            element.value = option.value;
            element.textContent = option.text;

            if (option.value && option.value === current) {
                element.selected = true;
            }

            selectEl.appendChild(element);
        });
    };

    const updateFilters = () => {
        const selectedCabangId = cabangSelect.value;
        const selectedJenjang = jenjangSelect.value;

        if (selectedCabangId) {
            jenjangContainer.style.display = 'block';

            const availableJenjangs = new Set();
            allKelasData.forEach((option) => {
                if (option.value !== '' && option.cabang == selectedCabangId) {
                    availableJenjangs.add(option.jenjang);
                }
            });

            const filteredJenjang = allJenjangData.filter((option) => (
                option.value === '' || availableJenjangs.has(option.value)
            ));
            rebuildSelect(jenjangSelect, filteredJenjang);

            if (availableJenjangs.has(selectedJenjang)) {
                jenjangSelect.value = selectedJenjang;
            }
        } else {
            jenjangContainer.style.display = 'none';
            rebuildSelect(jenjangSelect, allJenjangData);
        }

        const currentJenjang = jenjangSelect.value;
        if (selectedCabangId && currentJenjang) {
            kelasContainer.style.display = 'block';

            const filteredKelas = allKelasData.filter((option) => (
                option.value === '' ||
                (option.cabang == selectedCabangId && option.jenjang == currentJenjang)
            ));
            rebuildSelect(kelasSelect, filteredKelas);
        } else {
            kelasContainer.style.display = 'none';
            kelasSelect.value = '';
        }
    };

    cabangSelect.addEventListener('change', () => {
        jenjangSelect.value = '';
        kelasSelect.value = '';
        updateFilters();
    });

    jenjangSelect.addEventListener('change', () => {
        kelasSelect.value = '';
        updateFilters();
    });

    updateFilters();
}

function initializeBulkDelete() {
    const selectAll = document.getElementById('selectAll');
    const selectAllMobile = document.getElementById('selectAllMobile');
    const selectItems = document.querySelectorAll('.select-item');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    if (!bulkDeleteForm || selectItems.length === 0) {
        return;
    }

    const updateBulkDeleteButton = () => {
        const selectedCount = document.querySelectorAll('.select-item:checked').length;
        bulkDeleteForm.style.display = selectedCount > 0 ? 'block' : 'none';
    };

    const syncSelectAll = (checked) => {
        selectItems.forEach((item) => {
            item.checked = checked;
        });

        if (selectAll) {
            selectAll.checked = checked;
        }

        if (selectAllMobile) {
            selectAllMobile.checked = checked;
        }

        updateBulkDeleteButton();
    };

    selectAll?.addEventListener('change', (event) => syncSelectAll(event.target.checked));
    selectAllMobile?.addEventListener('change', (event) => syncSelectAll(event.target.checked));

    selectItems.forEach((item) => {
        item.addEventListener('change', () => {
            const selectedCount = document.querySelectorAll('.select-item:checked').length;
            const allChecked = selectedCount === selectItems.length;

            if (selectAll) {
                selectAll.checked = allChecked;
            }

            if (selectAllMobile) {
                selectAllMobile.checked = allChecked;
            }

            updateBulkDeleteButton();
        });
    });

    document.querySelectorAll('[data-show-bulk-delete-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const selectedItems = document.querySelectorAll('.select-item:checked');
            if (selectedItems.length === 0 || typeof bootstrap === 'undefined') {
                return;
            }

            const selectedCount = document.getElementById('selectedCount');
            if (selectedCount) {
                selectedCount.textContent = selectedItems.length;
            }

            const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
            modal.show();
        });
    });

    document.querySelectorAll('[data-submit-bulk-delete]').forEach((button) => {
        button.addEventListener('click', () => {
            const selectedItems = document.querySelectorAll('.select-item:checked');
            const existingInputs = bulkDeleteForm.querySelectorAll('input[name="ids[]"]');
            existingInputs.forEach((input) => input.remove());

            selectedItems.forEach((item) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = item.value;
                bulkDeleteForm.appendChild(input);
            });

            bulkDeleteForm.submit();
        });
    });

    updateBulkDeleteButton();
}
