<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Akses LMS Dinonaktifkan - SIPADUHOK</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap & Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @vite(['resources/css/errors/lms-disabled.css'])
</head>

<body data-lms-disabled-page data-redirect-url="{{ route('siswa.sia.dashboard') }}">
    <!-- Background Shapes -->
    <div class="bg-shape-1"></div>
    <div class="bg-shape-2"></div>

    <div class="container-wrapper">
        <!-- Floating Locks Icon -->
        <div class="icon-box">
            <i class="fas fa-lock"></i>
        </div>

        <h3 class="mb-2 fw-bold text-dark">Akses Terkunci</h3>

        <p class="mb-4 text-muted">
            Fitur <strong>Learning Management System</strong> saat ini dinonaktifkan untuk jenjang <span
                class="badge bg-label-primary text-primary lms-disabled-jenjang-badge">{{ $jenjang }}</span>.
        </p>

        <div class="alert alert-warning border-0 rounded-3 mb-4 text-start d-flex align-items-center lms-disabled-alert" role="alert">
            <i class="fas fa-info-circle me-3 fs-5"></i>
            <div class="small">
                Silakan hubungi Administrator jika Anda merasa ini adalah kesalahan.
            </div>
        </div>

        <div class="mb-4">
            <p class="text-muted mb-0">Mengalihkan kembali dalam</p>
            <div class="mt-2 lms-disabled-countdown">
                <span id="countdown">3</span>
            </div>
            <small class="text-muted">detik</small>
        </div>

        <a href="{{ route('siswa.sia.dashboard') }}" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    @vite(['resources/js/errors/lms-disabled.js'])
</body>

</html>
