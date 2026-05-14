document.addEventListener('DOMContentLoaded', function() {

    // ========== 3D Carousel Script ==========
    const items = document.querySelectorAll('.carousel-item');
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    const newsTitle = document.getElementById('newsTitle');
    const newsCategory = document.getElementById('newsCategory');
    const dotsContainer = document.getElementById('carouselDots');
    const carouselSection = document.querySelector('.carousel-3d');

    // PHP data is passed via window.PageData from the blade template
    const newsData = (window.PageData && window.PageData.newsData) ? window.PageData.newsData : [];

    let currentIndex = 0;
    const totalItems = items.length;

    // Build dots
    if (dotsContainer && totalItems > 1) {
        for (let i = 0; i < totalItems; i++) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('aria-label', `Slide ${i + 1}`);
            dot.addEventListener('click', () => {
                currentIndex = i;
                updateCarousel();
                restartAutoRotate();
            });
            dotsContainer.appendChild(dot);
        }
    }
    const dots = dotsContainer ? dotsContainer.querySelectorAll('button') : [];

    function updateCarousel() {
        items.forEach((item, index) => {
            item.classList.remove('center', 'left-1', 'left-2', 'right-1', 'right-2', 'hidden-item');

            const diff = (index - currentIndex + totalItems) % totalItems;

            if (diff === 0) {
                item.classList.add('center');
            } else if (diff === 1) {
                item.classList.add('right-1');
            } else if (diff === totalItems - 1) {
                item.classList.add('left-1');
            } else if (diff === 2) {
                item.classList.add('right-2');
            } else if (diff === totalItems - 2) {
                item.classList.add('left-2');
            } else {
                item.classList.add('hidden-item');
            }
        });

        if (newsData && newsData[currentIndex]) {
            if (newsTitle) newsTitle.textContent = newsData[currentIndex].title;
            if (newsCategory) newsCategory.textContent = newsData[currentIndex].category;
        }

        dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        updateCarousel();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        updateCarousel();
    }

    if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); restartAutoRotate(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); restartAutoRotate(); });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key === 'ArrowRight') nextSlide();
    });

    // Auto rotate
    let autoRotate = setInterval(nextSlide, 5000);
    function restartAutoRotate() {
        clearInterval(autoRotate);
        autoRotate = setInterval(nextSlide, 5000);
    }

    if (carouselSection) {
        carouselSection.addEventListener('mouseenter', () => clearInterval(autoRotate));
        carouselSection.addEventListener('mouseleave', () => {
            autoRotate = setInterval(nextSlide, 5000);
        });

        // Touch swipe support (mobile)
        let touchStartX = 0;
        let touchEndX = 0;
        const SWIPE_THRESHOLD = 40;

        carouselSection.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            clearInterval(autoRotate);
        }, { passive: true });

        carouselSection.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const delta = touchEndX - touchStartX;
            if (Math.abs(delta) > SWIPE_THRESHOLD) {
                if (delta < 0) nextSlide(); else prevSlide();
            }
            restartAutoRotate();
        }, { passive: true });
    }

    // Initialize carousel
    if (totalItems > 0) updateCarousel();

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                const offset = 100;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });

    // Scroll indicator fade
    const scrollIndicator = document.querySelector('.scroll-indicator');
    if (scrollIndicator) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollIndicator.classList.add('fade-out');
            } else {
                scrollIndicator.classList.remove('fade-out');
            }
        });
    }
});
