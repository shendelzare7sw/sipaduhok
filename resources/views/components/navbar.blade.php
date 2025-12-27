<style>
.btn-green {
    background-color: #287f3b;
    color: white;
    transition: 0.3s ease;
}

.btn-green:hover {
    background-color: #1f6a31;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    position: absolute;
    background-color: #f9f9f9;
    min-width: 220px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1000;
    border-radius: 0.375rem;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    padding-top: 8px;
    margin-top: -8px;
    pointer-events: none;
}

.dropdown-content.show,
.dropdown:hover .dropdown-content {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}

.dropdown-content:hover {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}

.dropdown::before {
    content: '';
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    height: 8px;
    z-index: 999;
}

.dropdown-item {
    color: #4B5563;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    transition: background-color 0.3s, color 0.3s;
}

.dropdown-item:hover {
    background-color: #EEF2FF;
    color: #165fac;
}

.nav-item {
    transition: color 0.3s, background-color 0.3s;
    padding: 0.5rem 1rem;
    border-radius: 9999px; 
}

.nav-item:hover {
    color: #165fac;
    background-color: white;
}

.dropdown > .nav-item:hover {
    color: #165fac;
    background-color: white;
}

.active-nav-btn {
    background-color: white;
    color: #165fac !important; 
}

#navbar.sticky-nav .active-nav-btn {
    background-color: #165fac; 
    color: white !important;
}

.nav-btn {
    transition: all 0.3s;
}

.nav-btn:hover {
    background-color: white;
    color: #165fac !important; 
}

#navbar.sticky-nav .nav-btn {
    color: #4B5563; 
}

#navbar.sticky-nav .nav-btn:hover {
    color: #165fac !important; 
    background-color: #f3f4f6; 
}

.dropdown .dropdown-content {
    top: 100%;
    margin-top: 0.5rem;
}

#navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 50;
    transition: all 0.3s ease;
    background-color: transparent; 
}

#navbar.sticky-nav {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

#navbar.sticky-nav .nav-item, 
#navbar.sticky-nav .dropdown .nav-item {
    color: #4B5563;
}

#navbar.sticky-nav .nav-item:hover,
#navbar.sticky-nav .dropdown .nav-item:hover {
    background-color: #f3f4f6; 
    color: #165fac; 
}

#navbar .dropdown-content {
    top: 100%;
}

/* Mobile Menu Styles - Warna Biru */
.mobile-menu {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(22, 95, 172, 0.98); /* Biru dengan transparansi */
    z-index: 100;
    overflow-y: auto;
    padding: 2rem 1rem;
}

.mobile-menu.active {
    display: block;
}

.mobile-menu a {
    color: white;
}

.mobile-menu a:hover {
    color: #e0e7ff;
}

.mobile-menu .active-mobile {
    background-color: rgba(255, 255, 255, 0.2);
    font-weight: 600;
}

.mobile-dropdown-content {
    display: none;
    flex-direction: column;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 0.5rem;
    margin-top: 0.5rem;
}

.mobile-dropdown-content.show {
    display: flex;
    max-height: 500px;
    transition: max-height 0.3s ease-in;
}

.mobile-dropdown-content a {
    padding: 0.75rem 1rem;
    border-left: 3px solid transparent;
}

.mobile-dropdown-content a:hover {
    border-left-color: white;
    background-color: rgba(255, 255, 255, 0.1);
}

.mobile-dropdown-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.mobile-menu-button svg {
    stroke: white;
    transition: stroke 0.3s ease;
}

#navbar.sticky-nav .mobile-menu-button svg {
    stroke: #4B5563; 
}

.mobile-menu-button:hover svg {
    stroke: #165fac; 
}

@media (max-width: 768px) {
    .hidden-mobile {
        display: none;
    }
    
    .block-mobile {
        display: block;
    }
    
    .mobile-menu-button {
        display: block;
    }
}

