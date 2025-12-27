{{-- 
    Sidebar Menu untuk Admin Dashboard
    File: resources/views/admin/partials/sidebar-menu.blade.php
    
    Compatible dengan SB Admin 2 Template
    
    Use Case Admin sesuai dokumen perancangan:
    - Mengelola Data Pengguna (Tenaga Pendidik & Siswa) → CRUD akun
    - Mengelola Tahun Ajaran
    - Mengelola Cabang (lokasi siswa)
    - Mengelola Data Kelas (nama kelas, jenjang)
    - Mengelola Data Wali Kelas (penunjukan)
    - Mengelola Data Guru Pengajar (penugasan)
    - Mengelola Data Siswa (assign ke kelas, cetak daftar)
    - Cetak Laporan
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- Nav Item - Dashboard -->
<li class="nav-item {{ $currentRoute == 'admin.dashboard' ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.dashboard') }}">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Manajemen Pengguna
</div>

<!-- Nav Item - User Management Collapse Menu -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'admin.users') ? 'active' : '' }}">
    <a class="nav-link {{ Str::startsWith($currentRoute, 'admin.users') ? '' : 'collapsed' }}" 
       href="#" 
       data-toggle="collapse" 
       data-target="#collapseUsers"
       aria-expanded="{{ Str::startsWith($currentRoute, 'admin.users') ? 'true' : 'false' }}" 
       aria-controls="collapseUsers">
        <i class="fas fa-fw fa-users"></i>
        <span>Manajemen User</span>
    </a>
    <div id="collapseUsers" 
         class="collapse {{ Str::startsWith($currentRoute, 'admin.users') ? 'show' : '' }}" 
         aria-labelledby="headingUsers" 
         data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Data Pengguna:</h6>
            <a class="collapse-item {{ $currentRoute == 'admin.users.tenaga-pendidik' ? 'active' : '' }}" 
               href="{{ route('admin.users.tenaga-pendidik') }}">
                <i class="fas fa-user-tie mr-1"></i> Tenaga Pendidik
            </a>
            <a class="collapse-item {{ $currentRoute == 'admin.users.siswa' ? 'active' : '' }}" 
               href="{{ route('admin.users.siswa') }}">
                <i class="fas fa-user-graduate mr-1"></i> Siswa
            </a>
        </div>
    </div>
</li>

<!-- Nav Item - Tahun Ajaran -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'admin.tahun-ajaran') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.tahun-ajaran.index') }}">
        <i class="fas fa-fw fa-calendar-alt"></i>
        <span>Tahun Ajaran</span>
    </a>
</li>

<!-- Nav Item - Cabang -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'admin.cabang') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.cabang.index') }}">
        <i class="fas fa-fw fa-building"></i>
        <span>Manajemen Cabang</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Data Akademik
</div>

<!-- Nav Item - Kelas -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'admin.kelas') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.kelas.index') }}">
        <i class="fas fa-fw fa-school"></i>
        <span>Data Kelas</span>
    </a>
</li>

<!-- Nav Item - Wali Kelas -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'admin.wali-kelas') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.wali-kelas.index') }}">
        <i class="fas fa-fw fa-chalkboard-teacher"></i>
        <span>Data Wali Kelas</span>
    </a>
</li>

<!-- Nav Item - Guru Pengajar -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'admin.guru-pengajar') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.guru-pengajar.index') }}">
        <i class="fas fa-fw fa-user-tie"></i>
        <span>Data Guru Pengajar</span>
    </a>
</li>

<!-- Nav Item - Manajemen Siswa -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'admin.manajemen-siswa') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.manajemen-siswa.index') }}">
        <i class="fas fa-fw fa-user-graduate"></i>
        <span>Manajemen Siswa</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Laporan
</div>

<!-- Nav Item - Cetak Laporan -->
<li class="nav-item {{ Str::startsWith($currentRoute, 'admin.cetak-laporan') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.cetak-laporan.index') }}">
        <i class="fas fa-fw fa-file-pdf"></i>
        <span>Cetak Laporan</span>
    </a>
</li>