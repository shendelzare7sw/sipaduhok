document.addEventListener('DOMContentLoaded', function () {

    // ==================== CAROUSEL FUNCTIONALITY ====================
    class Carousel {
        constructor(container) {
            this.container = container;
            this.wrapper = container.querySelector('.carousel-wrapper');
            this.slides = container.querySelectorAll('.carousel-slide');
            this.prevBtn = container.querySelector('.carousel-nav.prev');
            this.nextBtn = container.querySelector('.carousel-nav.next');
            this.indicators = container.querySelectorAll('.carousel-indicator');
            this.currentIndex = 0;
            this.autoPlayInterval = null;
            this.init();
        }

        init() {
            this.prevBtn.addEventListener('click', () => this.prev());
            this.nextBtn.addEventListener('click', () => this.next());
            this.indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => this.goTo(index));
            });
            this.addSwipeSupport();
            this.startAutoPlay();
            this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
            this.container.addEventListener('mouseleave', () => this.startAutoPlay());
        }

        goTo(index) {
            this.currentIndex = index;
            this.updateCarousel();
        }

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.slides.length;
            this.updateCarousel();
        }

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
            this.updateCarousel();
        }

        updateCarousel() {
            this.wrapper.style.transform = `translateX(-${this.currentIndex * 100}%)`;
            this.indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === this.currentIndex);
            });
        }

        startAutoPlay() {
            this.autoPlayInterval = setInterval(() => this.next(), 5000);
        }

        stopAutoPlay() {
            if (this.autoPlayInterval) {
                clearInterval(this.autoPlayInterval);
                this.autoPlayInterval = null;
            }
        }

        addSwipeSupport() {
            let startX = 0;
            let endX = 0;
            this.container.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
            this.container.addEventListener('touchmove', (e) => { endX = e.touches[0].clientX; }, { passive: true });
            this.container.addEventListener('touchend', () => {
                const diff = startX - endX;
                if (Math.abs(diff) > 50) {
                    diff > 0 ? this.next() : this.prev();
                }
            });
        }
    }

    // Initialize all carousels
    document.querySelectorAll('[data-carousel]').forEach(container => {
        new Carousel(container);
    });

    // ==================== SMOOTH SCROLL ====================
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

    // ==================== INTERSECTION OBSERVER ====================
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.carousel-container').forEach(card => observer.observe(card));
    document.querySelectorAll('.gallery-image').forEach(image => observer.observe(image));

    // ==================== NUMBER COUNTER ====================
    const animateCount = (element, target) => {
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        const originalText = element.textContent;
        const hasPlus = originalText.includes('+');
        const hasSquare = originalText.includes('m²');
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target + (hasPlus ? '+' : '') + (hasSquare ? 'm²' : '');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current) + (hasPlus ? '+' : '') + (hasSquare ? 'm²' : '');
            }
        }, 16);
    };

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.querySelectorAll('.stat-number').forEach(stat => {
                    const number = parseInt(stat.textContent.replace(/\D/g, ''));
                    if (!isNaN(number)) { stat.textContent = '0'; animateCount(stat, number); }
                });
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    const statsSection = document.querySelector('.py-20.bg-white');
    if (statsSection) statsObserver.observe(statsSection);

    // ==================== GALLERY LIGHTBOX ====================
    document.querySelectorAll('.gallery-image').forEach(image => {
        image.addEventListener('click', function() {
            const img = this.querySelector('img');
            const lightbox = document.createElement('div');
            lightbox.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4 cursor-pointer';
            lightbox.style.animation = 'fadeIn 0.3s ease';

            const lightboxImg = document.createElement('img');
            lightboxImg.src = img.src;
            lightboxImg.alt = img.alt;
            lightboxImg.className = 'max-w-full max-h-full object-contain rounded-lg';
            lightboxImg.style.animation = 'scaleIn 0.3s ease';

            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '×';
            closeBtn.className = 'absolute top-4 right-4 text-white text-5xl font-light hover:text-gray-300 transition-colors';

            lightbox.appendChild(lightboxImg);
            lightbox.appendChild(closeBtn);
            document.body.appendChild(lightbox);
            document.body.style.overflow = 'hidden';

            const closeLightbox = () => {
                lightbox.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    if (document.body.contains(lightbox)) document.body.removeChild(lightbox);
                    document.body.style.overflow = 'auto';
                }, 300);
            };

            lightbox.addEventListener('click', closeLightbox);
            closeBtn.addEventListener('click', closeLightbox);
            lightboxImg.addEventListener('click', (e) => e.stopPropagation());

            document.addEventListener('keydown', function escapeHandler(e) {
                if (e.key === 'Escape') { closeLightbox(); document.removeEventListener('keydown', escapeHandler); }
            });
        });
    });

    // ==================== FEATURE ITEMS ====================
    const featureObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.querySelectorAll('.feature-item').forEach((item, index) => {
                    setTimeout(() => { item.style.opacity = '1'; item.style.transform = 'translateX(0)'; }, index * 100);
                });
                featureObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.space-y-4').forEach(section => featureObserver.observe(section));

    // ==================== HERO STATS COUNTER ====================
    const heroStatsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.querySelectorAll('.text-4xl').forEach(stat => {
                    const text = stat.textContent;
                    const number = parseInt(text.replace(/\D/g, ''));
                    if (!isNaN(number)) {
                        let current = 0;
                        const increment = number / 50;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= number) { stat.textContent = text; clearInterval(timer); }
                            else { stat.textContent = Math.floor(current) + (text.includes('+') ? '+' : ''); }
                        }, 30);
                    }
                });
                heroStatsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    const heroStatsGrid = document.querySelector('.grid.grid-cols-2.md\\:grid-cols-4');
    if (heroStatsGrid) heroStatsObserver.observe(heroStatsGrid);

    // ==================== SCROLL PROGRESS ====================
    const progressBar = document.createElement('div');
    progressBar.className = 'fixed top-0 left-0 h-1 bg-gradient-to-r from-primary to-secondary z-50 transition-all duration-300';
    progressBar.style.width = '0%';
    document.body.appendChild(progressBar);

    window.addEventListener('scroll', () => {
        const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        progressBar.style.width = ((window.pageYOffset / windowHeight) * 100) + '%';
    });

    // ==================== BACK TO TOP ====================
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>';
    backToTop.className = 'fixed bottom-8 right-8 w-14 h-14 bg-primary text-white rounded-full shadow-lg hover:bg-secondary transition-all duration-300 z-40 opacity-0 pointer-events-none flex items-center justify-center';
    document.body.appendChild(backToTop);

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 500) {
            backToTop.style.opacity = '1';
            backToTop.style.pointerEvents = 'auto';
        } else {
            backToTop.style.opacity = '0';
            backToTop.style.pointerEvents = 'none';
        }
    });

    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // ==================== LIGHTBOX ANIMATIONS ====================
    if (!document.querySelector('#lightbox-animations')) {
        const style = document.createElement('style');
        style.id = 'lightbox-animations';
        style.textContent = `@keyframes fadeIn{from{opacity:0}to{opacity:1}}@keyframes fadeOut{from{opacity:1}to{opacity:0}}`;
        document.head.appendChild(style);
    }
});
