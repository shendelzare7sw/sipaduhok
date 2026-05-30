@php
    $userRole = auth()->user()->role ?? 'siswa';
    $ctx = request('ctx'); // 'lms', 'lms-guru', or null (Sneat context)

    // Determine layout based on role and context (ctx param from bell "Lihat Semua" link)
    $layout = match ($userRole) {
        'siswa'         => ($ctx === 'lms')      ? 'layouts.lms'      : 'layouts.sneat',
        'guru_pengajar' => ($ctx === 'lms-guru') ? 'layouts.lms-guru' : 'layouts.sneat',
        default         => 'layouts.sneat',
    };

    // Determine sidebar partial — guru LMS sidebar needs $kelas/$mapel (not available here),
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
        $userRole === 'orang_tua'                            => 'orang-tua.partials.sneat-sidebar-menu',
        default                                              => 'partials.sneat-sidebar',
    };
@endphp

@extends($layout)
@section('title', 'Notifikasi')

@section('sidebar-menu')
    @include($sidebarPartial)
@endsection

@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Pusat notifikasi & pesan sistem')

@section('content')
{{-- Inline styles work for ALL layouts (Sneat uses @yield('content'), LMS uses @yield('content')) --}}
<style>
.notif-container { width: 100%; }
.notif-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px; flex-wrap: wrap; gap: 10px;
}
.notif-unread-badge {
    font-size: 13px; font-weight: 600; background: #3b82f6; color: #fff;
    border-radius: 20px; padding: 2px 10px;
}

