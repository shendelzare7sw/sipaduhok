import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.siswa-lms-mapel-forum-show-page');

    if (!page) {
        return;
    }

    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

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

    const toggleReplyForm = (id) => {
        const form = document.getElementById(id);

        if (form) {
            form.classList.toggle('is-active');
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

    const handleFileSelect = (input) => {
        const file = input.files?.[0];
        const uploadArea = input.closest('.file-upload-area');

        if (!uploadArea) {
            return;
        }

        uploadArea.querySelector('.file-preview')?.remove();

        if (!file) {
            return;
        }

        const reader = new FileReader();
        const preview = document.createElement('div');
        preview.className = 'file-preview mt-3 text-center';

        reader.onload = (event) => {
            if (file.type.startsWith('image/')) {
                preview.innerHTML = `
                    <img src="${event.target.result}" class="img-fluid rounded border forum-preview-media" alt="">
                    <p class="small text-muted mt-1">${file.name}</p>
                `;
            } else if (file.type.startsWith('video/')) {
                preview.innerHTML = `
                    <video controls class="w-100 rounded border forum-preview-media">
                        <source src="${event.target.result}" type="${file.type}">
                    </video>
                    <p class="small text-muted mt-1">${file.name}</p>
                `;
            } else {
                preview.innerHTML = `
                    <div class="p-3 border rounded bg-light">
                        <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                        <p class="mb-0 text-truncate">${file.name}</p>
                    </div>
                `;
            }
        };

        reader.readAsDataURL(file);
        uploadArea.appendChild(preview);
    };

    const performSearch = () => {
        const searchTerm = page.querySelector('#searchInput')?.value.toLowerCase() || '';
        const roleFilter = page.querySelector('#filterRole')?.value || 'all';
        const authorFilter = page.querySelector('#filterAuthor')?.value || 'all';
        const posts = page.querySelectorAll('.post');
        const results = page.querySelector('#searchResults');
        let visibleCount = 0;

        posts.forEach((post) => {
            const content = post.querySelector('.post-content')?.textContent.toLowerCase() || '';
            const author = (post.dataset.author || '').toLowerCase();
            const role = post.dataset.role || '';
            const isMine = post.dataset.isMine === 'true';
            const matchSearch = searchTerm === '' || content.includes(searchTerm) || author.includes(searchTerm);
            const matchRole = roleFilter === 'all' || role === roleFilter;
            const matchAuthor = authorFilter === 'all' || (authorFilter === 'my' && isMine);
            const shouldShow = matchSearch && matchRole && matchAuthor;

            post.classList.toggle('is-hidden', !shouldShow);

            if (shouldShow) {
                visibleCount += 1;
            }
        });

        if (results) {
            results.textContent = searchTerm !== '' || roleFilter !== 'all' || authorFilter !== 'all'
                ? `Menampilkan ${visibleCount} dari ${posts.length} pesan`
                : '';
        }
    };

    page.querySelector('#searchInput')?.addEventListener('input', performSearch);
    page.querySelector('#filterRole')?.addEventListener('change', performSearch);
    page.querySelector('#filterAuthor')?.addEventListener('change', performSearch);

    page.addEventListener('click', (event) => {
        const replyButton = event.target.closest('[data-toggle-reply]');
        const attachButton = event.target.closest('[data-toggle-attach]');
        const deleteButton = event.target.closest('[data-delete-url]');
        const clearButton = event.target.closest('[data-clear-filters]');

        if (replyButton && page.contains(replyButton)) {
            toggleReplyForm(replyButton.dataset.toggleReply);
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
            const filterAuthor = page.querySelector('#filterAuthor');

            if (searchInput) {
                searchInput.value = '';
            }

            if (filterRole) {
                filterRole.value = 'all';
            }

            if (filterAuthor) {
                filterAuthor.value = 'all';
            }

            performSearch();
        }
    });

    page.addEventListener('change', (event) => {
        if (event.target.matches('input[type="file"][name="attachment[]"]')) {
            handleFileSelect(event.target);
        }
    });
});
