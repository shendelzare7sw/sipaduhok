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

    <style>
        body {
            background-color: #f5f5f9;
            font-family: 'Public Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Background Decoration */
        .bg-shape-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: rgba(105, 108, 255, 0.1);
            border-radius: 50%;
            z-index: -1;
        }

        .bg-shape-2 {
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(133, 146, 163, 0.1);
            border-radius: 50%;
            z-index: -1;
        }

        .container-wrapper {
            max-width: 480px;
            width: 90%;
            text-align: center;
            background: #fff;
            padding: 3rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
            position: relative;
            z-index: 10;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: #ffe0db;
            color: #ff3e1d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            animation: pulse 2s infinite;
        }

        .countdown-circle {
            background: #e7e7ff;
            color: #696cff;
            font-weight: 700;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            display: inline-block;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.4);
            }

            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(255, 62, 29, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 62, 29, 0);
            }
        }
    </style>
</head>

<body>
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
                class="badge bg-label-primary text-primary" style="background-color: #e7e7ff;">{{ $jenjang }}</span>.
        </p>

        <div class="alert alert-warning border-0 rounded-3 mb-4 text-start d-flex align-items-center" role="alert"
            style="background-color: #fff2d6; color: #b47f00;">
            <i class="fas fa-info-circle me-3 fs-5"></i>
            <div class="small">
                Silakan hubungi Administrator jika Anda merasa ini adalah kesalahan.
            </div>
        </div>

        <div class="mb-4">
            <p class="text-muted mb-0">Mengalihkan kembali dalam</p>
            <div class="mt-2" style="font-size: 2rem; font-weight: 800; color: #696cff;">
                <span id="countdown">3</span>
            </div>
            <small class="text-muted">detik</small>
        </div>

        <a href="{{ route('siswa.sia.dashboard') }}" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let count = 3;
            const countdownElement = document.getElementById('countdown');
            const redirectUrl = "{{ route('siswa.sia.dashboard') }}";

            // Visual countdown effect
            const timer = setInterval(function () {
                count--;
                if (countdownElement) {
                    countdownElement.textContent = count;
                    countdownElement.style.transform = "scale(1.2)";
                    setTimeout(() => countdownElement.style.transform = "scale(1)", 200);
                }

                if (count <= 0) {
                    clearInterval(timer);
                    window.location.href = redirectUrl;
                }
            }, 1000);
        });
    </script>
</body>

</html>