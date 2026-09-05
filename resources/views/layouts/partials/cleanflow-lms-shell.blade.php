<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $shellName) - SIPADUHOK</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js', $legacyLayoutCss])
    @stack('styles')
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-800 antialiased">
    <div id="admin-sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-[#1261a6] bg-gradient-to-b from-[#183d68] via-[#145b94] to-[#0b426f] text-white shadow-2xl transition-transform duration-200 lg:translate-x-0">
        <div class="relative flex h-20 shrink-0 items-center border-b border-white/15 px-4">
            <a href="{{ $homeUrl }}" class="flex min-w-0 flex-1 items-center gap-2.5 pr-11 no-underline lg:pr-0">
                <img src="{{ asset('img/logo.png') }}" alt="SIPADUHOK" class="h-12 w-12 shrink-0 object-contain">
                <span class="min-w-0">
                    <span class="block truncate text-[15px] font-extrabold tracking-tight text-white">{{ $shellName }}</span>
                    <span class="block truncate text-[10px] font-bold uppercase tracking-[0.16em] text-sky-100/90">{{ $shellSubtitle }}</span>
                </span>
            </a>
            <button type="button" data-sidebar-close class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-xl border border-white/10 bg-slate-950/10 text-sky-100 transition hover:border-white/25 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Tutup menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="min-h-0 flex-1 overflow-y-auto px-3 py-5" aria-label="Navigasi {{ $shellName }}">
            <div class="cleanflow-lms-nav">
                @yield('sidebar-menu')
            </div>
        </nav>

        <div class="shrink-0 border-t border-white/15 bg-slate-950/25 p-3 backdrop-blur-xl">
            <a href="{{ route('profile.index') }}" class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.06] px-3 py-2.5 text-sm text-white no-underline shadow-sm transition hover:border-white/20 hover:bg-white/10">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-sky-400/25 font-bold text-white ring-1 ring-sky-100/20">
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate font-bold text-white">{{ auth()->user()->name }}</span>
                    <span class="block truncate text-[11px] font-medium text-sky-100/85">{{ $roleLabel }}</span>
                </span>
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/[0.07] text-sky-100/80"><i class="fa-solid fa-gear text-xs"></i></span>
            </a>
        </div>
    </aside>

    <div class="flex min-h-screen flex-col lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur">
            <div class="flex h-20 w-full min-w-0 items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-open class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 lg:hidden" aria-label="Buka menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-sm font-extrabold text-slate-900 sm:text-base">@yield('page-title', $shellName)</h1>
                    <p class="mt-0.5 hidden truncate text-[11px] text-slate-500 sm:block">@yield('page-subtitle', $shellSubtitle)</p>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <x-cleanflow.notification-bell :ctx="$notificationContext" />
                    <x-cleanflow.user-menu :role-label="$roleLabel" :back-url="$backUrl" :back-label="$backLabel" />
                </div>
            </div>
        </header>

        <main class="legacy-content min-w-0 w-full flex-1 overflow-x-hidden px-3 pb-5 pt-2 sm:px-6 sm:pb-7 sm:pt-3 lg:px-8">
            @foreach(['success', 'error', 'warning', 'info'] as $flashType)
                @if(session($flashType))
                    <div class="hidden" data-flash="{{ $flashType === 'error' ? 'error' : $flashType }}" data-title="{{ $flashType === 'success' ? 'Berhasil' : ucfirst($flashType) }}" data-message="{{ session($flashType) }}"></div>
                @endif
            @endforeach
            @yield('content')
        </main>

        <x-cleanflow.app-footer />
    </div>

    @stack('modals')
    @stack('scripts')

    @if($showChatbot && canAccessChatbot(auth()->user()->role))
        @include('components.ai-chatbot')
    @endif
</body>
</html>
