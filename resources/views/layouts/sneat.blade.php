<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('/') }}" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - SIPADUHOK</title>

    <meta name="description" content="Sistem Informasi PKBM Duta House of Knowledge">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Icons (Font Awesome) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Core CSS (Sneat Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Sneat Core CSS from CDN -->
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/assets/vendor/css/core.css">
    <link rel="stylesheet" href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/assets/vendor/css/theme-default.css">

    <!-- Custom SIPADUHOK Styles -->
    <style>
        :root {
            /* SIPADUHOK Brand Colors */
            --bs-primary: #165fac;
            --bs-primary-rgb: 22, 95, 172;
            --primary-color: #165fac;
            --primary-dark: #0d3a6b;
            --primary-light: #4a8fd4;
        }

        /* Override Sneat primary color dengan brand SIPADUHOK */
        .bg-menu-theme {
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
        }

        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
        }

        /* Menu aktif */
        .menu-item.active > .menu-link {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #fff !important;
            font-weight: 600;
        }

        /* Logo Brand */
        .app-brand-logo img {
            max-height: 50px;
            max-width: 100%;
        }

        .app-brand-text {
            color: #fff !important;
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Sidebar Menu Items */
        .menu-item .menu-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.2s;
        }

        .menu-item .menu-link:hover {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Navbar User Info */
        .navbar-nav-right .dropdown-toggle::after {
            display: none;
        }

        /* Avatar Circle */
        .avatar-initial {
            background-color: var(--primary-color) !important;
            color: #fff;
            font-weight: 700;
        }

        /* Navbar adjustments */
        .layout-navbar {
            border-bottom: 1px solid #e7e7e7 !important;
        }

        /* Navbar Title & Breadcrumb Styling */
        .navbar-nav-right {
            justify-content: space-between;
            width: 100%;
        }

        /* Page Title Container in Navbar */
        .navbar-page-title {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 1.5rem;
            flex: 1;
        }

        .navbar-page-title h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #344054;
            line-height: 1.3;
        }

        .navbar-page-title small {
            font-size: 0.813rem;
            color: #6c757d;
            margin-top: 2px;
        }

        /* Card Hover Effect */
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        /* Alert Animation */
        .alert {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Menu Icon Spacing */
        .menu-icon {
            width: 24px;
            margin-right: 0.75rem;
            text-align: center;
        }

        /* Fix Menu Inner Shadow Overlap */
        .menu-inner-shadow {
            display: none !important;
        }

        .menu-inner {
            position: relative !important;
            z-index: 1 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        /* Ensure sidebar menu stays on top and visible */
        .layout-menu {
            z-index: 1045 !important;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
        }

        /* Menu Header Styling */
        .menu-header {
            padding: 0.75rem 1.5rem;
            margin-top: 0.75rem;
        }

        .menu-header-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6) !important;
            letter-spacing: 0.5px;
        }

        /* Footer */
        .content-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e7e7e7;
        }

        /* Pagination Styling - Enhanced & Clear */
        .pagination {
            gap: 6px;
            margin: 0;
        }

        .pagination .page-link {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 16px;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .pagination .page-link:hover {
            background-color: #f8f9fa;
            border-color: #14b8a6;
            color: #14b8a6;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(20, 184, 166, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #14b8a6;
            border-color: #14b8a6;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            background-color: #fff;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
        }

        /* Icon spacing dalam pagination */
        .pagination .page-link i {
            font-size: 0.875rem;
        }

        /* Modal fixes for Bootstrap 5 */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .modal {
            z-index: 1050;
        }

        .modal-dialog {
            z-index: 1060;
        }

        .modal-content {
            background-color: #fff;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.5);
            border: none;
        }

        /* Ensure modal header close button is properly positioned */
        .modal-header .btn-close {
            padding: 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
        }

        /* CRITICAL FIX: Full Width Content Container */
        .container-xxl {
            max-width: 100% !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }

        /* Adjust content wrapper padding for better spacing */
        .content-wrapper {
            padding: 0 !important;
        }

        .container-p-y {
            padding-top: 1.625rem !important;
            padding-bottom: 1.625rem !important;
        }

        /* Ensure layout page takes full width */
        .layout-page {
            width: 100% !important;
            background: transparent !important;
        }

        /* Prevent layout elements from covering sidebar */
        .layout-wrapper {
            background: transparent !important;
        }

        .layout-container {
            background: transparent !important;
        }

        /* ==========================================
           STICKY NAVBAR - FOLLOWS ON SCROLL
           ========================================== */
        .layout-navbar {
            position: sticky !important;
            top: 0 !important;
            z-index: 1040 !important;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.08);
        }

        /* Add padding to content wrapper to prevent content from jumping behind sticky navbar */
        .content-wrapper {
            padding-top: 0 !important;
        }

        /* ==========================================
           MOBILE MENU TOGGLE - ONLY MOBILE STYLES
           ========================================== */
        @media (max-width: 1199.98px) {
            /* Hide menu off-screen by default */
            .layout-menu {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                width: 260px;
                z-index: 1100;
                transform: translate3d(-100%, 0, 0);
                transition: transform 0.3s ease-in-out;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            }

            /* Show menu when menu-shown class is added */
            .layout-menu.menu-shown {
                transform: translate3d(0, 0, 0) !important;
            }

            /* Overlay backdrop */
            .layout-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1050;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }

            .layout-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            /* Hamburger button - transform to X when menu open */
            .layout-menu-toggle.navbar-nav a {
                cursor: pointer;
                transition: all 0.3s;
                position: relative;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .layout-menu-toggle.navbar-nav a i {
                transition: all 0.3s;
            }

            /* Transform hamburger to X when menu is open */
            .menu-open .layout-menu-toggle.navbar-nav a i.fa-bars::before {
                content: "\f00d"; /* FontAwesome times/X icon */
            }

            /* Sidebar toggle button styling */
            .layout-menu-toggle-sidebar {
                color: #fff !important;
                opacity: 1 !important;
            }

            .layout-menu-toggle-sidebar:hover {
                opacity: 0.8 !important;
                background-color: rgba(255, 255, 255, 0.1) !important;
                border-radius: 6px;
            }

            .layout-menu-toggle-sidebar i {
                color: #fff !important;
            }

            /* Page title responsive */
            .navbar-page-title h5 {
                font-size: 1.1rem;
            }

            .navbar-page-title small {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 991.98px) {
            .container-xxl {
                padding-left: 1.25rem !important;
                padding-right: 1.25rem !important;
            }
        }

        @media (max-width: 767.98px) {
            .container-xxl {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .container-p-y {
                padding-top: 1.25rem !important;
                padding-bottom: 1.25rem !important;
            }

            .navbar-page-title {
                padding-left: 0.75rem;
            }

            .navbar-page-title h5 {
                font-size: 1rem;
            }

            .navbar-page-title small {
                display: none; /* Hide subtitle on mobile for cleaner look */
            }

            /* Hamburger icon size */
            .layout-menu-toggle.navbar-nav a i {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .pagination .page-link {
                padding: 8px 12px;
                font-size: 14px;
            }

            .container-xxl {
                padding-left: 0.875rem !important;
                padding-right: 0.875rem !important;
            }

            /* Adjust navbar padding */
            .layout-navbar {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            /* Smaller icons on very small screens */
            .navbar-nav .avatar {
                width: 36px;
                height: 36px;
            }

            .navbar-nav .avatar-initial {
                font-size: 0.875rem;
            }

            .navbar-nav .fa-bell {
                font-size: 1.2rem !important;
            }

            .layout-menu-toggle.navbar-nav a i {
                font-size: 1.4rem;
            }

            .navbar-page-title h5 {
                font-size: 0.9rem;
            }
        }
    </style>

    @yield('styles')
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
                        <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size: 1rem;">SIPADUHOK</span>
                    </a>

                    <!-- Toggle button in sidebar (mobile only) -->
                    <a href="javascript:void(0);" class="layout-menu-toggle-sidebar menu-link text-large d-xl-none" id="sidebarToggle" style="margin-left: 1rem;">
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
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
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
                            <li class="nav-item navbar-dropdown dropdown me-3 me-xl-4">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="fas fa-bell fa-lg"></i>
                                    <span class="badge rounded-pill bg-danger badge-notifications">3</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-envelope me-2"></i> 4 pesan baru
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-users me-2"></i> 8 permintaan mengikuti
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-file me-2"></i> 3 laporan baru
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">Lihat semua notifikasi</a>
                                    </li>
                                </ul>
                            </li>

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-primary">A</span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <span class="avatar-initial rounded-circle bg-primary">A</span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ auth()->user()->name ?? 'Admin' }}</h6>
                                                    <small class="text-muted">{{ ucfirst(auth()->user()->role ?? 'admin') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-user me-2"></i>
                                            <span class="align-middle">Profil Saya</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-cog me-2"></i>
                                            <span class="align-middle">Pengaturan</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
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
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                Copyright © {{ date('Y') }}
                                <a href="{{ route('home') }}" target="_blank" class="footer-link fw-semibold">SIPADUHOK</a> -
                                PKBM House of Knowledge
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
    <a href="#" class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle" id="scrollToTop" style="width: 48px; height: 48px; display: none; z-index: 1050;">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- jQuery (load first) -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/assets/vendor/libs/jquery/jquery.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sneat Menu JS (only menu, skip helpers that might conflict) -->
    <script src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/assets/vendor/js/menu.js"></script>

    <!-- Custom SIPADUHOK Scripts -->
    <script>
        // Auto dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Scroll to top button
            const scrollBtn = document.getElementById('scrollToTop');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    scrollBtn.style.display = 'flex';
                    scrollBtn.style.alignItems = 'center';
                    scrollBtn.style.justifyContent = 'center';
                } else {
                    scrollBtn.style.display = 'none';
                }
            });

            scrollBtn.addEventListener('click', function(e) {
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

            // Function to toggle menu
            function toggleMenu() {
                if (layoutMenu && layoutMenu.classList.contains('menu-shown')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            }

            // Toggle menu when clicking hamburger/X
            if (menuToggleBtn) {
                menuToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleMenu();
                });
            }

            // Close menu when clicking sidebar toggle button
            const sidebarToggleBtn = document.getElementById('sidebarToggle');
            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMenu();
                });
            }

            // Close menu when clicking overlay
            if (layoutOverlay) {
                layoutOverlay.addEventListener('click', function() {
                    closeMenu();
                });
            }

            // Close menu when clicking content area (only on mobile)
            if (contentArea) {
                contentArea.addEventListener('click', function() {
                    if (window.innerWidth < 1200 && layoutMenu.classList.contains('menu-shown')) {
                        closeMenu();
                    }
                });
            }

            // Close menu on window resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1200) {
                    closeMenu();
                }
            });

            // Close menu when clicking any menu item (only on mobile)
            const menuLinks = document.querySelectorAll('.layout-menu .menu-link:not(.menu-toggle)');
            menuLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1200) {
                        closeMenu();
                    }
                });
            });

            // ==========================================
            // SUBMENU DROPDOWN TOGGLE
            // ==========================================
            const menuToggles = document.querySelectorAll('.menu-toggle');
            menuToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
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
                            document.querySelectorAll('.menu-sub').forEach(function(sub) {
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
            activeMenuItems.forEach(function(item) {
                const submenu = item.querySelector('.menu-sub');
                if (submenu) {
                    submenu.style.display = 'block';
                }
            });
        });
    </script>

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
                <div class="modal-body" style="font-size: 1rem;">
                    Apakah Anda yakin ingin keluar dari sistem? <br>
                    <small class="text-muted" style="font-size: 0.9rem;">Anda perlu login kembali untuk mengakses dashboard.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                            <i class="fas fa-sign-out-alt me-1"></i> Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
