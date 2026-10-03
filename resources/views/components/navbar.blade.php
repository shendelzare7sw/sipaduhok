@php
    $menuProfil = [
        ['url' => '/tentang-sekolah', 'label' => 'Tentang Sekolah'],
        ['url' => '/visi-misi', 'label' => 'Visi & Misi'],
        ['url' => '/struktur-organisasi', 'label' => 'Struktur Organisasi'],
        ['url' => '/profil-guru', 'label' => 'Profil Guru & Tenaga Ahli'],
    ];
    $menuProgram = [
        ['url' => '/program-paud-tk', 'label' => 'Pendidikan PAUD - TK'],
        ['url' => '/program-sd-sma', 'label' => 'Pendidikan SD - SMA'],
        ['url' => '/program-inklusi', 'label' => 'Program Inklusi'],
        ['url' => '/program-terapi', 'label' => 'Program Terapi'],
    ];
    $menuLain = [
        ['url' => '/fasilitas', 'label' => 'Fasilitas'],
        ['url' => '/ppdb', 'label' => 'PPDB'],
        ['url' => '/galeri', 'label' => 'Galeri'],
        ['url' => '/berita', 'label' => 'Berita'],
        ['url' => '/kontak', 'label' => 'Kontak'],
    ];
    $aktif = fn (string $url) => request()->is(ltrim($url, '/') ?: '/');
    $profilAktif = collect($menuProfil)->contains(fn ($menu) => $aktif($menu['url']));
    $programAktif = collect($menuProgram)->contains(fn ($menu) => $aktif($menu['url']));

    // Tombol menu desktop: putih di atas hero, abu-abu setelah navbar menjadi putih (group is-sticky).
    $tombolNav = 'rounded-full px-4 py-2 text-sm font-medium transition-all duration-300';
    $tombolBiasa = 'text-white hover:bg-white hover:!text-primary group-[.is-sticky]/nav:text-gray-600 group-[.is-sticky]/nav:hover:bg-gray-100';
    $tombolAktif = 'bg-white !text-primary group-[.is-sticky]/nav:bg-primary group-[.is-sticky]/nav:!text-white';
    $itemDropdown = 'block px-4 py-3 no-underline transition-colors duration-300 hover:bg-[#EEF2FF] hover:text-primary';
    $tautanMobile = 'w-full rounded-full px-4 py-2 text-center text-lg font-medium text-white no-underline hover:text-indigo-100';
@endphp

<div x-data="navbarPublik" x-on:keydown.escape.window="menuMobile = false">
    {{-- Menu mobile layar penuh --}}
    <div x-show="menuMobile" x-cloak class="fixed inset-0 z-[100] overflow-y-auto bg-[rgba(22,95,172,0.98)] px-4 py-8" role="dialog" aria-modal="true" aria-label="Menu navigasi">
        <div class="flex justify-end">
            <button type="button" x-on:click="menuMobile = false" class="p-2 text-white" aria-label="Tutup menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="mt-10 flex flex-col items-center space-y-2">
            <a href="{{ url('/') }}" class="{{ $tautanMobile }} {{ $aktif('/') ? 'bg-white/20 font-semibold' : '' }}">Beranda</a>

            @foreach([['Profil', $menuProfil, $profilAktif], ['Program', $menuProgram, $programAktif]] as [$judul, $daftar, $grupAktif])
                <div class="w-full py-2" x-data="{ buka: false }">
                    <button type="button" x-on:click="buka = !buka" :aria-expanded="buka" class="flex w-full items-center justify-center rounded-full px-4 py-2 text-lg font-medium text-white {{ $grupAktif ? 'bg-white/20 font-semibold' : '' }}">
                        {{ $judul }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    <div x-show="buka" x-cloak class="mt-2 flex flex-col overflow-hidden rounded-lg bg-black/20 px-2">
                        @foreach($daftar as $menu)
                            <a href="{{ url($menu['url']) }}" class="block border-l-[3px] border-transparent px-4 py-3 text-white no-underline hover:border-white hover:bg-white/10 hover:text-indigo-100 {{ $aktif($menu['url']) ? 'font-bold' : '' }}">{{ $menu['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @foreach($menuLain as $menu)
                <a href="{{ url($menu['url']) }}" class="{{ $tautanMobile }} {{ $aktif($menu['url']) ? 'bg-white/20 font-semibold' : '' }}">{{ $menu['label'] }}</a>
            @endforeach

            <div class="mt-6">
                <a href="{{ route('login') }}" class="rounded-full bg-secondary px-6 py-2 font-medium text-white no-underline transition duration-300 hover:bg-[#1f6a31] hover:text-indigo-100 {{ request()->is('login') ? 'ring-2 ring-white' : '' }}">Login</a>
            </div>
        </div>
    </div>

    {{-- Navbar desktop --}}
    <nav class="group/nav fixed top-0 z-50 w-full transition-all duration-300" :class="terscroll ? 'is-sticky bg-white shadow-[0_2px_10px_rgba(0,0,0,0.1)]' : 'bg-transparent'">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ url('/') }}"><img src="{{ asset('img/logo.png') }}" alt="Logo" width="100"></a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-center space-x-2">
                        <a href="{{ url('/') }}" class="{{ $tombolNav }} {{ $aktif('/') ? $tombolAktif : $tombolBiasa }}">Beranda</a>

                        @foreach([['Profil', $menuProfil, $profilAktif, 'w-64'], ['Program', $menuProgram, $programAktif, '']] as [$judul, $daftar, $grupAktif, $lebar])
                            <div class="relative inline-block before:absolute before:inset-x-0 before:top-full before:z-[999] before:h-2 before:content-['']"
                                 x-data="dropdownNav" x-on:mouseenter="tampil()" x-on:mouseleave="sembunyi()" x-on:click.outside="buka = false" x-on:keydown.escape.window="buka = false">
                                <button type="button" x-on:click="tampil()" :aria-expanded="buka" class="{{ $tombolNav }} {{ $grupAktif ? $tombolAktif : $tombolBiasa }}">
                                    {{ $judul }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 inline h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                                <div class="absolute top-full z-[1000] mt-2 min-w-[220px] {{ $lebar }} rounded-md bg-[#f9f9f9] pt-2 shadow-[0px_8px_16px_0px_rgba(0,0,0,0.2)] transition-all duration-300"
                                     :class="buka ? 'visible translate-y-0 opacity-100' : 'pointer-events-none invisible -translate-y-2.5 opacity-0'">
                                    @foreach($daftar as $menu)
                                        <a href="{{ url($menu['url']) }}" class="{{ $itemDropdown }} {{ $aktif($menu['url']) ? 'bg-blue-50 font-semibold text-blue-600' : 'text-gray-600' }}">{{ $menu['label'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @foreach($menuLain as $menu)
                            <a href="{{ url($menu['url']) }}" class="{{ $tombolNav }} {{ $aktif($menu['url']) ? $tombolAktif : $tombolBiasa }}">{{ $menu['label'] }}</a>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center">
                    <a href="{{ route('login') }}" class="hidden rounded-full bg-green-600 px-6 py-2 font-medium text-white no-underline transition duration-300 hover:bg-green-700 md:inline-block {{ request()->is('login') ? 'ring-2 ring-white' : '' }}">Login</a>
                    <button type="button" x-on:click="menuMobile = true" class="group/menu ml-4 block md:hidden" aria-label="Buka menu">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-white transition-colors duration-300 group-[.is-sticky]/nav:stroke-gray-600 group-hover/menu:!stroke-primary" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</div>
