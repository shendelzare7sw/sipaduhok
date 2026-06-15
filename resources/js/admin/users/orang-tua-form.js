document.addEventListener('DOMContentLoaded', () => {
    initializePasswordToggles();
    initializeStudentFilters();
    initializeCreateRelationshipForm();
    initializeEditRelationshipFields();
});

const hiddenClass = 'd-none';

function initializePasswordToggles() {
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const passwordField = document.getElementById(button.dataset.field);
            const toggleIcon = document.getElementById(button.dataset.icon);

            if (!passwordField || !toggleIcon) {
                return;
            }

            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            toggleIcon.classList.toggle('fa-eye', !isPassword);
            toggleIcon.classList.toggle('fa-eye-slash', isPassword);
        });
    });
}

function initializeStudentFilters() {
    const searchStudent = document.getElementById('searchStudent');
    const filterJenjang = document.getElementById('filterJenjang');
    const filterCabang = document.getElementById('filterCabang');
    const filterKelas = document.getElementById('filterKelas');
    const studentCount = document.getElementById('studentCount');

    if (!searchStudent || !filterJenjang || !filterCabang || !filterKelas || !studentCount) {
        return;
    }

    const filterStudentList = () => {
        const searchTerm = searchStudent.value.toLowerCase();
        const jenjangValue = filterJenjang.value;
        const cabangValue = filterCabang.value;
        const kelasValue = filterKelas.value;
        let visibleCount = 0;

        document.querySelectorAll('.student-option').forEach((option) => {
            const name = option.getAttribute('data-name');
            const nisn = option.getAttribute('data-nisn');
            const jenjang = option.getAttribute('data-jenjang');
            const cabang = option.getAttribute('data-cabang');
            const kelas = option.getAttribute('data-kelas');
            const isVisible = (
                (searchTerm === '' || name.includes(searchTerm) || nisn.includes(searchTerm)) &&
                (jenjangValue === '' || jenjang === jenjangValue) &&
                (cabangValue === '' || cabang === cabangValue) &&
                (kelasValue === '' || kelas === kelasValue)
            );

            option.classList.toggle(hiddenClass, !isVisible);
            if (isVisible) {
                visibleCount++;
            }
        });

        studentCount.textContent = visibleCount;
    };

    searchStudent.addEventListener('input', filterStudentList);
    filterJenjang.addEventListener('change', filterStudentList);
    filterCabang.addEventListener('change', filterStudentList);
    filterKelas.addEventListener('change', filterStudentList);

    document.querySelectorAll('[data-reset-student-filters]').forEach((button) => {
        button.addEventListener('click', () => {
            searchStudent.value = '';
            filterJenjang.value = '';
            filterCabang.value = '';
            filterKelas.value = '';
            filterStudentList();
        });
    });

    filterStudentList();
}

function initializeCreateRelationshipForm() {
    const form = document.querySelector('[data-orang-tua-create-form]');
    const relationshipSelect = document.getElementById('hubungan_keluarga');
    const otherRelationship = document.getElementById('hubungan_keluarga_lainnya');
    const otherField = document.getElementById('otherRelationshipCreateField');
    const relationshipModal = document.getElementById('relationshipModal');

    if (!relationshipSelect || !otherRelationship || !otherField) {
        return;
    }

    const toggleOtherField = () => {
        otherField.classList.toggle(hiddenClass, relationshipSelect.value !== 'lainnya');
    };

    relationshipSelect.addEventListener('change', toggleOtherField);
    toggleOtherField();

    form?.addEventListener('submit', (event) => {
        const checkedSiswa = document.querySelectorAll('input[name="siswa_ids[]"]:checked');
        const relationshipValue = relationshipSelect.value;
        const otherRelationshipValue = otherRelationship.value;

        if (relationshipValue && checkedSiswa.length === 0) {
            relationshipModal?.classList.add('show');
            event.preventDefault();
            return;
        }

        if (checkedSiswa.length > 0 && !relationshipValue) {
            alert('Anda telah memilih siswa, harap pilih Hubungan Keluarga!');
            relationshipSelect.focus();
            event.preventDefault();
            return;
        }

        if (checkedSiswa.length > 0 && relationshipValue === 'lainnya' && !otherRelationshipValue.trim()) {
            alert('Harap sebutkan hubungan keluarga lainnya!');
            otherRelationship.focus();
            event.preventDefault();
        }
    });

    document.querySelectorAll('[data-clear-relationship]').forEach((button) => {
        button.addEventListener('click', () => {
            relationshipSelect.value = '';
            otherRelationship.value = '';
            otherField.classList.add(hiddenClass);
            relationshipModal?.classList.remove('show');
        });
    });

    document.querySelectorAll('[data-close-relationship-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            relationshipModal?.classList.remove('show');
            document.querySelector('.card:nth-child(4)')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    relationshipModal?.addEventListener('click', (event) => {
        if (event.target === relationshipModal) {
            relationshipModal.classList.remove('show');
        }
    });
}

function initializeEditRelationshipFields() {
    document.querySelectorAll('[data-relationship-edit-id]').forEach((select) => {
        const studentParentId = select.dataset.relationshipEditId;
        const otherField = document.getElementById(`otherRelationshipEditField_${studentParentId}`);

        if (!otherField) {
            return;
        }

        const toggleOtherField = () => {
            otherField.classList.toggle(hiddenClass, select.value !== 'lainnya');
        };

        select.addEventListener('change', toggleOtherField);
        toggleOtherField();
    });
}
