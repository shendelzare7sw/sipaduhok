document.addEventListener('DOMContentLoaded', () => {
    const addParentButton = document.getElementById('btnTambahOrangTua');
    const addParentForm = document.getElementById('addParentFormContainer');
    const parentOptionSelect = document.getElementById('parentOptionSelect');
    const existingParentForm = document.getElementById('existingParentForm');
    const newParentForm = document.getElementById('newParentForm');
    const searchParentInput = document.getElementById('searchParentInput');
    const filterParentStatus = document.getElementById('filterParentStatus');
    const noParentFound = document.getElementById('noParentFound');
    const parentCount = document.getElementById('parentCount');
    const detachParentModal = document.getElementById('detachParentModal');
    const detachParentName = document.getElementById('detachParentName');
    const detachParentRelationship = document.getElementById('detachParentRelationship');
    let detachParentFormId = null;

    const filterParentList = () => {
        const searchTerm = (searchParentInput?.value || '').toLowerCase();
        const statusFilter = filterParentStatus?.value || '';
        let visibleCount = 0;

        document.querySelectorAll('.parent-option').forEach((option) => {
            const name = option.dataset.name || '';
            const email = option.dataset.email || '';
            const alreadyLinked = option.dataset.alreadyLinked === 'true';
            const status = option.dataset.status || '';
            const matchSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
            const matchStatus = !statusFilter || status === statusFilter;
            const isVisible = !alreadyLinked && matchSearch && matchStatus;

            option.classList.toggle('is-hidden', !isVisible);
            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (parentCount) {
            parentCount.textContent = visibleCount;
        }

        noParentFound?.classList.toggle('is-visible', visibleCount === 0);
    };

    const resetAddParentForms = () => {
        if (parentOptionSelect) {
            parentOptionSelect.value = '';
        }

        existingParentForm?.classList.remove('is-visible');
        newParentForm?.classList.remove('is-visible');
    };

    const hideAddParentForm = () => {
        addParentForm?.classList.remove('is-visible');
        resetAddParentForms();
    };

    addParentButton?.addEventListener('click', () => {
        addParentForm?.classList.add('is-visible');
        resetAddParentForms();
    });

    document.querySelectorAll('[data-hide-parent-form]').forEach((button) => {
        button.addEventListener('click', hideAddParentForm);
    });

    parentOptionSelect?.addEventListener('change', () => {
        const option = parentOptionSelect.value;
        existingParentForm?.classList.toggle('is-visible', option === 'existing');
        newParentForm?.classList.toggle('is-visible', option === 'new');

        if (option === 'existing') {
            if (searchParentInput) {
                searchParentInput.value = '';
            }
            filterParentList();
        }
    });

    searchParentInput?.addEventListener('input', filterParentList);
    filterParentStatus?.addEventListener('change', filterParentList);

    document.getElementById('attachParentForm')?.addEventListener('submit', (event) => {
        const parentSelected = document.querySelector('input[name="parent_id"]:checked');
        const relationship = document.getElementById('relationshipSelect')?.value;

        if (!parentSelected) {
            event.preventDefault();
            alert('Silakan pilih orang tua terlebih dahulu!');
            return;
        }

        if (!relationship) {
            event.preventDefault();
            alert('Silakan pilih hubungan dengan siswa!');
        }
    });

    document.getElementById('createParentForm')?.addEventListener('submit', (event) => {
        const name = document.querySelector('input[name="new_parent_name"]')?.value;
        const username = document.querySelector('input[name="new_parent_username"]')?.value;
        const email = document.querySelector('input[name="new_parent_email"]')?.value;
        const password = document.querySelector('input[name="new_parent_password"]')?.value;
        const relationship = document.getElementById('newRelationshipSelect')?.value;

        if (!name || !username || !email || !password) {
            event.preventDefault();
            alert('Silakan lengkapi semua field yang wajib diisi!');
            return;
        }

        if (password.length < 8) {
            event.preventDefault();
            alert('Password minimal 8 karakter!');
            return;
        }

        if (!relationship) {
            event.preventDefault();
            alert('Silakan pilih hubungan dengan siswa!');
        }
    });

    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const field = document.getElementById(button.dataset.target);
            const icon = document.getElementById(button.dataset.icon);

            if (!field || !icon) {
                return;
            }

            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        });
    });

    document.querySelectorAll('.parent-option').forEach((option) => {
        option.addEventListener('click', () => {
            document.querySelectorAll('.parent-option').forEach((item) => item.classList.remove('is-selected'));
            option.classList.add('is-selected');
        });
    });

    document.querySelectorAll('[data-detach-parent]').forEach((button) => {
        button.addEventListener('click', () => {
            detachParentFormId = button.dataset.parentId;

            if (detachParentName) {
                detachParentName.textContent = button.dataset.parentName || '';
            }

            if (detachParentRelationship) {
                detachParentRelationship.textContent = button.dataset.relationship || '';
            }

            if (detachParentModal) {
                new bootstrap.Modal(detachParentModal).show();
            }
        });
    });

    document.getElementById('submitDetachParentButton')?.addEventListener('click', () => {
        if (detachParentFormId) {
            document.getElementById(`detachParentForm${detachParentFormId}`)?.submit();
        }
    });
});
