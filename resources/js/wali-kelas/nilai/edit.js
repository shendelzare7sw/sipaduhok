/* Page asset: wali-kelas/nilai/edit. */
document.addEventListener('DOMContentLoaded', function () {
    function ensureWaliModalCloseButtons() {
        document.querySelectorAll('.modal .modal-header').forEach(function (header) {
            if (header.querySelector('.btn-close, [data-bs-dismiss="modal"][aria-label="Close"], [data-bs-dismiss="modal"][aria-label="Tutup"]')) {
                return;
            }

            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = 'btn-close';
            closeButton.setAttribute('data-bs-dismiss', 'modal');
            closeButton.setAttribute('aria-label', 'Close');

            const coloredHeader = header.classList.contains('text-white') ||
                ['bg-primary', 'bg-secondary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-dark']
                    .some(function (className) {
                        return header.classList.contains(className);
                    });

            if (coloredHeader) {
                closeButton.classList.add('btn-close-white');
            }

            header.appendChild(closeButton);
        });
    }

    ensureWaliModalCloseButtons();
    document.addEventListener('shown.bs.modal', ensureWaliModalCloseButtons);

    document.querySelectorAll('table.wk-card-table').forEach(function (table) {
        const labels = Array.from(table.querySelectorAll('thead th')).map(function (th) {
            return th.textContent.replace(/\s+/g, ' ').trim();
        });

        table.querySelectorAll('tbody tr').forEach(function (row) {
            Array.from(row.children).forEach(function (cell, index) {
                if (cell.tagName !== 'TD' || cell.hasAttribute('data-label')) {
                    return;
                }

                cell.setAttribute('data-label', labels[index] || '');
            });
        });
    });
});

function adjustValue(inputId, delta) {
    const input = document.getElementById(inputId);
    if (!input) return;

    let value = parseFloat(input.value) || 0;
    value = Math.max(0, Math.min(100, Math.round((value + delta) * 100) / 100));
    input.value = value;
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-adjust-target][data-adjust-delta]').forEach(function (button) {
        button.addEventListener('click', function () {
            adjustValue(this.dataset.adjustTarget, parseFloat(this.dataset.adjustDelta) || 0);
        });
    });

    document.querySelectorAll('.input-nilai').forEach(function (input) {
        input.addEventListener('input', function () {
            if (this.value === '') return;

            const numericValue = parseFloat(this.value);
            if (numericValue > 100) {
                const stringValue = this.value.toString();
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

        input.addEventListener('keydown', function (event) {
            if (['e', 'E', '-', '+'].includes(event.key)) {
                event.preventDefault();
            }
        });

        input.addEventListener('blur', function () {
            const numericValue = parseFloat(this.value);
            if (!Number.isNaN(numericValue)) {
                if (numericValue > 100) this.value = 100;
                if (numericValue < 0) this.value = 0;
            }
        });
    });

    function average(inputs) {
        let sum = 0;
        let count = 0;

        inputs.forEach(function (input) {
            const value = parseFloat(input.value);
            if (!Number.isNaN(value) && input.value.trim() !== '') {
                sum += value;
                count++;
            }
        });

        return count > 0 ? sum / count : null;
    }

    function collectMapelIds() {
        const ids = new Set();
        const pattern = /^(?:tugas|latihan|uh|pts|pas)-input-(\d+)$/;

        document.querySelectorAll('.input-nilai').forEach(function (input) {
            input.className.split(/\s+/).forEach(function (className) {
                const match = className.match(pattern);
                if (match) {
                    ids.add(match[1]);
                }
            });
        });

        return ids;
    }

    function setupRecalculation(mapelId) {
        const tugasInputs = document.querySelectorAll('.tugas-input-' + mapelId);
        const latihanInputs = document.querySelectorAll('.latihan-input-' + mapelId);
        const uhInputs = document.querySelectorAll('.uh-input-' + mapelId);
        const ptsInput = document.querySelector('.pts-input-' + mapelId);
        const pasInput = document.querySelector('.pas-input-' + mapelId);

        const rataTugasEl = document.getElementById('rata_tugas_' + mapelId);
        const rataLatihanEl = document.getElementById('rata_latihan_' + mapelId);
        const rataUhEl = document.getElementById('rata_uh_' + mapelId);
        const nilaiAkhirEl = document.getElementById('nilai_akhir_' + mapelId);

        function recalculate() {
            const rataTugas = average(tugasInputs);
            const rataLatihan = average(latihanInputs);
            const rataUh = average(uhInputs);
            const pts = parseFloat(ptsInput?.value) || 0;
            const pas = parseFloat(pasInput?.value) || 0;

            if (rataTugasEl) rataTugasEl.textContent = rataTugas !== null ? rataTugas.toFixed(1) : '-';
            if (rataLatihanEl) rataLatihanEl.textContent = rataLatihan !== null ? rataLatihan.toFixed(1) : '-';
            if (rataUhEl) rataUhEl.textContent = rataUh !== null ? rataUh.toFixed(1) : '-';

            if (nilaiAkhirEl) {
                if (rataTugas || rataLatihan || rataUh || pts || pas) {
                    const nilaiAkhir = (((rataTugas || 0) * 1) + ((rataLatihan || 0) * 1) + ((rataUh || 0) * 2) + (pts * 3) + (pas * 3)) / 10;
                    nilaiAkhirEl.textContent = nilaiAkhir.toFixed(2);
                } else {
                    nilaiAkhirEl.textContent = '-';
                }
            }
        }

        tugasInputs.forEach(function (input) {
            input.addEventListener('input', recalculate);
        });
        latihanInputs.forEach(function (input) {
            input.addEventListener('input', recalculate);
        });
        uhInputs.forEach(function (input) {
            input.addEventListener('input', recalculate);
        });
        ptsInput?.addEventListener('input', recalculate);
        pasInput?.addEventListener('input', recalculate);
    }

    collectMapelIds().forEach(setupRecalculation);
});
