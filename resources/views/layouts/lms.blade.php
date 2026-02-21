<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LMS') - HOK Learning</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    @stack('styles')

    <style>
        :root {
            --primary: #165fac;
            --primary-dark: #0d3f7a;
            --accent-yellow: #f59e0b;
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
        }

        /* Sidebar LMS */
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
        }

        .sidebar-lms::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-lms::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
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
            color: white;
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
            font-weight: 600;
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
            font-size: 16px;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--accent-yellow);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: white;
            font-weight: 600;
        }

        /* Main Content LMS */
        .main-content-lms {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        /* Header LMS */
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
        }

        .header-left h1 {
            font-size: 24px;
            color: var(--primary);
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .header-left p {
            font-size: 13px;
            color: #666;
            margin: 4px 0 0 0;
            line-height: 1.3;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notif-badge {
            position: relative;
            cursor: pointer;
            padding: 8px;
            transition: all 0.3s;
            border-radius: 8px;
        }

        .notif-badge:hover {
            background: #f3f4f6;
        }

        .notif-badge i {
            font-size: 20px;
            color: #666;
        }

        .notif-badge .badge {
            position: absolute;
            top: 4px;
            right: 4px;
            background: #dc2626;
            color: white;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 700;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .user-profile:hover {
            background: #f3f4f6;
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
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
        }

        .user-name {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
        }

        .user-role {
            font-size: 12px;
            color: #999;
        }

        /* Content Area */
        .content-lms {
            padding: 32px;
            flex: 1;
            width: 100%;
        }

        /* Card Custom - Menggunakan konsep dari Bendahara */
        .card-custom {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
            margin-bottom: 0;
        }

        .card-header-custom {
            padding: 20px 24px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            color: var(--primary);
        }

        .card-header-custom h6 {
            margin: 0;
            font-size: 16px;
        }

        /* Stats Grid - seperti Bendahara */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        /* Content Grid - untuk jadwal dan pengumuman */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        @media (min-width: 992px) {
            .content-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 95, 172, 0.3);
            color: white;
        }

        /* Alert Custom */
        .alert {
            border-radius: 12px;
        }

        /* Table Styling */
        .table {
            width: 100%;
            margin-bottom: 0;
        }

        .table> :not(caption)>*>* {
            padding: 16px;
            vertical-align: middle;
        }

        .table thead {
            background: #f9fafb;
        }

        .table tbody tr {
            transition: background 0.2s;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .header-left h1 {
                font-size: 20px;
            }

            .header-left p {
                font-size: 12px;
            }

            .user-info {
                display: none;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-card .stat-value {
                font-size: 28px;
            }
        }

        @media (max-width: 768px) {
            .sidebar-lms {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar-lms.active {
                transform: translateX(0);
            }

            .main-content-lms {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block !important;
            }

            .content-lms {
                padding: 20px;
            }

            .header-lms {
                padding: 16px 20px;
            }

            .header-right {
                gap: 12px;
            }

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

        /* Mobile specific adjustments override collapse state */
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }

            .main-content-lms {
                margin-left: 0 !important;
                width: 100% !important;
                min-width: 100%;
            }

            /* On mobile, sidebar is hidden by default (handled by fixed position + translate) */
            body.sidebar-collapsed .sidebar-lms {
                transform: translateX(-100%);
            }

            /* When active on mobile */
            .sidebar-lms.active {
                transform: translateX(0) !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar LMS -->
        <aside class="sidebar-lms">
            <div class="sidebar-logo">
                <img src="{{ asset('img/logo.png') }}" alt="HOK Logo">
                <h4>HOK Learning</h4>
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
                        <h1>@yield('page-title', 'HOK Learning')</h1>
                        <p>@yield('page-subtitle', 'Learning Management System')</p>
                    </div>
                </div>
                <div class="header-right">
                    <x-notification-bell ctx="lms" />
                    <div class="dropdown">
                        <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                @if(auth()->user()->foto_profil)
                                    <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="Avatar"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name }}</div>
                                <div class="user-role">Siswa</div>
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
                                    <i class="fas fa-arrow-left me-2"></i> Kembali ke SIA
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
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-circle"></i> Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-check-circle"></i> Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

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
                    <small class="text-muted">Anda perlu login kembali untuk mengakses kelas.</small>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar Toggle Logic
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar-lms');
            const body = document.body;

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    if (window.innerWidth > 768) {
                        // Desktop: Toggle collapse on body
                        body.classList.toggle('sidebar-collapsed');
                    } else {
                        // Mobile: Toggle active on sidebar
                        sidebar.classList.toggle('active');
                    }
                });
            }

            // Close sidebar when clicking outside (Mobile only)
            document.addEventListener('click', function (event) {
                if (window.innerWidth <= 768) {
                    // Note: check if sidebarToggle is present (it might not be passed if renamed/missing)
                    if (sidebarToggle && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>