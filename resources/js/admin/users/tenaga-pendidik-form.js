document.addEventListener('DOMContentLoaded', () => {
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
