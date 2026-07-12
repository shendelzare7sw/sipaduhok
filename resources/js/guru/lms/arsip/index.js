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

    form.addEventListener('change', function (e) {
        if (e.target.classList && e.target.classList.contains('arsip-check')) {
            refresh();
        }
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            allChecks().forEach((c) => {
                c.checked = false;
            });
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
