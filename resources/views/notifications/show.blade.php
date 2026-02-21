@php
    $userRole = auth()->user()->role ?? 'siswa';
    $layout = match ($userRole) {
        'siswa' => 'layouts.lms',
        'guru_pengajar' => 'layouts.lms-guru',
        default => 'layouts.sneat',
    };
    $sidebarPartial = match ($userRole) {
        'siswa' => 'siswa.partials.sidebar-lms',
        'guru_pengajar' => 'guru.partials.sidebar-lms',
        'admin' => 'admin.partials.sneat-sidebar-menu',
        'bendahara' => 'bendahara.partials.sneat-sidebar-menu',
        'wali_kelas' => 'wali-kelas.partials.sneat-sidebar-menu',
        'ketua_pkbm' => 'ketua.partials.sneat-sidebar-menu',
        'wakil_kepala_sekolah' => 'waka.partials.sneat-sidebar-menu',
        'sekretaris' => 'sekretaris.partials.sneat-sidebar-menu',
        'orang_tua' => 'orang-tua.partials.sneat-sidebar-menu',
        default => 'partials.sneat-sidebar',
    };

    $colors = [
        'primary' => '#3b82f6', 'success' => '#10b981', 'danger' => '#ef4444',
        'warning' => '#f59e0b', 'info' => '#06b6d4', 'secondary' => '#6b7280',
    ];
    $bg = $colors[$notification->color ?? 'secondary'] ?? '#6b7280';

    $tipeLabels = [
        'materi' => 'Materi', 'tugas' => 'Tugas', 'ujian' => 'Ujian',
        'forum' => 'Forum', 'pengumuman' => 'Pengumuman', 'deadline' => 'Deadline',
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
        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary">
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
                            {{ $notification->created_at->format('d M Y, H:i') }}
                            <span class="ms-1">({{ $notification->created_at->diffForHumans() }})</span>
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
        <div class="card-footer bg-light d-flex justify-content-between align-items-center px-4 py-2">
            <small class="text-muted">
                <i class="fas fa-check me-1 text-success"></i> Sudah dibaca
            </small>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteNotifModal">
                <i class="fas fa-trash me-1"></i> Hapus
            </button>
        </div>
    </div>

</div>
</div>

{{-- Hidden delete form --}}
<form id="deleteNotifForm" action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteNotifModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Hapus Notifikasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-1">Yakin ingin menghapus notifikasi ini?</p>
                <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('deleteNotifForm').submit()">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
