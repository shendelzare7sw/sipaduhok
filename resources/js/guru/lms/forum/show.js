import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

(() => {
    const page = document.querySelector('.guru-lms-forum-show-page');

    if (!page) {
        return;
    }

    const csrfToken = page.dataset.csrfToken || '';

    const toggleReplyForm = (id) => {
        page.querySelector(`#${CSS.escape(id)}`)?.classList.toggle('active');
    };

    const toggleUploadArea = (button) => {
        const area = button.nextElementSibling;

        if (!area) {
            return;
        }

        area.classList.toggle('show');
        button.classList.toggle('active', area.classList.contains('show'));
    };

    const toggleAttachmentArea = (id, button) => {
        const area = page.querySelector(`#${CSS.escape(id)}`);

        if (!area) {
            return;
        }

        area.classList.toggle('show');
        button?.classList.toggle('active', area.classList.contains('show'));
    };

    const performSearch = () => {
        const searchInput = page.querySelector('#searchInput');
        const filterRole = page.querySelector('#filterRole');
        const filterAuthor = page.querySelector('#filterAuthor');

        if (!searchInput || !filterRole || !filterAuthor) {
            return;
        }

        const searchTerm = searchInput.value.toLowerCase();
        const roleFilter = filterRole.value;
        const authorFilter = filterAuthor.value;
        const posts = page.querySelectorAll('.post');
        let visibleCount = 0;

        posts.forEach((post) => {
            const content = post.querySelector('.post-content')?.textContent.toLowerCase() || '';
            const author = (post.dataset.author || '').toLowerCase();
            const role = post.dataset.role || '';
            const isMine = post.dataset.isMine === 'true';

            const matchSearch = searchTerm === '' || content.includes(searchTerm) || author.includes(searchTerm);
            const matchRole = roleFilter === 'all' || role === roleFilter;
            const matchAuthor = authorFilter === 'all' || (authorFilter === 'my' && isMine);

            if (matchSearch && matchRole && matchAuthor) {
                post.classList.remove('hidden');
                visibleCount += 1;
            } else {
                post.classList.add('hidden');
            }
        });

        const results = page.querySelector('#searchResults');
        if (!results) {
            return;
        }

        if (searchTerm !== '' || roleFilter !== 'all' || authorFilter !== 'all') {
            results.textContent = `Menampilkan ${visibleCount} dari ${posts.length} pesan`;
        } else {
            results.textContent = '';
        }
    };

    const clearFilters = () => {
        const searchInput = page.querySelector('#searchInput');
        const filterRole = page.querySelector('#filterRole');
        const filterAuthor = page.querySelector('#filterAuthor');

        if (searchInput) searchInput.value = '';
        if (filterRole) filterRole.value = 'all';
        if (filterAuthor) filterAuthor.value = 'all';

        performSearch();
    };

    const handleFileSelect = (input) => {
        const file = input.files?.[0];
        const uploadArea = input.closest('.file-upload-area, .attachment-area');

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
                        Browser Anda tidak mendukung preview video.
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

    const updateFileName = (input) => {
        const targetId = input.dataset.fileNameTarget;
        const target = targetId ? page.querySelector(`#${CSS.escape(targetId)}`) : null;

        if (target) {
            target.textContent = input.files?.[0]?.name || '';
        }

        handleFileSelect(input);
    };

    const appendHiddenInput = (form, name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    };

    const submitDeleteForm = (url) => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.className = 'd-none';

        appendHiddenInput(form, '_token', csrfToken);
        appendHiddenInput(form, '_method', 'DELETE');

        document.body.appendChild(form);
        form.submit();
    };

    const confirmDeleteReply = async (url) => {
        try {
            const result = await Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus balasan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            });

            if (result.isConfirmed) {
                submitDeleteForm(url);
            }
        } catch (error) {
            if (window.confirm('Apakah Anda yakin ingin menghapus balasan ini?')) {
                submitDeleteForm(url);
            }
        }
    };

    page.querySelector('#searchInput')?.addEventListener('input', performSearch);
    page.querySelector('#filterRole')?.addEventListener('change', performSearch);
    page.querySelector('#filterAuthor')?.addEventListener('change', performSearch);

    page.addEventListener('click', (event) => {
        const replyToggle = event.target.closest('[data-toggle-reply]');
        const attachToggle = event.target.closest('[data-toggle-attach]');
        const attachmentToggle = event.target.closest('[data-toggle-attachment-area]');
        const clearButton = event.target.closest('[data-clear-filters]');
        const deleteButton = event.target.closest('[data-delete-reply-url]');

        if (replyToggle) {
            event.preventDefault();
            toggleReplyForm(replyToggle.dataset.toggleReply);
            return;
        }

        if (attachToggle) {
            event.preventDefault();
            toggleUploadArea(attachToggle);
            return;
        }

        if (attachmentToggle) {
            event.preventDefault();
            toggleAttachmentArea(attachmentToggle.dataset.toggleAttachmentArea, attachmentToggle);
            return;
        }

        if (clearButton) {
            event.preventDefault();
            clearFilters();
            return;
        }

        if (deleteButton) {
            event.preventDefault();
            confirmDeleteReply(deleteButton.dataset.deleteReplyUrl);
        }
    });

    page.addEventListener('change', (event) => {
        const input = event.target;

        if (input.matches('input[type="file"][name^="attachment"]')) {
            updateFileName(input);
        }
    });
})();
