import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const addButton = e.target.closest('.add-item');
        if (addButton) {
            e.preventDefault();

            const sectionId = addButton.dataset.sectionId;
            const container = document.querySelector(`.items-container[data-section-id="${sectionId}"]`);
            const templateContainer = document.querySelector(`.item-template[data-template-for="${sectionId}"]`);

            if (!templateContainer || !container) {
                console.error('Template or container not found for section: ' + sectionId);
                return;
            }

            const templateCard = templateContainer.querySelector('.item-wrapper');
            if (!templateCard) return;

            const clone = templateCard.cloneNode(true);
            const index = container.querySelectorAll(':scope > .item-wrapper').length;

            clone.classList.remove('d-none');

            const headerSpan = clone.querySelector('.card-header .fw-bold');
            if (headerSpan) headerSpan.textContent = `Item #${index + 1}`;

            clone.querySelectorAll('input, textarea, select').forEach((input) => {
                if (input.name) {
                    input.name = input.name.replace(/TEMPLATE_INDEX/g, index);
                }
                input.disabled = false;
            });

            container.appendChild(clone);
            clone.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const removeButton = e.target.closest('.remove-item');
        if (removeButton) {
            e.preventDefault();

            Swal.fire({
                title: 'Hapus item ini?',
                text: 'Tindakan ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (!result.isConfirmed) return;

                const wrapper = removeButton.closest('.item-wrapper');
                const container = wrapper ? wrapper.closest('.items-container') : null;

                if (!wrapper) return;

                wrapper.classList.add('is-removing');
                setTimeout(() => {
                    wrapper.remove();
                    if (container) reindexItems(container);
                }, 300);
            });
        }
    });

    function reindexItems(container) {
        const items = container.querySelectorAll(':scope > .item-wrapper');
        items.forEach((item, newIndex) => {
            const headerSpan = item.querySelector('.card-header .fw-bold');
            if (headerSpan) headerSpan.textContent = `Item #${newIndex + 1}`;

            item.querySelectorAll('input, textarea, select').forEach((input) => {
                if (input.name) {
                    input.name = input.name.replace(/\[items\]\[\d+\]/g, `[items][${newIndex}]`);
                }
            });
        });
    }

    document.addEventListener('change', function (e) {
        const input = e.target;
        if (input.type !== 'file' || !input.accept || !input.accept.includes('image')) return;

        const file = input.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            const newSrc = event.target.result;

            const prevSibling = input.previousElementSibling;
            if (
                prevSibling &&
                prevSibling.classList.contains('text-center') &&
                prevSibling.classList.contains('bg-light')
            ) {
                prevSibling.replaceChildren(createPreviewImage(newSrc, 'item'));
                return;
            }

            const inputGroup = input.closest('.input-group');
            if (!inputGroup) return;

            let previewSpan = inputGroup.querySelector('.input-group-text');
            if (!previewSpan) {
                previewSpan = document.createElement('span');
                previewSpan.className = 'input-group-text p-0 overflow-hidden landing-preview-thumb-wrap';
                inputGroup.insertBefore(previewSpan, input);
            }

            previewSpan.replaceChildren(createPreviewImage(newSrc, 'thumb'));
        };
        reader.readAsDataURL(file);
    });

    document.addEventListener(
        'error',
        function (e) {
            const image = e.target;
            if (!(image instanceof HTMLImageElement) || !image.dataset.hideParentOnError) return;

            const wrapper = image.closest('.landing-preview-thumb-wrap');
            if (wrapper) wrapper.classList.add('d-none');
        },
        true
    );

    function createPreviewImage(src, mode) {
        const image = document.createElement('img');
        image.src = src;
        image.alt = 'Preview';
        image.className =
            mode === 'item'
                ? 'img-fluid landing-item-preview-img'
                : 'w-100 h-100 landing-preview-thumb-img';
        return image;
    }

    const sections = document.querySelectorAll('.section-card');
    const navLinks = document.querySelectorAll('.section-nav-link');

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    navLinks.forEach((link) => link.classList.remove('active'));
                    const activeLink = document.querySelector(`.section-nav-link[href="#${entry.target.id}"]`);
                    if (activeLink) activeLink.classList.add('active');
                }
            });
        },
        { rootMargin: '-100px 0px -60% 0px' }
    );

    sections.forEach((section) => observer.observe(section));
});
