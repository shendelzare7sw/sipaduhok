document.addEventListener('DOMContentLoaded', () => {
    const config = document.getElementById('calendarShowConfig');
    const deleteButton = document.querySelector('[data-confirm-delete]');
    const deleteForm = document.getElementById('deleteForm');
    const deleteName = document.getElementById('deleteKalenderName');
    const visibilitySwitch = document.querySelector('[data-toggle-visibility]');
    const visibilityLabel = document.getElementById('visibilityLabel');

    const setVisibilityLabel = (isVisible) => {
        if (!visibilityLabel) {
            return;
        }

        const icon = document.createElement('i');
        icon.className = `fas ${isVisible ? 'fa-eye' : 'fa-eye-slash'} me-2`;

        visibilityLabel.className = `badge calendar-detail-badge ${isVisible ? 'bg-primary' : 'bg-secondary'}`;
        visibilityLabel.replaceChildren(icon, isVisible ? ' TAMPIL DI SISWA' : ' DISEMBUNYIKAN');
    };

    deleteButton?.addEventListener('click', () => {
        if (!config || !deleteForm || !deleteName) {
            return;
        }

        const id = deleteButton.dataset.kalenderId;
        const name = deleteButton.dataset.kalenderName || '';

        deleteName.textContent = name;
        deleteForm.action = config.dataset.deleteRouteTemplate.replace(':id', id);

        const modalElement = document.getElementById('deleteModal');
        if (modalElement && typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modalElement).show();
        }
    });

    visibilitySwitch?.addEventListener('change', async (event) => {
        if (!config || !visibilityLabel) {
            return;
        }

        const checkbox = event.currentTarget;
        const id = checkbox.dataset.kalenderId;
        const isChecked = checkbox.checked;

        setVisibilityLabel(isChecked);

        try {
            const response = await fetch(config.dataset.toggleRouteTemplate.replace(':id', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.dataset.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Gagal update status');
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 3000,
                });
            }
        } catch (error) {
            console.error('Error:', error);

            checkbox.checked = !isChecked;
            setVisibilityLabel(!isChecked);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengubah status visibilitas.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } else {
                alert('Gagal ubah status');
            }
        }
    });
});
