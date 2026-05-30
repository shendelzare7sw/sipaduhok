{{-- Notification Bell Component for Navbar
     @prop ctx  Pass 'lms' (siswa LMS) or 'lms-guru' (guru LMS) to make
                the "Lihat Semua" link and notification clicks context-aware.
                Omit (or null) for Sneat layout context. --}}
@props(['ctx' => null])

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
        /* No CSS animation — it uses transform which conflicts with our
           fixed-position overrides in LMS layouts. Fade only. */
        animation: notifFadeIn 0.15s ease-out;
    }

    @keyframes notifFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
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

        // Use relative path — avoids http/https mismatch (APP_URL vs actual protocol)
        fetch('/notifications/recent')
            .then(response => {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.json();
            })
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
                // Allow retry on next click
                notificationsLoaded = false;
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
            const notifLink   = notif.link  || '/notifications';

            html += `
            <div class="notification-item d-flex align-items-start ${unreadClass}"
                onclick="handleBellNotifClick(event, ${notif.id}, '${notifLink}', '${notif.tipe || ''}')">
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

    function handleBellNotifClick(event, id, link, tipe) {
        const bellCtx = '{{ $ctx ?? "" }}';

        // Catatan type → open detail page
        if (tipe === 'catatan') {
            let showUrl = '/notifications/' + id;
            if (bellCtx) showUrl += '?ctx=' + bellCtx;
            window.location.href = showUrl;
            return;
        }

        // Other types → mark as read, then navigate to linked menu
        let target = link;
        if (bellCtx && target.match(/\/notifications\/?(\?.*)?$/)) {
            target += (target.includes('?') ? '&' : '?') + 'ctx=' + bellCtx;
        }

        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).finally(() => { window.location.href = target; });
    }

    // Unified dropdown position fix.
    // In LMS context (bellCtx set) the header is position:sticky which breaks
    // Popper.js absolute positioning — so we always use position:fixed there.
    // In Sneat context we let Bootstrap/Popper handle it normally.
    function fixNotifDropdownPosition() {
        const menu = document.querySelector('.notif-dropdown-menu');
        if (!menu) return;

        const bellCtx  = '{{ $ctx ?? "" }}';
        const btn      = document.getElementById('notificationDropdown');
        if (!btn) return;

        const btnRect  = btn.getBoundingClientRect();
        const isMobile = window.innerWidth <= 575;

        if (isMobile) {
            // ── MOBILE (all contexts): full-width strip below the bell ──────────
            const margin = 16;
            // Try to anchor right edge to profile button, fallback to bell right
            const profileBtn = document.querySelector(
                '.user-profile, .dropdown-user .nav-link, ' +
                '.navbar-dropdown.dropdown-user a, ' +
                '.user-avatar, .avatar.avatar-online'
            );
            const anchorRight = profileBtn
                ? profileBtn.getBoundingClientRect().right
                : btnRect.right;
            const rightOffset = Math.max(margin, window.innerWidth - anchorRight);

            menu.setAttribute('style',
                `position:fixed!important;` +
                `top:${btnRect.bottom + 6}px!important;` +
                `left:${margin}px!important;` +
                `right:${rightOffset}px!important;` +
                `width:auto!important;` +
                `transform:none!important;` +
                `z-index:9999!important;`
            );

        } else if (bellCtx) {
            // ── DESKTOP/TABLET — LMS context ────────────────────────────────────
            // Popper.js struggles with position:sticky headers, so we take over.
            // Align right edge of dropdown with right edge of bell button.
            const dropW    = window.innerWidth <= 991 ? 320 : 360;
            const rightOff = Math.max(8, window.innerWidth - btnRect.right);

            menu.setAttribute('style',
                `position:fixed!important;` +
                `top:${btnRect.bottom + 4}px!important;` +
                `right:${rightOff}px!important;` +
                `left:auto!important;` +
                `width:${dropW}px!important;` +
                `transform:none!important;` +
                `z-index:9999!important;`
            );

        } else {
            // ── DESKTOP/TABLET — Sneat context ──────────────────────────────────
            // Bootstrap/Popper.js works fine here; just remove any leftover style.
            menu.removeAttribute('style');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Load unread count on page load — relative path, protocol-agnostic
        fetch('/notifications/unread-count')
            .then(r => { if (!r.ok) throw new Error(); return r.json(); })
            .then(data => updateNotificationBadge(data.count))
            .catch(() => {});

        const btn = document.getElementById('notificationDropdown');
        if (btn) {
            btn.addEventListener('show.bs.dropdown', function () {
                // Two rAFs: first lets Popper.js run, second lets us override it
                requestAnimationFrame(() => requestAnimationFrame(fixNotifDropdownPosition));
            });

            btn.addEventListener('hidden.bs.dropdown', function () {
                const menu = document.querySelector('.notif-dropdown-menu');
                if (menu) menu.removeAttribute('style');
            });
        }

        window.addEventListener('resize', function () {
            if (document.querySelector('.notif-dropdown-menu.show')) {
                fixNotifDropdownPosition();
            }
        });
    });

    setInterval(() => {
        fetch('/notifications/unread-count')
            .then(r => { if (!r.ok) throw new Error(); return r.json(); })
            .then(data => updateNotificationBadge(data.count))
            .catch(() => {});
    }, 30000);
</script>
