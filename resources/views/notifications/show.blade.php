@php
    $userRole = auth()->user()->role ?? 'siswa';
    $ctx = request('ctx'); // 'lms', 'lms-guru', or null (Sneat context)

    // Determine layout based on role and context (ctx param from index or bell)
    $layout = match ($userRole) {
        'siswa'         => ($ctx === 'lms')      ? 'layouts.lms'      : 'layouts.sneat',
        'guru_pengajar' => ($ctx === 'lms-guru') ? 'layouts.lms-guru' : 'layouts.sneat',
        default         => 'layouts.sneat',
    };

    // Determine sidebar partial
    $sidebarPartial = match (true) {
        $userRole === 'siswa'         && $ctx === 'lms'      => 'siswa.partials.sidebar-lms',
        $userRole === 'siswa'                                 => 'siswa.partials.sneat-sidebar-sia',
        $userRole === 'guru_pengajar' && $ctx === 'lms-guru' => 'guru.partials.sidebar-lms-notif',
        $userRole === 'guru_pengajar'                        => 'guru.partials.sneat-sidebar-menu',
        $userRole === 'admin'                                => 'admin.partials.sneat-sidebar-menu',
        $userRole === 'bendahara'                            => 'bendahara.partials.sneat-sidebar-menu',
        $userRole === 'wali_kelas'                           => 'wali-kelas.partials.sneat-sidebar-menu',
        $userRole === 'ketua_pkbm'                           => 'ketua.partials.sneat-sidebar-menu',
        $userRole === 'wakil_kepala_sekolah'                 => 'waka.partials.sneat-sidebar-menu',
        $userRole === 'sekretaris'                           => 'sekretaris.partials.sneat-sidebar-menu',
        $userRole === 'orang_tua'                            => 'orang-tua.partials.sneat-sidebar-menu',
        default                                              => 'partials.sneat-sidebar',
    };

    $colors = [
        'primary' => '#3b82f6', 'success' => '#10b981', 'danger' => '#ef4444',
        'warning' => '#f59e0b', 'info' => '#06b6d4', 'secondary' => '#6b7280',
    ];
    $bg = $colors[$notification->color ?? 'secondary'] ?? '#6b7280';

    $tipeLabels = [
        'materi' => 'Materi', 'tugas' => 'Tugas', 'ujian' => 'Ujian',
        'forum' => 'Forum', 'pengumuman' => 'Pengumuman', 'deadline' => 'Tenggat',
        'nilai' => 'Nilai', 'izin' => 'Izin', 'catatan' => 'Catatan',
        'pembayaran' => 'Keuangan', 'rapor' => 'Rapor', 'sistem' => 'Sistem',
        'kelas' => 'Kelas', 'kenaikan' => 'Kenaikan',
    ];
    $tipeLabel = $tipeLabels[$notification->tipe] ?? ucfirst($notification->tipe);
@endphp

@extends($layout)
@section('title', 'Detail Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Detail pesan')

@section('sidebar-menu')
    @include($sidebarPartial)
@endsection

@section('content')
<div class="container-fluid py-4">
<div>

    {{-- Back button --}}
    <div class="mb-3">
        <a href="{{ route('notifications.index', $ctx ? ['ctx' => $ctx] : []) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Notifikasi
        </a>
    </div>

    {{-- Notification Card --}}
    <div class="card shadow-sm" style="border-radius: 12px; overflow: hidden;">

        {{-- Color Banner --}}
        <div style="height: 5px; background: {{ $bg }};"></div>

        <div class="card-body p-4">

            {{-- Header row --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div style="width:52px;height:52px;border-radius:50%;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="{{ $notification->icon ?? 'fas fa-bell' }} text-white fa-lg"></i>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <span class="badge rounded-pill px-3 py-1"
                            style="background: {{ $bg }}20; color: {{ $bg }}; border: 1px solid {{ $bg }}40; font-size: 12px;">
                            {{ $tipeLabel }}
                        </span>
                        <span class="text-muted small">
                            <i class="fas fa-clock me-1"></i>
                            {{ $notification->created_at->copy()->locale('id')->translatedFormat('d M Y, H:i') }}
                            <span class="ms-1">({{ $notification->created_at->copy()->locale('id')->diffForHumans() }})</span>
                        </span>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ $notification->judul }}</h5>
                </div>
            </div>

            <hr class="my-3">

            {{-- Message body --}}
            <div class="mb-4">
                <p class="text-secondary" style="font-size: 15px; line-height: 1.7; white-space: pre-line;">{{ $notification->pesan }}</p>
            </div>

            {{-- Link to source --}}
            @if($notification->link && !str_contains($notification->link, '/notifications'))
                <div class="d-grid">
                    <a href="{{ $notification->link }}" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-2"></i> Lihat Sumber
                    </a>
                </div>
            @endif

        </div>

        {{-- Footer actions --}}
        <div class="card-footer bg-light px-4 py-2">
            <small class="text-muted">
                <i class="fas fa-check me-1 text-success"></i> Sudah dibaca
            </small>
        </div>
    </div>

</div>
</div>

@endsection
