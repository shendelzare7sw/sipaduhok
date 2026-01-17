/**
 * Confirm Modal Handler
 * Reusable modal untuk konfirmasi aksi
 */

class ConfirmModal {
    constructor() {
        this.modal = null;
        this.confirmBtn = null;
        this.callback = null;
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.modal = document.getElementById('confirmModal');
            this.confirmBtn = document.getElementById('confirmModalBtn');

            if (this.modal && this.confirmBtn) {
                // Bootstrap 5 modal instance
                this.modalInstance = new bootstrap.Modal(this.modal);

                // Handle confirm button click
                this.confirmBtn.addEventListener('click', () => {
                    if (this.callback && typeof this.callback === 'function') {
                        this.callback();
                    }
                    this.modalInstance.hide();
                });

                // Reset callback saat modal ditutup
                this.modal.addEventListener('hidden.bs.modal', () => {
                    this.callback = null;
                });
            }
        });
    }

    show(options = {}) {
        const {
            title = 'Konfirmasi',
            message = 'Apakah Anda yakin?',
            type = 'warning', // warning, danger, success, info
            confirmText = 'Ya, Lanjutkan',
            cancelText = 'Batal',
            onConfirm = null
        } = options;

        // Set title
        const titleElement = document.getElementById('confirmModalTitle');
        if (titleElement) titleElement.textContent = title;

        // Set message
        const messageElement = document.getElementById('confirmModalMessage');
        if (messageElement) messageElement.textContent = message;

        // Set icon based on type
        const iconElement = document.getElementById('confirmModalIcon');
        if (iconElement) {
            let iconHTML = '';
            switch (type) {
                case 'danger':
                    iconHTML = '<i class="bx bx-error-circle text-danger" style="font-size: 4rem;"></i>';
                    break;
                case 'warning':
                    iconHTML = '<i class="bx bx-error text-warning" style="font-size: 4rem;"></i>';
                    break;
                case 'success':
                    iconHTML = '<i class="bx bx-check-circle text-success" style="font-size: 4rem;"></i>';
                    break;
                case 'info':
                    iconHTML = '<i class="bx bx-info-circle text-info" style="font-size: 4rem;"></i>';
                    break;
                default:
                    iconHTML = '<i class="bx bx-help-circle text-secondary" style="font-size: 4rem;"></i>';
            }
            iconElement.innerHTML = iconHTML;
        }

        // Set button text and color
        const confirmBtnElement = document.getElementById('confirmModalBtn');
        if (confirmBtnElement) {
            confirmBtnElement.textContent = confirmText;

            // Remove all color classes
            confirmBtnElement.classList.remove('btn-primary', 'btn-danger', 'btn-warning', 'btn-success', 'btn-info');

            // Add appropriate color class
            switch (type) {
                case 'danger':
                    confirmBtnElement.classList.add('btn-danger');
                    break;
                case 'warning':
                    confirmBtnElement.classList.add('btn-warning');
                    break;
                case 'success':
                    confirmBtnElement.classList.add('btn-success');
                    break;
                case 'info':
                    confirmBtnElement.classList.add('btn-info');
                    break;
                default:
                    confirmBtnElement.classList.add('btn-primary');
            }
        }

        // Set header color
        const headerElement = document.getElementById('confirmModalHeader');
        if (headerElement) {
            headerElement.classList.remove('bg-danger', 'bg-warning', 'bg-success', 'bg-info');
            headerElement.classList.remove('text-white');

            if (type === 'danger') {
                headerElement.classList.add('bg-danger', 'text-white');
            } else if (type === 'warning') {
                headerElement.classList.add('bg-warning');
            }
        }

        // Store callback
        this.callback = onConfirm;

        // Show modal
        if (this.modalInstance) {
            this.modalInstance.show();
        }
    }

    hide() {
        if (this.modalInstance) {
            this.modalInstance.hide();
        }
    }
}

// Initialize
const confirmModalHandler = new ConfirmModal();

// Global helper function
window.showConfirm = function(options) {
    confirmModalHandler.show(options);
};

// Backward compatibility - simple signature
window.showConfirmSimple = function(title, message, callback, type = 'warning') {
    confirmModalHandler.show({
        title: title,
        message: message,
        type: type,
        onConfirm: callback
    });
};

// Helper untuk form delete dengan konfirmasi
window.confirmDelete = function(formId, itemName = 'data ini') {
    showConfirm({
        title: 'Konfirmasi Hapus',
        message: `Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`,
        type: 'danger',
        confirmText: 'Ya, Hapus',
        onConfirm: function() {
            document.getElementById(formId).submit();
        }
    });
    return false; // Prevent default form submission
};

// Helper untuk konfirmasi umum dengan submit form
window.confirmSubmit = function(formId, title, message) {
    showConfirm({
        title: title,
        message: message,
        type: 'warning',
        onConfirm: function() {
            document.getElementById(formId).submit();
        }
    });
    return false;
};
