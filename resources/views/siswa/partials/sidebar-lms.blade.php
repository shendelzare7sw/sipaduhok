{{-- Navigasi LMS Siswa (HOK Learning). --}}
@php
    $sectionClass = 'px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400';
    $siswa = auth()->user()->siswa;
    $mataPelajaran = \App\Models\JadwalPelajaran::whereHas('kelas', function ($q) use ($siswa) {
            $q->where('kelas.id', $siswa->kelas_id ?? 0);
        })
        ->with('mataPelajaran')
        ->get()
        ->pluck('mataPelajaran')
        ->filter(fn ($mapel) => $mapel && $siswa && $siswa->canAccessMapel($mapel))
        ->unique('id')
        ->sortBy('nama_mapel');
@endphp

<x-cleanflow.lms-nav-link :href="route('siswa.sia.dashboard')" icon="fa-arrow-left">Kembali ke SIA</x-cleanflow.lms-nav-link>

<p class="{{ $sectionClass }}">Beranda</p>
<x-cleanflow.lms-nav-link :href="route('siswa.lms.dashboard')" icon="fa-house" :active="request()->routeIs('siswa.lms.dashboard')">Beranda</x-cleanflow.lms-nav-link>

<p class="{{ $sectionClass }}">Mata pelajaran</p>
@forelse($mataPelajaran as $mapel)
    <x-cleanflow.lms-nav-link :href="route('siswa.lms.mapel.show', $mapel->id)" icon="fa-book" :active="request()->is('siswa/lms/mata-pelajaran/'.$mapel->id.'*')" title="{{ $mapel->nama_mapel }}">{{ $mapel->nama_mapel }}</x-cleanflow.lms-nav-link>
@empty
    <x-cleanflow.lms-nav-link icon="fa-circle-info" muted>Belum ada mata pelajaran</x-cleanflow.lms-nav-link>
@endforelse

<p class="{{ $sectionClass }}">Akademik</p>
<x-cleanflow.lms-nav-link :href="route('siswa.lms.kalender')" icon="fa-calendar-days" :active="request()->routeIs('siswa.lms.kalender*')">Kalender akademik</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('siswa.lms.jadwal')" icon="fa-clock-rotate-left" :active="request()->routeIs('siswa.lms.jadwal')">Jadwal pelajaran</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('siswa.lms.guru')" icon="fa-chalkboard-user" :active="request()->routeIs('siswa.lms.guru')">Daftar guru</x-cleanflow.lms-nav-link>
