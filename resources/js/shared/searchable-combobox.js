export function initSearchableCombobox({
    wrapperId,
    inputId,
    listId,
    groups,
    emptyText = 'Tidak ditemukan. Anda tetap bisa mengetik sendiri.',
}) {
    const wrapper = document.getElementById(wrapperId);
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);

    if (!wrapper || !input || !list) {
        return;
    }

    const groupEntries = Object.entries(groups);
    let selectableItems = [];
    let activeIndex = -1;

    const closeList = () => {
        list.classList.remove('is-open');
        selectableItems = [];
        activeIndex = -1;
    };

    const openList = () => {
        list.classList.add('is-open');
    };

    const setActive = (index) => {
        selectableItems.forEach((el) => el.classList.remove('is-active'));
        activeIndex = index;
        const target = selectableItems[activeIndex];
        if (target) {
            target.classList.add('is-active');
            target.scrollIntoView({ block: 'nearest' });
        }
    };

    const selectOption = (value) => {
        input.value = value;
        closeList();
    };

    const renderList = (rawQuery) => {
        const term = rawQuery.trim().toLowerCase();
        list.innerHTML = '';
        selectableItems = [];

        groupEntries.forEach(([groupName, options]) => {
            const filtered = term
                ? options.filter((opt) => opt.toLowerCase().includes(term))
                : options;

            if (!filtered.length) {
                return;
            }

            const groupEl = document.createElement('li');
            groupEl.className = 'scb-group';
            groupEl.textContent = groupName;
            list.appendChild(groupEl);

            filtered.forEach((opt) => {
                const itemEl = document.createElement('li');
                itemEl.className = 'scb-item';
                itemEl.setAttribute('role', 'option');
                itemEl.textContent = opt;
                itemEl.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    selectOption(opt);
                });
                list.appendChild(itemEl);
                selectableItems.push(itemEl);
            });
        });

        const exactMatch = selectableItems.some(
            (el) => el.textContent.toLowerCase() === term
        );

        if (term && !exactMatch) {
            const customEl = document.createElement('li');
            customEl.className = 'scb-item scb-custom';
            customEl.setAttribute('role', 'option');

            const icon = document.createElement('i');
            icon.className = 'fas fa-plus';
            customEl.appendChild(icon);
            customEl.appendChild(document.createTextNode(` Gunakan "${rawQuery.trim()}"`));

            customEl.addEventListener('mousedown', (event) => {
                event.preventDefault();
                selectOption(rawQuery.trim());
            });
            list.appendChild(customEl);
            selectableItems.push(customEl);
        }

        if (!selectableItems.length) {
            const emptyEl = document.createElement('li');
            emptyEl.className = 'scb-empty';
            emptyEl.textContent = emptyText;
            list.appendChild(emptyEl);
        }

        openList();
    };

    input.addEventListener('focus', () => renderList(input.value));
    input.addEventListener('input', () => renderList(input.value));

    input.addEventListener('keydown', (event) => {
        if (!list.classList.contains('is-open')) {
            if (event.key === 'ArrowDown') {
                renderList(input.value);
            }
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (selectableItems.length) {
                setActive((activeIndex + 1) % selectableItems.length);
            }
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (selectableItems.length) {
                setActive((activeIndex - 1 + selectableItems.length) % selectableItems.length);
            }
        } else if (event.key === 'Enter') {
            if (activeIndex >= 0 && selectableItems[activeIndex]) {
                event.preventDefault();
                selectableItems[activeIndex].dispatchEvent(new Event('mousedown'));
            } else {
                closeList();
            }
        } else if (event.key === 'Escape') {
            closeList();
        }
    });

    document.addEventListener('click', (event) => {
        if (!wrapper.contains(event.target)) {
            closeList();
        }
    });
}
