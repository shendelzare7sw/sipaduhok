const resetSelect = (select, label) => {
    if (!select) {
        return;
    }

    select.replaceChildren(new Option(label, ''));
};

const toggleGroup = (group, visible) => {
    group?.classList.toggle('is-hidden', !visible);
};

const parseKelasData = (page) => {
    const dataElement = page.querySelector('#laporanKelasData');

    if (!dataElement?.dataset.kelas) {
        return [];
    }

    try {
        return JSON.parse(dataElement.dataset.kelas);
    } catch {
        return [];
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.admin-laporan-page');

    if (!page) {
        return;
    }

    const kelasData = parseKelasData(page);
    const cabangSelect = page.querySelector('#siswa_cabang');
    const jenjangGroup = page.querySelector('#siswa_jenjang_group');
    const jenjangSelect = page.querySelector('#siswa_jenjang');
    const kelasGroup = page.querySelector('#siswa_kelas_group');
    const kelasSelect = page.querySelector('#siswa_kelas');

    if (!cabangSelect || !jenjangSelect || !kelasSelect) {
        return;
    }

    cabangSelect.addEventListener('change', () => {
        const cabangId = cabangSelect.value;
        resetSelect(jenjangSelect, 'Semua Jenjang');
        resetSelect(kelasSelect, 'Semua Kelas');

        if (!cabangId) {
            toggleGroup(jenjangGroup, false);
            toggleGroup(kelasGroup, false);
            return;
        }

        const jenjangs = [...new Set(kelasData
            .filter((kelas) => String(kelas.cabang_id) === String(cabangId))
            .map((kelas) => kelas.jenjang))]
            .sort();

        jenjangs.forEach((jenjang) => {
            jenjangSelect.add(new Option(jenjang, jenjang));
        });

        toggleGroup(jenjangGroup, true);
        toggleGroup(kelasGroup, false);
    });

    jenjangSelect.addEventListener('change', () => {
        const cabangId = cabangSelect.value;
        const jenjang = jenjangSelect.value;
        resetSelect(kelasSelect, 'Semua Kelas');

        if (!jenjang) {
            toggleGroup(kelasGroup, false);
            return;
        }

        kelasData
            .filter((kelas) => String(kelas.cabang_id) === String(cabangId) && kelas.jenjang === jenjang)
            .forEach((kelas) => {
                kelasSelect.add(new Option(kelas.nama_kelas, kelas.id));
            });

        toggleGroup(kelasGroup, true);
    });
});
