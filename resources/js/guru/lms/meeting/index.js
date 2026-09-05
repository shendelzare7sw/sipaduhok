import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const showSuccessNotification = (button, originalHtml, originalClass) => {
    button.innerHTML = '<i class="fas fa-check me-1"></i> Tersalin!';
    button.className = 'btn btn-success btn-sm text-white';

    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.className = originalClass;
    }, 2000);

    Swal.fire({
        toast: true,
        position: 'bottom-end',
        icon: 'success',
        title: 'Link Meeting Tersalin!',
        text: 'Link telah disalin ke clipboard Anda.',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
};

const fallbackCopyToClipboard = (link, button, originalHtml, originalClass) => {
    const textarea = document.createElement('textarea');
    const targetContainer = button.closest('.guru-lms-meeting-page') || document.body;
    textarea.value = link;
    textarea.className = 'pointer-events-none fixed opacity-0';
    targetContainer.appendChild(textarea);

    try {
        textarea.select();
        document.execCommand('copy');
        showSuccessNotification(button, originalHtml, originalClass);
    } catch (error) {
        console.error('Fallback copy failed:', error);
        window.alert('Gagal menyalin link ke clipboard');
    } finally {
        targetContainer.removeChild(textarea);
    }
};

const copyLink = (button) => {
    const link = button.dataset.copyLink;

    if (!link) {
        window.alert('Link tidak ditemukan');
        return;
    }

    const originalHtml = button.innerHTML;
    const originalClass = button.className;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(link)
            .then(() => showSuccessNotification(button, originalHtml, originalClass))
            .catch((error) => {
                console.warn('Clipboard API failed, using fallback:', error);
                fallbackCopyToClipboard(link, button, originalHtml, originalClass);
            });
        return;
    }

    fallbackCopyToClipboard(link, button, originalHtml, originalClass);
};

document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-meeting-page');
    if (!page) {
        return;
    }

    page.querySelectorAll('[data-copy-link]').forEach((button) => {
        button.addEventListener('click', () => copyLink(button));
    });

    const deleteForm = page.querySelector('#deleteForm');
    const deleteModalElement = page.querySelector('#deleteModal');
    const hapusTerkaitCheck = page.querySelector('#hapusTerkaitCheck');

    if (!deleteForm || !deleteModalElement || !window.bootstrap) {
        return;
    }

    const deleteModal = new window.bootstrap.Modal(deleteModalElement);

    page.querySelectorAll('[data-delete-url]').forEach((button) => {
        button.addEventListener('click', () => {
            deleteForm.action = button.dataset.deleteUrl || '';

            if (hapusTerkaitCheck) {
                hapusTerkaitCheck.checked = false;
            }

            deleteModal.show();
        });
    });
});
