<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses LMS Dinonaktifkan - SIPADUHOK</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/cleanflow.css', 'resources/js/cleanflow.js'])
</head>
<body class="relative flex min-h-full items-center justify-center overflow-hidden bg-slate-50 px-4 py-10 font-sans text-slate-800 antialiased">
    <span class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-indigo-200/50 blur-3xl" aria-hidden="true"></span>
    <span class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-violet-200/50 blur-3xl" aria-hidden="true"></span>

    <main class="relative w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-xl shadow-indigo-900/10 sm:p-8"
          x-data="{ sisa: 3 }"
          x-init="const t = setInterval(() => { sisa--; if (sisa <= 0) { clearInterval(t); window.location.href = $el.dataset.redirectUrl; } }, 1000)"
          data-redirect-url="{{ route('siswa.sia.dashboard') }}">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-2xl text-white shadow-lg shadow-indigo-500/30"><i class="fa-solid fa-lock" aria-hidden="true"></i></span>

        <h1 class="mt-5 text-xl font-extrabold text-slate-900">Akses terkunci</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Fitur <strong class="text-slate-700">Learning Management System</strong> saat ini dinonaktifkan untuk jenjang
            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $jenjang }}</span>.
        </p>

        <p class="mt-4 flex items-start gap-2 rounded-xl bg-amber-50 px-3 py-2.5 text-left text-xs leading-5 text-amber-800">
            <i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>Silakan hubungi Administrator jika Anda merasa ini adalah kesalahan.
        </p>

        <div class="mt-5">
            <p class="text-xs text-slate-500">Mengalihkan kembali dalam</p>
            <p class="mt-1 text-4xl font-extrabold text-indigo-600" x-text="sisa" aria-live="polite">3</p>
            <p class="text-xs text-slate-500">detik</p>
        </div>

        <a href="{{ route('siswa.sia.dashboard') }}" class="mt-6 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-bold text-white no-underline shadow-sm transition hover:bg-indigo-700">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke dashboard
        </a>
    </main>
</body>
</html>
