<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Latihan') - HOK Learning</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #165fac;
            --primary-dark: #0d3f7a;
            --secondary: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #e9ecef;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Minimal Header */
        .exam-header {
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
        }

        .exam-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .exam-user {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.9rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        /* Main Content Wrapper - FULL WIDTH */
        .exam-wrapper {
            margin-top: 60px;
            padding: 1rem;
            min-height: calc(100vh - 60px);
        }

        .exam-container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Mobile Responsive */
        @media (max-width: 575px) {
            .exam-header {
                padding: 0.5rem 1rem;
            }

            .exam-brand {
                font-size: 1rem;
            }

            .exam-wrapper {
                padding: 0.5rem;
            }
        }
    </style>
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
                <small style="opacity: 0.8">
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>