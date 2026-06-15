(() => {
    const page = document.querySelector('.guru-lms-soal-form-page');

    if (!page) {
        return;
    }

    const tipeSelect = page.querySelector('#tipeSoal');
    const sections = {
        pilihan_ganda: page.querySelector('#sectionPilgan'),
        pilihan_ganda_kompleks: page.querySelector('#sectionPilganKompleks'),
        benar_salah: page.querySelector('#sectionBenarSalah'),
        isian_singkat: page.querySelector('#sectionIsian'),
        uraian: page.querySelector('#sectionUraian'),
    };

    const showSection = (type) => {
        Object.values(sections).forEach((section) => section?.classList.remove('is-active'));
        sections[type]?.classList.add('is-active');
    };

    tipeSelect?.addEventListener('change', () => showSection(tipeSelect.value));

    if (tipeSelect) {
        showSection(tipeSelect.value);
    }

    const addBsBtn = page.querySelector('#addBsRow');
    const bsTbody = page.querySelector('#bsTbody');
    let bsIdx = Number.parseInt(bsTbody?.dataset.nextIndex || '0', 10);

    addBsBtn?.addEventListener('click', () => {
        if (!bsTbody) {
            return;
        }

        const row = `
            <tr>
                <td>
                    <input type="text" name="pilihan_jawaban_bs[${bsIdx}][pernyataan]" class="form-control" placeholder="Tulis pernyataan...">
                </td>
                <td class="text-center align-middle">
                    <select name="pilihan_jawaban_bs[${bsIdx}][kunci]" class="form-select form-select-sm">
                        <option value="B">Benar</option>
                        <option value="S">Salah</option>
                    </select>
                </td>
            </tr>
        `;

        bsTbody.insertAdjacentHTML('beforeend', row);
        bsIdx += 1;
    });
})();
