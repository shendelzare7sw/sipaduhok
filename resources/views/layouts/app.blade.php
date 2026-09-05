<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIPADUHOK</title>
    <meta name="description" content="Sistem Informasi PKBM Duta House of Knowledge">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @yield('styles')
    @stack('styles')
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-800 antialiased">
    @php
        $dashboardRoute = match (auth()->user()->role ?? null) {
            'admin', 'super_admin' => 'admin.dashboard',
            'ketua_pkbm' => 'ketua.dashboard',
            'wakil_kepala_sekolah' => 'waka.dashboard',
            'sekretaris' => 'sekretaris.dashboard',
            'bendahara' => 'bendahara.dashboard',
            'wali_kelas' => 'wali.dashboard',
            'guru_pengajar' => 'guru.dashboard',
            'siswa' => 'siswa.dashboard',
            'orang_tua' => 'wali-siswa.dashboard',
            default => 'dashboard',
        };
        $sidebarMap = [
            'admin' => 'admin.partials.cleanflow-sidebar',
            'super_admin' => 'admin.partials.cleanflow-sidebar',
            'ketua_pkbm' => 'ketua.partials.sidebar',
            'wakil_kepala_sekolah' => 'waka.partials.sidebar',
            'sekretaris' => 'sekretaris.partials.sidebar',
            'bendahara' => 'bendahara.partials.sidebar',
            'wali_kelas' => 'wali-kelas.partials.sidebar',
            'guru_pengajar' => 'guru.partials.sidebar',
            'siswa' => 'siswa.partials.sidebar-sia',
            'orang_tua' => 'wali-siswa.partials.sidebar',
        ];
        $sidebarView = $sidebarMap[auth()->user()->role ?? ''] ?? null;
    @endphp

    <div id="admin-sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-[#1261a6] bg-gradient-to-b from-[#183d68] via-[#145b94] to-[#0b426f] text-white shadow-2xl transition-transform duration-200 lg:translate-x-0">
        <div class="relative flex h-20 shrink-0 items-center border-b border-white/15 px-4">
            <a href="{{ route($dashboardRoute) }}" class="flex min-w-0 flex-1 items-center gap-2.5 pr-11 no-underline lg:pr-0">
                <img src="{{ asset('img/logo.png') }}" alt="SIPADUHOK" class="h-12 w-12 shrink-0 object-contain">
                <span class="min-w-0">
                    <span class="block truncate text-[15px] font-extrabold tracking-tight text-white">SIPADUHOK</span>
                    <span class="block truncate text-[10px] font-bold uppercase tracking-[0.16em] text-sky-100/90">{{ auth()->user()->role_label }}</span>
                </span>
            </a>
            <button type="button" data-sidebar-close class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-xl border border-white/10 bg-slate-950/10 text-sky-100 transition hover:border-white/25 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Tutup menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="min-h-0 flex-1 overflow-y-auto px-3 py-5" aria-label="Navigasi utama">
            <ul class="cleanflow-nav min-w-0">
                @if(trim($__env->yieldContent('sidebar-menu')))
                    @yield('sidebar-menu')
                @elseif($sidebarView && view()->exists($sidebarView))
                    @include($sidebarView)
                @endif
            </ul>
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
                    <span class="block truncate text-[11px] font-medium text-sky-100/85">{{ auth()->user()->role_label }}</span>
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

                <button type="button" data-search-open class="hidden h-11 max-w-xl flex-1 items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 text-left text-sm text-slate-500 transition hover:border-brand-500 hover:bg-white sm:flex">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <span class="flex-1">Cari menu atau pekerjaan...</span>
                    <kbd class="rounded-md border border-slate-200 bg-white px-2 py-1 text-[10px] font-semibold text-slate-400">Ctrl K</kbd>
                </button>

                <div class="min-w-0 flex-1 sm:hidden">
                    <h1 class="truncate text-sm font-extrabold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                    @if(trim($__env->yieldContent('page-subtitle')))
                        <p class="mt-0.5 truncate text-[11px] text-slate-500">@yield('page-subtitle')</p>
                    @endif
                </div>

                <div class="ml-auto flex items-center gap-2">
                    <button type="button" data-search-open class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:border-brand-500 hover:text-brand-600 sm:hidden" aria-label="Cari menu">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <x-cleanflow.notification-bell />
                    <x-cleanflow.user-menu :role-label="auth()->user()->role_label" />
                </div>
            </div>
        </header>

        <main class="legacy-content min-w-0 w-full flex-1 overflow-x-clip px-3 pb-5 pt-2 sm:px-6 sm:pb-7 sm:pt-3 lg:px-8">
            @foreach(['success', 'error', 'warning', 'info'] as $flashType)
                @if(session($flashType))
                    <div class="hidden" data-flash="{{ $flashType === 'error' ? 'error' : $flashType }}" data-title="{{ $flashType === 'success' ? 'Berhasil' : ucfirst($flashType) }}" data-message="{{ session($flashType) }}"></div>
                @endif
            @endforeach
            @yield('content')
        </main>

        <x-cleanflow.app-footer />
    </div>

    <x-cleanflow.menu-search />

    @stack('scripts')
    @yield('scripts')

    @auth
        @if(canAccessChatbot(auth()->user()->role))
            @include('components.ai-chatbot')
        @endif
    @endauth

    @stack('modals')
</body>
</html>
