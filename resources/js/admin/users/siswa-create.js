const hiddenClass = 'd-none';

const get = (id) => document.getElementById(id);

function setVisible(element, visible) {
    if (!element) return;
    element.classList.toggle(hiddenClass, !visible);
}

function preventEnterSubmit(input) {
    if (!input) return;

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });
}

function updateTempSelection() {
    const selectedCount = get('selectedCount');
    if (!selectedCount) return;

    selectedCount.textContent = document.querySelectorAll('.kelas-checkbox:checked').length;
}

function createKelasChip(item) {
    const chip = document.createElement('div');
    chip.className = 'selected-kelas-chip badge bg-primary d-flex align-items-center p-2';

    const icon = document.createElement('i');
    icon.className = 'fas fa-school me-2';

    const name = document.createElement('span');
    name.className = 'selected-kelas-chip-text';
    name.textContent = item.name;

    const jenjang = document.createElement('span');
    jenjang.className = 'selected-kelas-chip-level ms-2 badge bg-white text-primary';
    jenjang.textContent = item.jenjang;

    chip.append(icon, name, jenjang);
    return chip;
}

function updateSelectedKelasUI(items) {
    const textPlaceholder = get('selectedKelasText');
    const chipsContainer = get('selectedKelasChips');

    if (!textPlaceholder || !chipsContainer) return;

    setVisible(textPlaceholder, items.length === 0);
    setVisible(chipsContainer, items.length > 0);
    chipsContainer.replaceChildren();

    items.forEach((item) => chipsContainer.appendChild(createKelasChip(item)));
}

function openKelasModal() {
    const kelasSelect = get('kelasSelect');
    const modalElement = get('kelasModal');
    if (!kelasSelect || !modalElement || !window.bootstrap) return;

    document.querySelectorAll('.kelas-checkbox').forEach((checkbox) => {
        checkbox.checked = checkbox.value === kelasSelect.value && kelasSelect.value !== '';
    });

    updateTempSelection();
    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
}

function filterKelasList() {
    const cabangSelect = get('cabangSelect');
    const cabangFilter = get('filterCabang')?.value || cabangSelect?.value || '';
    const jenjangFilter = get('filterJenjang')?.value || '';
    const searchText = (get('searchKelas')?.value || '').toLowerCase();

    document.querySelectorAll('.kelas-item').forEach((item) => {
        const itemCabang = item.dataset.cabangId;
        const itemJenjang = item.dataset.jenjang;
        const itemName = item.dataset.name || '';

        const visible = (!cabangFilter || itemCabang === cabangFilter)
            && (!jenjangFilter || itemJenjang === jenjangFilter)
            && (!searchText || itemName.toLowerCase().includes(searchText));

        item.classList.toggle('is-hidden', !visible);
    });
}

function confirmKelasSelection() {
    const checkbox = document.querySelector('.kelas-checkbox:checked');
    const select = get('kelasSelect');
    const modalElement = get('kelasModal');

    if (!select || !modalElement || !window.bootstrap) return;

    if (checkbox) {
        select.value = checkbox.value;
        updateSelectedKelasUI([{
            id: checkbox.value,
            name: checkbox.dataset.name,
            jenjang: checkbox.dataset.jenjang,
        }]);
    } else {
        select.value = '';
        updateSelectedKelasUI([]);
    }

    window.bootstrap.Modal.getInstance(modalElement)?.hide();
}

function togglePassword(button) {
    const field = get(button.dataset.field);
    const icon = get(button.dataset.icon);

    if (!field || !icon) return;

    const isPassword = field.type === 'password';
    field.type = isPassword ? 'text' : 'password';
    icon.classList.toggle('fa-eye', !isPassword);
    icon.classList.toggle('fa-eye-slash', isPassword);
}

function filterParentList() {
    const searchTerm = (get('searchParent')?.value || '').toLowerCase();
    const statusFilter = get('filterParentStatus')?.value || '';
    const noParentFound = get('noParentFound');
    const parentCount = get('parentCount');
    let visibleCount = 0;

    document.querySelectorAll('.parent-option').forEach((option) => {
        const name = option.dataset.name || '';
        const username = option.dataset.username || '';
        const status = option.dataset.status || '';

        const visible = (!searchTerm || name.includes(searchTerm) || username.includes(searchTerm))
            && (!statusFilter || status === statusFilter);

        option.classList.toggle('is-hidden', !visible);
        if (visible) visibleCount++;
    });

    if (parentCount) parentCount.textContent = visibleCount;
    setVisible(noParentFound, visibleCount === 0);
}

function toggleParentForm() {
    const option = get('parentOption')?.value;
    const existingForm = get('existingParentForm');
    const newForm = get('newParentForm');

    setVisible(existingForm, option === 'existing');
    setVisible(newForm, option === 'new');

    if (option === 'existing') {
        const searchParent = get('searchParent');
        if (searchParent) searchParent.value = '';
        filterParentList();
    }
}

function toggleRelationship(selectId, fieldId) {
    setVisible(get(fieldId), get(selectId)?.value === 'lainnya');
}

document.addEventListener('DOMContentLoaded', () => {
    const selectedOption = document.querySelector('#kelasSelect option:checked');
    if (selectedOption?.value) {
        updateSelectedKelasUI([{
            id: selectedOption.value,
            name: selectedOption.textContent.trim(),
            jenjang: selectedOption.dataset.jenjang,
        }]);
    }

    get('cabangSelect')?.addEventListener('change', () => {
        const kelasSelect = get('kelasSelect');
        if (kelasSelect) kelasSelect.value = '';
        updateSelectedKelasUI([]);
        filterKelasList();
    });

    document.querySelector('[data-open-kelas-modal]')?.addEventListener('click', openKelasModal);
    document.querySelector('[data-confirm-kelas-selection]')?.addEventListener('click', confirmKelasSelection);
    document.querySelectorAll('.kelas-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', updateTempSelection);
    });

    get('filterCabang')?.addEventListener('change', filterKelasList);
    get('filterJenjang')?.addEventListener('change', filterKelasList);
    get('searchKelas')?.addEventListener('input', filterKelasList);
    preventEnterSubmit(get('searchKelas'));

    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => togglePassword(button));
    });

    get('parentOption')?.addEventListener('change', toggleParentForm);
    get('searchParent')?.addEventListener('input', filterParentList);
    get('filterParentStatus')?.addEventListener('change', filterParentList);
    preventEnterSubmit(get('searchParent'));

    get('existing_relationship')?.addEventListener('change', () => {
        toggleRelationship('existing_relationship', 'existingRelationshipOtherField');
    });
    get('new_relationship')?.addEventListener('change', () => {
        toggleRelationship('new_relationship', 'newRelationshipOtherField');
    });

    toggleParentForm();
    toggleRelationship('existing_relationship', 'existingRelationshipOtherField');
    toggleRelationship('new_relationship', 'newRelationshipOtherField');
});
