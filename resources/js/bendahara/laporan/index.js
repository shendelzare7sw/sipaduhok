document.addEventListener('DOMContentLoaded', () => {
    const cabangSelect = document.getElementById('cabangFilter');
    const jenjangSelect = document.getElementById('jenjangFilter');
    const jenjangContainer = document.getElementById('jenjangFilterContainer');
    const kelasSelect = document.getElementById('kelasFilter');
    const kelasContainer = document.getElementById('kelasFilterContainer');

    document.querySelectorAll('[data-chart-height]').forEach((bar) => {
        bar.style.height = `${bar.dataset.chartHeight || 0}%`;
    });

    if (!cabangSelect || !jenjangSelect || !kelasSelect) {
        return;
    }

    const allJenjangData = Array.from(jenjangSelect.options).map((option) => ({
        value: option.value,
        text: option.text,
    }));
    const allKelasData = Array.from(kelasSelect.options).map((option) => ({
        value: option.value,
        text: option.text,
        cabang: option.dataset.cabang,
        jenjang: option.dataset.jenjang,
    }));

    const rebuildSelect = (selectEl, options) => {
        const current = selectEl.value;
        selectEl.innerHTML = '';

        options.forEach((option) => {
            const element = document.createElement('option');
            element.value = option.value;
            element.textContent = option.text;

            if (option.cabang) {
                element.dataset.cabang = option.cabang;
            }
            if (option.jenjang) {
                element.dataset.jenjang = option.jenjang;
            }
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
            jenjangContainer?.classList.remove('filter-cascade-hidden');
            const availableJenjangs = new Set();
            allKelasData.forEach((option) => {
                if (option.value !== '' && option.cabang === selectedCabangId) {
                    availableJenjangs.add(option.jenjang);
                }
            });

            const filteredJenjang = allJenjangData.filter((option) => option.value === '' || availableJenjangs.has(option.value));
            rebuildSelect(jenjangSelect, filteredJenjang);
            if (availableJenjangs.has(selectedJenjang)) {
                jenjangSelect.value = selectedJenjang;
            }
        } else {
            jenjangContainer?.classList.add('filter-cascade-hidden');
            jenjangSelect.value = '';
        }

        const currentJenjang = jenjangSelect.value;
        if (currentJenjang) {
            kelasContainer?.classList.remove('filter-cascade-hidden');
            const filteredKelas = allKelasData.filter((option) => (
                option.value === '' || (option.cabang === selectedCabangId && option.jenjang === currentJenjang)
            ));
            rebuildSelect(kelasSelect, filteredKelas);
        } else {
            kelasContainer?.classList.add('filter-cascade-hidden');
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
});
