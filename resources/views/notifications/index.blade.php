@php
    $userRole = auth()->user()->role ?? 'siswa';
    $ctx = request('ctx'); // 'lms', 'lms-guru', or null (Sneat context)

    // Determine layout based on role and context (ctx param from bell "Lihat Semua" link)
    $layout = match ($userRole) {
        'siswa'         => ($ctx === 'lms')      ? 'layouts.lms'      : 'layouts.sneat',
        'guru_pengajar' => ($ctx === 'lms-guru') ? 'layouts.lms-guru' : 'layouts.sneat',
        default         => 'layouts.sneat',
    };

    // Determine sidebar partial. Guru LMS sidebar needs $kelas/$mapel (not available here),
    // so we use a dedicated simple sidebar for LMS notification context.
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
        $userRole === 'orang_tua'                            => 'wali-siswa.partials.sneat-sidebar-menu',
        default                                              => 'partials.sneat-sidebar',
    };

    $isLmsLayout = in_array($layout, ['layouts.lms', 'layouts.lms-guru'], true);
    $notificationColorKeys = ['primary', 'success', 'danger', 'warning', 'info', 'secondary'];
    $tipeLabels = [
        'materi' => 'Materi', 'tugas' => 'Tugas', 'ujian' => 'Ujian',
        'forum' => 'Forum', 'pengumuman' => 'Pengumuman', 'deadline' => 'Tenggat',
        'nilai' => 'Nilai', 'izin' => 'Izin', 'catatan' => 'Catatan',
        'pembayaran' => 'Keuangan', 'rapor' => 'Rapor', 'sistem' => 'Sistem',
        'kelas' => 'Kelas', 'kenaikan' => 'Kenaikan',
    ];
@endphp

@extends($layout)
@section('title', 'Notifikasi')

@push('styles')
    @vite(['resources/css/notifications/index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/notifications/index.js'])
@endpush

@section('sidebar-menu')
    @include($sidebarPartial)
@endsection

@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Pusat notifikasi & pesan sistem')

@section('content')

