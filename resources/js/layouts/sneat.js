import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;
// Auto dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Scroll to top button
            const scrollBtn = document.getElementById('scrollToTop');
            let lastScrollY = 0;
            let fabHideTimer = null;

            function showFabsMobile() {
                const chatFab = document.getElementById('aiChatbotFab');
                scrollBtn.classList.remove('fab-hidden-mobile');
                chatFab?.classList.remove('fab-hidden-mobile');
            }

            function hideFabsMobile() {
                const chatFab = document.getElementById('aiChatbotFab');
                scrollBtn.classList.add('fab-hidden-mobile');
                chatFab?.classList.add('fab-hidden-mobile');
            }

            window.addEventListener('scroll', function () {
                const currentScrollY = window.scrollY;

                // Show/hide scroll-to-top based on scroll distance
                if (currentScrollY > 300) {
                    scrollBtn.style.display = 'flex';
                } else {
                    scrollBtn.style.display = 'none';
                }

                // All screens: auto-hide both FABs on scroll down
                if (currentScrollY > lastScrollY && currentScrollY > 80) {
                    // Scrolling down -> hide both
                    hideFabsMobile();
                } else if (currentScrollY < lastScrollY) {
                    // Scrolling up -> show both
                    showFabsMobile();
                }

                // Show again after scroll stops (1.5s)
                clearTimeout(fabHideTimer);
                fabHideTimer = setTimeout(showFabsMobile, 1500);

                lastScrollY = currentScrollY;
            });

            scrollBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // ==========================================
            // MOBILE MENU TOGGLE FUNCTIONALITY
            // ==========================================
            const layoutMenu = document.getElementById('layout-menu');
            const layoutOverlay = document.querySelector('.layout-overlay');
            const menuToggleBtn = document.querySelector('.layout-menu-toggle.navbar-nav a'); // Hamburger
            const contentArea = document.querySelector('.content-wrapper'); // Content area
            const layoutWrapper = document.querySelector('.layout-wrapper');

            // Function to open menu
            function openMenu() {
                if (layoutMenu) layoutMenu.classList.add('menu-shown');
                if (layoutOverlay) layoutOverlay.classList.add('active');
                if (layoutWrapper) layoutWrapper.classList.add('menu-open');
                document.body.style.overflow = 'hidden'; // Prevent body scroll
            }

            // Function to close menu
            function closeMenu() {
                if (layoutMenu) layoutMenu.classList.remove('menu-shown');
                if (layoutOverlay) layoutOverlay.classList.remove('active');
                if (layoutWrapper) layoutWrapper.classList.remove('menu-open');
                document.body.style.overflow = ''; // Restore body scroll
            }
            
            // ==========================================
            // REST OF JAVASCRIPT
            // ==========================================

            // Function to toggle menu
            function toggleMenu() {
                if (layoutMenu && layoutMenu.classList.contains('menu-shown')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            }

            // Toggle menu when clicking hamburger/X
            // stopPropagation: cegah local Sneat menu.js dari menangani event yang sama
            if (menuToggleBtn) {
                menuToggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleMenu();
                });
            }

            // Close menu when clicking sidebar toggle button
            const sidebarToggleBtn = document.getElementById('sidebarToggle');
            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeMenu();
                });
            }

            // Close menu when clicking overlay
            if (layoutOverlay) {
                layoutOverlay.addEventListener('click', function () {
                    closeMenu();
                });
            }

            // Close menu when clicking content area (only on mobile)
            if (contentArea) {
                contentArea.addEventListener('click', function () {
                    if (window.innerWidth < 1200 && layoutMenu.classList.contains('menu-shown')) {
                        closeMenu();
                    }
                });
            }

            // Close menu on window resize to desktop
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1200) {
                    closeMenu();
                }
            });

            // Close menu when clicking any menu item (only on mobile)
            const menuLinks = document.querySelectorAll('.layout-menu .menu-link:not(.menu-toggle)');
            menuLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 1200) {
                        closeMenu();
                    }
                });
            });

            // ==========================================
            // SUBMENU DROPDOWN TOGGLE
            // ==========================================
            const menuToggles = document.querySelectorAll('.menu-toggle');
            menuToggles.forEach(function (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    const menuItem = this.closest('.menu-item');
                    const submenu = menuItem.querySelector('.menu-sub');

                    // Toggle active class
                    menuItem.classList.toggle('open');

                    // Slide toggle submenu
                    if (submenu) {
                        if (submenu.style.display === 'block') {
                            submenu.style.display = 'none';
                        } else {
                            // Close other submenus
                            document.querySelectorAll('.menu-sub').forEach(function (sub) {
                                if (sub !== submenu) {
                                    sub.style.display = 'none';
                                    sub.closest('.menu-item').classList.remove('open');
                                }
                            });
                            submenu.style.display = 'block';
                        }
                    }
                });
            });

            // Open submenu if already active
            const activeMenuItems = document.querySelectorAll('.menu-item.active.open');
            activeMenuItems.forEach(function (item) {
                const submenu = item.querySelector('.menu-sub');
                if (submenu) {
                    submenu.style.display = 'block';
                }
            });
        });

