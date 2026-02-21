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
@endphp

@extends($layout)
@section('title', 'Notifikasi')

@section('sidebar-menu')
    @include($sidebarPartial)
@endsection

@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Pusat notifikasi & pesan sistem')

@section('styles')
<style>
.notif-container {
    width: 100%;
}
.notif-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 10px;
}
.notif-title {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 10px;
}
.notif-unread-badge {
    font-size: 13px;
    font-weight: 600;
    background: #3b82f6;
    color: #fff;
    border-radius: 20px;
    padding: 2px 10px;
}

/* Toolbar */
.notif-toolbar {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.notif-search-wrap {
    flex: 1;
    min-width: 200px;
    position: relative;
}
.notif-search-wrap .fa-search {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 13px;
}
.notif-search-input {
    width: 100%;
    padding: 8px 12px 8px 32px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border 0.2s;
}
.notif-search-input:focus { border-color: #3b82f6; }

.filter-btn-group { display: flex; gap: 4px; }
.filter-btn {
    padding: 7px 13px;
    font-size: 13px;
    border: 1px solid #d1d5db;
    border-radius: 7px;
    background: #f9fafb;
    color: #374151;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}
.filter-btn:hover { background: #e5e7eb; color: #1f2937; }
.filter-btn.active { background: #eff6ff; border-color: #3b82f6; color: #1d4ed8; font-weight: 600; }

.date-range-wrap { display: flex; align-items: center; gap: 6px; }
.date-input {
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 7px;
    font-size: 13px;
    outline: none;
    color: #374151;
    max-width: 130px;
}
.date-input:focus { border-color: #3b82f6; }

/* Bulk Toolbar */
.bulk-toolbar {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    padding: 10px 14px;
    display: none;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.bulk-toolbar.visible { display: flex; }
.bulk-count {
    font-size: 13px;
    font-weight: 600;
    color: #1d4ed8;
    margin-right: auto;
}
.bulk-btn {
    padding: 6px 14px;
    font-size: 13px;
    border-radius: 7px;
    border: 1px solid;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
    transition: all 0.15s;
}
.bulk-btn-read  { background: #d1fae5; border-color: #6ee7b7; color: #065f46; }
.bulk-btn-read:hover { background: #a7f3d0; }
.bulk-btn-unread  { background: #fef3c7; border-color: #fde68a; color: #92400e; }
.bulk-btn-unread:hover { background: #fde68a; }
.bulk-btn-delete { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }
.bulk-btn-delete:hover { background: #fecaca; }

/* Card list */
.notif-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
}
.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 16px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.15s;
    position: relative;
}
.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: #f9fafb; }
.notif-item.unread { background: #eff6ff; }
.notif-item.unread:hover { background: #dbeafe; }
.notif-item.selected { background: #e0e7ff; }

.notif-cb { flex-shrink: 0; margin-top: 3px; accent-color: #3b82f6; width: 16px; height: 16px; cursor: pointer; }

.notif-icon-circle {
    width: 38px; height: 38px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 14px;
    color: #fff;
}

.notif-body { flex: 1; min-width: 0; }
.notif-row1 {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 2px;
}
.notif-sender {
    font-size: 13px;
    font-weight: 700;
    color: #1f2937;
    white-space: nowrap;
    flex-shrink: 0;
}
.notif-subject {
    font-size: 13px;
    color: #4b5563;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}
.notif-item.unread .notif-sender { color: #1d4ed8; }
.notif-item.unread .notif-subject { color: #1f2937; font-weight: 600; }

.notif-preview {
    font-size: 12px;
    color: #9ca3af;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.notif-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    flex-shrink: 0;
    min-width: 70px;
}
.notif-time {
    font-size: 11px;
    color: #9ca3af;
    white-space: nowrap;
}
.notif-item.unread .notif-time { color: #3b82f6; font-weight: 600; }
.unread-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #3b82f6;
}

.notif-empty {
    padding: 60px 20px;
    text-align: center;
    color: #9ca3af;
}
.notif-empty i { font-size: 48px; margin-bottom: 12px; opacity: 0.4; }
.notif-empty p { font-size: 15px; }

.notif-pagination {
    margin-top: 12px;
    display: flex;
    justify-content: center;
}

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
    .notif-header { gap: 8px; }
    .notif-title { font-size: 17px; }
}
@media (max-width: 575.98px) {
    .notif-toolbar { padding: 8px 10px; }
    .notif-item { padding: 9px 10px; }
    .filter-btn { padding: 6px 10px; font-size: 12px; }
}
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
<div class="notif-container">

    {{-- Header --}}
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

    {{-- Toolbar: Search + Filter --}}
    <form method="GET" action="{{ route('notifications.index') }}" id="filterForm">
        <div class="notif-toolbar">
            {{-- Search --}}
            <div class="notif-search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="notif-search-input"
                    placeholder="Cari Pesan..."
                    value="{{ request('search') }}"
                    oninput="debounceSubmit()">
            </div>

            {{-- Read/Unread Filter --}}
            <div class="filter-btn-group">
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), [])) }}"
                    class="filter-btn {{ !request('filter') ? 'active' : '' }}">
                    Semua
                </a>
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), ['filter' => 'unread'])) }}"
                    class="filter-btn {{ request('filter') === 'unread' ? 'active' : '' }}">
                    <i class="fas fa-circle text-primary" style="font-size:8px"></i> Belum Dibaca
                </a>
                <a href="{{ route('notifications.index', array_merge(request()->except('filter', 'page'), ['filter' => 'read'])) }}"
                    class="filter-btn {{ request('filter') === 'read' ? 'active' : '' }}">
                    Sudah Dibaca
                </a>
            </div>

            {{-- Date Filter --}}
            <div class="date-range-wrap">
                <input type="date" name="date_from" class="date-input"
                    value="{{ request('date_from') }}"
                    title="Dari tanggal"
                    onchange="this.form.submit()">
                <span style="color:#9ca3af;font-size:12px">s/d</span>
                <input type="date" name="date_to" class="date-input"
                    value="{{ request('date_to') }}"
                    title="Sampai tanggal"
                    onchange="this.form.submit()">
            </div>

            @if(request()->hasAny(['search', 'filter', 'date_from', 'date_to']))
                <a href="{{ route('notifications.index') }}" class="filter-btn" title="Reset filter">
                    <i class="fas fa-times"></i>
                </a>
            @endif

            {{-- Hidden filter if passed via link --}}
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
                onclick="handleNotifClick(event, {{ $notif->id }}, this)">

                {{-- Checkbox --}}
                <input type="checkbox" class="notif-cb notif-check"
                    data-id="{{ $notif->id }}"
                    onclick="event.stopPropagation(); updateBulkToolbar()"
                    title="Pilih">

                {{-- Icon --}}
                <div class="notif-icon-circle" style="background: {{ $bg }}">
                    <i class="{{ $notif->icon ?? 'fas fa-bell' }}"></i>
                </div>

                {{-- Body --}}
                <div class="notif-body">
                    <div class="notif-row1">
                        <span class="notif-sender">
                            @php
                                $tipeLabels = [
                                    'materi' => 'Materi', 'tugas' => 'Tugas', 'ujian' => 'Ujian',
                                    'forum' => 'Forum', 'pengumuman' => 'Pengumuman', 'deadline' => 'Deadline',
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

                {{-- Meta --}}
                <div class="notif-meta" onclick="event.stopPropagation()">
                    <span class="notif-time" title="{{ $notif->created_at->format('d M Y, H:i') }}">
                        {{ $notif->created_at->diffForHumans() }}
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
                    <a href="{{ route('notifications.index') }}" class="filter-btn" style="margin: 0 auto; width: fit-content;">
                        <i class="fas fa-times me-1"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="notif-pagination">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
</div>

{{-- Hidden forms for JS actions --}}
<form id="bulkActionForm" method="POST" action="{{ route('notifications.bulk-action') }}" style="display:none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
    <div id="bulkIdsContainer"></div>
</form>

{{-- Bootstrap Delete Confirm Modal --}}
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

@endsection

@section('scripts')
<script>
// Debounce for search
let searchTimer;
function debounceSubmit() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
}

// Click on notification row - go directly to the linked menu, mark as read silently
function handleNotifClick(event, id, el) {
    if (event.target.type === 'checkbox') return;

    const link = el.dataset.link;
    const currentBase = '{{ url("/notifications") }}';

    // Mark as read silently (non-blocking)
    fetch(`${currentBase}/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).catch(() => {});

    // Update visual state immediately (mark as read)
    el.classList.remove('unread');
    el.querySelector('.unread-dot')?.remove();

    // Navigate to the target link if it points outside this page
    if (link && !link.endsWith('/notifications') && !link.endsWith('/notifications/')) {
        window.location.href = link;
    }
    // If link is /notifications or empty - just mark visually (user stays on page)
}

// Update bulk toolbar
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

// Toggle select all
function toggleSelectAll(cb) {
    document.querySelectorAll('.notif-check').forEach(el => {
        el.checked = cb.checked;
    });
    updateBulkToolbar();
}

// Perform bulk action
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
