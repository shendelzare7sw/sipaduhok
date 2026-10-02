{{-- Navigasi halaman notifikasi dalam konteks LMS Guru (tanpa $kelas/$mapel). --}}
@php
    $sectionClass = 'px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400 first:pt-0';
@endphp

<p class="{{ $sectionClass }}">Notifikasi</p>
<x-cleanflow.lms-nav-link :href="route('notifications.index', ['ctx' => 'lms-guru'])" icon="fa-bell" :active="! request('filter')">Semua notifikasi</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('notifications.index', ['ctx' => 'lms-guru', 'filter' => 'unread'])" icon="fa-envelope" :active="request('filter') === 'unread'">Belum dibaca</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('notifications.index', ['ctx' => 'lms-guru', 'filter' => 'read'])" icon="fa-envelope-open" :active="request('filter') === 'read'">Sudah dibaca</x-cleanflow.lms-nav-link>

<p class="{{ $sectionClass }}">Navigasi</p>
<x-cleanflow.lms-nav-link :href="route('guru.dashboard')" icon="fa-gauge">Dashboard guru</x-cleanflow.lms-nav-link>
<x-cleanflow.lms-nav-link :href="route('guru.kelas.index')" icon="fa-chalkboard-user">Daftar kelas LMS</x-cleanflow.lms-nav-link>
