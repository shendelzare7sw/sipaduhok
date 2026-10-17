// Teks satu baris yang terpotong (judul/subjudul topbar, deskripsi sidebar) digeser pelan agar terbaca utuh:
// diam sejenak -> geser ke kanan sampai akhir teks terlihat -> diam -> memudar kembali ke awal -> ulang.
// Hanya aktif bila teks memang terpotong; elemen cukup diberi atribut data-marquee (tetap pakai class truncate).

const KECEPATAN = 32; // px per detik, sama untuk teks pendek maupun panjang
const JEDA_AWAL = 1800;
const JEDA_AKHIR = 1600;
const PUDAR = 350;
const RUANG_PUDAR = 16; // lebar gradasi kanan; teks digeser sedikit lebih jauh agar huruf terakhir tidak ikut pudar

// Utility Tailwind (tanpa CSS custom): teks tidak diberi elipsis dan sisi kanan memudar selama bergeser.
const KELAS_AKTIF = ['text-clip', '[mask-image:linear-gradient(90deg,#000_calc(100%-16px),transparent)]', '[-webkit-mask-image:linear-gradient(90deg,#000_calc(100%-16px),transparent)]'];
const KELAS_TRACK = ['inline-block', 'will-change-transform'];

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const state = new WeakMap();

const stop = (el) => {
    const s = state.get(el);
    if (!s) return;
    s.animation?.cancel();
    s.animation = null;
    el.classList.remove(...KELAS_AKTIF);
    s.track?.classList.remove(...KELAS_TRACK);
};

const ensureTrack = (el) => {
    const s = state.get(el);
    if (s.track) return s.track;
    const track = document.createElement('span');
    while (el.firstChild) track.appendChild(el.firstChild);
    el.appendChild(track);
    s.track = track;
    return track;
};

const measure = (el) => {
    const s = state.get(el);
    if (!s) return;
    const width = el.clientWidth;
    if (width === 0) return; // tersembunyi (submenu tertutup); diukur lagi saat tampil
    const track = ensureTrack(el);
    // Lebar asli teks (tidak terpengaruh translate saat animasi berjalan).
    const overflow = Math.ceil(track.getBoundingClientRect().width) - width;

    if (overflow <= 1 || reduceMotion.matches) {
        stop(el);
        if (overflow > 1 && !el.title) el.title = el.textContent.trim();
        return;
    }

    const shift = overflow + RUANG_PUDAR;
    if (s.animation && s.shift === shift) return;
    s.animation?.cancel();
    s.shift = shift;
    el.classList.add(...KELAS_AKTIF);
    track.classList.add(...KELAS_TRACK);

    const move = (shift / KECEPATAN) * 1000;
    const total = JEDA_AWAL + move + JEDA_AKHIR + PUDAR * 2;
    let t = 0;
    const at = (ms) => (t += ms) / total;
    const start = 'translateX(0)';
    const end = `translateX(${-shift}px)`;

    s.animation = track.animate([
        { transform: start, opacity: 1, offset: 0 },
        { transform: start, opacity: 1, offset: at(JEDA_AWAL), easing: 'ease-in-out' },
        { transform: end, opacity: 1, offset: at(move) },
        { transform: end, opacity: 1, offset: at(JEDA_AKHIR) },
        { transform: end, opacity: 0, offset: at(PUDAR) },
        { transform: start, opacity: 0, offset: Math.min(1, t / total + 0.0001) },
        { transform: start, opacity: 1, offset: 1 },
    ], { duration: total, iterations: Infinity });

    if (!s.visible) s.animation.pause();
};

const resizeObserver = typeof ResizeObserver === 'function'
    ? new ResizeObserver((entries) => entries.forEach((entry) => measure(entry.target)))
    : null;

// Hemat baterai: animasi berhenti saat elemen di luar layar (mis. sidebar ponsel sedang tertutup).
const visibilityObserver = typeof IntersectionObserver === 'function'
    ? new IntersectionObserver((entries) => entries.forEach((entry) => {
        const s = state.get(entry.target);
        if (!s) return;
        s.visible = entry.isIntersecting;
        if (!s.animation) return;
        if (s.visible && !s.hovered) s.animation.play();
        else s.animation.pause();
    }))
    : null;

export function initMarquee(root = document) {
    root.querySelectorAll('[data-marquee]').forEach((el) => {
        if (state.has(el)) return;
        state.set(el, { visible: !visibilityObserver, hovered: false });

        // Saat disentuh/diarahkan, teks berhenti agar mudah dibaca.
        const host = el.closest('a, button') || el;
        host.addEventListener('mouseenter', () => { const s = state.get(el); s.hovered = true; s.animation?.pause(); });
        host.addEventListener('mouseleave', () => { const s = state.get(el); s.hovered = false; if (s.visible) s.animation?.play(); });

        resizeObserver?.observe(el);
        visibilityObserver?.observe(el);
        measure(el);
    });
}

export function remeasureMarquee(root = document) {
    root.querySelectorAll('[data-marquee]').forEach((el) => measure(el));
}

reduceMotion.addEventListener?.('change', () => remeasureMarquee());
document.fonts?.ready.then(() => remeasureMarquee());