/**
 * Currency Formatter
 * Format input dengan pemisah ribuan (titik) tanpa desimal
 */

class CurrencyFormatter {
    constructor() {
        this.init();
    }

    init() {
        // Auto-bind ke semua input dengan class .currency-input
        document.addEventListener('DOMContentLoaded', () => {
            this.bindInputs();
        });
    }

    bindInputs() {
        const inputs = document.querySelectorAll('.currency-input');
        inputs.forEach(input => {
            // Set initial value jika ada
            if (input.value) {
                input.value = this.formatNumber(input.value);
            }

            // Format saat user mengetik
            input.addEventListener('input', (e) => {
                this.handleInput(e.target);
            });

            // Format saat blur (kehilangan focus)
            input.addEventListener('blur', (e) => {
                this.handleBlur(e.target);
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                setTimeout(() => this.handleInput(e.target), 0);
            });
        });
    }

    handleInput(input) {
        // Simpan posisi cursor
        const cursorPosition = input.selectionStart;
        const oldLength = input.value.length;

        // Hapus semua karakter selain angka
        let value = input.value.replace(/[^0-9]/g, '');

        // Format dengan pemisah ribuan
        const formatted = this.formatNumber(value);

        // Update nilai input
        input.value = formatted;

        // Restore posisi cursor dengan adjustment
        const newLength = formatted.length;
        const diff = newLength - oldLength;
        input.setSelectionRange(cursorPosition + diff, cursorPosition + diff);

        // Update hidden input jika ada
        this.updateHiddenInput(input, value);
    }

    handleBlur(input) {
        // Cleanup format saat blur
        let value = input.value.replace(/[^0-9]/g, '');
        input.value = this.formatNumber(value);
        this.updateHiddenInput(input, value);
    }

    formatNumber(value) {
        // Hapus leading zeros kecuali value = "0"
        value = value.replace(/^0+/, '') || '0';

        // Tambahkan pemisah ribuan (titik)
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    parseValue(formatted) {
        // Parse formatted string ke number
        return parseInt(formatted.replace(/\./g, ''), 10) || 0;
    }

    updateHiddenInput(input, rawValue) {
        // Cari hidden input yang sesuai
        const hiddenInputName = input.name + '_raw';
        let hiddenInput = document.querySelector(`input[name="${hiddenInputName}"]`);

        if (!hiddenInput) {
            // Buat hidden input jika belum ada
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = hiddenInputName;
            input.parentNode.insertBefore(hiddenInput, input.nextSibling);
        }

        hiddenInput.value = rawValue;
    }

    // Static method untuk digunakan di form submit handler
    static prepareFormData(formElement) {
        const currencyInputs = formElement.querySelectorAll('.currency-input');
        currencyInputs.forEach(input => {
            // Ganti nilai dengan raw value (tanpa pemisah)
            const rawValue = input.value.replace(/\./g, '');
            input.value = rawValue;
        });
    }
}

// Initialize
const currencyFormatter = new CurrencyFormatter();

// Helper function untuk manual format
window.formatCurrency = function(value) {
    value = String(value).replace(/[^0-9]/g, '');
    value = value.replace(/^0+/, '') || '0';
    return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

// Helper function untuk parse
window.parseCurrency = function(formatted) {
    return parseInt(String(formatted).replace(/\./g, ''), 10) || 0;
};

// Export untuk digunakan di form submit
window.CurrencyFormatter = CurrencyFormatter;

(function () {
        // Only activate when there is an edit form (hidden _method = PUT/PATCH)
        var hasPutForm = document.querySelector('input[name="_method"][value="PUT"], input[name="_method"][value="PATCH"]');
        if (!hasPutForm) return;

        var isDirty = false;
        var isSubmitting = false;

        document.addEventListener('input', function (e) {
            if (e.target.closest('form')) isDirty = true;
        });
        document.addEventListener('change', function (e) {
            if (e.target.closest('form')) isDirty = true;
        });
        document.addEventListener('submit', function () {
            isSubmitting = true;
        });

        window.addEventListener('beforeunload', function (e) {
            if (isDirty && !isSubmitting) {
                e.preventDefault();
                e.returnValue = 'Perubahan belum disimpan. Yakin ingin meninggalkan halaman ini?';
            }
        });

        // Allow programmatic navigation (e.g. back buttons with href) to bypass
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-unsaved-bypass]');
            if (btn) isSubmitting = true;
        });
    })();
