document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-admin-users-index]');

    if (!root || typeof bootstrap === 'undefined') {
        return;
    }

    const buildActionUrl = (template, id) => template.replace('__ID__', encodeURIComponent(id));

    document.querySelectorAll('[data-delete-tenaga-pendidik]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('deleteTenagaPendidikName').textContent = button.dataset.name || '';
            document.getElementById('deleteTenagaPendidikEmail').textContent = button.dataset.email || '';
            document.getElementById('deleteTenagaPendidikRole').textContent = button.dataset.role || '';

            const form = document.getElementById('deleteTenagaPendidikForm');
            form.action = buildActionUrl(root.dataset.deleteTenagaPendidikUrlTemplate, button.dataset.id);

            const modal = new bootstrap.Modal(document.getElementById('deleteTenagaPendidikModal'));
            modal.show();
        });
    });

    document.querySelectorAll('[data-delete-siswa]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('deleteSiswaName').textContent = button.dataset.name || '';
            document.getElementById('deleteSiswaNis').textContent = button.dataset.nis || '';
            document.getElementById('deleteSiswaNisn').textContent = button.dataset.nisn || '';

            const kelasContainer = document.getElementById('deleteSiswaKelasContainer');
            const kelas = button.dataset.kelas || '';

            if (kelas) {
                document.getElementById('deleteSiswaKelas').textContent = kelas;
                kelasContainer.style.display = '';
            } else {
                kelasContainer.style.display = 'none';
            }

            const form = document.getElementById('deleteSiswaForm');
            form.action = buildActionUrl(root.dataset.deleteSiswaUrlTemplate, button.dataset.id);

            const modal = new bootstrap.Modal(document.getElementById('deleteSiswaModal'));
            modal.show();
        });
    });
});
