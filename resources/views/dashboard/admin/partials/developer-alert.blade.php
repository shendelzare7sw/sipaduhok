<div class="dev-alert">
    <div class="d-flex align-items-center gap-3">
        <div class="text-primary fs-4">
            <i class="fas fa-headset"></i>
        </div>
        <div>
            <h6 class="mb-1 fw-bold text-dark">Butuh Bantuan Teknis?</h6>
            <p class="mb-0 text-muted dev-alert-text">
                Laporkan kendala/bug pada sistem untuk peningkatan kualitas.
            </p>
        </div>
    </div>
    <a href="https://wa.me/6282113100791?text=Halo%20Developer,%20saya%20menemukan%20kendala/bug%20pada%20sistem"
        target="_blank"
        class="btn btn-sm btn-outline-primary fw-medium px-3">
        <i class="fab fa-whatsapp me-2"></i>Kontak Developer
    </a>
</div>

<script>
    // Info kontak developer hanya tampil 5 detik lalu menghilang (tidak permanen).
    (function () {
        setTimeout(function () {
            document.querySelectorAll('.dev-alert').forEach(function (el) {
                el.style.transition = 'opacity .5s ease';
                el.style.opacity = '0';
                setTimeout(function () { el.style.display = 'none'; }, 500);
            });
        }, 5000);
    })();
</script>
