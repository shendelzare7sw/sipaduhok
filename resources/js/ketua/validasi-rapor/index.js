const getSelectedCheckboxes = () => Array.from(document.querySelectorAll('.siswa-checkbox:checked'));

const showModal = (id) => {
    const element = document.getElementById(id);

    if (element) {
        new bootstrap.Modal(element).show();
    }
};

const fillActionModal = (event, nameTargetId, formId) => {
    const button = event.relatedTarget;
    const nameTarget = document.getElementById(nameTargetId);
    const form = document.getElementById(formId);

    if (!button || !nameTarget || !form) {
        return;
    }

    nameTarget.textContent = button.dataset.name;
    form.action = button.dataset.action;
};

const submitBulkValidation = () => {
    const selected = getSelectedCheckboxes().map((checkbox) => checkbox.value);
    const config = document.getElementById('validasiRaporConfig');

    if (!config?.dataset.bulkRoute || !config.dataset.csrf) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = config.dataset.bulkRoute;

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = config.dataset.csrf;
    form.appendChild(csrf);

    selected.forEach((id) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'siswa_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-submit]').forEach((select) => {
        select.addEventListener('change', () => select.form?.submit());
    });

    document.getElementById('checkAll')?.addEventListener('change', (event) => {
        document.querySelectorAll('.siswa-checkbox').forEach((checkbox) => {
            checkbox.checked = event.target.checked;
        });
    });

    document.getElementById('validasiModal')?.addEventListener('show.bs.modal', (event) => {
        fillActionModal(event, 'validasiNamaSiswa', 'validasiForm');
    });

    document.getElementById('batalkanModal')?.addEventListener('show.bs.modal', (event) => {
        fillActionModal(event, 'batalkanNamaSiswa', 'batalkanForm');
    });

    document.getElementById('revisiModal')?.addEventListener('show.bs.modal', (event) => {
        fillActionModal(event, 'revisiNamaSiswa', 'revisiForm');
    });

    document.getElementById('btnValidasiTerpilih')?.addEventListener('click', () => {
        const selected = getSelectedCheckboxes();

        if (selected.length === 0) {
            showModal('peringatanModal');
            return;
        }

        const totalTarget = document.getElementById('jumlahTerpilih');

        if (totalTarget) {
            totalTarget.textContent = selected.length;
        }

        showModal('validasiTerpilihModal');
    });

    document.getElementById('btnKonfirmasiTerpilih')?.addEventListener('click', submitBulkValidation);

    document.getElementById('btnValidasiSemua')?.addEventListener('click', () => {
        showModal('validasiSemuaModal');
    });
});
