<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('/') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - SIPADUHOK</title>

    <meta name="description" content="Sistem Informasi PKBM Duta House of Knowledge">

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <!-- Layout Assets -->
    @vite([
        'resources/css/layouts/sneat.css',
        'resources/css/components/notification-bell.css',
    ])
    @auth
        @if(canAccessChatbot(auth()->user()->role))
            @vite(['resources/css/components/ai-chatbot.css'])
        @endif
    @endauth
    @yield('styles')
    @stack('styles')
    @vite(['resources/css/layouts/sneat-overrides.css'])
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Sidebar/Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route('home') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="app-brand-logo img">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold ms-2 sneat-brand-text">SIPADUHOK</span>
                    </a>

                    <!-- Toggle button in sidebar (mobile only) -->
                    <a href="#"
                        class="layout-menu-toggle-sidebar menu-link text-large d-xl-none sneat-sidebar-toggle"
                        id="sidebarToggle">
                        <i class="fas fa-bars fa-lg"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    @yield('sidebar-menu')
                </ul>
            </aside>
            
            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="#">
                            <i class="fas fa-bars fa-lg"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

                        <!-- Page Title & Subtitle -->
                        <div class="navbar-page-title">
                            <h5>@yield('page-title', 'Dashboard')</h5>
                            @if(trim($__env->yieldContent('page-subtitle')))
                                <small>@yield('page-subtitle')</small>
                            @endif
                        </div>

                        <!-- Right Side Navbar -->
                        <ul class="navbar-nav flex-row align-items-center ms-auto">

                            <!-- Notifications -->
                            <li class="nav-item me-3 me-xl-4">
                                <x-notification-bell />
                            </li>

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="#"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        @if(auth()->user()->foto_profil)
                                            <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="Avatar"
                                                class="rounded-circle sneat-avatar-img">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-primary">
                                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.index') }}">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        @if(auth()->user()->foto_profil)
                                                            <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                                                                alt="Avatar" class="rounded-circle sneat-avatar-img">
                                                        @else
                                                            <span class="avatar-initial rounded-circle bg-primary">
                                                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ auth()->user()->name ?? 'Admin' }}</h6>
                                                    <small class="text-muted">
                                                        {{ auth()->user()->roleRelation ? auth()->user()->roleRelation->display_name : ucwords(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.index') }}">
                                            <i class="fas fa-user me-2"></i>
                                            <span class="align-middle">Profil Saya</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('account.settings') }}">
                                            <i class="fas fa-cog me-2"></i>
                                            <span class="align-middle">Pengaturan</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#logoutModal">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            <span class="align-middle">Logout</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <!-- Flash Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Page Content -->
                        @yield('content')

                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div
                            class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                Copyright &copy; {{ date('Y') }}
                                <a href="{{ route('home') }}" target="_blank"
                                    class="footer-link fw-semibold">SIPADUHOK</a> -
                                PKBM House of Knowledge
                            </div>
                            <div>
                                @if(in_array(auth()->user()->role ?? '', ['admin', 'waka', 'super_admin']))
                                    <a href="https://wa.me/6282113100791?text=Halo%20Developer,%20saya%20menemukan%20kendala/bug%20pada%20sistem" 
                                       target="_blank" rel="noopener noreferrer" class="footer-link me-4">
                                       <i class="fab fa-whatsapp me-1 text-success"></i> Kontak Developer
                                    </a>
                                @endif
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Scroll to Top Button -->
    <!-- Desktop: bottom: 82px = sejajar dengan chatbot FAB (side by side) -->
    <a href="#" class="btn btn-primary position-fixed rounded-circle shadow sneat-scroll-to-top" id="scrollToTop">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt me-2"></i>Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body sneat-logout-body">
                    Apakah Anda yakin ingin keluar dari sistem? <br>
                    <small class="text-muted sneat-logout-note">Anda perlu login kembali untuk mengakses
                        dashboard.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary sneat-logout-action" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger sneat-logout-action">
                            <i class="fas fa-sign-out-alt me-1"></i> Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @vite([
        'resources/js/layouts/sneat.js',
        'resources/js/components/notification-bell.js',
    ])
    @stack('scripts')
    @yield('scripts')

    {{-- AI Chatbot + Sistem Helper - Dual Mode (Role-based access) --}}
    @auth
        @if(canAccessChatbot(auth()->user()->role))
            @vite(['resources/js/components/ai-chatbot.js'])
            @include('components.ai-chatbot')
        @endif
    @endauth

    @stack('modals')
</body>

</html>
