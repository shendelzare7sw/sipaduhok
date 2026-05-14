<script>
function confirmDeleteCatatan(id, judul) {
    const titleEl = document.getElementById('deleteCatatanJudul');
    const confirmBtn = document.getElementById('confirmDeleteCatatanBtn');
    const form = document.getElementById('deleteCatatanForm');
    const modalEl = document.getElementById('deleteCatatanModal');

    if (!titleEl || !confirmBtn || !form || !modalEl) {
        return;
    }

    titleEl.textContent = judul;
    form.action = @js($basePath ?? '/catatan') + '/' + id;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    confirmBtn.onclick = function() {
        modal.hide();
        form.submit();
    };
}
</script>
