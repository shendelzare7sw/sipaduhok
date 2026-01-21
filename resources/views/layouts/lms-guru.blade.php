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
        body.sidebar-collapsed .sidebar-lms {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed .main-content-lms {
            margin-left: 0;
            width: 100%;
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

            /* On mobile, standard active class is used (transform 0) */
            .sidebar-lms.active {
                transform: translateX(0);
            }

            /* collapsed state shouldn't affect mobile logic heavily, 
               but ensure body.sidebar-collapsed doesn't break things. 
               We will use specific JS logic to differentiate. */

            .main-content-lms {
                margin-left: 0 !important;
                width: 100% !important;
                min-width: 100%;
            }

            .sidebar-toggle {
                display: block;
                /* Always visible */
            }

            /* ... existing mobile styles ... */
            .header-lms {
                padding: 16px 20px;
            }

            /* ... */
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

        .sidebar-overlay.active {
            display: block;
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
                    <x-notification-bell />
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
                                <a class="dropdown-item" href="{{ route('home') }}">
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
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar dari sistem LMS? <br>
                    <small class="text-muted">Anda perlu login kembali untuk mengakses panel Guru.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-1"></i> Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>