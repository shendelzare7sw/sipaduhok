{{-- Context Info --}}
<div style="padding: 16px 20px; background: rgba(255,255,255,0.1); margin: 0 16px 16px; border-radius: 8px;">
    <div style="font-size: 12px; opacity: 0.7; margin-bottom: 4px;">Anda Mengajar:</div>
    <div style="font-weight: 700; font-size: 14px;">{{ $mapel->nama_mapel ?? 'N/A' }}</div>
    <div style="font-size: 12px; opacity: 0.9;">Kelas {{ $kelas->nama_kelas ?? 'N/A' }}</div>
</div>

<div class="nav-section-title">MENU LMS</div>
<a href="{{ route('guru.lms.dashboard', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i>
    <span>Beranda</span>
</a>

<a href="{{ route('guru.lms.materi.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.materi.*') ? 'active' : '' }}">
    <i class="fas fa-book"></i>
    <span>Materi</span>
</a>

<a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.tugas.*') ? 'active' : '' }}">
    <i class="fas fa-tasks"></i>
    <span>Tugas & Latihan</span>
    @if(isset($tugasBelumDikoreksi) && $tugasBelumDikoreksi > 0)
        <span class="badge-notif">{{ $tugasBelumDikoreksi }}</span>
    @endif
</a>

<a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.ujian.*') ? 'active' : '' }}">
    <i class="fas fa-file-alt"></i>
    <span>Ujian</span>
</a>

<a href="{{ route('guru.lms.kuis.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.kuis.*') ? 'active' : '' }}">
    <i class="fas fa-question-circle"></i>
    <span>Kuis</span>
</a>

<a href="{{ route('guru.lms.nilai.index', [$kelas->id, $mapel->id]) }}"
    class="nav-link {{ request()->routeIs('guru.lms.nilai.*') ? 'active' : '' }}">
    <i class="fas fa-chart-line"></i>
    <span>Nilai Siswa</span>
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

<div class="nav-section-title">NAVIGASI</div>
<a href="{{ route('guru.dashboard') }}" class="nav-link">
    <i class="fas fa-arrow-left"></i>
    <span>Kembali ke Dashboard</span>
</a>
