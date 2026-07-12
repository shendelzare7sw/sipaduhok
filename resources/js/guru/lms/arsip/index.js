/* Arsip LMS Guru — seleksi & salin massal */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('bulkSalinForm');
    if (!form) return;

    const bar = document.getElementById('bulkBar');
    const countEl = document.getElementById('bulkCount');
    const countBtnEl = document.getElementById('bulkCountBtn');
    const tujuan = document.getElementById('bulkTujuan');
    const kelasInput = document.getElementById('bulkKelasId');
    const mapelInput = document.getElementById('bulkMapelId');
    const cancelBtn = document.getElementById('bulkCancel');

    const allChecks = () => Array.from(form.querySelectorAll('.arsip-check'));
    const selected = () => allChecks().filter((c) => c.checked);

    function refresh() {
        const n = selected().length;
        if (countEl) countEl.textContent = n;
        if (countBtnEl) countBtnEl.textContent = n;
        if (bar) bar.hidden = n === 0;
    }

    // Sinkronkan checkbox "Pilih semua" per tipe (checked / indeterminate).
    function syncSectionSelectAll(section) {
        if (!section) return;
        const sa = section.querySelector('.arsip-select-all');
        if (!sa) return;
        const boxes = Array.from(section.querySelectorAll('.arsip-check'));
        const checked = boxes.filter((c) => c.checked).length;
        sa.checked = checked > 0 && checked === boxes.length;
        sa.indeterminate = checked > 0 && checked < boxes.length;
    }

    function syncAllSections() {
        form.querySelectorAll('.arsip-section').forEach(syncSectionSelectAll);
    }

    form.addEventListener('change', function (e) {
        const t = e.target;
        if (!t.classList) return;

        if (t.classList.contains('arsip-select-all')) {
            // Toggle semua item di tipe (section) ini.
            const section = t.closest('.arsip-section');
            if (section) {
                section.querySelectorAll('.arsip-check').forEach((c) => {
                    c.checked = t.checked;
                });
            }
            refresh();
        } else if (t.classList.contains('arsip-check')) {
            syncSectionSelectAll(t.closest('.arsip-section'));
            refresh();
        }
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            allChecks().forEach((c) => {
                c.checked = false;
            });
            syncAllSections();
            refresh();
        });
    }

    form.addEventListener('submit', function (e) {
        if (selected().length === 0) {
            e.preventDefault();
            return;
        }
        // Pecah "kelasId|mapelId" ke dua hidden input.
        const parts = tujuan && tujuan.value ? tujuan.value.split('|') : [];
        if (parts.length !== 2) {
            e.preventDefault();
            alert('Pilih kelas tujuan terlebih dahulu.');
            return;
        }
        kelasInput.value = parts[0];
        mapelInput.value = parts[1];
    });

    refresh();
});