@media (min-width: 769px) {
    .mobile-menu-button {
        display: none;
    }
}
</style>
<!-- Mobile Menu -->
<div id="mobileMenu" class="mobile-menu">
    <div class="flex justify-end">
        <button id="closeMobileMenu" class="text-white p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <div class="flex flex-col items-center mt-10 space-y-2">
        <!-- Beranda -->
        <a href="{{ url('/') }}" class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center {{ request()->is('/') ? 'active-mobile' : '' }}">Beranda</a>       
        <!-- Profil Dropdown -->
        <div class="w-full">
            <div class="mobile-dropdown py-2">
                <button class="mobile-dropdown-toggle text-white px-4 py-2 text-lg font-medium rounded-full {{ request()->is('tentang-sekolah') || request()->is('visi-misi') || request()->is('sejarah') || request()->is('struktur-organisasi') || request()->is('profil-guru') || request()->is('legalitas') ? 'active-mobile' : '' }}">
                    Profil
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="mobile-dropdown-content px-2">
                    <a href="{{ url('/tentang-sekolah') }}" class="text-white hover:text-white py-2 block {{ request()->is('tentang-sekolah') ? 'font-bold' : '' }}">Tentang Sekolah</a>
                    <a href="{{ url('/visi-misi') }}" class="text-white hover:text-white py-2 block {{ request()->is('visi-misi') ? 'font-bold' : '' }}">Visi & Misi</a>
                    <a href="{{ url('/struktur-organisasi') }}" class="text-white hover:text-white py-2 block {{ request()->is('struktur-organisasi') ? 'font-bold' : '' }}">Struktur Organisasi</a>
                    <a href="{{ url('/profil-guru') }}" class="text-white hover:text-white py-2 block {{ request()->is('profil-guru') ? 'font-bold' : '' }}">Profil Guru & Tenaga Ahli</a>
                </div>
            </div>
        </div>
        <!-- Program Dropdown -->
        <div class="w-full">
            <div class="mobile-dropdown py-2">
                <button class="mobile-dropdown-toggle text-white px-4 py-2 text-lg font-medium rounded-full {{ request()->is('program-paud') || request()->is('program-kb-tk') || request()->is('program-sd') || request()->is('program-smp') || request()->is('program-sma') || request()->is('program-homeschooling') || request()->is('program-inklusi') || request()->is('program-terapi') ? 'active-mobile' : '' }}">
                    Program
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div class="mobile-dropdown-content px-2">
                    <a href="{{ url('/program-paud-tk') }}" class="text-white hover:text-white py-2 block {{ request()->is('program-paud-tk') ? 'font-bold' : '' }}">Pendidikan PAUD - TK</a>
                    <a href="{{ url('/program-sd-sma') }}" class="text-white hover:text-white py-2 block {{ request()->is('program-sd-sma') ? 'font-bold' : '' }}">Pendidikan SD - SMA</a>
                    <a href="{{ url('/program-inklusi') }}" class="text-white hover:text-white py-2 block {{ request()->is('program-inklusi') ? 'font-bold' : '' }}">Program Inklusi</a>
                    <a href="{{ url('/program-terapi') }}" class="text-white hover:text-white py-2 block {{ request()->is('program-terapi') ? 'font-bold' : '' }}">Program Terapi</a>
                </div>
            </div>
        </div>
        <!-- Fasilitas -->
        <a href="{{ url('/fasilitas') }}" 
        class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center 
        {{ request()->is('fasilitas') ? 'active-mobile' : '' }}">
        Fasilitas
        </a>

        <!-- PPDB  -->
        <a href="{{ url('/ppdb') }}" 
        class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center 
        {{ request()->is('ppdb') ? 'active-mobile' : '' }}">
        PPDB
        </a>
        <!-- Galeri -->
        <a href="{{ url('/galeri') }}" class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center {{ request()->is('galeri') ? 'active-mobile' : '' }}">Galeri</a>       
        <!-- Berita -->
        <a href="{{ url('/berita') }}" class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center {{ request()->is('berita') ? 'active-mobile' : '' }}">Berita</a>       
        <!-- Kontak -->
        <a href="{{ url('/kontak') }}" class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center {{ request()->is('kontak') ? 'active-mobile' : '' }}">Kontak</a>       
        <!-- Login Button -->
        <div class="mt-6">
            <a href="/login" class="btn-green font-medium py-2 px-6 rounded-full {{ request()->is('login') ? 'ring-2 ring-white' : '' }}">
                Login
            </a>
        </div>
    </div>
