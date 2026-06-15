<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LMS Guru') - HOK Teaching</title>

    @vite([
        'resources/css/layouts/lms-guru.css',
        'resources/css/components/notification-bell.css',
        'resources/css/guru/partials/sidebar-lms.css',
    ])
    @auth
        @if(canAccessChatbot(auth()->user()->role))
            @vite(['resources/css/components/ai-chatbot.css'])
        @endif
    @endauth
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
                                <span class="fw-semibold layout-user-name">{{ auth()->user()->name }}</span>
                                <small class="text-muted layout-user-role">Guru</small>
                            </div>
                            <div class="user-avatar">
                                @if(auth()->user()->foto_profil)
                                    <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="Avatar"
                                        class="layout-user-avatar-img">
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

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg lms-logout-modal">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="lms-logout-icon">
                            <i class="fas fa-sign-out-alt lms-logout-icon-symbol"></i>
                        </div>
                        <h5 class="modal-title mb-0 fw-bold lms-logout-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="mb-1 lms-logout-text">Apakah Anda yakin ingin keluar dari sistem LMS?</p>
                    <small class="text-muted">Anda perlu login kembali untuk mengakses panel Guru.</small>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2 flex-nowrap">
                    <button type="button" class="btn btn-light btn-sm fw-semibold flex-fill lms-logout-cancel" data-bs-dismiss="modal">
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

    @vite([
        'resources/js/layouts/lms-guru.js',
        'resources/js/components/notification-bell.js',
    ])
    @stack('modals')
    @stack('scripts')

    {{-- AI Chatbot Integration (Role-based access) --}}
    @auth
        @if(canAccessChatbot(auth()->user()->role))
            @vite(['resources/js/components/ai-chatbot.js'])
            @include('components.ai-chatbot')
        @endif
    @endauth
</body>

</html>