<div class="container-fluid py-3">
<div class="notif-container"
    data-notifications-page
    data-notifications-base-url="{{ url('/notifications') }}"
    data-notifications-context="{{ $ctx ?? '' }}"
    data-is-lms-layout="{{ $isLmsLayout ? 'true' : 'false' }}">

    {{-- Header: unread badge + mark all --}}
    @if($unreadCount > 0)
    <div class="notif-header">
        <span class="notif-unread-badge">{{ $unreadCount }} belum dibaca</span>
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button class="filter-btn" type="submit">
                <i class="fas fa-check-double"></i> Tandai Semua Dibaca
            </button>
        </form>
    </div>
    @endif

    {{-- Toolbar: Search + Filter + Date --}}
    <form method="GET" action="{{ route('notifications.index') }}" id="filterForm">
        {{-- Keep ctx param across filter/search requests --}}
        @if($ctx)
            <input type="hidden" name="ctx" value="{{ $ctx }}">
        @endif
        <div class="notif-toolbar">
            <div class="notif-search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="notif-search-input"
                    placeholder="Cari Pesan..."
                    value="{{ request('search') }}"
                    data-filter-search>
            </div>

            <div class="filter-btn-group">
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), $ctx ? ['ctx' => $ctx] : [])) }}"
                    class="filter-btn {{ !request('filter') ? 'active' : '' }}">Semua</a>
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), ['filter' => 'unread'] + ($ctx ? ['ctx' => $ctx] : []))) }}"
                    class="filter-btn {{ request('filter') === 'unread' ? 'active' : '' }}">
                    <i class="fas fa-circle text-primary notif-filter-dot"></i> Belum Dibaca
                </a>
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), ['filter' => 'read'] + ($ctx ? ['ctx' => $ctx] : []))) }}"
                    class="filter-btn {{ request('filter') === 'read' ? 'active' : '' }}">Sudah Dibaca</a>
            </div>

            <div class="date-range-wrap">
                <input type="date" name="date_from" class="date-input"
                    value="{{ request('date_from') }}" title="Dari tanggal" data-auto-submit>
                <span class="date-range-separator">s/d</span>
                <input type="date" name="date_to" class="date-input"
                    value="{{ request('date_to') }}" title="Sampai tanggal" data-auto-submit>
            </div>

            @if(request()->hasAny(['search', 'filter', 'date_from', 'date_to']))
                <a href="{{ route('notifications.index', $ctx ? ['ctx' => $ctx] : []) }}"
                    class="filter-btn" title="Reset filter"><i class="fas fa-times"></i></a>
            @endif

            @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif
        </div>
    </form>

    {{-- Bulk Action Toolbar --}}
    <div class="bulk-toolbar" id="bulkToolbar">
        <input type="checkbox" id="selectAllCb" class="notif-cb" title="Pilih semua">
        <span class="bulk-count" id="bulkCount">0 dipilih</span>
        <button class="bulk-btn bulk-btn-read" data-bulk-action="read">
            <i class="fas fa-envelope-open"></i> Tandai Dibaca
        </button>
        <button class="bulk-btn bulk-btn-unread" data-bulk-action="unread">
            <i class="fas fa-envelope"></i> Tandai Belum Dibaca
        </button>
        <button class="bulk-btn bulk-btn-delete" data-bulk-action="delete">
            <i class="fas fa-trash"></i> Hapus
        </button>
    </div>

    {{-- Notification List --}}
    <div class="notif-card">
        @forelse($notifications as $notif)
            @php
                $colorKey = in_array($notif->color ?? 'secondary', $notificationColorKeys, true)
                    ? ($notif->color ?? 'secondary')
                    : 'secondary';
                $isUnread = is_null($notif->read_at);
            @endphp
            <div class="notif-item {{ $isUnread ? 'unread' : '' }}"
                id="notif-{{ $notif->id }}"
                data-link="{{ $notif->link ?? '' }}"
                data-notification-row
                data-notification-id="{{ $notif->id }}"
                data-notification-type="{{ $notif->tipe }}">

                <input type="checkbox" class="notif-cb notif-check"
                    data-id="{{ $notif->id }}"
                    data-stop-notification-click
                    title="Pilih">

                <div class="notif-icon-circle notif-tone-{{ $colorKey }}">
                    <i class="{{ $notif->icon ?? 'fas fa-bell' }}"></i>
                </div>

                <div class="notif-body">
                    <div class="notif-row1">
                        <span class="notif-sender">
                            {{ $tipeLabels[$notif->tipe] ?? ucfirst($notif->tipe) }}
                        </span>
                        <span class="notif-subject">{{ $notif->judul }}</span>
                    </div>
                    <div class="notif-preview">{{ $notif->pesan }}</div>
                </div>

                <div class="notif-meta" data-stop-notification-click>
                    <span class="notif-time" title="{{ $notif->created_at->copy()->locale('id')->translatedFormat('d M Y, H:i') }}">
                        {{ $notif->created_at->copy()->locale('id')->diffForHumans() }}
                    </span>
                    @if($isUnread)
                        <div class="unread-dot" title="Belum dibaca"></div>
                    @endif
                </div>
            </div>
        @empty
            <div class="notif-empty">
                <i class="fas fa-bell-slash d-block"></i>
                <p>
                    @if(request()->hasAny(['search', 'filter', 'date_from', 'date_to']))
                        Tidak ada notifikasi yang cocok dengan filter
                    @else
                        Belum ada notifikasi
                    @endif
                </p>
                @if(request()->hasAny(['search', 'filter', 'date_from', 'date_to']))
                    <a href="{{ route('notifications.index', $ctx ? ['ctx' => $ctx] : []) }}"
                        class="filter-btn notif-reset-filter">
                        <i class="fas fa-times me-1"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="notif-pagination">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
</div>

{{-- Hidden form for bulk actions --}}
<form id="bulkActionForm" method="POST" action="{{ route('notifications.bulk-action') }}" class="notif-hidden-form">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    @if($ctx)
        <input type="hidden" name="ctx" value="{{ $ctx }}">
    @endif
    <div id="bulkIdsContainer"></div>
</form>

{{-- Bulk Delete Confirm Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered bulk-delete-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Hapus Notifikasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p id="bulkDeleteMsg" class="mb-1">Yakin ingin menghapus notifikasi yang dipilih?</p>
                <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmBulkDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
