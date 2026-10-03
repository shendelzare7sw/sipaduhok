{{--
    Shell mode fokus Latihan/Ujian Siswa (layouts.lms-latihan & layouts.lms-ujian).
    Migrasi ekuivalen-piksel dari CSS Bootstrap lama: header biru tetap 60px, latar abu,
    font Inter. Breakpoint memakai nilai Bootstrap (576/992 px) agar perilaku tidak bergeser.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $defaultTitle) - HOK Learning</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/cleanflow.css', 'resources/js/cleanflow.js'])
</head>
<body class="m-0 min-h-screen bg-[#e9ecef] p-0 font-['Inter',sans-serif] text-base leading-normal text-[#212529]">
    <header class="fixed inset-x-0 top-0 z-[1000] flex h-[60px] items-center justify-between bg-[#165fac] px-4 py-2 text-white shadow-[0_2px_4px_rgba(0,0,0,0.1)] min-[576px]:px-6 min-[576px]:py-3">
        <div class="text-base font-bold uppercase leading-6 tracking-[0.5px] min-[576px]:text-xl min-[576px]:leading-[30px]">
            <i class="fas fa-graduation-cap mr-2"></i> SISWA
        </div>
        <div class="flex items-center gap-4 text-[0.9rem] leading-[1.5]">
            <div class="hidden text-right min-[576px]:block">
                <div class="font-bold">{{ Auth::user()->name }}</div>
                <small class="text-[0.875em] opacity-80">{{ $roleLabel }}</small>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-[1.2rem]">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>

    <div class="mt-[60px] min-h-[calc(100vh-60px)] p-2 min-[576px]:p-4">
        <div class="m-0 w-full max-w-full p-0">
            @yield('content')
        </div>
    </div>
</body>
</html>
