import { initSearchableCombobox } from '../../shared/searchable-combobox.js';
import { TEMPAT_LAHIR_OPTIONS } from '../../shared/tempat-lahir-options.js';

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

function createKelasChip(item) {
    const chip = document.createElement('span');
    chip.className = 'selected-kelas-chip badge bg-purple';

    const icon = document.createElement('i');
    icon.className = 'fas fa-check me-1';

    const name = document.createTextNode(item.name);

    const jenjang = document.createElement('small');
    jenjang.className = 'selected-kelas-chip-level';
    jenjang.textContent = `(${item.jenjang})`;

    chip.append(icon, name, jenjang);
    return chip;
}

function updateSelectedKelasUIEdit(items) {
    const textPlaceholder = get('selectedKelasTextEdit');
    const chipsContainer = get('selectedKelasChipsEdit');

    if (!textPlaceholder || !chipsContainer) return;

    setVisible(textPlaceholder, items.length === 0);
    setVisible(chipsContainer, items.length > 0);
    chipsContainer.replaceChildren();

    items.forEach((item) => chipsContainer.appendChild(createKelasChip(item)));
}

function openKelasModalEdit() {
    const kelasSelect = get('kelasSelectEdit');
    const modalElement = get('kelasModalEdit');
    if (!kelasSelect || !modalElement || !window.bootstrap) return;

    document.querySelectorAll('.kelas-checkbox-edit').forEach((checkbox) => {
        checkbox.checked = checkbox.value === kelasSelect.value && kelasSelect.value !== '';
    });

    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
}

function filterKelasListEdit() {
    const cabangSelectEdit = get('cabangSelectEdit');
    const cabangFilter = get('filterCabangEdit')?.value || cabangSelectEdit?.value || '';
    const jenjangFilter = get('filterJenjangEdit')?.value || '';
    const searchText = (get('searchKelasEdit')?.value || '').toLowerCase();

    document.querySelectorAll('.kelas-item-edit').forEach((item) => {
        const visible = (!cabangFilter || item.dataset.cabangId === cabangFilter)
            && (!jenjangFilter || item.dataset.jenjang === jenjangFilter)
            && (!searchText || (item.dataset.name || '').toLowerCase().includes(searchText));

        item.classList.toggle('is-hidden', !visible);
    });
}

function confirmKelasSelectionEdit() {
    const checkbox = document.querySelector('.kelas-checkbox-edit:checked');
    const select = get('kelasSelectEdit');
    const modalElement = get('kelasModalEdit');

    if (!select || !checkbox || !modalElement || !window.bootstrap) return;

    select.value = checkbox.value;
    updateSelectedKelasUIEdit([{
        id: checkbox.value,
        name: checkbox.dataset.name,
        jenjang: checkbox.dataset.jenjang,
    }]);

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

function toggleAddParentForm() {
    const option = get('addParentOption')?.value;
    const existingForm = get('addExistingParentForm');
    const newForm = get('addNewParentForm');

    setVisible(existingForm, option === 'existing');
    setVisible(newForm, option === 'new');

    if (option === 'existing') {
        const searchParent = get('searchParent');
        if (searchParent) searchParent.value = '';
        filterParentList();
    }
}

let parentIdToDelete = null;
let elementToDelete = null;

function openDeleteModal(parentId, button) {
    parentIdToDelete = parentId;
    elementToDelete = button.closest('.parent-row');
    get('confirmationModal')?.classList.add('is-visible');
}

function closeDeleteModal() {
    get('confirmationModal')?.classList.remove('is-visible');
    parentIdToDelete = null;
    elementToDelete = null;
}

function confirmParentRemoval() {
    if (!parentIdToDelete || !elementToDelete) return;

    const input = get(`remove_parent_${parentIdToDelete}`);
    if (input) input.value = parentIdToDelete;

    elementToDelete.classList.add('parent-row--removed');
    closeDeleteModal();
}

function filterParentList() {
    const searchTerm = (get('searchParent')?.value || '').toLowerCase();
    const statusFilter = get('filterParentStatus')?.value || '';
    const noParentFound = get('noParentFound');
    const parentCount = get('parentCount');
    let visibleCount = 0;

    document.querySelectorAll('.parent-option').forEach((option) => {
        if (option.dataset.alreadyLinked === 'true') {
            option.classList.add('is-hidden');
            return;
        }

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

function toggleRelationship(selectId, fieldId) {
    setVisible(get(fieldId), get(selectId)?.value === 'lainnya');
}

document.addEventListener('DOMContentLoaded', () => {
    initSearchableCombobox({
        wrapperId: 'tempatLahirCombobox',
        inputId: 'tempatLahirInput',
        listId: 'tempatLahirList',
        groups: TEMPAT_LAHIR_OPTIONS,
        emptyText: 'Tidak ditemukan. Anda tetap bisa mengetik tempat lahir sendiri.',
    });

    const selectedOption = document.querySelector('#kelasSelectEdit option:checked');
    if (selectedOption?.value) {
        updateSelectedKelasUIEdit([{
            id: selectedOption.value,
            name: selectedOption.textContent.trim(),
            jenjang: selectedOption.dataset.jenjang,
        }]);
    }

    document.querySelector('[data-open-kelas-modal-edit]')?.addEventListener('click', openKelasModalEdit);
    document.querySelector('[data-confirm-kelas-selection-edit]')?.addEventListener('click', confirmKelasSelectionEdit);

    get('filterCabangEdit')?.addEventListener('change', filterKelasListEdit);
    get('filterJenjangEdit')?.addEventListener('change', filterKelasListEdit);
    get('searchKelasEdit')?.addEventListener('input', filterKelasListEdit);
    preventEnterSubmit(get('searchKelasEdit'));

    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => togglePassword(button));
    });

    get('addParentOption')?.addEventListener('change', toggleAddParentForm);
    get('searchParent')?.addEventListener('input', filterParentList);
    get('filterParentStatus')?.addEventListener('change', filterParentList);
    preventEnterSubmit(get('searchParent'));

    document.querySelectorAll('[data-remove-parent]').forEach((button) => {
        button.addEventListener('click', () => openDeleteModal(button.dataset.parentId, button));
    });
    document.querySelectorAll('[data-close-confirmation]').forEach((button) => {
        button.addEventListener('click', closeDeleteModal);
    });
    get('confirmDeleteBtn')?.addEventListener('click', confirmParentRemoval);
    get('confirmationModal')?.addEventListener('click', (event) => {
        if (event.target === event.currentTarget) closeDeleteModal();
    });

    get('add_existing_relationship')?.addEventListener('change', () => {
        toggleRelationship('add_existing_relationship', 'addExistingRelationshipOtherField');
    });
    get('add_new_relationship')?.addEventListener('change', () => {
        toggleRelationship('add_new_relationship', 'addNewRelationshipOtherField');
    });

    toggleAddParentForm();
    filterParentList();
    toggleRelationship('add_existing_relationship', 'addExistingRelationshipOtherField');
    toggleRelationship('add_new_relationship', 'addNewRelationshipOtherField');
});
