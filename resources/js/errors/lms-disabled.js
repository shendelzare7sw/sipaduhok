document.addEventListener('DOMContentLoaded', () => {
    let count = 3;
    const countdownElement = document.getElementById('countdown');
    const page = document.querySelector('[data-lms-disabled-page]');
    const redirectUrl = page?.dataset.redirectUrl;

    if (!countdownElement || !redirectUrl) {
        return;
    }

    const timer = window.setInterval(() => {
        count -= 1;
        countdownElement.textContent = String(count);
        countdownElement.classList.add('is-pulsing');

        window.setTimeout(() => {
            countdownElement.classList.remove('is-pulsing');
        }, 200);

        if (count <= 0) {
            window.clearInterval(timer);
            window.location.href = redirectUrl;
        }
    }, 1000);
});
