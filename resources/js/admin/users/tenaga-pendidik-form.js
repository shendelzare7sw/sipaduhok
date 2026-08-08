import { initSearchableCombobox } from '../../shared/searchable-combobox.js';
import { PENDIDIKAN_TERAKHIR_OPTIONS } from '../../shared/pendidikan-terakhir-options.js';
import { TEMPAT_LAHIR_OPTIONS } from '../../shared/tempat-lahir-options.js';

document.addEventListener('DOMContentLoaded', () => {
    initSearchableCombobox({
        wrapperId: 'pendidikanTerakhirCombobox',
        inputId: 'pendidikanTerakhirInput',
        listId: 'pendidikanTerakhirList',
        groups: PENDIDIKAN_TERAKHIR_OPTIONS,
        emptyText: 'Tidak ditemukan. Anda tetap bisa mengetik pendidikan sendiri.',
    });

    initSearchableCombobox({
        wrapperId: 'tempatLahirCombobox',
        inputId: 'tempatLahirInput',
        listId: 'tempatLahirList',
        groups: TEMPAT_LAHIR_OPTIONS,
        emptyText: 'Tidak ditemukan. Anda tetap bisa mengetik tempat lahir sendiri.',
    });

    const flexibleRoles = ['ketua_pkbm', 'sekretaris', 'bendahara'];
    const defaultCabangId = '1';
    const roleSelect = document.querySelector('select[name="role"]');
    const cabangSelect = document.getElementById('cabangSelect');
    const cabangHidden = document.getElementById('cabangHidden');
    const cabangInfo = document.getElementById('cabangInfo');

    const handleRoleChange = () => {
        if (!roleSelect || !cabangSelect || !cabangHidden || !cabangInfo) {
            return;
        }

        const isFlexible = flexibleRoles.includes(roleSelect.value);

        if (isFlexible) {
            cabangSelect.value = defaultCabangId;
            cabangSelect.disabled = true;
            cabangSelect.removeAttribute('name');
            cabangHidden.value = defaultCabangId;
            cabangHidden.name = 'cabang_id';
            cabangHidden.disabled = false;
            cabangInfo.classList.remove('d-none');
            return;
        }

        cabangSelect.disabled = false;
        cabangSelect.name = 'cabang_id';
        cabangHidden.disabled = true;
        cabangHidden.removeAttribute('name');
        cabangInfo.classList.add('d-none');
    };

    roleSelect?.addEventListener('change', handleRoleChange);
    handleRoleChange();

    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const inputId = button.dataset.target;
            const passwordInput = document.getElementById(inputId);
            const icon = document.getElementById(`${inputId}-icon`);

            if (!passwordInput || !icon) {
                return;
            }

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        });
    });
});
