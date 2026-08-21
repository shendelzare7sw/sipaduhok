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
        <!-- Tampilan publik difokuskan pada layanan homeschooling perorangan. -->
        <a href="{{ url('/program-homeschooling') }}"
            class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center {{ request()->is('program-homeschooling') ? 'active-mobile' : '' }}">
            Homeschooling
        </a>

        <!-- Pendaftaran -->
        <a href="{{ route('pendaftaran') }}"
        class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center 
        {{ request()->is('pendaftaran') ? 'active-mobile' : '' }}">
        Pendaftaran
        </a>
        <!-- Kontak -->
        <a href="{{ url('/kontak') }}" class="text-white px-4 py-2 text-lg font-medium rounded-full w-full text-center {{ request()->is('kontak') ? 'active-mobile' : '' }}">Kontak</a>       
        <!-- Login Button -->
        <div class="mt-6">
            <a href="{{ route('login') }}" class="btn-green font-medium py-2 px-6 rounded-full {{ request()->is('login') ? 'ring-2 ring-white' : '' }}">
                Masuk LMS
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
                        <img src="{{ asset('img/logo/logo.png') }}" alt="SipaduHOK" class="h-12 w-auto max-w-[180px] object-contain">
                    </a>
                </div>
            </div>            
            <div class="hidden md:block">
                <div class="ml-10 flex items-center space-x-2">
                    <!-- Beranda -->
                    <a href="{{ url('/') }}" class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('/') ? 'active-nav-btn' : '' }}">Beranda</a>
                    <a href="{{ url('/program-homeschooling') }}"
                        class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('program-homeschooling') ? 'active-nav-btn' : '' }}">
                        Homeschooling
                    </a>

                    <!-- Pendaftaran -->
                    <a href="{{ route('pendaftaran') }}"
                        class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn 
                        {{ request()->is('pendaftaran') ? 'active-nav-btn' : '' }}">
                        Pendaftaran
                    </a>
                    <!-- Kontak -->
                    <a href="{{ url('/kontak') }}" class="text-white px-4 py-2 rounded-full text-sm font-medium nav-btn {{ request()->is('kontak') ? 'active-nav-btn' : '' }}">Kontak</a>
                </div>
            </div>
            
            <div class="flex items-center">
                <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-full transition duration-300 hidden md:inline-block {{ request()->is('login') ? 'ring-2 ring-white' : '' }}">
                    Masuk LMS
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
