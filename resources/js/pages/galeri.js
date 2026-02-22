document.addEventListener('DOMContentLoaded', function () {

    // Filter Functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.masonry-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const filter = this.getAttribute('data-filter');
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            galleryItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                    setTimeout(() => item.classList.add('active'), 100);
                } else {
                    item.style.display = 'none';
                    item.classList.remove('active');
                }
            });
        });
    });

    // Scroll Reveal Animation
    const revealElements = document.querySelectorAll('.scroll-reveal');
    const revealOnScroll = () => {
        revealElements.forEach(el => {
            if (el.getBoundingClientRect().top < window.innerHeight - 100) {
                el.classList.add('active');
            }
        });
    };
    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll();

    // Modal Functionality
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalDate = document.getElementById('modalDate');
    const closeModal = document.getElementById('closeModal');

    let currentImageIndex = 0;
    const galleryItemsArray = Array.from(document.querySelectorAll('.gallery-item'));

    galleryItems.forEach((item, index) => {
        item.addEventListener('click', function () {
            currentImageIndex = index;
            openModal(this);
        });
    });

    function openModal(item) {
        const img = item.querySelector('img');
        const title = item.querySelector('h3').textContent;
        const date = item.querySelector('.gallery-info p').textContent;
        modal.classList.add('active');
        modalImg.src = img.src;
        if (modalTitle) modalTitle.textContent = title;
        if (modalDate) modalDate.textContent = date;
        document.body.style.overflow = 'hidden';
    }

    closeModal.addEventListener('click', function () {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });

    // Modal Navigation
    document.getElementById('prevImage').addEventListener('click', function (e) {
        e.stopPropagation();
        currentImageIndex = (currentImageIndex - 1 + galleryItemsArray.length) % galleryItemsArray.length;
        openModal(galleryItemsArray[currentImageIndex]);
    });

    document.getElementById('nextImage').addEventListener('click', function (e) {
        e.stopPropagation();
        currentImageIndex = (currentImageIndex + 1) % galleryItemsArray.length;
        openModal(galleryItemsArray[currentImageIndex]);
    });

    // Keyboard Navigation
    document.addEventListener('keydown', function (e) {
        if (modal.classList.contains('active')) {
            if (e.key === 'ArrowLeft') document.getElementById('prevImage').click();
            else if (e.key === 'ArrowRight') document.getElementById('nextImage').click();
            else if (e.key === 'Escape') closeModal.click();
        }
    });
});
