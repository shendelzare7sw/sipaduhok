<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PKBM House Of Knowledge</title>
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('img/icons/favicon-48.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/icons/apple-touch-icon.png') }}">
    {{-- Halaman login & pemulihan akun tidak perlu muncul di hasil pencarian. --}}
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/public-site.css', 'resources/js/public-site.js'])
    @stack('head')
</head>
{{-- Halaman login & pemulihan akun: tanpa navbar/footer situs. --}}
<body class="overflow-x-hidden font-poppins [&_[x-cloak]]:hidden" x-data>
    @yield('content')
</body>
</html>
