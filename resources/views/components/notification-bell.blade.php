{{-- Notification Bell Component for Navbar --}}
<div class="nav-item dropdown navbar-dropdown" id="notification-dropdown">
    <a class="nav-link dropdown-toggle hide-arrow position-relative notification-bell-btn" href="javascript:void(0);"
        id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" onclick="loadNotifications()">
        <i class="fas fa-bell fa-lg"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge"
            style="display: none;" id="notification-count">
            0
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-end shadow-lg notif-dropdown-menu"
        aria-labelledby="notificationDropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center py-2 px-3 bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="fas fa-bell me-2 text-primary"></i>Notifikasi</h6>
            <a href="{{ route('notifications.index') }}" class="text-primary small fw-medium">Lihat Semua</a>
        </div>
        <div id="notification-list" class="notif-list-scroll">
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
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

<style>
    /* ===== DROPDOWN MENU ===== */
    .notif-dropdown-menu {
        width: 360px;
        max-width: 100vw;
        max-height: 480px;
        overflow: hidden;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        animation: notifFadeIn 0.18s ease-out;
        margin-top: 0.5rem !important; /* Add some space below the bell */
    }

    @keyframes notifFadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .notif-list-scroll {
        max-height: 340px;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .notif-list-scroll::-webkit-scrollbar { width: 4px; }
    .notif-list-scroll::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 4px;
    }

    /* ===== NOTIFICATION ITEMS ===== */
    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s;
        cursor: pointer;
    }

    .notification-item:last-child { border-bottom: none; }
    .notification-item:hover { background: #f8f9fa; }

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
        font-size: 14px;
    }

    /* ===== BELL BUTTON ===== */
    .notification-bell-btn {
        color: #555;
        transition: all 0.3s ease;
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgba(0, 0, 0, 0.05);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        border: 1px solid transparent;
        padding: 0 !important;
        line-height: 1 !important;
    }

    .notification-bell-btn i {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 !important;
    }

    .notification-bell-btn:hover {
        background-color: rgba(0, 0, 0, 0.12);
        color: #fbbf24 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
        border-color: rgba(0, 0, 0, 0.1);
    }

    .notification-bell-btn:focus {
        color: #fbbf24 !important;
        background-color: rgba(0, 0, 0, 0.12);
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2);
        border-color: rgba(0, 0, 0, 0.1);
    }

    /* ===== TABLET (≤991px) ===== */
    @media (max-width: 991.98px) {
        .notif-dropdown-menu {
            width: 320px;
        }
        .notif-list-scroll {
            max-height: 300px;
        }
    }

    /* ===== MOBILE (≤575px) ===== */
    @media (max-width: 575.98px) {
        /* Posisi & lebar dikendalikan JS dengan position:fixed */
        .notif-list-scroll {
            max-height: 320px;
        }
        .notification-item {
            padding: 12px 14px;
        }
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
            .catch(() => {
                document.getElementById('notification-list').innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-exclamation-circle fa-lg mb-2 d-block"></i>
                        <p class="small mb-0">Gagal memuat notifikasi</p>
                    </div>`;
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
                    <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                    <p class="small mb-0">Tidak ada notifikasi</p>
                </div>`;
            return;
        }

        let html = '';
        notifications.forEach(notif => {
            const unreadClass = notif.read_at ? '' : 'unread';
            const colorClass  = notif.color || 'secondary';
            const iconClass   = notif.icon  || 'fas fa-bell';
            const notifLink   = notif.link  || '{{ route("notifications.index") }}';

            html += `
            <div class="notification-item d-flex align-items-start ${unreadClass}"
                onclick="handleBellNotifClick(event, ${notif.id}, '${notifLink}')">
                <div class="notification-icon bg-${colorClass} text-white me-3">
                    <i class="${iconClass}"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="mb-1 small fw-bold text-truncate">${notif.judul}</h6>
                    <p class="mb-1 small text-muted" style="line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">${notif.pesan}</p>
                    <small class="text-muted">${notif.created_at_formatted || ''}</small>
                </div>
                ${!notif.read_at ? '<span class="badge bg-primary rounded-pill ms-2 flex-shrink-0" style="font-size:10px;">Baru</span>' : ''}
            </div>`;
        });

        container.innerHTML = html;
    }

    function handleBellNotifClick(event, id, link) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).finally(() => { window.location.href = link; });
    }

    function fixNotifDropdownMobile() {
        const menu = document.querySelector('.notif-dropdown-menu');
        if (!menu) return;

        if (window.innerWidth > 575) {
            // Kembalikan ke CSS normal (desktop/tablet)
            menu.removeAttribute('style');
            return;
        }

        const btn = document.getElementById('notificationDropdown');
        if (!btn) return;

        const btnRect = btn.getBoundingClientRect();
        const marginSisi = 20; // jarak dari tepi kiri & kanan viewport

        // Cari tombol profil sebagai acuan batas kanan dropdown
        // Fallback ke posisi kanan bell jika profil tidak ditemukan
        const profileBtn = document.querySelector(
            '.dropdown-user .nav-link, .navbar-dropdown.dropdown-user a, ' +
            '.user-avatar, .avatar.avatar-online'
        );
        const anchorRight = profileBtn
            ? profileBtn.getBoundingClientRect().right
            : btnRect.right;

        // Kanan dropdown sejajar dengan kanan tombol profil
        const rightOffset = Math.max(marginSisi, window.innerWidth - anchorRight);

        menu.setAttribute('style',
            `position: fixed !important;` +
            `top: ${btnRect.bottom + 6}px !important;` +
            `left: ${marginSisi}px !important;` +
            `right: ${rightOffset}px !important;` +
            `width: auto !important;` +
            `transform: none !important;` +
            `z-index: 9999 !important;`
        );
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Load unread count
        fetch('{{ route("notifications.unread-count") }}')
            .then(r => r.json())
            .then(data => updateNotificationBadge(data.count))
            .catch(() => {});

        const btn = document.getElementById('notificationDropdown');
        if (btn) {
            // Jalankan setelah Popper.js selesai positioning
            btn.addEventListener('show.bs.dropdown', function () {
                requestAnimationFrame(fixNotifDropdownMobile);
            });

            // Reset saat dropdown ditutup
            btn.addEventListener('hidden.bs.dropdown', function () {
                const menu = document.querySelector('.notif-dropdown-menu');
                if (menu && window.innerWidth <= 575) menu.removeAttribute('style');
            });
        }

        window.addEventListener('resize', function () {
            if (document.querySelector('.notif-dropdown-menu.show')) {
                fixNotifDropdownMobile();
            }
        });
    });

    setInterval(() => {
        fetch('{{ route("notifications.unread-count") }}')
            .then(r => r.json())
            .then(data => updateNotificationBadge(data.count))
            .catch(() => {});
    }, 30000);
</script>