/* Toolbar */
.notif-toolbar {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 10px;
    padding: 10px 14px; display: flex; align-items: center; gap: 10px;
    margin-bottom: 10px; flex-wrap: wrap;
}
.notif-search-wrap { flex: 1; min-width: 200px; position: relative; }
.notif-search-wrap .fa-search {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    color: #9ca3af; font-size: 13px;
}
.notif-search-input {
    width: 100%; padding: 8px 12px 8px 32px; border: 1px solid #d1d5db;
    border-radius: 8px; font-size: 14px; outline: none; transition: border 0.2s;
}
.notif-search-input:focus { border-color: #3b82f6; }
.filter-btn-group { display: flex; gap: 4px; }
.filter-btn {
    padding: 7px 13px; font-size: 13px; border: 1px solid #d1d5db;
    border-radius: 7px; background: #f9fafb; color: #374151; cursor: pointer;
    transition: all 0.15s; text-decoration: none; display: inline-flex;
    align-items: center; gap: 5px; white-space: nowrap;
}
.filter-btn:hover { background: #e5e7eb; color: #1f2937; }
.filter-btn.active { background: #eff6ff; border-color: #3b82f6; color: #1d4ed8; font-weight: 600; }
.date-range-wrap { display: flex; align-items: center; gap: 6px; }
.date-input {
    padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 7px;
    font-size: 13px; outline: none; color: #374151; max-width: 130px;
}
.date-input:focus { border-color: #3b82f6; }

/* Bulk Toolbar */
.bulk-toolbar {
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px;
    padding: 10px 14px; display: none; align-items: center; gap: 10px;
    margin-bottom: 10px; flex-wrap: wrap;
}
.bulk-toolbar.visible { display: flex; }
.bulk-count { font-size: 13px; font-weight: 600; color: #1d4ed8; margin-right: auto; }
.bulk-btn {
    padding: 6px 14px; font-size: 13px; border-radius: 7px; border: 1px solid;
    cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    font-weight: 500; transition: all 0.15s;
}
.bulk-btn-read    { background: #d1fae5; border-color: #6ee7b7; color: #065f46; }
.bulk-btn-read:hover { background: #a7f3d0; }
.bulk-btn-unread  { background: #fef3c7; border-color: #fde68a; color: #92400e; }
.bulk-btn-unread:hover { background: #fde68a; }
.bulk-btn-delete  { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }
.bulk-btn-delete:hover { background: #fecaca; }

/* Card list */
.notif-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;
}
.notif-item {
    display: flex; align-items: flex-start; gap: 12px; padding: 13px 16px;
    border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.15s;
    position: relative;
}
.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: #f9fafb; }
.notif-item.unread { background: #eff6ff; }
.notif-item.unread:hover { background: #dbeafe; }
.notif-item.selected { background: #e0e7ff; }
.notif-cb { flex-shrink: 0; margin-top: 3px; accent-color: #3b82f6; width: 16px; height: 16px; cursor: pointer; }
.notif-icon-circle {
    width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center;
    justify-content: center; flex-shrink: 0; font-size: 14px; color: #fff;
}
.notif-body { flex: 1; min-width: 0; }
.notif-row1 { display: flex; align-items: baseline; gap: 8px; margin-bottom: 2px; }
.notif-sender { font-size: 13px; font-weight: 700; color: #1f2937; white-space: nowrap; flex-shrink: 0; }
.notif-subject {
    font-size: 13px; color: #4b5563; white-space: nowrap; overflow: hidden;
    text-overflow: ellipsis; flex: 1;
}
.notif-item.unread .notif-sender { color: #1d4ed8; }
.notif-item.unread .notif-subject { color: #1f2937; font-weight: 600; }
.notif-preview { font-size: 12px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.notif-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0; min-width: 70px; }
.notif-time { font-size: 11px; color: #9ca3af; white-space: nowrap; }
.notif-item.unread .notif-time { color: #3b82f6; font-weight: 600; }
.unread-dot { width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; }
.notif-empty { padding: 60px 20px; text-align: center; color: #9ca3af; }
.notif-empty i { font-size: 48px; margin-bottom: 12px; opacity: 0.4; }
.notif-empty p { font-size: 15px; }
.notif-pagination { margin-top: 12px; display: flex; justify-content: center; }

/* =====================================================================
   Modal centering — use flex on modal itself for rock-solid centering
   regardless of layout CSS overrides on .modal-dialog margin.
   ===================================================================== */

/* When Bootstrap shows the modal, override display:block with flex     */
/* so the dialog is centred by flex, not by margin:auto (which some     */
/* layout files override to margin:0.5rem).                              */
#bulkDeleteModal {
    position: fixed !important;
    inset: 0 !important;
}
/* These apply whenever the modal element is visible (show + hide animation phases) */
#bulkDeleteModal.modal-flex {
    display: flex !important;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
}
/* Remove Bootstrap's min-height centering trick — flex container handles it */
#bulkDeleteModal .modal-dialog.modal-dialog-centered {
    min-height: 0 !important;
    margin-top: 1rem;
    margin-bottom: 1rem;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

@php
    $isLmsLayout = in_array($layout, ['layouts.lms', 'layouts.lms-guru']);
@endphp

/* Mobile responsive */
@media (max-width: 767.98px) {
    .notif-toolbar { flex-wrap: wrap; gap: 8px; }
    .notif-search-wrap { flex: 1 1 100%; min-width: 0; order: -1; }
    .filter-btn-group { flex-wrap: wrap; }
    .date-range-wrap { flex: 1 1 100%; }
    .date-input { max-width: none; flex: 1; }
    .notif-item { gap: 8px; padding: 10px 12px; }
    .notif-icon-circle { width: 32px; height: 32px; font-size: 12px; }
    .notif-meta { min-width: 56px; }
    .notif-subject { display: none; }
    .bulk-toolbar { flex-wrap: wrap; gap: 6px; }
    .bulk-count { flex: 1 1 100%; }
}
@media (max-width: 575.98px) {
    .notif-toolbar { padding: 8px 10px; }
    .notif-item { padding: 9px 10px; }
    .filter-btn { padding: 6px 10px; font-size: 12px; }
}
</style>


<div class="container-fluid py-3">
<div class="notif-container">

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
                    oninput="debounceSubmit()">
            </div>

            <div class="filter-btn-group">
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), $ctx ? ['ctx' => $ctx] : [])) }}"
                    class="filter-btn {{ !request('filter') ? 'active' : '' }}">Semua</a>
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), ['filter' => 'unread'] + ($ctx ? ['ctx' => $ctx] : []))) }}"
                    class="filter-btn {{ request('filter') === 'unread' ? 'active' : '' }}">
                    <i class="fas fa-circle text-primary" style="font-size:8px"></i> Belum Dibaca
                </a>
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), ['filter' => 'read'] + ($ctx ? ['ctx' => $ctx] : []))) }}"
                    class="filter-btn {{ request('filter') === 'read' ? 'active' : '' }}">Sudah Dibaca</a>
            </div>

            <div class="date-range-wrap">
                <input type="date" name="date_from" class="date-input"
                    value="{{ request('date_from') }}" title="Dari tanggal" onchange="this.form.submit()">
                <span style="color:#9ca3af;font-size:12px">s/d</span>
                <input type="date" name="date_to" class="date-input"
                    value="{{ request('date_to') }}" title="Sampai tanggal" onchange="this.form.submit()">
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
        <input type="checkbox" id="selectAllCb" class="notif-cb" title="Pilih semua" onchange="toggleSelectAll(this)">
        <span class="bulk-count" id="bulkCount">0 dipilih</span>
        <button class="bulk-btn bulk-btn-read" onclick="bulkAction('read')">
            <i class="fas fa-envelope-open"></i> Tandai Dibaca
        </button>
        <button class="bulk-btn bulk-btn-unread" onclick="bulkAction('unread')">
            <i class="fas fa-envelope"></i> Tandai Belum Dibaca
        </button>
        <button class="bulk-btn bulk-btn-delete" onclick="bulkAction('delete')">
            <i class="fas fa-trash"></i> Hapus
        </button>
    </div>

    {{-- Notification List --}}
    <div class="notif-card">
        @forelse($notifications as $notif)
            @php
                $colors = [
                    'primary' => '#3b82f6', 'success' => '#10b981', 'danger' => '#ef4444',
                    'warning' => '#f59e0b', 'info' => '#06b6d4', 'secondary' => '#6b7280',
                ];
                $bg = $colors[$notif->color ?? 'secondary'] ?? '#6b7280';
                $isUnread = is_null($notif->read_at);
            @endphp
            <div class="notif-item {{ $isUnread ? 'unread' : '' }}"
                id="notif-{{ $notif->id }}"
                data-link="{{ $notif->link ?? '' }}"
                data-tipe="{{ $notif->tipe }}"
                onclick="handleNotifClick(event, {{ $notif->id }}, this)">

                <input type="checkbox" class="notif-cb notif-check"
                    data-id="{{ $notif->id }}"
                    onclick="event.stopPropagation(); updateBulkToolbar()"
                    title="Pilih">

                <div class="notif-icon-circle" style="background: {{ $bg }}">
                    <i class="{{ $notif->icon ?? 'fas fa-bell' }}"></i>
                </div>

                <div class="notif-body">
                    <div class="notif-row1">
                        <span class="notif-sender">
                            @php
                                $tipeLabels = [
                                    'materi' => 'Materi', 'tugas' => 'Tugas', 'ujian' => 'Ujian',
                                    'forum' => 'Forum', 'pengumuman' => 'Pengumuman', 'deadline' => 'Tenggat',
                                    'nilai' => 'Nilai', 'izin' => 'Izin', 'catatan' => 'Catatan',
                                    'pembayaran' => 'Keuangan', 'rapor' => 'Rapor', 'sistem' => 'Sistem',
                                    'kelas' => 'Kelas', 'kenaikan' => 'Kenaikan',
                                ];
                            @endphp
                            {{ $tipeLabels[$notif->tipe] ?? ucfirst($notif->tipe) }}
                        </span>
                        <span class="notif-subject">{{ $notif->judul }}</span>
                    </div>
                    <div class="notif-preview">{{ $notif->pesan }}</div>
                </div>

                <div class="notif-meta" onclick="event.stopPropagation()">
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
                        class="filter-btn" style="margin: 0 auto; width: fit-content;">
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
<form id="bulkActionForm" method="POST" action="{{ route('notifications.bulk-action') }}" style="display:none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    @if($ctx)
        <input type="hidden" name="ctx" value="{{ $ctx }}">
    @endif
    <div id="bulkIdsContainer"></div>
</form>

{{-- Bulk Delete Confirm Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Move modal to <body> so it uses the full viewport as stacking context
    const m = document.getElementById('bulkDeleteModal');
    if (m && m.parentElement !== document.body) document.body.appendChild(m);

    /**
     * Flex display management via JS events (not CSS .show class):
     *
     * Why JS instead of `#bulkDeleteModal.show { display:flex }` CSS?
     *   Bootstrap removes the .show class BEFORE the fade-out animation
     *   completes. The instant .show is gone, the CSS flex rule stops
     *   applying, and Bootstrap's inline `display:block` shows briefly
     *   without flex-centering — causing a visible flicker/jump.
     *
     * Solution: add class .modal-flex on show-start, remove ONLY after
     *   fully hidden (hidden.bs.modal). This keeps flex active through
     *   the entire enter AND exit animation.
     */
    m.addEventListener('show.bs.modal', function () {
        m.classList.add('modal-flex');
@if($isLmsLayout)
        updateLmsPadding();
@endif
    });

    m.addEventListener('hidden.bs.modal', function () {
        m.classList.remove('modal-flex');
@if($isLmsLayout)
        m.style.paddingLeft = '';
@endif
    });

@if($isLmsLayout)
    /**
     * LMS desktop: sidebar takes `sidebarWidth` px on the left.
     * By setting padding-left = sidebarWidth on the flex modal container,
     * justify-content:center centres within the remaining space (content area).
     *
     * Mobile (≤8px) / sidebar-collapsed: no padding → centres to full viewport
     *   = content area (sidebar is hidden).
     */
    function updateLmsPadding() {
        const isMobile  = window.innerWidth <= 768;
        const collapsed = document.body.classList.contains('sidebar-collapsed');
        if (isMobile || collapsed) {
            m.style.paddingLeft = '';
        } else {
            const sw = parseFloat(
                getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width')
            ) || 280;
            m.style.paddingLeft = sw + 'px';
        }
    }

    const bodyObserver = new MutationObserver(function () {
        if (m.classList.contains('modal-flex')) updateLmsPadding();
    });
    bodyObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });

    window.addEventListener('resize', function () {
        if (m.classList.contains('modal-flex')) updateLmsPadding();
    });
@endif
});

