import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

(() => {
    const page = document.querySelector('.guru-lms-forum-index-page');

    if (!page) {
        return;
    }

    const csrfToken = page.dataset.csrfToken || '';

    const appendHiddenInput = (form, name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    };

    const submitForumForm = (url, method, fields = {}) => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.className = 'd-none';

        appendHiddenInput(form, '_token', csrfToken);
        appendHiddenInput(form, '_method', method);

        Object.entries(fields).forEach(([name, value]) => {
            appendHiddenInput(form, name, value);
        });

        document.body.appendChild(form);
        form.submit();
    };

    const confirmDelete = async (url) => {
        try {
            const result = await Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus diskusi ini?</p>
                        <div class="form-check">
                            <input class="form-check-input border border-secondary" type="checkbox" id="swal-hapus-terkait" value="1">
                            <label class="form-check-label text-danger small" for="swal-hapus-terkait">
                                Hapus juga diskusi ini dari kelas lain? (Jika ada duplikat)
                            </label>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                preConfirm: () => document.getElementById('swal-hapus-terkait')?.checked || false,
            });

            if (result.isConfirmed) {
                submitForumForm(url, 'DELETE', result.value ? { hapus_terkait: '1' } : {});
            }
        } catch (error) {
            if (window.confirm('Apakah Anda yakin ingin menghapus diskusi ini?')) {
                submitForumForm(url, 'DELETE');
            }
        }
    };

    const confirmSyncForum = async (url, title) => {
        try {
            const result = await Swal.fire({
                title,
                html: `
                    <div class="text-start">
                        <p class="mb-3">Apakah Anda yakin ingin melakukan tindakan ini?</p>
                        <div class="alert alert-info py-2 mb-0">
                            <div class="form-check mb-0">
                                <input class="form-check-input border border-primary border-2" type="checkbox" id="swal-sync-forum" checked>
                                <label class="form-check-label fw-bold text-primary" for="swal-sync-forum">
                                    Terapkan juga ke kelas lain?
                                </label>
                            </div>
                            <small class="d-block mt-1 text-muted">
                                Aksi akan diterapkan pada diskusi dengan judul yang sama di kelas yang Anda ampu (jika ada).
                            </small>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                preConfirm: () => document.getElementById('swal-sync-forum')?.checked || false,
            });

            if (result.isConfirmed) {
                submitForumForm(url, 'PATCH', { sync_kelas: result.value ? '1' : '0' });
            }
        } catch (error) {
            if (window.confirm('Apakah Anda yakin ingin melakukan tindakan ini?')) {
                submitForumForm(url, 'PATCH', { sync_kelas: '1' });
            }
        }
    };

    page.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('[data-forum-delete-url]');
        const syncButton = event.target.closest('[data-forum-sync-url]');

        if (!deleteButton && !syncButton) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        if (deleteButton) {
            confirmDelete(deleteButton.dataset.forumDeleteUrl);
            return;
        }

        confirmSyncForum(syncButton.dataset.forumSyncUrl, syncButton.dataset.forumSyncTitle || 'Konfirmasi');
    });
})();
