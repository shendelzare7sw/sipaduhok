{{-- Notification Bell Component for Navbar
     @prop ctx  Pass 'lms' or 'lms-guru' to keep notification links context-aware.
                Omit for the Sneat layout context. --}}
@props(['ctx' => null])

@php
    $bellCtx = $ctx ?? '';
@endphp

<div class="nav-item dropdown navbar-dropdown"
    id="notification-dropdown"
    data-notification-context="{{ $bellCtx }}"
    data-notification-recent-url="{{ url('/notifications/recent') }}"
    data-notification-unread-url="{{ url('/notifications/unread-count') }}"
    data-notification-read-url-template="{{ url('/notifications/__ID__/read') }}">
    <a class="nav-link dropdown-toggle hide-arrow position-relative notification-bell-btn"
        href="javascript:void(0);"
        id="notificationDropdown"
        data-bs-toggle="dropdown"
        data-notification-trigger
        aria-expanded="false">
        <i class="fas fa-bell fa-lg"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge d-none"
            id="notification-count">
            0
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-end shadow-lg notif-dropdown-menu"
        aria-labelledby="notificationDropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center py-2 px-3 bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="fas fa-bell me-2 text-primary"></i>Notifikasi</h6>
            <a href="{{ route('notifications.index', $ctx ? ['ctx' => $ctx] : []) }}" class="text-primary small fw-medium">Lihat Semua</a>
        </div>
        <div id="notification-list" class="notif-list-scroll">
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
            </div>
        </div>
        <div class="dropdown-footer text-center py-2 border-top bg-light">
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-link text-muted">
                    <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
                </button>
            </form>
        </div>
    </div>
</div>
