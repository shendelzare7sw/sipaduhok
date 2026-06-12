let currentKelasId = null;
let currentKelasName = '';

const filterWaliList = () => {
    const searchInput = document.getElementById('searchWali');
    const searchTerm = (searchInput?.value || '').toLowerCase();

    document.querySelectorAll('.wali-option').forEach((option) => {
        const name = option.dataset.name || '';
        const matchSearch = !searchTerm || name.includes(searchTerm);

        option.classList.toggle('is-hidden', !matchSearch);
    });
};

const openAssignModal = (trigger) => {
    currentKelasId = trigger.dataset.kelasId;
    currentKelasName = trigger.dataset.kelasName || '';
    const currentWaliId = trigger.dataset.currentWaliId || '';
    const kelasName = document.getElementById('kelasName');
    const assignForm = document.getElementById('assignForm');
    const searchWali = document.getElementById('searchWali');
    const removeButton = document.getElementById('btnRemoveWali');

    if (kelasName) {
        kelasName.value = currentKelasName;
    }

    if (assignForm) {
        assignForm.action = `/waka/wali-kelas/${currentKelasId}/assign`;
    }

    if (searchWali) {
        searchWali.value = '';
    }

    document.querySelectorAll('input[name="wali_kelas_id"]').forEach((radio) => {
        radio.checked = false;
    });

    if (currentWaliId) {
        const radio = document.querySelector(`input[name="wali_kelas_id"][value="${currentWaliId}"]`);
        if (radio) {
            radio.checked = true;
            radio.scrollIntoView({ block: 'center', behavior: 'smooth' });
            removeButton?.classList.remove('is-hidden');
        } else {
            removeButton?.classList.add('is-hidden');
        }
    } else {
        removeButton?.classList.add('is-hidden');
    }

    filterWaliList();

    const modalElement = document.getElementById('assignModal');
    if (modalElement && window.bootstrap?.Modal) {
        new window.bootstrap.Modal(modalElement).show();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-submit]').forEach((select) => {
        select.addEventListener('change', () => select.form?.submit());
    });

    document.querySelectorAll('[data-assign-wali]').forEach((button) => {
        button.addEventListener('click', () => openAssignModal(button));
    });

    document.getElementById('searchWali')?.addEventListener('input', filterWaliList);

    document.querySelectorAll('.wali-option').forEach((option) => {
        option.addEventListener('click', (event) => {
            if (event.target.tagName !== 'INPUT') {
                const radio = option.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            }
        });
    });

    document.getElementById('btnRemoveWali')?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const deleteKelasName = document.getElementById('deleteKelasName');
        if (deleteKelasName) {
            deleteKelasName.textContent = currentKelasName;
        }

        const assignModal = window.bootstrap?.Modal.getInstance(document.getElementById('assignModal'));
        assignModal?.hide();

        window.setTimeout(() => {
            const confirmModalElement = document.getElementById('confirmDeleteModal');
            if (confirmModalElement && window.bootstrap?.Modal) {
                new window.bootstrap.Modal(confirmModalElement).show();
            }
        }, 300);
    });

    document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {
        const confirmModal = window.bootstrap?.Modal.getInstance(document.getElementById('confirmDeleteModal'));
        confirmModal?.hide();

        document.querySelectorAll('input[name="wali_kelas_id"]').forEach((radio) => {
            radio.checked = false;
        });

        window.setTimeout(() => {
            document.getElementById('assignForm')?.submit();
        }, 300);
    });
});
