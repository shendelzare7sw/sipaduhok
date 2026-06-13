document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.class-checkbox');
    const checkAll = document.getElementById('checkAllKelas');
    const buttonText = document.getElementById('selectedKelasText');
    const cabangSelect = document.querySelector('select[name="cabang_id"]');
    const jenjangSelect = document.querySelector('select[name="jenjang"]');
    const searchInput = document.getElementById('searchKelasInput');
    const emptyState = document.getElementById('kelasEmptyState');
    const kelasItems = document.querySelectorAll('.kelas-item');
    const jenjangGroups = document.querySelectorAll('.kelas-jenjang-group');

    document.querySelectorAll('[data-auto-submit]').forEach((element) => {
        element.addEventListener('change', () => element.form?.submit());
    });

    const updateButtonText = () => {
        if (!buttonText) return;

        const checked = Array.from(checkboxes).filter((checkbox) => checkbox.checked);
        if (checked.length === 0) {
            buttonText.textContent = 'Pilih Kelas';
            buttonText.style.color = '#94a3b8';
            return;
        }

        buttonText.textContent = checked.length === checkboxes.length
            ? `Semua Kelas (${checked.length})`
            : `${checked.length} Kelas Dipilih`;
        buttonText.style.color = '#1e293b';
    };

    const applyKelasFilter = () => {
        const cabangFilter = cabangSelect?.value || '';
        const jenjangFilter = jenjangSelect?.value || '';
        const searchText = searchInput?.value.trim().toLowerCase() || '';
        let visibleCount = 0;
        const visibleJenjangs = new Set();

        kelasItems.forEach((item) => {
            const itemCabang = item.getAttribute('data-cabang-id');
            const itemJenjang = item.getAttribute('data-jenjang');
            const itemSearch = item.getAttribute('data-search') || '';

            const visible = (!cabangFilter || itemCabang === cabangFilter)
                && (!jenjangFilter || itemJenjang === jenjangFilter)
                && (!searchText || itemSearch.includes(searchText));

            item.style.display = visible ? '' : 'none';
            if (visible) {
                visibleCount++;
                visibleJenjangs.add(itemJenjang);
            }
        });

        jenjangGroups.forEach((group) => {
            group.style.display = visibleJenjangs.has(group.getAttribute('data-jenjang')) ? '' : 'none';
        });

        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? '' : 'none';
        }
    };

    checkAll?.addEventListener('change', () => {
        checkboxes.forEach((checkbox) => {
            const item = checkbox.closest('.kelas-item');
            if (item && item.style.display !== 'none') {
                checkbox.checked = checkAll.checked;
            }
        });
        updateButtonText();
    });

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            updateButtonText();
            if (!checkAll) return;

            const visibleCheckboxes = Array.from(checkboxes).filter((item) => {
                const listItem = item.closest('.kelas-item');
                return listItem && listItem.style.display !== 'none';
            });
            checkAll.checked = visibleCheckboxes.length > 0 && visibleCheckboxes.every((item) => item.checked);
        });
    });

    searchInput?.addEventListener('input', applyKelasFilter);
    searchInput?.addEventListener('click', (event) => event.stopPropagation());
    cabangSelect?.addEventListener('change', applyKelasFilter);
    jenjangSelect?.addEventListener('change', applyKelasFilter);

    applyKelasFilter();
    updateButtonText();
    if (checkAll) {
        checkAll.checked = checkboxes.length > 0 && Array.from(checkboxes).every((checkbox) => checkbox.checked);
    }
});
