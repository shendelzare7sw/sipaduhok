document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-nilai-page');

    if (!page) {
        return;
    }

    const adjustValue = (inputId, delta) => {
        const input = page.querySelector(`#${CSS.escape(inputId)}`);

        if (!input) {
            return;
        }

        const currentValue = parseFloat(input.value) || 0;
        let newValue = currentValue + delta;
        newValue = Math.max(0, Math.min(100, newValue));
        newValue = Math.round(newValue * 100) / 100;

        input.value = newValue;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    };

    page.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => {
            field.form?.submit();
        });
    });

    page.querySelectorAll('.input-nilai').forEach((input) => {
        input.addEventListener('input', function handleScoreInput() {
            const value = this.value;

            if (value === '') {
                return;
            }

            const numberValue = parseFloat(value);

            if (numberValue > 100) {
                const stringValue = value.toString();

                if (!stringValue.includes('.')) {
                    const corrected = stringValue.slice(0, -1) + '.' + stringValue.slice(-1);

                    if (parseFloat(corrected) <= 100) {
                        this.value = corrected;
                        return;
                    }
                }

                this.value = 100;
            }
        });

        input.addEventListener('keydown', (event) => {
            if (['e', 'E', '-', '+'].includes(event.key)) {
                event.preventDefault();
            }
        });

        input.addEventListener('blur', function handleScoreBlur() {
            const value = parseFloat(this.value);

            if (Number.isNaN(value)) {
                return;
            }

            if (value > 100) this.value = 100;
            if (value < 0) this.value = 0;
        });
    });

    page.addEventListener('click', (event) => {
        const adjustButton = event.target.closest('[data-adjust-target]');

        if (adjustButton) {
            adjustValue(adjustButton.dataset.adjustTarget, Number(adjustButton.dataset.adjustDelta || 0));
            return;
        }

        if (event.target.closest('[data-open-recalculate]')) {
            const modalElement = page.querySelector('#recalculateModal');
            if (modalElement && window.bootstrap) {
                new window.bootstrap.Modal(modalElement).show();
            }
            return;
        }

        const submitButton = event.target.closest('[data-submit-form]');

        if (submitButton) {
            page.querySelector(`#${CSS.escape(submitButton.dataset.submitForm)}`)?.submit();
        }
    });

    const importForm = page.querySelector('#importForm');

    importForm?.addEventListener('submit', (event) => {
        const fileInput = page.querySelector('#importFile');
        const file = fileInput?.files?.[0];

        if (!file) {
            event.preventDefault();
            window.alert('Pilih file Excel terlebih dahulu!');
            return;
        }

        const allowedExtensions = /(\.xlsx|\.xls)$/i;
        if (!allowedExtensions.exec(file.name)) {
            event.preventDefault();
            window.alert('File harus berformat .xlsx atau .xls');
            fileInput.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            event.preventDefault();
            window.alert('Ukuran file maksimal 5MB');
            fileInput.value = '';
            return;
        }

        const importButton = page.querySelector('#importBtn');
        if (importButton) {
            importButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengimport...';
            importButton.disabled = true;
        }
    });

    page.querySelector('#importModal')?.addEventListener('hidden.bs.modal', () => {
        importForm?.reset();

        const importButton = page.querySelector('#importBtn');
        if (importButton) {
            importButton.innerHTML = '<i class="fas fa-upload me-1"></i> Import Nilai';
            importButton.disabled = false;
        }
    });
});
