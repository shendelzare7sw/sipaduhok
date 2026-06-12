document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            const icon = button.querySelector('i');

            if (!input || !icon) {
                return;
            }

            const shouldShowPassword = input.type === 'password';
            input.type = shouldShowPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', shouldShowPassword);
            icon.classList.toggle('fa-eye-slash', !shouldShowPassword);
        });
    });
});
