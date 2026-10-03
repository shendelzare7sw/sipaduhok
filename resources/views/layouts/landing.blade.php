<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('seo')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/public-site.css', 'resources/js/public-site.js'])
    @stack('head')
</head>
{{-- Halaman publik: navbar + konten + footer. Section diberi jarak gulir 100px agar tidak tertutup navbar tetap. --}}
<body class="@yield('body_class', 'bg-white') font-poppins [&_section]:scroll-mt-[100px] [&_[x-cloak]]:hidden" x-data>
    <x-navbar />

    @yield('content')

    <x-footer />

    {{-- Lightbox foto (klik foto mana pun untuk memperbesar) --}}
    <x-lms.lightbox />

    @stack('modals')
</body>
</html>
