{{-- Context Info --}}
<div class="lms-teaching-context">
    <div class="lms-teaching-context-label">Anda Mengajar:</div>
    <div class="lms-teaching-context-subject">{{ $mapel->nama_mapel ?? 'N/A' }}</div>
    <div class="lms-teaching-context-class">Kelas {{ $kelas->nama_kelas ?? 'N/A' }}</div>
</div>

<div class="nav-section-title">UTAMA</div>
<a href="{{ route('guru.lms.dashboard', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i>
    <span>Beranda</span>
</a>

<div class="nav-section-title">PEMBELAJARAN</div>
<a href="{{ route('guru.lms.materi.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.materi.*') ? 'active' : '' }}">
    <i class="fas fa-book"></i>
    <span>Materi</span>
</a>

<a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.tugas.*') ? 'active' : '' }}">
    <i class="fas fa-tasks"></i>
    <span>Tugas</span>
    @if(isset($tugasBelumDikoreksi) && $tugasBelumDikoreksi > 0)
        <span class="badge-notif">{{ $tugasBelumDikoreksi }}</span>
    @endif
</a>

<a href="{{ route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.latihan.*') ? 'active' : '' }}">
    <i class="fas fa-pencil-ruler"></i>
    <span>Latihan</span>
</a>

<a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.ujian.*') ? 'active' : '' }}">
    <i class="fas fa-file-alt"></i>
    <span>Ujian</span>
</a>

<a href="{{ route('guru.lms.forum.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.forum.*') ? 'active' : '' }}">
    <i class="fas fa-comments"></i>
    <span>Forum Diskusi</span>
</a>

<a href="{{ route('guru.lms.meeting.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.meeting.*') ? 'active' : '' }}">
    <i class="fas fa-video"></i>
    <span>Kelas Virtual</span>
</a>

<div class="nav-section-title">PENILAIAN</div>
<a href="{{ route('guru.lms.nilai.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.nilai.*') ? 'active' : '' }}">
    <i class="fas fa-chart-line"></i>
    <span>Nilai Siswa</span>
</a>

<div class="nav-section-title">NAVIGASI</div>
<a href="{{ route('guru.dashboard') }}" class="nav-link">
    <i class="fas fa-arrow-left"></i>
    <span>Kembali ke Dashboard</span>
</a>
