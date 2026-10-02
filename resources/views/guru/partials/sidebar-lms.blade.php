{{-- Navigasi LMS Guru (HOK Teaching) per kelas + mata pelajaran. --}}
@php
    $lmsArgs = [$kelas->id, $mapel->id];
    $sectionClass = 'px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400';
@endphp

<div class="mb-3 overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 p-4 text-white shadow-lg shadow-indigo-600/25">
    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-indigo-100">Anda mengajar</p>
    <p class="mt-1 truncate text-base font-extrabold !text-white" title="{{ $mapel->nama_mapel ?? '-' }}">{{ $mapel->nama_mapel ?? 'N/A' }}</p>
    <p class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-bold text-white"><i class="fa-solid fa-chalkboard" aria-hidden="true"></i>Kelas {{ $kelas->nama_kelas ?? 'N/A' }}</p>
</div>

<p class="{{ $sectionClass }}">Utama</p>
<x-cleanflow.lms-nav-link :href="route('guru.lms.dashboard', $lmsArgs)" icon="fa-house" :active="request()->routeIs('guru.lms.dashboard')">Beranda kelas</x-cleanflow.lms-nav-link>

<p class="{{ $sectionClass }}">Pembelajaran</p>
<x-cleanflow.lms-nav-link :href="route('guru.lms.materi.index', $lmsArgs)" icon="fa-book" :active="request()->routeIs('guru.lms.materi.*')">Materi</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('guru.lms.tugas.index', $lmsArgs)" icon="fa-list-check" :active="request()->routeIs('guru.lms.tugas.*')" :badge="($tugasBelumDikoreksi ?? 0) > 0 ? $tugasBelumDikoreksi : null">Tugas</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('guru.lms.latihan.index', $lmsArgs)" icon="fa-pencil-ruler" :active="request()->routeIs('guru.lms.latihan.*')">Latihan</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('guru.lms.ujian.index', $lmsArgs)" icon="fa-file-lines" :active="request()->routeIs('guru.lms.ujian.*')">Ujian</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('guru.lms.forum.index', $lmsArgs)" icon="fa-comments" :active="request()->routeIs('guru.lms.forum.*')">Forum diskusi</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('guru.lms.meeting.index', $lmsArgs)" icon="fa-video" :active="request()->routeIs('guru.lms.meeting.*')">Kelas virtual</x-cleanflow.lms-nav-link>

<p class="{{ $sectionClass }}">Penilaian</p>
<x-cleanflow.lms-nav-link :href="route('guru.lms.nilai.index', $lmsArgs)" icon="fa-chart-line" :active="request()->routeIs('guru.lms.nilai.*')">Nilai siswa</x-cleanflow.lms-nav-link>

<p class="{{ $sectionClass }}">Navigasi</p>
<x-cleanflow.lms-nav-link :href="route('guru.kelas.mapel', $kelas->id)" icon="fa-layer-group">Ganti mata pelajaran</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('guru.dashboard')" icon="fa-arrow-left">Kembali ke SIA</x-cleanflow.lms-nav-link>
