<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LMS Guru') - HOK Teaching</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')

    <style>
        :root {
            --primary: #165fac;
            --primary-dark: #0d3f7a;
            --accent-orange: #ea580c;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar-lms {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-logo img {
            width: 80px;
            height: 60px;
            object-fit: contain;
        }

        .sidebar-logo h4 {
            margin-top: 12px;
            font-weight: 700;
            font-size: 18px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .nav-section-title {
            padding: 8px 20px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            margin-top: 16px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .nav-link i {
            width: 24px;
            margin-right: 12px;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--accent-orange);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: white;
            font-weight: 600;
        }

        .badge-notif {
            margin-left: auto;
            background: #dc2626;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
        }

        /* Main Content */
        .main-content-lms {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        /* Header */
        .header-lms {
            background: white;
            padding: 20px 32px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-left h1 {
            font-size: 24px;
            color: var(--primary);
            font-weight: 700;
            margin: 0;
        }

        .header-left p {
            font-size: 13px;
            color: #666;
            margin: 4px 0 0 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Content */
        .content-lms {
            padding: 32px;
            flex: 1;
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
        }

        .cursor-pointer {
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 4px 12px;
            border-radius: 8px;
        }

        .cursor-pointer:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Card Custom */
        .card-custom {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }

        .card-header-custom {
            padding: 20px 24px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            color: var(--primary);
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            margin-right: 16px;
            transition: color 0.3s;
        }

        .sidebar-toggle:hover {
            color: var(--primary-dark);
        }

        /* Desktop Collapse State */
        @media (min-width: 769px) {
            body.sidebar-collapsed .sidebar-lms {
                transform: translateX(-100%);
            }

            body.sidebar-collapsed .main-content-lms {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Mobile specific adjustments */
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 260px;
            }
        }

        @media (max-width: 768px) {
            .sidebar-lms {
                transform: translateX(-100%);
            }

            .sidebar-lms.active {
                transform: translateX(0) !important;
            }

            .main-content-lms {
                margin-left: 0 !important;
                width: 100% !important;
                min-width: 100%;
            }

            .sidebar-toggle {
                display: block;
            }

            /* Reduce header padding on mobile */
            .header-lms {
                padding: 12px 16px;
            }

            /* Reduce content padding on mobile */
            .content-lms {
                padding: 16px;
            }

            /* Header title smaller on mobile */
            .header-left h1 {
                font-size: 18px;
            }

            .header-left p {
                font-size: 12px;
            }

            /* Stats cards: single column */
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-card .stat-value {
                font-size: 24px;
            }

            /* Tables scrollable on mobile */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Modals: keep auto margin for horizontal centering */
            .modal-dialog {
                margin: 0.5rem auto;
            }
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        @media (max-width: 768px) {
            .sidebar-overlay.active {
                display: block;
            }
        }

        @media (max-width: 575.98px) {
            html.lms-notif-open,
            body.lms-notif-open {
                overscroll-behavior: none;
            }

            body.lms-notif-open {
                overflow: hidden;
            }

            .notif-dropdown-menu {
                overscroll-behavior: contain;
            }

            .notif-dropdown-menu.lms-notif-locked {
                box-sizing: border-box !important;
                position: fixed !important;
                inset: auto !important;
                top: var(--lms-notif-top, 0px) !important;
                left: var(--lms-notif-left, 16px) !important;
                right: auto !important;
                width: var(--lms-notif-width, calc(100vw - 32px)) !important;
                min-width: var(--lms-notif-width, calc(100vw - 32px)) !important;
                max-width: var(--lms-notif-width, calc(100vw - 32px)) !important;
                max-height: var(--lms-notif-max-height, 420px) !important;
                margin: 0 !important;
                transform: none !important;
                overflow: hidden !important;
                z-index: 9999 !important;
            }

            .notif-dropdown-menu.lms-notif-locked .dropdown-header {
                gap: 12px;
                min-width: 0;
            }

            .notif-dropdown-menu.lms-notif-locked .dropdown-header h6 {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .notif-dropdown-menu.lms-notif-locked .dropdown-header a {
                flex-shrink: 0;
                white-space: nowrap;
            }

            .notif-dropdown-menu.lms-notif-locked .notif-list-scroll {
                max-height: var(--lms-notif-list-max-height, 300px) !important;
                overflow-y: auto !important;
                overscroll-behavior: contain;
                -webkit-overflow-scrolling: touch;
                touch-action: pan-y;
            }

            .notif-list-scroll {
                overscroll-behavior: contain;
                -webkit-overflow-scrolling: touch;
                touch-action: pan-y;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar-lms" id="sidebar">
            <div class="sidebar-logo">
                <img src="{{ asset('img/logo.png') }}" alt="HOK Logo">
                <h4>HOK Teaching</h4>
            </div>

            <nav class="sidebar-menu">
                @yield('sidebar-menu')
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content-lms">
            <!-- Header -->
            <header class="header-lms">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1>@yield('page-title', 'HOK Teaching')</h1>
                        <p>@yield('page-subtitle', 'Learning Management System')</p>
                    </div>
                </div>
                <div class="header-right">
                    <x-notification-bell ctx="lms-guru" />
                    <div class="dropdown">
                        <div class="d-flex align-items-center gap-3 cursor-pointer" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <div class="d-none d-md-flex flex-column text-end">
                                <span class="fw-semibold" style="font-size: 14px;">{{ auth()->user()->name }}</span>
                                <small class="text-muted" style="font-size: 12px;">Guru</small>
                            </div>
                            <div class="user-avatar">
                                @if(auth()->user()->foto_profil)
                                    <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="Avatar"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.index') }}">
                                    <i class="fas fa-user me-2"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('account.settings') }}">
                                    <i class="fas fa-cog me-2"></i> Pengaturan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('guru.dashboard') }}">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                    data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content-lms">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong><i class="fas fa-exclamation-circle"></i> Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong><i class="fas fa-check-circle"></i> Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (function () {
            const mobileQuery = window.matchMedia('(max-width: 575.98px)');
            const openClass = 'lms-notif-open';
            const lockedClass = 'lms-notif-locked';
            let lockedScrollY = null;
            let previousBodyStyle = null;

            function notifMenu() {
                return document.querySelector('.notif-dropdown-menu');
            }

            function notifButton() {
                return document.getElementById('notificationDropdown');
            }

            function isMobileNotif() {
                return mobileQuery.matches;
            }

            function currentViewport() {
                const viewport = window.visualViewport;

                return {
                    width: Math.floor(viewport?.width || document.documentElement.clientWidth || window.innerWidth),
                    height: Math.floor(viewport?.height || document.documentElement.clientHeight || window.innerHeight),
                    left: Math.floor(viewport?.offsetLeft || 0),
                    top: Math.floor(viewport?.offsetTop || 0)
                };
            }

            function setMenuVar(menu, name, value) {
                menu.style.setProperty(name, value);
            }

            function clearMobileLock(menu) {
                if (!menu) return;

                menu.classList.remove(lockedClass);
                delete menu.dataset.lmsNotifLocked;
                menu.style.removeProperty('--lms-notif-top');
                menu.style.removeProperty('--lms-notif-left');
                menu.style.removeProperty('--lms-notif-width');
                menu.style.removeProperty('--lms-notif-max-height');
                menu.style.removeProperty('--lms-notif-list-max-height');
            }

            function lockPageScroll() {
                if (!isMobileNotif() || lockedScrollY !== null) return;

                lockedScrollY = window.scrollY || document.documentElement.scrollTop || 0;
                previousBodyStyle = {
                    position: document.body.style.position,
                    top: document.body.style.top,
                    left: document.body.style.left,
                    right: document.body.style.right,
                    width: document.body.style.width,
                    overflow: document.body.style.overflow
                };

                document.documentElement.classList.add(openClass);
                document.body.classList.add(openClass);
                document.body.style.position = 'fixed';
                document.body.style.top = `-${lockedScrollY}px`;
                document.body.style.left = '0';
                document.body.style.right = '0';
                document.body.style.width = '100%';
                document.body.style.overflow = 'hidden';
            }

            function unlockPageScroll() {
                if (lockedScrollY === null || !previousBodyStyle) return;

                document.documentElement.classList.remove(openClass);
                document.body.classList.remove(openClass);
                document.body.style.position = previousBodyStyle.position;
                document.body.style.top = previousBodyStyle.top;
                document.body.style.left = previousBodyStyle.left;
                document.body.style.right = previousBodyStyle.right;
                document.body.style.width = previousBodyStyle.width;
                document.body.style.overflow = previousBodyStyle.overflow;
                window.scrollTo(0, lockedScrollY);
                lockedScrollY = null;
                previousBodyStyle = null;
            }

            window.fixLmsGuruNotifDropdownPosition = function (force) {
                const menu = notifMenu();
                const btn = notifButton();
                if (!menu || !btn) return;

                if (!isMobileNotif()) {
                    clearMobileLock(menu);

                    const btnRect = btn.getBoundingClientRect();
                    const viewportWidth = document.documentElement.clientWidth || window.innerWidth;
                    const dropW = viewportWidth <= 991 ? 320 : 360;
                    const rightOff = Math.max(8, viewportWidth - btnRect.right);

                    menu.setAttribute('style',
                        `position:fixed!important;` +
                        `top:${btnRect.bottom + 4}px!important;` +
                        `right:${rightOff}px!important;` +
                        `left:auto!important;` +
                        `width:${dropW}px!important;` +
                        `transform:none!important;` +
                        `z-index:9999!important;`
                    );
                    return;
                }

                if (!force && menu.dataset.lmsNotifLocked === '1') {
                    menu.classList.add(lockedClass);
                    return;
                }

                const btnRect = btn.getBoundingClientRect();
                const viewport = currentViewport();
                const margin = viewport.width <= 320 ? 10 : 16;
                const header = btn.closest('.header-lms, header');
                const headerBottom = header ? header.getBoundingClientRect().bottom : btnRect.bottom;
                const topInViewport = Math.max(btnRect.bottom, headerBottom) + 8;
                const top = viewport.top + topInViewport;
                const width = Math.max(1, viewport.width - (margin * 2));
                const left = viewport.left + margin;
                const maxHeight = Math.min(480, Math.max(1, viewport.height - topInViewport - margin));
                const listMaxHeight = Math.max(1, maxHeight - 98);

                menu.removeAttribute('style');
                setMenuVar(menu, '--lms-notif-top', `${top}px`);
                setMenuVar(menu, '--lms-notif-left', `${left}px`);
                setMenuVar(menu, '--lms-notif-width', `${width}px`);
                setMenuVar(menu, '--lms-notif-max-height', `${maxHeight}px`);
                setMenuVar(menu, '--lms-notif-list-max-height', `${listMaxHeight}px`);
                menu.dataset.lmsNotifLocked = '1';
                menu.classList.add(lockedClass);
            };

            window.fixNotifDropdownPosition = window.fixLmsGuruNotifDropdownPosition;

            function initLmsGuruNotifDropdown() {
                const btn = notifButton();
                const menu = notifMenu();
                if (!btn || !menu) return;

                btn.setAttribute('data-bs-display', 'static');

                btn.addEventListener('show.bs.dropdown', function () {
                    clearMobileLock(menu);
                    window.fixLmsGuruNotifDropdownPosition(true);
                    lockPageScroll();

                    requestAnimationFrame(function () {
                        window.fixLmsGuruNotifDropdownPosition(false);
                    });
                });

                btn.addEventListener('hidden.bs.dropdown', function () {
                    clearMobileLock(menu);
                    menu.removeAttribute('style');
                    unlockPageScroll();
                });

                const scrollArea = menu.querySelector('.notif-list-scroll');
                if (scrollArea) {
                    scrollArea.addEventListener('touchmove', function (event) {
                        event.stopPropagation();
                    }, { passive: true });

                    scrollArea.addEventListener('wheel', function (event) {
                        event.stopPropagation();
                    }, { passive: true });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLmsGuruNotifDropdown);
            } else {
                initLmsGuruNotifDropdown();
            }
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            function toggleSidebar() {
                if (window.innerWidth > 768) {
                    // Desktop: Toggle collapse on body
                    body.classList.toggle('sidebar-collapsed');
                } else {
                    // Mobile: Toggle active on sidebar and overlay
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                }
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }

            // Clean up state on resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    // If resizing to desktop, remove active class from sidebar and overlay
                    // to prevent them from getting stuck in "mobile open" state
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });
    </script>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-sign-out-alt" style="color:#dc2626;font-size:16px;"></i>
                        </div>
                        <h5 class="modal-title mb-0 fw-bold" id="logoutModalLabel" style="font-size:16px;">Konfirmasi Logout</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="mb-1" style="font-size:14px;">Apakah Anda yakin ingin keluar dari sistem LMS?</p>
                    <small class="text-muted">Anda perlu login kembali untuk mengakses panel Guru.</small>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2 flex-nowrap">
                    <button type="button" class="btn btn-light btn-sm fw-semibold flex-fill" data-bs-dismiss="modal" style="border:1px solid #e5e7eb;">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="flex-fill d-flex">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm fw-semibold flex-fill">
                            <i class="fas fa-sign-out-alt me-1"></i> Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')

    {{-- AI Chatbot Integration (Role-based access) --}}
    @auth
        @if(canAccessChatbot(auth()->user()->role))
            @include('components.ai-chatbot')
        @endif
    @endauth
</body>

</html>
