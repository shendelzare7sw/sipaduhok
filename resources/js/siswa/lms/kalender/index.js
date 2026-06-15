document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.siswa-lms-kalender-index-page');

    if (!page) {
        return;
    }

    const initResizer = () => {
        const resizer = page.querySelector('#dragMe');

        if (!resizer || resizer.dataset.initialized === 'true') {
            return;
        }

        const leftSide = resizer.previousElementSibling;
        const rightSide = resizer.nextElementSibling;

        if (!leftSide || !rightSide) {
            return;
        }

        let x = 0;
        let rightWidth = 0;

        const mouseMoveHandler = (event) => {
            const dx = event.clientX - x;
            const newRightWidth = rightWidth - dx;

            if (newRightWidth > 150 && newRightWidth < 600) {
                rightSide.style.width = `${newRightWidth}px`;
            }
        };

        const mouseUpHandler = () => {
            document.removeEventListener('mousemove', mouseMoveHandler);
            document.removeEventListener('mouseup', mouseUpHandler);
            resizer.classList.remove('resizing');
            document.body.style.removeProperty('cursor');
            document.body.style.removeProperty('user-select');
        };

        resizer.addEventListener('mousedown', (event) => {
            x = event.clientX;
            rightWidth = rightSide.getBoundingClientRect().width;

            document.addEventListener('mousemove', mouseMoveHandler);
            document.addEventListener('mouseup', mouseUpHandler);
            resizer.classList.add('resizing');

            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';
        });

        resizer.dataset.initialized = 'true';
    };

    const applySearch = () => {
        const searchInput = page.querySelector('#searchEvent');
        const searchTerm = searchInput?.value.toLowerCase() ?? '';
        const events = page.querySelectorAll('.event');
        const cells = page.querySelectorAll('.calendar-table td');

        cells.forEach((cell) => {
            cell.classList.remove('calendar-search-highlight');
        });

        events.forEach((event) => {
            const text = event.getAttribute('data-event') || event.textContent.toLowerCase();
            const cell = event.closest('td');
            const matches = searchTerm === '' || text.includes(searchTerm);

            event.classList.toggle('is-hidden', !matches);

            if (searchTerm !== '' && matches && cell && !cell.classList.contains('today')) {
                cell.classList.add('calendar-search-highlight');
            }
        });
    };

    const initSearch = () => {
        const searchInput = page.querySelector('#searchEvent');

        if (!searchInput || searchInput.dataset.initialized === 'true') {
            return;
        }

        searchInput.addEventListener('input', applySearch);
        searchInput.dataset.initialized = 'true';
    };

    page.addEventListener('click', (event) => {
        const detailTarget = event.target.closest('[data-detail-url]');

        if (!detailTarget || !page.contains(detailTarget)) {
            return;
        }

        window.location.href = detailTarget.dataset.detailUrl;
    });

    page.addEventListener('click', (event) => {
        const link = event.target.closest('.btn-nav');

        if (!link || !link.href || !link.closest('.calendar-container')) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        fetch(link.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                return response.json();
            })
            .then((data) => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data.html, 'text/html');
                const newCalendarSection = doc.querySelector('.calendar-section');
                const newLegend = doc.querySelector('.legend');
                const currentSection = page.querySelector('.calendar-section');
                const currentLegend = page.querySelector('.legend');

                if (newCalendarSection && currentSection) {
                    currentSection.outerHTML = newCalendarSection.outerHTML;
                }

                if (newLegend && currentLegend) {
                    currentLegend.outerHTML = newLegend.outerHTML;
                }

                initResizer();
                applySearch();
                window.history.pushState({ path: link.href }, '', link.href);
            })
            .catch((error) => {
                console.error('Error loading calendar:', error);
                window.location.href = link.href;
            });
    });

    initResizer();
    initSearch();
    applySearch();
});
