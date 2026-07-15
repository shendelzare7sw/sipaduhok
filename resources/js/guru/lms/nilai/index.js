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
            // ATURAN DESIMAL: titik. Koma otomatis dikonversi; karakter lain dibuang;
            // hanya satu titik yang diperbolehkan.
            let v = this.value.replace(/,/g, '.').replace(/[^0-9.]/g, '');
            const firstDot = v.indexOf('.');
            if (firstDot !== -1) {
                v = v.slice(0, firstDot + 1) + v.slice(firstDot + 1).replace(/\./g, '');
            }
            if (v !== this.value) {
                this.value = v;
            }

            if (v === '') {
                return;
            }

            const numberValue = parseFloat(v);

            if (numberValue > 100) {
                if (!v.includes('.')) {
                    const corrected = v.slice(0, -1) + '.' + v.slice(-1);

                    if (parseFloat(corrected) <= 100) {
                        this.value = corrected;
                        return;
                    }
                }

                this.value = 100;
            }
        });

        input.addEventListener('keydown', (event) => {
            const k = event.key;
            // Izinkan tombol kontrol & kombinasi Ctrl/Meta (copy/paste, panah, dll).
            if (k.length !== 1 || event.ctrlKey || event.metaKey) {
                return;
            }
            // Hanya digit, koma, dan titik yang boleh diketik.
            if (!/[0-9.,]/.test(k)) {
                event.preventDefault();
            }
        });

        input.addEventListener('blur', function handleScoreBlur() {
            const raw = this.value.replace(/,/g, '.');
            const value = parseFloat(raw);

            if (Number.isNaN(value)) {
                this.value = '';
                return;
            }

            let clamped = Math.max(0, Math.min(100, value));
            clamped = Math.round(clamped * 100) / 100;
            this.value = String(clamped);
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