</div>
<!-- Desktop Navbar -->
<nav id="navbar" class="w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" width="100">
                    </a>
                </div>
            </div>            
            <div class="hidden md:block">
                <div class="ml-10 flex items-center space-x-2">
                    <!-- Beranda -->
                    <a href="{{ url('/') }}" class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('/') ? 'active-nav-btn' : '' }}">Beranda</a>
                    <!-- Profil Dropdown -->
                    <div class="dropdown">
                        <button type="button" class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('tentang-sekolah') || request()->is('visi-misi') || request()->is('sejarah') || request()->is('struktur-organisasi') || request()->is('profil-guru') || request()->is('legalitas') ? 'active-nav-btn' : '' }}">
                            Profil 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="dropdown-content w-64">
                            <a href="{{ url('/tentang-sekolah') }}" class="dropdown-item {{ request()->is('tentang-sekolah') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">Tentang Sekolah</a>
                            <a href="{{ url('/visi-misi') }}" class="dropdown-item {{ request()->is('visi-misi') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">Visi & Misi</a>
                            <a href="{{ url('/struktur-organisasi') }}" class="dropdown-item {{ request()->is('struktur-organisasi') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">Struktur Organisasi</a>
                            <a href="{{ url('/profil-guru') }}" class="dropdown-item {{ request()->is('profil-guru') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">Profil Guru & Tenaga Ahli</a>
        
                        </div>
                    </div>
                    <!-- Program Dropdown -->
                    <div class="dropdown">
                        <button type="button" class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('program-paud') || request()->is('program-kb-tk') || request()->is('program-sd') || request()->is('program-smp') || request()->is('program-sma') || request()->is('program-homeschooling') || request()->is('program-inklusi') || request()->is('program-terapi') ? 'active-nav-btn' : '' }}">
                            Program 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="dropdown-content">
                            <a href="{{ url('/program-paud-tk') }}" class="dropdown-item {{ request()->is('program-paud-tk') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">Pendidikan PAUD - TK</a>
                            <a href="{{ url('/program-sd-sma') }}" class="dropdown-item {{ request()->is('program-sd-sma') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">Pendidikan SD - SMA</a>     
                            <a href="{{ url('/program-inklusi') }}" class="dropdown-item {{ request()->is('program-inklusi') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">Program Inklusi</a>
                            <a href="{{ url('/program-terapi') }}" class="dropdown-item {{ request()->is('program-terapi') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">Program Terapi</a>
                        </div>
                    </div>
                    <!-- Fasilitas -->
                     <a href="{{ url('/fasilitas') }}"
                        class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn 
                        {{ request()->is('fasilitas') ? 'active-nav-btn' : '' }}">
                        Fasilitas
                    </a>

                    <!-- PPDB -->
                    <a href="{{ url('/ppdb') }}"
                        class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn 
                        {{ request()->is('ppdb') ? 'active-nav-btn' : '' }}">
                        PPDB
                    </a>
                    <!-- Galeri -->
                    <a href="{{ url('/galeri') }}" class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('galeri') ? 'active-nav-btn' : '' }}">Galeri</a>
                    <!-- Berita -->
                    <a href="{{ url('/berita') }}" class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('berita') ? 'active-nav-btn' : '' }}">Berita</a>      
                    <!-- Kontak -->
                    <a href="{{ url('/kontak') }}" class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('kontak') ? 'active-nav-btn' : '' }}">Kontak</a>
                </div>
            </div>
            
            <div class="flex items-center">
                <a href="/login" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-full transition duration-300 hidden md:inline-block {{ request()->is('login') ? 'ring-2 ring-white' : '' }}">
                    Login
                </a>
                <button id="mobileMenuButton" class="md:hidden text-white ml-4 mobile-menu-button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

<script>
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
</script>