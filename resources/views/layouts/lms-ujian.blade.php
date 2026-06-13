<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ujian') - HOK Learning</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/layouts/lms-ujian.css'])
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
                    {{ isset($ujian) ? $ujian->tipe_label : 'Peserta Ujian' }}
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
