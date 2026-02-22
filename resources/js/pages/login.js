window.togglePassword = function() {
    const passwordInput = document.getElementById('password');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
};
