{{-- Guru LMS Notification Sidebar
     Simple sidebar for the /notifications page when accessed from LMS context.
     Does NOT require $kelas or $mapel variables. --}}

<div class="nav-section-title">NOTIFIKASI</div>
<a href="{{ route('notifications.index', ['ctx' => 'lms-guru']) }}"
    class="nav-link {{ !request('filter') ? 'active' : '' }}">
    <i class="fas fa-bell"></i>
    <span>Semua Notifikasi</span>
</a>
<a href="{{ route('notifications.index', ['ctx' => 'lms-guru', 'filter' => 'unread']) }}"
    class="nav-link {{ request('filter') === 'unread' ? 'active' : '' }}">
    <i class="fas fa-envelope"></i>
    <span>Belum Dibaca</span>
</a>
<a href="{{ route('notifications.index', ['ctx' => 'lms-guru', 'filter' => 'read']) }}"
    class="nav-link {{ request('filter') === 'read' ? 'active' : '' }}">
    <i class="fas fa-envelope-open"></i>
    <span>Sudah Dibaca</span>
</a>

<div class="nav-section-title">NAVIGASI</div>
<a href="{{ route('guru.dashboard') }}" class="nav-link">
    <i class="fas fa-tachometer-alt"></i>
    <span>Dashboard Guru</span>
</a>
<a href="{{ route('guru.kelas.index') }}" class="nav-link">
    <i class="fas fa-chalkboard-teacher"></i>
    <span>Daftar Kelas LMS</span>
</a>
