document.addEventListener('DOMContentLoaded', () => {
    const catatanValidasi = document.getElementById('catatanValidasi');
    const setujuiModal = document.getElementById('setujuiModal');
    const tolakModal = document.getElementById('tolakModal');
    const alasanTolak = document.getElementById('alasanTolak');
    const submitTolakButton = document.querySelector('[data-submit-tolak]');

    setujuiModal?.addEventListener('show.bs.modal', () => {
        const catatanSetujui = document.getElementById('catatanSetujui');

        if (catatanSetujui) {
            catatanSetujui.value = catatanValidasi?.value || '';
        }
    });

    tolakModal?.addEventListener('hidden.bs.modal', () => {
        if (alasanTolak) {
            alasanTolak.value = '';
            alasanTolak.classList.remove('is-invalid');
        }
    });

    submitTolakButton?.addEventListener('click', () => {
        if (!alasanTolak?.value.trim()) {
            alasanTolak?.classList.add('is-invalid');
            return;
        }

        alasanTolak.classList.remove('is-invalid');

        const catatanTolak = document.getElementById('catatanTolak');
        const formTolak = document.getElementById('formTolak');
        const catatan = catatanValidasi?.value || '';
        const alasan = alasanTolak.value.trim();

        if (catatanTolak) {
            catatanTolak.value = catatan
                ? `${catatan}\n[Alasan Tolak] ${alasan}`
                : `[Alasan Tolak] ${alasan}`;
        }

        formTolak?.submit();
    });
});
