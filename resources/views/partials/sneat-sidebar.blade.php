<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- App Brand -->
    <div class="app-brand demo">
        <a href="{{
            auth()->user()->role == 'admin' ? route('admin.dashboard') : (
            auth()->user()->role == 'ketua_pkbm' ? route('ketua.dashboard') : (
            auth()->user()->role == 'sekretaris' ? route('sekretaris.dashboard') : (
            auth()->user()->role == 'bendahara' ? route('bendahara.dashboard') : (
            auth()->user()->role == 'wali_kelas' ? route('wali.dashboard') : (
            auth()->user()->role == 'guru_pengajar' ? route('guru.dashboard') : (
            auth()->user()->role == 'siswa' ? route('siswa.dashboard') : route('dashboard')
        ))))))
        }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIPADUHOK">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">SIPADUHOK</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="fas fa-times align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <!-- Menu Items - Akan di-render dari view yang meng-extend layout ini -->
    <ul class="menu-inner py-1">
        @yield('sidebar-menu')
    </ul>

</aside>
<!-- / Menu -->
