{{--
    Navigasi CleanFlow untuk Guru.
    Diselaraskan dengan 39 Use Case Diagram SIPADUHOK
--}}

@php
    $currentRoute = Route::currentRouteName();
@endphp

{{-- Dashboard --}}
<li class="menu-item {{ $currentRoute == 'guru.dashboard' ? 'active' : '' }}">
    <a href="{{ route('guru.dashboard') }}" class="menu-link">
        <i class="menu-icon fas fa-home"></i>
        <div>Dashboard</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC38: Melihat Informasi Akademik             --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Akademik</span>
</li>

<li class="menu-item {{ $currentRoute == 'guru.jadwal.index' || Str::startsWith($currentRoute, 'guru.kelas') ? 'active open' : '' }}">
    <a href="#" class="menu-link menu-toggle">
        <i class="menu-icon fas fa-calendar-alt"></i>
        <div>Informasi Akademik</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ $currentRoute == 'guru.jadwal.index' ? 'active' : '' }}">
            <a href="{{ route('guru.jadwal.index') }}" class="menu-link">
                <div>Jadwal Mengajar</div>
            </a>
        </li>
        <li class="menu-item {{ Str::startsWith($currentRoute, 'guru.kelas') ? 'active' : '' }}">
            <a href="{{ route('guru.kelas.index') }}" class="menu-link">
                <div>Semua Kelas</div>
            </a>
        </li>
    </ul>
</li>

{{-- ============================================ --}}
{{-- UC27: Kelola Materi Pembelajaran (Arsip)     --}}
{{-- ============================================ --}}
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Pembelajaran</span>
</li>

{{-- Arsip LMS (lintas TA) --}}
<li class="menu-item {{ Str::startsWith($currentRoute, 'guru.lms.arsip') ? 'active' : '' }}">
    <a href="{{ route('guru.lms.arsip.index') }}" class="menu-link">
        <i class="menu-icon fas fa-archive"></i>
        <div>Arsip LMS</div>
    </a>
</li>

{{-- ============================================ --}}
{{-- UC37: Membaca Catatan Monitoring             --}}
{{-- ============================================ --}}
{{-- Catatan Monitoring --}}
<li class="menu-item {{ Str::startsWith($currentRoute, 'guru.lms.catatan-monitoring') ? 'active' : '' }}">
    <a href="{{ route('guru.lms.catatan-monitoring.index') }}" class="menu-link">
        <i class="menu-icon fas fa-comment-dots"></i>
        <div>Catatan Monitoring</div>
        @php
            $unreadCm = 0;
            try {
                $tpId = optional(\App\Models\TenagaPendidik::where('user_id', auth()->id())->first())->id;
                if ($tpId) {
                    $unreadCm = \App\Models\CatatanMonitoring::forGuru($tpId)->unread()->count();
                }
            } catch (\Throwable $e) { /* table may not exist yet */ }
        @endphp
        @if($unreadCm > 0)
            <span class="badge bg-danger rounded-pill ms-auto">{{ $unreadCm }}</span>
        @endif
    </a>
</li>

{{-- ============================================ --}}
{{-- Kelas Saya (Akses Cepat ke LMS)              --}}
{{-- ============================================ --}}
@if(isset($sidebarKelas) && count($sidebarKelas) > 0)
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Kelas Saya (Akses Cepat)</span>
    </li>
    
    @foreach($sidebarKelas as $kelasId => $items)
        @php
            $kelas = $items->first()->kelas;
            $isActive = Str::startsWith($currentRoute, 'guru.lms') && request()->route('kelas') == $kelasId;
        @endphp
        <li class="menu-item {{ $isActive ? 'active open' : '' }}">
            <a href="#" class="menu-link menu-toggle">
                <i class="menu-icon fas fa-chalkboard"></i>
                <div>{{ $kelas->nama_kelas }}</div>
            </a>
            <ul class="menu-sub">
                @foreach($items as $item)
                    @php
                        // Check if mapel relation exists
                        $mapel = $item->mataPelajaran;
                        $mapelId = $item->mata_pelajaran_id;
                        $isSubActive = $isActive && request()->route('mapel') == $mapelId;
                    @endphp
                    @if($mapel)
                        <li class="menu-item {{ $isSubActive ? 'active' : '' }}">
                            <a href="{{ route('guru.lms.dashboard', [$kelasId, $mapelId]) }}" class="menu-link">
                                <div class="text-wrap lh-sm">{{ $mapel->nama_mapel }}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
    @endforeach
@endif
