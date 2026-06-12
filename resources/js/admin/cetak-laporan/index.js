document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.admin-cetak-laporan-page');

    if (!page) {
        return;
    }

    const dataTemplate = page.querySelector('#cetakLaporanKelasData');
    const cabangSelect = page.querySelector('#siswa_cabang');
    const jenjangGroup = page.querySelector('#siswa_jenjang_group');
    const jenjangSelect = page.querySelector('#siswa_jenjang');
    const kelasGroup = page.querySelector('#siswa_kelas_group');
    const kelasSelect = page.querySelector('#siswa_kelas');

    if (!dataTemplate || !cabangSelect || !jenjangGroup || !jenjangSelect || !kelasGroup || !kelasSelect) {
        return;
    }

    let kelasData = [];

    try {
        kelasData = JSON.parse(dataTemplate.textContent || '[]');
    } catch (error) {
        kelasData = [];
    }

    const resetSelect = (select, label) => {
        select.replaceChildren(new Option(label, ''));
    };

    const setGroupVisible = (group, isVisible) => {
        group.classList.toggle('is-hidden', !isVisible);
    };

    cabangSelect.addEventListener('change', () => {
        const cabangId = cabangSelect.value;

        resetSelect(jenjangSelect, 'Semua Jenjang');
        resetSelect(kelasSelect, 'Semua Kelas');

        if (!cabangId) {
            setGroupVisible(jenjangGroup, false);
            setGroupVisible(kelasGroup, false);
            return;
        }

        const jenjangs = [...new Set(kelasData
            .filter((kelas) => String(kelas.cabang_id) === cabangId)
            .map((kelas) => kelas.jenjang))]
            .sort();

        jenjangs.forEach((jenjang) => {
            jenjangSelect.append(new Option(jenjang, jenjang));
        });

        setGroupVisible(jenjangGroup, true);
        setGroupVisible(kelasGroup, false);
    });

    jenjangSelect.addEventListener('change', () => {
        const cabangId = cabangSelect.value;
        const jenjang = jenjangSelect.value;

        resetSelect(kelasSelect, 'Semua Kelas');

        if (!jenjang) {
            setGroupVisible(kelasGroup, false);
            return;
        }

        kelasData
            .filter((kelas) => String(kelas.cabang_id) === cabangId && kelas.jenjang === jenjang)
            .forEach((kelas) => {
                kelasSelect.append(new Option(kelas.nama_kelas, kelas.id));
            });

        setGroupVisible(kelasGroup, true);
    });
});
