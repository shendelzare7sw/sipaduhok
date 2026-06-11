/* Akademik index behavior extracted from the former shared loader. */
document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('akademikIndexConfig');
    const basePath = (configEl?.dataset.basePath || '').replace(/\/$/, '');
    const deleteLabelId = configEl?.dataset.deleteLabelId || 'deleteAkademikName';

    const getModal = (modalEl) => {
        if (!modalEl || typeof bootstrap === 'undefined') {
            return null;
        }

        return bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    };

    const buildUrl = (id, suffix = '') => `${basePath}/${id}${suffix}`;

    document.querySelectorAll('[data-ak-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => field.form?.submit());
    });

    document.querySelectorAll('[data-ak-delete]').forEach((button) => {
        button.addEventListener('click', () => {
            const titleEl = document.getElementById(deleteLabelId);
            const form = document.getElementById('deleteForm');
            const modal = getModal(document.getElementById('deleteModal'));

            if (!titleEl || !form || !modal) {
                return;
            }

            titleEl.textContent = button.dataset.akDeleteTitle || '';
            form.action = buildUrl(button.dataset.akDeleteId || '');
            modal.show();
        });
    });

    document.querySelectorAll('[data-ak-open-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            getModal(document.getElementById(button.dataset.akOpenModal))?.show();
        });
    });

    document.querySelectorAll('[data-ak-featured-confirm]').forEach((button) => {
        button.addEventListener('click', async () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const beritaId = button.dataset.beritaId;

            if (!csrfToken || !beritaId) {
                window.alert('CSRF token atau data berita tidak ditemukan.');
                return;
            }

            try {
                const response = await fetch(buildUrl(beritaId, '/toggle-featured'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.content,
                    },
                });

                const data = await response.json();

                if (data.success) {
                    getModal(button.closest('.modal'))?.hide();
                    setTimeout(() => window.location.reload(), 250);
                    return;
                }

                window.alert(`Gagal: ${data.message || 'Terjadi kesalahan'}`);
            } catch (error) {
                console.error(error);
                window.alert('Terjadi kesalahan koneksi.');
            }
        });
    });
});
