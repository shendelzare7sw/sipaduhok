(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const root = document.getElementById('notification-dropdown');

        if (!root) {
            return;
        }

        const trigger = document.getElementById('notificationDropdown');
        const badge = document.getElementById('notification-count');
        const list = document.getElementById('notification-list');
        const menu = root.querySelector('.notif-dropdown-menu');
        const context = root.dataset.notificationContext || '';
        const recentUrl = root.dataset.notificationRecentUrl || '/notifications/recent';
        const unreadUrl = root.dataset.notificationUnreadUrl || '/notifications/unread-count';
        const readUrlTemplate = root.dataset.notificationReadUrlTemplate || '/notifications/__ID__/read';
        let notificationsLoaded = false;

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function sanitizeClassList(value, fallback) {
            const classList = String(value || fallback).replace(/[^a-zA-Z0-9_\- ]/g, '').trim();

            return classList || fallback;
        }

        function updateNotificationBadge(count) {
            if (!badge) {
                return;
            }

            const total = Number(count) || 0;

            if (total > 0) {
                badge.textContent = total > 99 ? '99+' : total;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        function renderEmptyNotifications() {
            list.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                    <p class="small mb-0">Tidak ada notifikasi</p>
                </div>`;
        }

        function renderLoadError() {
            list.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-exclamation-circle fa-lg mb-2 d-block"></i>
                    <p class="small mb-0">Gagal memuat notifikasi</p>
                </div>`;
        }

        function renderNotifications(notifications) {
            if (!list) {
                return;
            }

            if (!notifications.length) {
                renderEmptyNotifications();
                return;
            }

            list.innerHTML = notifications.map((notif) => {
                const unreadClass = notif.read_at ? '' : 'unread';
                const colorClass = sanitizeClassList(notif.color, 'secondary');
                const iconClass = sanitizeClassList(notif.icon, 'fas fa-bell');
                const notifLink = notif.link || '/notifications';
                const notifType = notif.tipe || '';
                const newBadge = !notif.read_at
                    ? '<span class="badge bg-primary rounded-pill ms-2 flex-shrink-0 notification-new-badge">Baru</span>'
                    : '';

                return `
                    <div class="notification-item d-flex align-items-start ${unreadClass}"
                        data-notification-item
                        data-notification-id="${escapeHtml(notif.id)}"
                        data-notification-link="${escapeHtml(notifLink)}"
                        data-notification-type="${escapeHtml(notifType)}">
                        <div class="notification-icon bg-${colorClass} text-white me-3">
                            <i class="${iconClass}"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="mb-1 small fw-bold text-truncate">${escapeHtml(notif.judul)}</h6>
                            <p class="mb-1 small text-muted notification-message">${escapeHtml(notif.pesan)}</p>
                            <small class="text-muted">${escapeHtml(notif.created_at_formatted || '')}</small>
                        </div>
                        ${newBadge}
                    </div>`;
            }).join('');
        }

        function fetchUnreadCount() {
            fetch(unreadUrl)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }

                    return response.json();
                })
                .then((data) => updateNotificationBadge(data.count))
                .catch(() => {});
        }

        function loadNotifications() {
            if (notificationsLoaded || !list) {
                return;
            }

            fetch(recentUrl)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }

                    return response.json();
                })
                .then((data) => {
                    notificationsLoaded = true;
                    updateNotificationBadge(data.unread_count);
                    renderNotifications(data.notifications || []);
                })
                .catch(() => {
                    renderLoadError();
                    notificationsLoaded = false;
                });
        }

        function buildContextualTarget(link) {
            let target = link || '/notifications';

            if (context && target.match(/\/notifications\/?(\?.*)?$/)) {
                target += (target.includes('?') ? '&' : '?') + 'ctx=' + encodeURIComponent(context);
            }

            return target;
        }

        function handleNotificationClick(id, link, type) {
            if (type === 'catatan') {
                window.location.href = '/notifications/' + encodeURIComponent(id)
                    + (context ? '?ctx=' + encodeURIComponent(context) : '');
                return;
            }

            const target = buildContextualTarget(link);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const headers = { Accept: 'application/json' };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            fetch(readUrlTemplate.replace('__ID__', encodeURIComponent(id)), {
                method: 'POST',
                headers,
            }).finally(() => {
                window.location.href = target;
            });
        }

        function fixNotifDropdownPosition() {
            if (!menu || !trigger) {
                return;
            }

            const btnRect = trigger.getBoundingClientRect();
            const isMobile = window.innerWidth <= 575;

            if (isMobile) {
                const margin = 16;
                const profileBtn = document.querySelector(
                    '.user-profile, .dropdown-user .nav-link, '
                    + '.navbar-dropdown.dropdown-user a, '
                    + '.user-avatar, .avatar.avatar-online'
                );
                const anchorRight = profileBtn
                    ? profileBtn.getBoundingClientRect().right
                    : btnRect.right;
                const rightOffset = Math.max(margin, window.innerWidth - anchorRight);

                menu.setAttribute('style',
                    'position:fixed!important;'
                    + `top:${btnRect.bottom + 6}px!important;`
                    + `left:${margin}px!important;`
                    + `right:${rightOffset}px!important;`
                    + 'width:auto!important;'
                    + 'transform:none!important;'
                    + 'z-index:9999!important;'
                );
            } else if (context) {
                const dropdownWidth = window.innerWidth <= 991 ? 320 : 360;
                const rightOffset = Math.max(8, window.innerWidth - btnRect.right);

                menu.setAttribute('style',
                    'position:fixed!important;'
                    + `top:${btnRect.bottom + 4}px!important;`
                    + `right:${rightOffset}px!important;`
                    + 'left:auto!important;'
                    + `width:${dropdownWidth}px!important;`
                    + 'transform:none!important;'
                    + 'z-index:9999!important;'
                );
            } else {
                menu.removeAttribute('style');
            }
        }

        fetchUnreadCount();
        setInterval(fetchUnreadCount, 30000);

        if (trigger) {
            trigger.addEventListener('click', loadNotifications);
            trigger.addEventListener('show.bs.dropdown', () => {
                requestAnimationFrame(() => requestAnimationFrame(fixNotifDropdownPosition));
            });
            trigger.addEventListener('hidden.bs.dropdown', () => {
                if (menu) {
                    menu.removeAttribute('style');
                }
            });
        }

        if (list) {
            list.addEventListener('click', (event) => {
                const item = event.target.closest('[data-notification-item]');

                if (!item) {
                    return;
                }

                handleNotificationClick(
                    item.dataset.notificationId,
                    item.dataset.notificationLink,
                    item.dataset.notificationType
                );
            });
        }

        window.addEventListener('resize', () => {
            if (menu?.classList.contains('show')) {
                fixNotifDropdownPosition();
            }
        });
    });
})();
