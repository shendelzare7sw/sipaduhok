/* Page asset: wali-kelas/rapor/edit. */
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

document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.rapor-edit-page');
    const csrfToken = page?.dataset.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';
    const reorderUrl = page?.dataset.reorderUrl || '';
    const kehadiranUrl = page?.dataset.kehadiranUrl || '';

    function updateTotalKetidakhadiran() {
        const sakit = parseInt(document.querySelector('input[name="jumlah_sakit"]')?.value, 10) || 0;
        const izin = parseInt(document.querySelector('input[name="jumlah_izin"]')?.value, 10) || 0;
        const alpha = parseInt(document.querySelector('input[name="jumlah_alpha"]')?.value, 10) || 0;
        const total = document.getElementById('totalKetidakhadiran');
        if (total) {
            total.textContent = sakit + izin + alpha;
        }
    }

    document.querySelectorAll('input[name="jumlah_sakit"], input[name="jumlah_izin"], input[name="jumlah_alpha"]').forEach(function (input) {
        input.addEventListener('input', updateTotalKetidakhadiran);
    });

    function updateRowNumbers() {
        document.querySelectorAll('#nilaiSortable tr').forEach(function (row, index) {
            const numCell = row.querySelector('.row-number');
            if (numCell) {
                numCell.textContent = index + 1;
            }
        });
    }

    function saveOrder() {
        if (!reorderUrl) return;

        const rows = document.querySelectorAll('#nilaiSortable tr[data-id]');
        const order = Array.from(rows).map(function (row) {
            return row.dataset.id;
        });

        fetch(reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                order: order,
                sync_all: document.getElementById('syncOrderAll')?.checked || false,
            }),
        }).catch(function (error) {
            console.error('Reorder error:', error);
        });
    }

    const sortableEl = document.getElementById('nilaiSortable');
    if (sortableEl && window.Sortable) {
        new window.Sortable(sortableEl, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            delay: 150,
            delayOnTouchOnly: true,
            touchStartThreshold: 5,
            forceFallback: false,
            onEnd: function () {
                updateRowNumbers();
                saveOrder();
            },
        });
    }

    function moveRow(btn, direction) {
        const row = btn.closest('tr');
        const tbody = row?.parentNode;
        if (!row || !tbody) return;

        if (direction === 'up' && row.previousElementSibling) {
            tbody.insertBefore(row, row.previousElementSibling);
        } else if (direction === 'down' && row.nextElementSibling) {
            tbody.insertBefore(row.nextElementSibling, row);
        }

        updateRowNumbers();
        saveOrder();
    }

    document.querySelectorAll('[data-move-row]').forEach(function (button) {
        button.addEventListener('click', function () {
            moveRow(this, this.dataset.moveRow);
        });
    });

    document.querySelectorAll('.visibility-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const row = this.closest('tr');
            row?.classList.toggle('row-hidden', !this.checked);
        });
    });

    let kegiatanIndex = document.querySelectorAll('#kegiatanBody tr').length;

    function addKegiatanRow() {
        const tbody = document.getElementById('kegiatanBody');
        if (!tbody) return;

        const alignment = document.querySelector('[name="keterangan_ekstra_alignment"]')?.value || 'left';
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center align-middle kegiatan-no" data-label="No">${kegiatanIndex + 1}</td>
            <td data-label="Nama Kegiatan"><input type="text" name="kegiatan_ekstra[${kegiatanIndex}][kegiatan_nama]" class="form-control form-control-sm" placeholder="Nama kegiatan..."></td>
            <td data-label="Predikat"><select name="kegiatan_ekstra[${kegiatanIndex}][predikat]" class="form-select form-select-sm"><option value="">-</option><option value="A">A</option><option value="B">B</option><option value="C">C</option></select></td>
            <td data-label="Keterangan"><input type="text" name="kegiatan_ekstra[${kegiatanIndex}][keterangan]" class="form-control form-control-sm keterangan-ekstra-input alignment-${alignment}" placeholder="Keterangan..."></td>
            <td class="text-center" data-label="Aksi"><button type="button" class="btn btn-outline-danger btn-sm" data-remove-kegiatan><i class="fas fa-trash-alt"></i></button></td>
        `;
        tbody.appendChild(row);
        kegiatanIndex++;
    }

    function removeKegiatanRow(btn) {
        btn.closest('tr')?.remove();
        document.querySelectorAll('#kegiatanBody .kegiatan-no').forEach(function (element, index) {
            element.textContent = index + 1;
        });
    }

    document.querySelector('[data-add-kegiatan]')?.addEventListener('click', addKegiatanRow);
    document.getElementById('kegiatanBody')?.addEventListener('click', function (event) {
        const button = event.target.closest('[data-remove-kegiatan]');
        if (button) {
            removeKegiatanRow(button);
        }
    });

    const syncButton = document.getElementById('btnSyncKehadiran');
    syncButton?.addEventListener('click', function () {
        if (!kehadiranUrl) return;

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sinkronisasi...';

        fetch(kehadiranUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.success) {
                    document.querySelector('input[name="jumlah_sakit"]').value = data.sakit;
                    document.querySelector('input[name="jumlah_izin"]').value = data.izin;
                    document.querySelector('input[name="jumlah_alpha"]').value = data.alpha;
                    updateTotalKetidakhadiran();
                    btn.innerHTML = '<i class="fas fa-check me-1"></i>Berhasil!';
                    btn.classList.replace('btn-outline-info', 'btn-outline-success');
                    setTimeout(function () {
                        btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Sinkron dari Presensi';
                        btn.classList.replace('btn-outline-success', 'btn-outline-info');
                    }, 2000);
                }
            })
            .catch(function () {
                btn.innerHTML = '<i class="fas fa-times me-1"></i>Gagal';
                btn.classList.replace('btn-outline-info', 'btn-outline-danger');
                setTimeout(function () {
                    btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i>Sinkron dari Presensi';
                    btn.classList.replace('btn-outline-danger', 'btn-outline-info');
                }, 2000);
            })
            .finally(function () {
                btn.disabled = false;
            });
    });

    document.querySelectorAll('.rapor-alignment-select').forEach(function (select) {
        const applyAlignment = function () {
            document.querySelectorAll(select.dataset.target).forEach(function (target) {
                target.classList.remove('alignment-left', 'alignment-center', 'alignment-right', 'alignment-justify');
                target.classList.add('alignment-' + select.value);
            });
        };

        select.addEventListener('change', applyAlignment);
        applyAlignment();
    });

    // Auto-grow textarea "Deskripsi Capaian" agar teks panjang (mis. hasil terapan
    // template) langsung terlihat tanpa perlu di-resize manual.
    (function initDeskripsiAutoGrow() {
        const grow = function (el) {
            el.style.height = 'auto';
            el.style.height = (el.scrollHeight + 2) + 'px';
        };
        document.querySelectorAll('#nilaiTable .deskripsi-input').forEach(function (ta) {
            grow(ta);
            ta.addEventListener('input', function () { grow(ta); });
        });
    })();
});
