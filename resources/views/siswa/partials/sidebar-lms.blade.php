<!-- Kembali ke SIA -->
<a href="{{ route('siswa.sia.dashboard') }}" class="nav-link">
    <i class="bi bi-arrow-left-circle-fill"></i> Kembali ke SIA
</a>

<div class="nav-section-title">BERANDA</div>
<a href="{{ route('siswa.lms.dashboard') }}" class="nav-link {{ request()->routeIs('siswa.lms.dashboard') ? 'active' : '' }}">
    <i class="bi bi-house-door-fill"></i> Beranda
</a>

{{-- MATA PELAJARAN - Prioritas Utama --}}
<div class="nav-section-title">MATA PELAJARAN</div>
@php
    $siswa = auth()->user()->siswa;
    $siswa = auth()->user()->siswa;
    $mataPelajaran = \App\Models\JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
            $q->where('kelas.id', $siswa->kelas_id ?? 0);
        })
        ->with('mataPelajaran')
        ->get()
        ->pluck('mataPelajaran')
        ->unique('id')
        ->sortBy('nama_mapel');
@endphp

@forelse($mataPelajaran as $mapel)
<a href="{{ route('siswa.lms.mapel.show', $mapel->id) }}" 
   class="nav-link {{ request()->is('siswa/lms/mata-pelajaran/' . $mapel->id . '*') ? 'active' : '' }}">
    <i class="bi bi-book"></i> {{ $mapel->nama_mapel }}
</a>
@empty
<div class="nav-link text-muted" style="opacity: 0.5; cursor: default;">
    <i class="bi bi-info-circle"></i> Belum ada mata pelajaran
</div>
@endforelse

{{-- MENU AKADEMIK --}}
<div class="nav-section-title">AKADEMIK</div>
<a href="{{ route('siswa.lms.kalender') }}" class="nav-link {{ request()->routeIs('siswa.lms.kalender*') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Kalender Akademik
</a>
<a href="{{ route('siswa.lms.jadwal') }}" class="nav-link {{ request()->routeIs('siswa.lms.jadwal') ? 'active' : '' }}">
    <i class="bi bi-clock-history"></i> Jadwal Pelajaran
</a>
<a href="{{ route('siswa.lms.guru') }}" class="nav-link {{ request()->routeIs('siswa.lms.guru') ? 'active' : '' }}">
    <i class="bi bi-person-video3"></i> Daftar Guru
</a>