let searchTimer;
function debounceSubmit() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
}

// Click on notification row
// - catatan type → navigate to detail page (Gmail-style)
// - other types  → mark as read, then redirect to linked menu
function handleNotifClick(event, id, el) {
    if (event.target.type === 'checkbox') return;

    const tipe = el.dataset.tipe;
    const baseUrl = '{{ url("/notifications") }}';

    if (tipe === 'catatan') {
        // Open detail page
        let showUrl = baseUrl + '/' + id;
        @if(request('ctx'))
            showUrl += '?ctx={{ request("ctx") }}';
        @endif
        window.location.href = showUrl;
        return;
    }

    // Other types: mark as read silently, then go to linked menu
    fetch(`${baseUrl}/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).catch(() => {});

    el.classList.remove('unread');
    el.querySelector('.unread-dot')?.remove();

    const link = el.dataset.link;
    if (link && link.trim() !== '') {
        window.location.href = link;
    }
}

function updateBulkToolbar() {
    const checked = document.querySelectorAll('.notif-check:checked');
    const toolbar = document.getElementById('bulkToolbar');
    const countEl = document.getElementById('bulkCount');
    if (checked.length > 0) {
        toolbar.classList.add('visible');
        countEl.textContent = checked.length + ' dipilih';
    } else {
        toolbar.classList.remove('visible');
        document.getElementById('selectAllCb').checked = false;
    }
}

function toggleSelectAll(cb) {
    document.querySelectorAll('.notif-check').forEach(el => { el.checked = cb.checked; });
    updateBulkToolbar();
}

function bulkAction(action) {
    const checked = document.querySelectorAll('.notif-check:checked');
    if (checked.length === 0) return;

    if (action === 'delete') {
        document.getElementById('bulkDeleteMsg').textContent =
            `Yakin ingin menghapus ${checked.length} notifikasi yang dipilih?`;
        const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
        modal.show();
        document.getElementById('confirmBulkDeleteBtn').onclick = function () {
            modal.hide();
            submitBulkAction('delete');
        };
        return;
    }
    submitBulkAction(action);
}

function submitBulkAction(action) {
    const checked = document.querySelectorAll('.notif-check:checked');
    const form = document.getElementById('bulkActionForm');
    document.getElementById('bulkActionInput').value = action;
    const container = document.getElementById('bulkIdsContainer');
    container.innerHTML = '';
    checked.forEach(el => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = el.dataset.id;
        container.appendChild(input);
    });
    form.submit();
}
</script>
@endsection
