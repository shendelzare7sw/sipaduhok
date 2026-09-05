<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="m-0 min-h-screen bg-slate-100 font-sans text-slate-950 print:bg-white">
    @hasSection('page-content')
        @yield('page-content')
    @else
    <header class="sticky top-0 z-20 flex items-center justify-between gap-3 border-b border-slate-200 bg-white/95 px-3 py-3 shadow-sm backdrop-blur print:hidden sm:px-5">
        <a href="@yield('back-url')" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a>
        <div class="min-w-0 text-center"><p class="truncate text-xs font-bold uppercase tracking-wider text-brand-600">Pratinjau dokumen</p><p class="truncate text-sm font-extrabold text-slate-900">@yield('title')</p></div>
        <button type="button" data-print-page class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-print" aria-hidden="true"></i><span class="hidden sm:inline">Cetak</span></button>
    </header>

    <main class="min-w-0 w-full overflow-x-auto p-3 print:overflow-visible print:p-0 sm:p-5">
        <article class="@yield('document-width', 'min-w-[900px]') w-full bg-white p-6 shadow-sm print:min-w-0 print:max-w-none print:p-0 print:shadow-none sm:p-8">
            @hasSection('document-header')
                @yield('document-header')
            @else
                @include('layouts.partials.print-header', ['cabang' => $cabang ?? null])
            @endif
            <div class="my-5 text-center print:my-4"><h1 class="text-base font-extrabold uppercase underline print:text-[12pt]">@yield('report-title')</h1>@yield('report-meta')</div>
            @yield('report-content')
            @hasSection('report-footer')
                @yield('report-footer')
            @else
                <footer class="mt-8 flex items-start justify-between gap-8 text-[10px] leading-5 print:mt-6 print:text-[8pt]">
                    <div><p>Dicetak pada: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</p><p>Oleh: {{ auth()->user()->name }}</p></div>
                    <div class="w-64 text-center"><p>Tangerang Selatan, {{ now()->locale('id')->translatedFormat('d F Y') }}</p><p>Mengetahui,</p><div class="h-16"></div><p class="border-t border-slate-900 pt-1">Kepala PKBM House of Knowledge</p></div>
                </footer>
            @endif
        </article>
    </main>
    @endif
</body>
</html>
