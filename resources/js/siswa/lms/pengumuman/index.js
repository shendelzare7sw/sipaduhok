document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.siswa-lms-pengumuman-index-page');

    if (!page) {
        return;
    }

    const indexUrl = page.dataset.indexUrl || window.location.href;
    const priorityTabs = Array.from(page.querySelectorAll('.priority-tab'));
    const announcementItems = Array.from(page.querySelectorAll('.announcement-item'));
    const searchInput = page.querySelector('#searchInput');
    const dateFromInput = page.querySelector('#dateFrom');
    const dateToInput = page.querySelector('#dateTo');
    const dateFilterButton = page.querySelector('[data-action="date-filter"]');
    const sortSelect = page.querySelector('#sortSelect');

    const priorityMap = {
        normal: 'biasa',
        high: 'penting',
        urgent: 'mendesak',
    };

    const getActivePriority = () => {
        return page.querySelector('.priority-tab.active')?.dataset.priority || 'all';
    };

    const matchesPriority = (item, priority) => {
        if (priority === 'all') {
            return true;
        }

        return item.dataset.priority === priorityMap[priority];
    };

    const matchesSearch = (item, keyword) => {
        if (!keyword) {
            return true;
        }

        const subject = item.querySelector('.item-subject')?.textContent.toLowerCase() || '';
        const snippet = item.querySelector('.item-snippet')?.textContent.toLowerCase() || '';

        return subject.includes(keyword) || snippet.includes(keyword);
    };

    const applyFilters = () => {
        const activePriority = getActivePriority();
        const keyword = searchInput?.value.trim().toLowerCase() || '';

        announcementItems.forEach((item) => {
            const isVisible = matchesPriority(item, activePriority) && matchesSearch(item, keyword);
            item.classList.toggle('is-hidden', !isVisible);
        });
    };

    const navigateWithParams = (params) => {
        const url = new URL(indexUrl, window.location.origin);

        Object.entries(params).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            }
        });

        window.location.href = url.toString();
    };

    priorityTabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            priorityTabs.forEach((item) => item.classList.remove('active'));
            tab.classList.add('active');
            applyFilters();
        });
    });

    searchInput?.addEventListener('input', applyFilters);

    dateFilterButton?.addEventListener('click', () => {
        const dateFrom = dateFromInput?.value;
        const dateTo = dateToInput?.value;

        if (dateFrom && dateTo) {
            navigateWithParams({ from: dateFrom, to: dateTo });
        }
    });

    sortSelect?.addEventListener('change', () => {
        navigateWithParams({ sort: sortSelect.value });
    });
});
