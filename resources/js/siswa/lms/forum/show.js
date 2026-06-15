import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.siswa-lms-forum-show-page');

    if (!page) {
        return;
    }

    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    const getById = (id) => document.getElementById(id);

    const closeForms = () => {
        page.querySelectorAll('.reply-form.is-active').forEach((form) => {
            form.classList.remove('is-active');
        });

        page.querySelectorAll('.edit-form.is-active').forEach((form) => {
            form.classList.remove('is-active');
        });

        page.querySelectorAll('.post-content.is-hidden').forEach((content) => {
            content.classList.remove('is-hidden');
        });
    };

    const toggleReplyForm = (id) => {
        const form = getById(id);

        if (!form) {
            return;
        }

        const isActive = form.classList.contains('is-active');
        closeForms();

        if (!isActive) {
            form.classList.add('is-active');
        }
    };

    const toggleEditForm = (id) => {
        const display = getById(`content-${id}`);
        const form = getById(`edit-${id}`);

        if (!form || !display) {
            return;
        }

        const isActive = form.classList.contains('is-active');
        closeForms();

        if (!isActive) {
            form.classList.add('is-active');
            display.classList.add('is-hidden');
        }
    };

    const toggleAttachArea = (button) => {
        const area = button.nextElementSibling;

        if (!area) {
            return;
        }

        area.classList.toggle('show');
        button.classList.toggle('active', area.classList.contains('show'));
    };

    const submitDeleteForm = (url) => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.className = 'd-none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = getCsrfToken();
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    };

    const confirmDeleteReply = async (url) => {
        const result = await Swal.fire({
            title: 'Hapus Balasan?',
            text: 'Balasan yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        });

        if (result.isConfirmed) {
            submitDeleteForm(url);
        }
    };

    const performSearch = () => {
        const searchInput = page.querySelector('#searchInput');
        const filterRole = page.querySelector('#filterRole');
        const resultBox = page.querySelector('#searchResults');
        const term = searchInput?.value.toLowerCase() || '';
        const role = filterRole?.value || 'all';
        const posts = page.querySelectorAll('.post');
        let visible = 0;

        posts.forEach((post) => {
            const contentText = post.querySelector('.post-content')?.innerText.toLowerCase() || '';
            const author = (post.getAttribute('data-author') || '').toLowerCase();
            const postRole = post.getAttribute('data-role') || '';
            const matchTerm = term === '' || contentText.includes(term) || author.includes(term);
            const matchRole = role === 'all' || postRole === role;
            const shouldShow = matchTerm && matchRole;

            post.classList.toggle('is-hidden', !shouldShow);

            if (shouldShow) {
                visible += 1;
            }
        });

        if (resultBox) {
            resultBox.innerText = term || role !== 'all' ? `Menampilkan ${visible} pesan` : '';
        }
    };

    page.querySelector('#searchInput')?.addEventListener('input', performSearch);
    page.querySelector('#filterRole')?.addEventListener('change', performSearch);

    page.addEventListener('click', (event) => {
        const replyButton = event.target.closest('[data-toggle-reply]');
        const editButton = event.target.closest('[data-toggle-edit]');
        const attachButton = event.target.closest('[data-toggle-attach]');
        const deleteButton = event.target.closest('[data-delete-url]');
        const clearButton = event.target.closest('[data-clear-filters]');

        if (replyButton && page.contains(replyButton)) {
            toggleReplyForm(replyButton.dataset.toggleReply);
            return;
        }

        if (editButton && page.contains(editButton)) {
            toggleEditForm(editButton.dataset.toggleEdit);
            return;
        }

        if (attachButton && page.contains(attachButton)) {
            toggleAttachArea(attachButton);
            return;
        }

        if (deleteButton && page.contains(deleteButton)) {
            confirmDeleteReply(deleteButton.dataset.deleteUrl);
            return;
        }

        if (clearButton && page.contains(clearButton)) {
            const searchInput = page.querySelector('#searchInput');
            const filterRole = page.querySelector('#filterRole');

            if (searchInput) {
                searchInput.value = '';
            }

            if (filterRole) {
                filterRole.value = 'all';
            }

            performSearch();
        }
    });
});
