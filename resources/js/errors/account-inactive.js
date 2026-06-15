document.addEventListener('DOMContentLoaded', () => {
    let seconds = 3;
    const countdownElement = document.getElementById('countdown');
    const page = document.querySelector('[data-account-inactive-page]');
    const redirectUrl = page?.dataset.redirectUrl;

    if (!countdownElement || !redirectUrl) {
        return;
    }

    const timer = window.setInterval(() => {
        seconds -= 1;
        countdownElement.textContent = String(seconds);

        if (seconds <= 0) {
            window.clearInterval(timer);
            window.location.href = redirectUrl;
        }
    }, 1000);
});
