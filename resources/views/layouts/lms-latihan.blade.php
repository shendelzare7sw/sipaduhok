<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Latihan') - HOK Learning</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/layouts/lms-latihan.css', 'resources/js/layouts/lms-latihan.js'])
    @stack('styles')
</head>

<body>

    <!-- Header -->
    <header class="exam-header">
        <div class="exam-brand">
            <i class="fas fa-graduation-cap me-2"></i> SISWA
        </div>
        <div class="exam-user">
            <div class="text-end d-none d-sm-block">
                <div class="fw-bold">{{ Auth::user()->name }}</div>
                <small class="exam-role-label">
                    Latihan
                </small>
            </div>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>

    <!-- Content -->
    <div class="exam-wrapper">
        <div class="exam-container">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>

</html>
