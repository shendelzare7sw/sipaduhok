import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const fireAlert = (options) => Swal.fire(options);

const deleteTagihan = (button) => {
    const tagihanLabel = button.dataset.tagihanLabel;
    const deleteUrl = button.dataset.deleteUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fireAlert({
        title: 'Hapus Tagihan?',
        html: `Apakah Anda yakin ingin menghapus tagihan <strong>${tagihanLabel}</strong>?<br><small class="text-muted">Aksi ini tidak dapat dibatalkan.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        allowOutsideClick: false,
        allowEscapeKey: false,
    }).then((result) => {
        if (!result.isConfirmed || !deleteUrl) {
            return;
        }

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken || '',
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then((response) => {
                if (response.ok) {
                    return fireAlert({
                        title: 'Terhapus!',
                        text: 'Tagihan berhasil dihapus.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                    }).then(() => {
                        window.location.reload();
                    });
                }

                return response.json().then((data) => {
                    throw new Error(data.error || 'Gagal menghapus tagihan');
                });
            })
            .catch((error) => {
                fireAlert({
                    title: 'Error!',
                    text: error.message || 'Terjadi kesalahan saat menghapus tagihan',
                    icon: 'error',
                    confirmButtonText: 'OK',
                });
            });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-tagihan-btn').forEach((button) => {
        button.addEventListener('click', () => deleteTagihan(button));
    });
});
