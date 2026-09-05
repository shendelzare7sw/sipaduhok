import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import './legacy-content.js';

window.Swal = Swal;
window.Alpine = Alpine;
window.CleanFlow = window.CleanFlow || {};
window.CleanFlow.confirmAction = (options = {}) => Swal.fire({
    icon: options.icon || 'warning',
    title: options.title || 'Konfirmasi tindakan',
    text: options.text || 'Pastikan data yang dipilih sudah benar.',
    showCancelButton: true,
    confirmButtonText: options.confirmText || 'Ya, lanjutkan',
    cancelButtonText: options.cancelText || 'Batal',
    confirmButtonColor: options.danger === false ? '#285dcc' : '#dc2626',
    reverseButtons: true,
    focusCancel: true,
});

window.CleanFlow.copyText = async (value) => {
    if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(value);
        return;
    }

    const input = document.createElement('textarea');
    input.value = value;
    input.setAttribute('readonly', '');
    input.style.position = 'fixed';
    input.style.opacity = '0';
    document.body.appendChild(input);
    input.select();
    const copied = document.execCommand('copy');
    input.remove();
    if (!copied) throw new Error('Browser menolak akses clipboard.');
};

const normalize = (value) => (value || '')
    .toLocaleLowerCase('id-ID')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');

Alpine.data('codeSuggestions', (endpoint) => ({
    open: false,
    loading: false,
    message: '',
    suggestions: [],
    requestController: null,

    async load() {
        const level = this.$refs.level?.value || '';
        this.open = true;
        this.suggestions = [];

        if (!level || !endpoint) {
            this.message = 'Pilih jenjang terlebih dahulu.';
            return;
        }

        this.requestController?.abort();
        this.requestController = new AbortController();
        this.loading = true;
        this.message = 'Mencari kode yang tersedia…';

        try {
            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('jenjang', level);
            const response = await fetch(url, { signal: this.requestController.signal });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();
            this.suggestions = Array.isArray(data.suggestions) ? data.suggestions : [];
            this.message = this.suggestions.length
                ? 'Pilih salah satu kode:'
                : 'Tidak ada saran. Masukkan kode secara manual.';
        } catch (error) {
            if (error.name !== 'AbortError') this.message = 'Saran gagal dimuat. Masukkan kode secara manual.';
        } finally {
            this.loading = false;
        }
    },

    select(code) {
        if (this.$refs.code) this.$refs.code.value = code;
        this.open = false;
        this.$refs.code?.focus();
    },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('aiChatbotWindow')) {
        import('./components/ai-chatbot.js');
    }

    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');

    const closeSidebar = () => {
        sidebar?.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    const openSidebar = () => {
        sidebar?.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    document.querySelectorAll('[data-sidebar-open]').forEach((button) => button.addEventListener('click', openSidebar));
    document.querySelectorAll('[data-sidebar-close]').forEach((button) => button.addEventListener('click', closeSidebar));
    overlay?.addEventListener('click', closeSidebar);

    document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.getElementById(button.getAttribute('aria-controls'));
            const expanded = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', String(!expanded));
            button.closest('.menu-item')?.classList.toggle('open', !expanded);
            target?.classList.toggle('hidden', expanded);
            button.querySelector('[data-menu-chevron]')?.classList.toggle('rotate-180', !expanded);
        });
    });

    document.querySelectorAll('.cleanflow-nav .menu-toggle:not([data-menu-toggle])').forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            toggle.closest('.menu-item')?.classList.toggle('open');
        });
    });

    document.querySelectorAll('[data-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => field.form?.requestSubmit());
    });

    document.querySelectorAll('[data-print-page]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });
    if (document.querySelector('[data-auto-print]')) {
        window.setTimeout(() => window.print(), 250);
    }

    document.querySelectorAll('[data-bulk-select-all]').forEach((selectAll) => {
        const formId = selectAll.dataset.bulkSelectAll;
        const items = [...document.querySelectorAll(`[data-bulk-item][form="${formId}"]`)];
        const submit = document.querySelector(`[data-bulk-submit="${formId}"]`);
        const sync = () => {
            const available = items.filter((item) => !item.disabled);
            const selected = available.filter((item) => item.checked).length;
            selectAll.checked = available.length > 0 && selected === available.length;
            selectAll.indeterminate = selected > 0 && selected < available.length;
            if (submit) {
                submit.disabled = selected === 0;
                submit.querySelector('[data-bulk-count]')?.replaceChildren(String(selected));
            }
        };

        selectAll.addEventListener('change', () => {
            items.filter((item) => !item.disabled).forEach((item) => { item.checked = selectAll.checked; });
            sync();
        });
        items.forEach((item) => item.addEventListener('change', sync));
        const updateAvailability = () => {
            items.forEach((item) => {
                item.disabled = item.getClientRects().length === 0;
                if (item.disabled) item.checked = false;
            });
            sync();
        };
        window.addEventListener('resize', updateAvailability, { passive: true });
        updateAvailability();
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            const icon = button.querySelector('i');
            if (!(input instanceof HTMLInputElement) || !icon) return;

            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', show);
            icon.classList.toggle('fa-eye-slash', !show);
        });
    });

    document.querySelectorAll('[data-role-cabang-form]').forEach((form) => {
        const roleSelect = form.querySelector('[data-role-select]');
        const cabangSelect = form.querySelector('[data-cabang-select]');
        const cabangHidden = form.querySelector('[data-cabang-hidden]');
        const cabangInfo = form.querySelector('[data-cabang-info]');
        const flexibleRoles = (form.dataset.flexibleRoles || '').split(',').filter(Boolean);
        const defaultCabangId = form.dataset.defaultCabangId || '';

        const syncCabang = () => {
            if (!roleSelect || !cabangSelect || !cabangHidden) return;
            const isFlexible = flexibleRoles.includes(roleSelect.value);

            cabangSelect.disabled = isFlexible;
            cabangSelect.toggleAttribute('required', !isFlexible);
            cabangSelect.name = isFlexible ? '' : 'cabang_id';
            cabangHidden.disabled = !isFlexible;
            cabangHidden.name = isFlexible ? 'cabang_id' : '';
            cabangHidden.value = isFlexible ? defaultCabangId : '';
            if (isFlexible) cabangSelect.value = defaultCabangId;
            cabangInfo?.classList.toggle('hidden', !isFlexible);
        };

        roleSelect?.addEventListener('change', syncCabang);
        syncCabang();
    });

    document.querySelectorAll('[data-filter-root]').forEach((root) => {
        const controls = [...root.querySelectorAll('[data-filter-key]')];
        const items = [...root.querySelectorAll('[data-filter-item]')];
        const count = root.querySelector('[data-filter-count]');

        const applyFilters = () => {
            let visible = 0;
            items.forEach((item) => {
                const matches = controls.every((control) => {
                    const query = normalize(control.value.trim());
                    if (!query) return true;
                    const value = normalize(item.dataset[control.dataset.filterKey] || '');
                    return control.dataset.filterMode === 'contains'
                        ? value.includes(query)
                        : value === query;
                });
                item.classList.toggle('hidden', !matches);
                if (matches) visible += 1;
            });
            if (count) count.textContent = String(visible);
        };

        controls.forEach((control) => {
            control.addEventListener(control instanceof HTMLSelectElement ? 'change' : 'input', applyFilters);
        });
        root.querySelectorAll('[data-filter-reset]').forEach((button) => {
            button.addEventListener('click', () => {
                controls.forEach((control) => { control.value = ''; });
                applyFilters();
            });
        });
        applyFilters();
    });

    document.querySelectorAll('[data-wali-student-link-form]').forEach((form) => {
        const students = [...form.querySelectorAll('[data-student-link-checkbox]')];
        const relationship = form.querySelector('[data-student-relationship]');
        const otherWrapper = form.querySelector('[data-other-relationship]');
        const otherInput = otherWrapper?.querySelector('input');
        const selectedCount = form.querySelector('[data-selected-student-count]');

        const syncRelationship = () => {
            const total = students.filter((student) => student.checked).length;
            const usesOther = relationship?.value === 'lainnya';
            if (relationship) relationship.required = total > 0;
            otherWrapper?.classList.toggle('hidden', !usesOther);
            if (otherInput) otherInput.required = usesOther;
            if (selectedCount) selectedCount.textContent = String(total);
        };

        students.forEach((student) => student.addEventListener('change', syncRelationship));
        relationship?.addEventListener('change', syncRelationship);
        form.addEventListener('submit', (event) => {
            const hasStudents = students.some((student) => student.checked);
            if (relationship?.value && !hasStudents) {
                event.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Pilih siswa terlebih dahulu',
                    text: 'Hubungan keluarga hanya dapat disimpan jika minimal satu siswa dipilih.',
                    confirmButtonColor: '#285dcc',
                });
            }
        });
        syncRelationship();
    });

    document.querySelectorAll('[data-relationship-select]').forEach((select) => {
        const wrapper = document.getElementById(select.dataset.otherTarget);
        const input = wrapper?.querySelector('input');
        const syncOtherRelationship = () => {
            const visible = select.value === 'lainnya';
            wrapper?.classList.toggle('hidden', !visible);
            if (input) input.required = visible;
        };
        select.addEventListener('change', syncOtherRelationship);
        syncOtherRelationship();
    });

    document.querySelectorAll('[data-conditional-select]').forEach((select) => {
        const root = select.closest('[data-conditional-root]') || select.parentElement;
        const panels = [...(root?.querySelectorAll('[data-conditional-panel]') || [])];

        const syncConditionalPanels = () => {
            panels.forEach((panel) => {
                const active = panel.dataset.conditionalPanel === select.value;
                panel.classList.toggle('hidden', !active);
                panel.querySelectorAll('input, select, textarea').forEach((field) => {
                    field.disabled = !active;
                });
                panel.querySelectorAll('[data-required-when-active]').forEach((field) => {
                    field.required = active;
                });
            });
        };

        select.addEventListener('change', syncConditionalPanels);
        syncConditionalPanels();
    });

    document.querySelectorAll('[data-catatan-form]').forEach((form) => {
        const choices = [...form.querySelectorAll('[data-recipient-choice]')];
        const panels = [...form.querySelectorAll('[data-recipient-panel]')];
        const roleSelect = form.querySelector('[data-recipient-role]');
        const recipients = [...form.querySelectorAll('[data-recipient-checkbox]')];
        const selectedCount = form.querySelector('[data-recipient-count]');

        const updateRecipientCount = () => {
            if (selectedCount) selectedCount.textContent = String(recipients.filter((item) => item.checked).length);
        };
        const syncRecipientMode = () => {
            const mode = choices.find((choice) => choice.checked)?.value;
            panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.recipientPanel !== mode));
            if (roleSelect) roleSelect.required = mode === 'role';
        };

        choices.forEach((choice) => choice.addEventListener('change', syncRecipientMode));
        recipients.forEach((recipient) => recipient.addEventListener('change', updateRecipientCount));
        form.querySelector('[data-select-visible-recipients]')?.addEventListener('click', () => {
            form.querySelectorAll('[data-recipient-item]:not(.hidden) [data-recipient-checkbox]').forEach((item) => { item.checked = true; });
            updateRecipientCount();
        });
        form.querySelector('[data-clear-recipients]')?.addEventListener('click', () => {
            recipients.forEach((item) => { item.checked = false; });
            updateRecipientCount();
        });
        form.addEventListener('submit', (event) => {
            const mode = choices.find((choice) => choice.checked)?.value;
            if (mode === 'individu' && !recipients.some((item) => item.checked)) {
                event.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Penerima belum dipilih',
                    text: 'Pilih minimal satu penerima individu sebelum mengirim catatan.',
                    confirmButtonColor: '#285dcc',
                });
            }
        });

        syncRecipientMode();
        updateRecipientCount();
    });

    document.querySelectorAll('[data-auto-dismiss]').forEach((element) => {
        const delay = Number(element.dataset.autoDismiss) || 5000;
        window.setTimeout(() => {
            element.classList.add('pointer-events-none', 'opacity-0');
            window.setTimeout(() => element.remove(), 500);
        }, delay);
    });

    const scrollToTop = document.querySelector('[data-scroll-to-top]');
    const getScrollTop = () => Math.max(
        window.scrollY || 0,
        document.documentElement.scrollTop || 0,
        document.body.scrollTop || 0,
    );
    const updateScrollToTop = () => {
        const visible = getScrollTop() > 120;
        scrollToTop?.classList.toggle('hidden', !visible);
        scrollToTop?.classList.toggle('flex', visible);
    };

    window.addEventListener('scroll', updateScrollToTop, { passive: true });
    document.addEventListener('scroll', updateScrollToTop, { passive: true, capture: true });
    scrollToTop?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.scrollingElement?.scrollTo({ top: 0, behavior: 'smooth' });
    });
    updateScrollToTop();

    document.addEventListener('click', (event) => {
        const openButton = event.target.closest('[data-dialog-open]');
        if (openButton) {
            const dialog = document.getElementById(openButton.dataset.dialogOpen);
            if (dialog instanceof HTMLDialogElement) {
                const iframe = dialog.querySelector('iframe[data-src]');
                if (iframe && !iframe.getAttribute('src')) iframe.src = iframe.dataset.src;
                dialog.showModal();
            }
            return;
        }

        const closeButton = event.target.closest('[data-dialog-close]');
        if (closeButton) closeButton.closest('dialog')?.close();
    });

    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) dialog.close();
        });
    });

    const searchPanel = document.getElementById('admin-search-panel');
    const searchBackdrop = document.getElementById('admin-search-backdrop');
    const searchInput = document.getElementById('admin-menu-search');
    const emptyState = document.getElementById('admin-search-empty');
    const searchResults = document.getElementById('admin-search-results');

    if (searchResults) {
        const seenLinks = new Set();
        document.querySelectorAll('.cleanflow-nav a.menu-link[href]').forEach((link) => {
            const href = link.href;
            const label = link.textContent.replace(/\s+/g, ' ').trim();
            if (!label || !href || href.endsWith('#') || seenLinks.has(href)) return;

            seenLinks.add(href);
            const item = document.createElement('a');
            item.href = href;
            item.dataset.searchItem = '';
            item.dataset.searchText = label;
            item.className = 'flex items-center gap-3 rounded-xl px-3 py-3 no-underline hover:bg-brand-50';

            const icon = document.createElement('span');
            icon.className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600';
            const sourceIcon = link.querySelector('i');
            icon.innerHTML = sourceIcon
                ? `<i class="${sourceIcon.className.replace(/[^a-zA-Z0-9_\- ]/g, '')}"></i>`
                : '<i class="fa-solid fa-arrow-right"></i>';

            const text = document.createElement('span');
            text.className = 'min-w-0 flex-1';
            const title = document.createElement('span');
            title.className = 'block truncate text-sm font-semibold text-slate-900';
            title.textContent = label;
            text.appendChild(title);

            const arrow = document.createElement('i');
            arrow.className = 'fa-solid fa-arrow-right text-xs text-slate-300';
            item.append(icon, text, arrow);
            searchResults.appendChild(item);
        });
    }

    const searchItems = [...document.querySelectorAll('[data-search-item]')];

    const openSearch = () => {
        searchPanel?.classList.remove('hidden');
        searchBackdrop?.classList.remove('hidden');
        requestAnimationFrame(() => searchInput?.focus());
    };

    const closeSearch = () => {
        searchPanel?.classList.add('hidden');
        searchBackdrop?.classList.add('hidden');
        if (searchInput) searchInput.value = '';
        searchItems.forEach((item) => item.classList.remove('hidden'));
        emptyState?.classList.add('hidden');
    };

    const filterSearch = () => {
        const query = normalize(searchInput?.value.trim());
        let visible = 0;

        searchItems.forEach((item) => {
            const matches = !query || normalize(item.dataset.searchText).includes(query);
            item.classList.toggle('hidden', !matches);
            if (matches) visible += 1;
        });

        emptyState?.classList.toggle('hidden', visible !== 0);
    };

    document.querySelectorAll('[data-search-open]').forEach((button) => button.addEventListener('click', openSearch));
    document.querySelectorAll('[data-search-close]').forEach((button) => button.addEventListener('click', closeSearch));
    searchBackdrop?.addEventListener('click', closeSearch);
    searchInput?.addEventListener('input', filterSearch);

    document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            openSearch();
        }

        if (event.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) {
            event.preventDefault();
            openSearch();
        }

        if (event.key === 'Escape') {
            closeSearch();
            closeSidebar();
        }
    });

    const notificationRoot = document.getElementById('admin-notification-dropdown');
    const notificationToggle = notificationRoot?.querySelector('[data-notification-toggle]');
    const notificationPanel = document.getElementById('admin-notification-panel');
    const notificationList = document.getElementById('admin-notification-list');
    const notificationBadge = document.getElementById('admin-notification-count');
    let notificationsLoadedAt = 0;

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const safeIcon = (value) => {
        const classes = String(value || 'fas fa-bell').replace(/[^a-zA-Z0-9_\- ]/g, '').trim();
        return classes || 'fas fa-bell';
    };

    const notificationTone = (color) => ({
        primary: 'bg-blue-50 text-blue-600',
        success: 'bg-emerald-50 text-emerald-600',
        warning: 'bg-amber-50 text-amber-600',
        danger: 'bg-red-50 text-red-600',
        info: 'bg-cyan-50 text-cyan-600',
        secondary: 'bg-slate-100 text-slate-600',
    }[color] || 'bg-slate-100 text-slate-600');

    const updateNotificationBadge = (count) => {
        if (!notificationBadge) return;
        const total = Number(count) || 0;
        notificationBadge.textContent = total > 99 ? '99+' : String(total);
        notificationBadge.classList.toggle('hidden', total === 0);
        notificationBadge.classList.toggle('flex', total > 0);
    };

    const renderNotifications = (notifications) => {
        if (!notificationList) return;

        if (!notifications.length) {
            notificationList.innerHTML = `
                <div class="px-4 py-10 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-regular fa-bell-slash"></i></span>
                    <p class="mt-3 text-sm font-bold text-slate-700">Belum ada notifikasi</p>
                    <p class="mt-1 text-xs text-slate-500">Pembaruan baru akan tampil di sini.</p>
                </div>`;
            return;
        }

        notificationList.innerHTML = notifications.map((notification) => `
            <button type="button" class="flex w-full items-start gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-brand-50 ${notification.read_at ? '' : 'bg-blue-50/50'}"
                data-admin-notification
                data-id="${escapeHtml(notification.id)}"
                data-link="${escapeHtml(notification.link || '/notifications')}"
                data-type="${escapeHtml(notification.tipe || '')}">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ${notificationTone(notification.color)}"><i class="${safeIcon(notification.icon)}"></i></span>
                <span class="min-w-0 flex-1">
                    <span class="flex items-start gap-2">
                        <strong class="min-w-0 flex-1 truncate text-xs text-slate-900 sm:text-sm">${escapeHtml(notification.judul)}</strong>
                        ${notification.read_at ? '' : '<span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-brand-500"></span>'}
                    </span>
                    <span class="mt-1 block line-clamp-2 text-xs leading-5 text-slate-500">${escapeHtml(notification.pesan)}</span>
                    <span class="mt-1.5 block text-[10px] font-medium text-slate-400">${escapeHtml(notification.created_at_formatted || '')}</span>
                </span>
            </button>`).join('');
    };

    const fetchUnreadNotifications = () => {
        if (!notificationRoot) return;
        fetch(notificationRoot.dataset.unreadUrl, { headers: { Accept: 'application/json' } })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((data) => updateNotificationBadge(data.count))
            .catch(() => {});
    };

    const loadNotifications = () => {
        if (!notificationRoot || !notificationList) return;
        if (Date.now() - notificationsLoadedAt < 15000) return;

        notificationList.innerHTML = '<div class="flex items-center justify-center gap-2 px-4 py-10 text-xs text-slate-500"><i class="fa-solid fa-circle-notch animate-spin text-brand-500"></i> Memuat notifikasi...</div>';
        fetch(notificationRoot.dataset.recentUrl, { headers: { Accept: 'application/json' } })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((data) => {
                notificationsLoadedAt = Date.now();
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications || []);
            })
            .catch(() => {
                notificationList.innerHTML = '<div class="px-4 py-10 text-center text-xs text-slate-500"><i class="fa-solid fa-circle-exclamation mb-2 block text-lg text-red-400"></i>Notifikasi gagal dimuat.</div>';
            });
    };

    const closeNotifications = () => {
        notificationPanel?.classList.add('hidden');
        notificationToggle?.setAttribute('aria-expanded', 'false');
    };

    notificationToggle?.addEventListener('click', () => {
        const willOpen = notificationPanel?.classList.contains('hidden');
        notificationPanel?.classList.toggle('hidden', !willOpen);
        notificationToggle.setAttribute('aria-expanded', String(willOpen));
        if (willOpen) {
            document.querySelector('details[open]')?.removeAttribute('open');
            loadNotifications();
        }
    });

    notificationList?.addEventListener('click', (event) => {
        const item = event.target.closest('[data-admin-notification]');
        if (!item || !notificationRoot) return;

        let target = item.dataset.type === 'catatan'
            ? `/notifications/${encodeURIComponent(item.dataset.id)}`
            : (item.dataset.link || '/notifications');
        const context = notificationRoot.dataset.context;
        if (context && /^\/notifications\/?(?:\?.*)?$/.test(target)) {
            target += `${target.includes('?') ? '&' : '?'}ctx=${encodeURIComponent(context)}`;
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch(notificationRoot.dataset.readUrlTemplate.replace('__ID__', encodeURIComponent(item.dataset.id)), {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
        }).finally(() => {
            window.location.href = target;
        });
    });

    document.addEventListener('click', (event) => {
        if (notificationRoot && !notificationRoot.contains(event.target)) closeNotifications();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeNotifications();
    });

    fetchUnreadNotifications();
    if (notificationRoot) window.setInterval(fetchUnreadNotifications, 30000);

    document.querySelectorAll('[data-flash]').forEach((flash) => {
        Swal.fire({
            icon: flash.dataset.flash,
            title: flash.dataset.title,
            text: flash.dataset.message,
            timer: flash.dataset.flash === 'success' ? 2600 : undefined,
            showConfirmButton: flash.dataset.flash !== 'success',
            confirmButtonColor: '#285dcc',
        });
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.confirmed === 'true') return;

        const method = form.querySelector('input[name="_method"]')?.value?.toUpperCase();
        const needsConfirmation = form.hasAttribute('data-confirm') || method === 'DELETE';
        if (!needsConfirmation) return;

        event.preventDefault();
        const isLogout = form.dataset.confirm === 'logout';
        const result = await Swal.fire({
            icon: 'warning',
            title: form.dataset.confirmTitle || (isLogout ? 'Keluar dari SIPADUHOK?' : 'Konfirmasi tindakan'),
            text: form.dataset.confirmMessage || (isLogout
                ? 'Anda perlu masuk kembali untuk mengakses dashboard.'
                : 'Tindakan ini tidak dapat dibatalkan.'),
            showCancelButton: true,
            confirmButtonText: form.dataset.confirmText || (isLogout ? 'Ya, keluar' : 'Ya, lanjutkan'),
            cancelButtonText: 'Batal',
            confirmButtonColor: isLogout ? '#dc2626' : '#285dcc',
            reverseButtons: true,
            focusCancel: true,
        });

        if (result.isConfirmed) {
            form.dataset.confirmed = 'true';
            form.requestSubmit();
        }
    }, true);
});
