document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-template-edit').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('editForm').action = this.dataset.updateUrl || '';
            document.getElementById('edit_mata_pelajaran_id').value = this.dataset.mataPelajaranId || '';
            document.getElementById('edit_nama_template').value = this.dataset.namaTemplate || '';
            document.getElementById('edit_template_text').value = this.dataset.templateText || '';

            bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
        });
    });

    document.getElementById('hapusTemplateModal')?.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        document.getElementById('formHapusTemplate').action = button.dataset.action || '';
        document.getElementById('namaTemplateDihapus').textContent = button.dataset.nama || '';
    });
});
