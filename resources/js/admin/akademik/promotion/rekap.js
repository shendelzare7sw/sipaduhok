import Swal from 'sweetalert2';

let triggerBtn;

const getTotalActiveGlobal = () => {
    const config = document.getElementById('promotionRekapConfig');
    return Number(config?.dataset.totalActiveGlobal || 0);
};

const updateButtonState = () => {
    if (!triggerBtn) {
        triggerBtn = document.querySelector('#promoteSelectedTrigger');
    }

    const count = document.querySelectorAll('.simCheck:checked').length;
    const isSelectAll = document.getElementById('selectAllFlag')?.value === '1';

    if (triggerBtn) {
        triggerBtn.disabled = count === 0 && !isSelectAll;
    }
};

const toggleAllCheckboxes = (source, className) => {
    const checkboxes = document.querySelectorAll(`.${className}`);

    checkboxes.forEach((checkbox) => {
        checkbox.checked = source.checked;
    });

    const mobileCb = document.getElementById('selectAllSimMobile');
    const desktopCb = document.getElementById('selectAllSim');

    if (mobileCb && mobileCb !== source) {
        mobileCb.checked = source.checked;
    }

    if (desktopCb && desktopCb !== source) {
        desktopCb.checked = source.checked;
    }

    const banner = document.getElementById('selectAllBanner');
    if (banner) {
        if (source.checked) {
            banner.classList.remove('d-none');
            document.getElementById('selectAllPagesLink')?.classList.remove('d-none');
            document.getElementById('clearSelectAllLink')?.classList.add('d-none');

            const bannerText = document.getElementById('bannerText');
            if (bannerText) {
                bannerText.innerHTML = `Semua <strong>${checkboxes.length}</strong> siswa di halaman ini dipilih.`;
            }
        } else {
            banner.classList.add('d-none');
        }
    }

    const flagInput = document.getElementById('selectAllFlag');
    if (flagInput) {
        flagInput.value = '0';
    }

    updateButtonState();
};

const enableSelectAllPages = () => {
    const selectAllFlag = document.getElementById('selectAllFlag');
    const bannerText = document.getElementById('bannerText');

    if (selectAllFlag) {
        selectAllFlag.value = '1';
    }

    if (bannerText) {
        bannerText.innerHTML = `Semua <strong>${getTotalActiveGlobal()}</strong> siswa di semua halaman dipilih.`;
    }

    document.getElementById('selectAllPagesLink')?.classList.add('d-none');
    document.getElementById('clearSelectAllLink')?.classList.remove('d-none');
    updateButtonState();
};

const clearSelectAllPages = () => {
    const selectAllFlag = document.getElementById('selectAllFlag');
    const checkboxes = document.querySelectorAll('.simCheck');
    const bannerText = document.getElementById('bannerText');

    if (selectAllFlag) {
        selectAllFlag.value = '0';
    }

    if (bannerText) {
        bannerText.innerHTML = `Semua <strong>${checkboxes.length}</strong> siswa di halaman ini dipilih.`;
    }

    document.getElementById('selectAllPagesLink')?.classList.remove('d-none');
    document.getElementById('clearSelectAllLink')?.classList.add('d-none');
    updateButtonState();
};

const clearSearchAndSubmit = (button) => {
    const input = button.closest('.input-group')?.querySelector('input');
    const form = button.closest('form');

    if (input) {
        input.value = '';
    }

    form?.submit();
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => {
            field.form?.submit();
        });
    });

    document.querySelectorAll('[data-clear-search]').forEach((button) => {
        button.addEventListener('click', () => clearSearchAndSubmit(button));
    });

    document.querySelectorAll('[data-toggle-all-checkboxes]').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            toggleAllCheckboxes(checkbox, checkbox.dataset.targetClass || 'simCheck');
        });
    });

    document.querySelector('[data-select-all-pages]')?.addEventListener('click', (event) => {
        event.preventDefault();
        enableSelectAllPages();
    });

    document.querySelector('[data-clear-select-all-pages]')?.addEventListener('click', (event) => {
        event.preventDefault();
        clearSelectAllPages();
    });

    document.querySelectorAll('.simCheck').forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            if (!this.checked) {
                const flagInput = document.getElementById('selectAllFlag');
                if (flagInput) {
                    flagInput.value = '0';
                }

                const mobileCb = document.getElementById('selectAllSimMobile');
                const desktopCb = document.getElementById('selectAllSim');

                if (mobileCb) {
                    mobileCb.checked = false;
                }

                if (desktopCb) {
                    desktopCb.checked = false;
                }

                document.getElementById('selectAllBanner')?.classList.add('d-none');
            }

            updateButtonState();
        });
    });

    const promoteBtn = document.querySelector('[data-bs-target="#promoteSelectedModal"]');
    if (promoteBtn) {
        promoteBtn.addEventListener('click', () => {
            const isSelectAll = document.getElementById('selectAllFlag')?.value === '1';
            const uniqueIds = new Set();

            document.querySelectorAll('.simCheck:checked').forEach((checkbox) => {
                uniqueIds.add(checkbox.value);
            });

            const selectedCount = document.getElementById('selectedCount');
            if (selectedCount) {
                selectedCount.textContent = isSelectAll ? getTotalActiveGlobal() : uniqueIds.size;
            }

            const warningEl = document.getElementById('selectAllWarning');
            if (warningEl) {
                warningEl.classList.toggle('d-none', !isSelectAll);
                warningEl.classList.toggle('d-block', isSelectAll);
            }

            if (uniqueIds.size === 0 && !isSelectAll) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Pilih siswa terlebih dahulu!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                } else {
                    alert('Pilih siswa terlebih dahulu!');
                }
            }
        });
    }

    updateButtonState();

    const promoteForm = document.getElementById('promoteSelectedForm');
    const modalSubmitBtn = document.getElementById('confirmPromoteBtn');

    if (promoteForm && modalSubmitBtn) {
        modalSubmitBtn.addEventListener('click', (event) => {
            event.preventDefault();
            promoteForm.querySelectorAll('.dynamic-siswa-id').forEach((element) => element.remove());

            if (document.getElementById('selectAllFlag')?.value === '0') {
                const ids = new Set();
                document.querySelectorAll('.simCheck:checked').forEach((checkbox) => {
                    ids.add(checkbox.value);
                });

                ids.forEach((id) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'siswa_ids[]';
                    input.value = id;
                    input.className = 'dynamic-siswa-id';
                    promoteForm.appendChild(input);
                });
            }

            promoteForm.submit();
        });
    }

    const cancelModal = document.getElementById('cancelScheduleModal');
    if (cancelModal) {
        cancelModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const form = cancelModal.querySelector('#cancelScheduleForm');
            const dateText = cancelModal.querySelector('#scheduleDateText');

            if (form) {
                form.action = button.getAttribute('data-url');
            }

            if (dateText) {
                dateText.textContent = button.getAttribute('data-date');
            }
        });
    }
});
