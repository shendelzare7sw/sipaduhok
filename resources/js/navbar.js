document.addEventListener('DOMContentLoaded', function() {
    // Sticky navbar on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 100) {
            navbar.classList.add('sticky-nav');
        } else {
            navbar.classList.remove('sticky-nav');
        }
    });

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const mobileMenu = document.getElementById('mobileMenu');

    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    closeMobileMenu.addEventListener('click', function() {
        mobileMenu.classList.remove('active');
        document.body.style.overflow = 'auto';
    });

    // Mobile dropdown toggles
    const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');

    mobileDropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const dropdownContent = this.nextElementSibling;
            dropdownContent.classList.toggle('show');
        });
    });

    // Desktop dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const dropdownTrigger = dropdown.querySelector('button');
        const dropdownContent = dropdown.querySelector('.dropdown-content');
        let hoverTimeout;
        let isOpen = false;

        function showDropdown() {
            clearTimeout(hoverTimeout);
            dropdownContent.classList.add('show');
            isOpen = true;
        }

        function hideDropdown() {
            hoverTimeout = setTimeout(() => {
                dropdownContent.classList.remove('show');
                isOpen = false;
            }, 250);
        }

        dropdown.addEventListener('mouseenter', () => {
            showDropdown();
        });

        dropdown.addEventListener('mouseleave', () => {
            hideDropdown();
        });

        if (dropdownTrigger) {
            dropdownTrigger.addEventListener('click', (e) => {
                if (isOpen && dropdownTrigger.getAttribute('href') !== '#') {
                    return true;
                }

                if (!isOpen) {
                    e.preventDefault();
                    showDropdown();
                }
            });
        }

        if (dropdownContent) {
            dropdownContent.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
            });

            dropdownContent.addEventListener('mouseleave', () => {
                hideDropdown();
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        dropdowns.forEach(dropdown => {
            const dropdownContent = dropdown.querySelector('.dropdown-content');
            if (!dropdown.contains(event.target)) {
                if (dropdownContent) {
                    dropdownContent.classList.remove('show');
                }
            }
        });
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            dropdowns.forEach(dropdown => {
                const dropdownContent = dropdown.querySelector('.dropdown-content');
                if (dropdownContent) {
                    dropdownContent.classList.remove('show');
                }
            });
        }
    });
});
