<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - SIPADUHOK</title>

    <!-- Custom fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin 2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #165fac;
            --primary-dark: #0d3a6b;
            --sidebar-width: 17rem; /* Diperbesar dari 14rem */
        }

        /* Sidebar customization */
        .sidebar {
            width: var(--sidebar-width) !important;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        }

        .sidebar .nav-item .nav-link {
            color: rgba(255,255,255,.8);
            padding: 1.1rem 1rem; /* Padding lebih besar */
            font-size: 1rem; /* Font lebih besar dari 0.875rem default */
        }

        .sidebar .nav-item .nav-link i {
            font-size: 1.1rem; /* Icon lebih besar */
            width: 1.5rem; /* Lebar icon lebih besar */
        }

        .sidebar .nav-item .nav-link span {
            font-size: 1rem; /* Text lebih besar */
            font-weight: 500; /* Sedikit lebih bold */
        }

        .sidebar .nav-item .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,.1);
        }

        .sidebar .nav-item .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.15);
            font-weight: 600;
        }

        .sidebar-brand {
            height: 5rem; /* Diperbesar dari 4.375rem */
            background-color: rgba(0,0,0,.1);
        }

        .sidebar-brand-icon img {
            max-height: 3.5rem; /* Logo lebih besar */
            max-width: 100%;
        }

        /* Sidebar heading text lebih besar */
        .sidebar-heading {
            font-size: 0.75rem; /* Sedikit lebih besar */
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        /* Collapse menu items */
        .sidebar .collapse-item {
            font-size: 0.95rem; /* Submenu lebih besar */
            padding: 0.7rem 1rem;
        }

        .sidebar .collapse-header {
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* Topbar */
        .topbar {
            height: 5rem; /* Sesuaikan dengan sidebar-brand height */
            background-color: #fff;
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
        }

        .topbar .nav-item .nav-link {
            height: 5rem; /* Sesuaikan dengan topbar height */
            display: flex;
            align-items: center;
            padding: 0 1.25rem; /* Padding lebih besar */
        }

        .topbar h1 {
            font-size: 1.5rem; /* Title lebih besar */
            font-weight: 700;
        }

        .topbar .small, .topbar small {
            font-size: 0.9rem; /* Subtitle lebih besar */
        }

        /* User info in topbar */
        .topbar .nav-item .nav-link span {
            font-size: 0.95rem; /* Text user lebih besar */
        }

        .topbar .rounded-circle {
            width: 45px !important; /* Avatar lebih besar */
            height: 45px !important;
            font-size: 1.1rem;
        }

        /* Alert auto dismiss */
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

        /* Card hover effect */
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }

        /* Stat cards */
        .card-stat {
            border-left: 4px solid;
        }

        .card-stat.card-primary {
            border-color: #4e73df;
        }

        .card-stat.card-success {
            border-color: #1cc88a;
        }

        .card-stat.card-warning {
            border-color: #f6c23e;
        }

        .card-stat.card-info {
            border-color: #36b9cc;
        }

        /* Badge notification */
        .badge-counter {
            position: absolute;
            transform: scale(.7);
            transform-origin: top right;
            right: .25rem;
            top: .25rem;
        }

        /* Dropdown menu text lebih besar */
        .dropdown-menu {
            font-size: 0.95rem;
        }

        .dropdown-item {
            font-size: 0.95rem;
            padding: 0.75rem 1.5rem;
        }

        .dropdown-header {
            font-size: 0.85rem;
            font-weight: 700;
        }

        .dropdown-list .dropdown-item {
            padding: 1rem 1.5rem;
        }

        .icon-circle {
            width: 2.75rem; /* Icon circle lebih besar */
            height: 2.75rem;
        }

        .icon-circle i {
            font-size: 1rem;
        }

        /* Responsive sidebar */
        @media (max-width: 768px) {
            .sidebar {
                width: 0 !important;
            }

            .sidebar.toggled {
                width: var(--sidebar-width) !important;
            }

            /* Topbar responsive */
            .topbar h1 {
                font-size: 1.2rem;
            }

            .topbar .small {
                font-size: 0.8rem;
            }
        }

        @media (min-width: 769px) and (max-width: 992px) {
            /* Untuk tablet, sidebar sedikit lebih kecil */
            :root {
                --sidebar-width: 15rem;
            }
        }
    </style>

    @yield('styles')
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="{{
            auth()->user()->role == 'admin' ? route('admin.dashboard') : (
            auth()->user()->role == 'ketua_pkbm' ? route('ketua.dashboard') : (
            auth()->user()->role == 'sekretaris' ? route('sekretaris.dashboard') : (
            auth()->user()->role == 'bendahara' ? route('bendahara.dashboard') : (
            auth()->user()->role == 'wali_kelas' ? route('wali.dashboard') : (
            auth()->user()->role == 'guru_pengajar' ? route('guru.dashboard') : (
            auth()->user()->role == 'siswa' ? route('siswa.dashboard') : route('dashboard')
        ))))))
        }}">
            <div class="sidebar-brand-icon">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIPADUHOK">
            </div>
        </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Items from Child Views -->
            @yield('sidebar-menu')

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Page Title -->
                    <div class="d-none d-sm-block">
                        <h1 class="h4 mb-0 text-gray-800" style="font-size: 1.5rem; font-weight: 700;">@yield('page-title', 'Dashboard')</h1>
                        <p class="mb-0 text-gray-600 small" style="font-size: 0.95rem;">@yield('page-subtitle', 'Selamat datang di dashboard')</p>
                    </div>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Notifications -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw" style="font-size: 1.3rem;"></i>
                                <!-- Counter - Notifications -->
                                <span class="badge badge-danger badge-counter" style="font-size: 0.7rem;">3+</span>
                            </a>
                            <!-- Dropdown - Notifications -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header" style="font-size: 0.9rem; font-weight: 700;">
                                    Notifikasi
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#" style="padding: 1rem 1.5rem;">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500" style="font-size: 0.85rem;">12 Des 2024</div>
                                        <span class="font-weight-bold" style="font-size: 0.95rem;">Laporan baru menunggu persetujuan</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#" style="padding: 1rem 1.5rem;">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500" style="font-size: 0.85rem;">7 Des 2024</div>
                                        <span style="font-size: 0.95rem;">Pembayaran baru diterima!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#" style="padding: 1rem 1.5rem;">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500" style="font-size: 0.85rem;">2 Des 2024</div>
                                        <span style="font-size: 0.95rem;">Ada tagihan yang belum lunas</span>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#" style="font-size: 0.9rem; padding: 0.75rem;">Lihat Semua Notifikasi</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600">
                                    <span style="font-size: 0.95rem; font-weight: 600;">{{ auth()->user()->name }}</span>
                                    <br>
                                    <small class="text-muted" style="font-size: 0.85rem;">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</small>
                                </span>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 45px; font-weight: 700; font-size: 1.1rem;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" style="font-size: 0.95rem; padding: 0.75rem 1.5rem;">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>
                                <a class="dropdown-item" href="#" style="font-size: 0.95rem; padding: 0.75rem 1.5rem;">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Pengaturan
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal" style="font-size: 0.95rem; padding: 0.75rem 1.5rem;">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 0.95rem;">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 0.95rem;">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="font-size: 0.95rem;">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert" style="font-size: 0.95rem;">
                            <i class="fas fa-info-circle mr-2"></i>
                            {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SIPADUHOK {{ date('Y') }} - PKBM House of Knowledge</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 1.1rem; font-weight: 600;">
                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                        Konfirmasi Logout
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" style="font-size: 1rem;">
                    Apakah Anda yakin ingin keluar dari sistem? <br>
                    <small class="text-muted" style="font-size: 0.9rem;">Anda perlu login kembali untuk mengakses dashboard.</small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                            <i class="fas fa-sign-out-alt mr-1"></i> Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- SB Admin 2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

    <script>
        // Auto dismiss alerts after 5 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>

    @yield('scripts')

</body>
</html>
