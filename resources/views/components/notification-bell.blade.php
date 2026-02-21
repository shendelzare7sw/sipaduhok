{{-- Notification Bell Component for Navbar --}}
<div class="dropdown" id="notification-dropdown">
    <button class="btn btn-link nav-link position-relative notification-bell-btn" type="button"
        id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" onclick="loadNotifications()">
        <i class="fas fa-bell fa-lg"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge"
            style="display: none;" id="notification-count">
            0
        </span>
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow" style="width: 360px; max-height: 480px; overflow: hidden;"
        aria-labelledby="notificationDropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center py-2 px-3 bg-light">
            <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Notifikasi</h6>
            <a href="{{ route('notifications.index') }}" class="text-primary small">Lihat Semua</a>
        </div>
        <div id="notification-list" style="max-height: 360px; overflow-y: auto;">
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        <div class="dropdown-footer text-center py-2 border-top">
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-link text-muted">
                    <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    #notification-dropdown .dropdown-menu {
        animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item.unread {
        background: #f0f7ff;
        border-left: 3px solid var(--primary, #165fac);
    }

    .notification-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Notification Bell Button Hover Fix */
    .notification-bell-btn {
        color: #555;
        transition: all 0.3s ease;
        width: 42px;
        height: 42px;
        border-radius: 8px;
        /* Kotak dengan rounded corners */
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgba(0, 0, 0, 0.05);
        /* Light background untuk visibility */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        /* Subtle shadow */
        border: 1px solid transparent;
        /* Prepare border for hover */
        padding: 0 !important;
        /* Remove default Bootstrap padding */
        line-height: 1 !important;
        /* Reset line height */
    }

    .notification-bell-btn i {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 !important;
        /* Remove any margin */
    }

    .notification-bell-btn:hover {
        background-color: rgba(0, 0, 0, 0.12);
        /* Darker background on hover */
        color: #fbbf24 !important;
        /* Yellow/amber on hover */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        /* Enhanced shadow */
        transform: translateY(-2px);
        border: 1px solid rgba(0, 0, 0, 0.1);
        /* Border saat hover */
    }

    .notification-bell-btn:focus {
        color: #fbbf24 !important;
        background-color: rgba(0, 0, 0, 0.12);
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2);
        /* Yellow glow on focus */
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    let notificationsLoaded = false;

    function loadNotifications() {
        if (notificationsLoaded) return;

        fetch('{{ route("notifications.recent") }}')
            .then(response => response.json())
            .then(data => {
                notificationsLoaded = true;
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                document.getElementById('notification-list').innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-exclamation-circle mb-2"></i>
                    <p class="small mb-0">Gagal memuat notifikasi</p>
                </div>
            `;
            });
    }

    function updateNotificationBadge(count) {
        const badge = document.getElementById('notification-count');
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    }

    function renderNotifications(notifications) {
        const container = document.getElementById('notification-list');

        if (notifications.length === 0) {
            container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                <p class="small mb-0">Tidak ada notifikasi</p>
            </div>
        `;
            return;
        }

        let html = '';
        notifications.forEach(notif => {
            const unreadClass = notif.read_at ? '' : 'unread';
            const colorClass = notif.color || 'secondary';
            const iconClass = notif.icon || 'fas fa-bell';
            const notifLink = notif.link || '{{ route("notifications.index") }}';

            html += `
            <div class="notification-item d-flex align-items-start text-decoration-none text-dark ${unreadClass}"
                style="cursor:pointer"
                onclick="handleBellNotifClick(event, ${notif.id}, '${notifLink}')">
                <div class="notification-icon bg-${colorClass} text-white me-3">
                    <i class="${iconClass}"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 small fw-bold">${notif.judul}</h6>
                    <p class="mb-1 small text-muted" style="line-height: 1.3;">${notif.pesan}</p>
                    <small class="text-muted">${notif.created_at_formatted || ''}</small>
                </div>
                ${!notif.read_at ? '<span class="badge bg-primary rounded-pill ms-2">Baru</span>' : ''}
            </div>
        `;
        });

        container.innerHTML = html;
    }

    function handleBellNotifClick(event, id, link) {
        // Mark as read, then navigate (always navigate even if fetch fails)
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).finally(() => {
            window.location.href = link;
        });
    }

    // Load unread count on page load
    document.addEventListener('DOMContentLoaded', function () {
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => updateNotificationBadge(data.count))
            .catch(error => console.error('Error:', error));
    });

    // Poll for new notifications every 30 seconds
    setInterval(() => {
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => updateNotificationBadge(data.count))
            .catch(error => console.error('Error:', error));
    }, 30000);
</script>