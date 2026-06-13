<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Non-Aktif - SIPADU</title>
    @vite(['resources/css/errors/account-inactive.css'])
</head>
<body data-account-inactive-page data-redirect-url="{{ route('login') }}">
    <div class="account-inactive-card">
        <div class="account-inactive-icon">!</div>
        <h1 class="account-inactive-title">Akses Ditolak</h1>
        <p class="account-inactive-text">Akun Anda telah dinonaktifkan oleh Administrator.</p>
        <p class="account-inactive-text">Anda akan diarahkan ke halaman login dalam <span id="countdown" class="account-inactive-timer">3</span> detik...</p>
        <a href="{{ route('login') }}" class="account-inactive-button">Login Sekarang</a>
    </div>

    @vite(['resources/js/errors/account-inactive.js'])
</body>
</html>
