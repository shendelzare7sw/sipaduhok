/**
 * Shared JavaScript logic for Authentication & Recovery Pages
 * Specifically designed to remove inline `<script>` tags from Blade views for cleaner source code.
 */

// --- 1. Admin Recovery Page Logic (admin-recovery.blade.php / reset-password-ticket.blade.php) ---
window.togglePasswordVisibility = function (inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('eye-icon-' + inputId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-1.828 0-3.572-.49-5.11-1.35z" />';
    }
};

// --- 2. User Recovery Logic (user-recovery.blade.php) ---
window.updatePlaceholder = function () {
    const tipe = document.getElementById('tipe_recovery');
    const input = document.getElementById('identifier');
    const helpText = document.getElementById('identifierHelp');

    if (!tipe || !input || !helpText) return;

    switch (tipe.value) {
        case 'lupa_username':
            input.placeholder = "NISN / NIP / No. HP / Email Pemulihan";
            helpText.innerText = "Masukkan Nomor Induk, No. HP, atau Email Pemulihan yang terdaftar.";
            break;
        case 'lupa_password':
            input.placeholder = "Username / Email / NISN / NIP";
            helpText.innerText = "Masukkan Username, Email, NISN (Siswa), atau NIP (Guru/Pegawai) Anda.";
            break;
        case 'lupa_keduanya':
            input.placeholder = "NISN / NIP / No. HP / Email Pemulihan";
            helpText.innerText = "Masukkan identitas selain Username/Email Login, seperti NISN, NIP, No. HP, atau Email Pemulihan.";
            break;
        default:
            input.placeholder = "NISN (Siswa) / NIP (Guru)";
            helpText.innerText = "Mohon masukkan Induk Resmi Anda (Siswa: NISN, Pegawai: NIP).";
            break;
    }
};

// --- 3. Login Page Logic (login.blade.php) ---
window.refreshCaptcha = function () {
    const captchaImage = document.getElementById('captchaImage');
    if (captchaImage) {
        captchaImage.src = '/captcha?' + Math.random();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    // Hidden trigger logic for admin recovery
    let logoClickCount = 0;
    let logoClickTimer;

    const logos = document.querySelectorAll('.logo-container, .mobile-logo-container');

    logos.forEach(targetElement => {
        targetElement.addEventListener('click', function (e) {
            logoClickCount++;

            clearTimeout(logoClickTimer);

            // Reset count after 2 seconds of inactivity
            logoClickTimer = setTimeout(() => {
                logoClickCount = 0;
            }, 2000);

            // 5 clicks = magical redirect
            if (logoClickCount === 5) {
                logoClickCount = 0;

                // Fetch CSRF token from the meta tag if available
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') :
                    (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

                // Send AJAX request to unlock the route
                fetch('/admin-recovery/unlock', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({})
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = "/admin-recovery";
                        } else {
                            console.error('Failed to unlock admin recovery.');
                        }
                    })
                    .catch(error => console.error('Error unlocking admin recovery:', error));
            }
        });
    });
});
