// Inti halaman publik (landing + auth): Alpine + komponen kecil. Tanpa CSS kustom —
// semua tampilan memakai kelas Tailwind (lihat tailwind.landing.config.js).
import Alpine from 'alpinejs';
import { registerMediaLightbox } from './components/media-lightbox.js';

window.Alpine = Alpine;

const HEX = /^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i;

// Warna dari CMS (color picker admin) dikirim lewat atribut data-warna (dan data-warna-akhir untuk
// gradien) lalu dipasang sebagai variabel CSS --warna / --warna-akhir; kelas Tailwind memakainya,
// mis. text-[var(--warna)]. Hanya hex valid yang diterima.
const terapkanWarna = (root = document) => {
    root.querySelectorAll('[data-warna], [data-warna-akhir]').forEach((el) => {
        ['warna', 'warna-akhir'].forEach((nama) => {
            const warna = el.getAttribute(`data-${nama}`);
            if (warna && HEX.test(warna)) el.style.setProperty(`--${nama}`, warna);
        });
    });
};

// Navbar: transparan di atas hero, putih setelah digulir; menu mobile layar penuh.
Alpine.data('navbarPublik', () => ({
    terscroll: false,
    menuMobile: false,
    init() {
        const cek = () => { this.terscroll = window.scrollY > 100; };
        cek();
        window.addEventListener('scroll', cek, { passive: true });
        this.$watch('menuMobile', (buka) => document.body.classList.toggle('overflow-hidden', buka));
    },
}));

// Dropdown desktop navbar: buka saat hover/klik, tutup 250 ms setelah kursor keluar.
Alpine.data('dropdownNav', () => ({
    buka: false,
    jeda: null,
    tampil() { clearTimeout(this.jeda); this.buka = true; },
    sembunyi() { this.jeda = setTimeout(() => { this.buka = false; }, 250); },
}));

// Carousel 3D berita beranda. Kelas posisi ditulis utuh agar terdeteksi Tailwind.
const POSISI = {
    center: 'left-1/2 z-[5] opacity-100 [transform:translateX(-50%)_scale(1)] md:[transform:translateX(-50%)_scale(1.1)]',
    'left-1': 'left-[18%] z-[3] opacity-70 [transform:translateX(-50%)_scale(0.85)_rotateY(8deg)] sm:left-[10%] sm:z-[4] sm:opacity-50 sm:[transform:translateX(-50%)_scale(0.72)_rotateY(20deg)] md:left-1/4 md:opacity-90 md:[transform:translateX(-50%)_scale(0.9)_rotateY(15deg)]',
    'right-1': 'left-[82%] z-[3] opacity-70 [transform:translateX(-50%)_scale(0.85)_rotateY(-8deg)] sm:left-[90%] sm:z-[4] sm:opacity-50 sm:[transform:translateX(-50%)_scale(0.72)_rotateY(-20deg)] md:left-3/4 md:opacity-90 md:[transform:translateX(-50%)_scale(0.9)_rotateY(-15deg)]',
    'left-2': 'left-[5%] z-[3] pointer-events-none opacity-0 [transform:translateX(-50%)_scale(0.75)_rotateY(25deg)] md:pointer-events-auto md:opacity-60',
    'right-2': 'left-[95%] z-[3] pointer-events-none opacity-0 [transform:translateX(-50%)_scale(0.75)_rotateY(-25deg)] md:pointer-events-auto md:opacity-60',
    tersembunyi: 'pointer-events-none opacity-0',
};

Alpine.data('carouselBerita', (berita = []) => ({
    berita,
    aktif: 0,
    timer: null,
    sentuhX: 0,
    init() {
        this.mulai();
        window.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.sebelumnya();
            if (e.key === 'ArrowRight') this.berikutnya();
        });
    },
    get jumlah() { return this.berita.length; },
    posisi(index) {
        const n = this.jumlah;
        const selisih = (index - this.aktif + n) % n;
        if (selisih === 0) return POSISI.center;
        if (selisih === 1) return POSISI['right-1'];
        if (selisih === n - 1) return POSISI['left-1'];
        if (selisih === 2) return POSISI['right-2'];
        if (selisih === n - 2) return POSISI['left-2'];
        return POSISI.tersembunyi;
    },
    ke(index) { this.aktif = index; this.mulai(); },
    berikutnya() { if (this.jumlah) this.aktif = (this.aktif + 1) % this.jumlah; },
    sebelumnya() { if (this.jumlah) this.aktif = (this.aktif - 1 + this.jumlah) % this.jumlah; },
    geser(arah) { arah > 0 ? this.berikutnya() : this.sebelumnya(); this.mulai(); },
    jeda() { clearInterval(this.timer); },
    mulai() { this.jeda(); this.timer = setInterval(() => this.berikutnya(), 5000); },
    sentuhMulai(e) { this.sentuhX = e.changedTouches[0].screenX; this.jeda(); },
    sentuhSelesai(e) {
        const delta = e.changedTouches[0].screenX - this.sentuhX;
        if (Math.abs(delta) > 40) delta < 0 ? this.berikutnya() : this.sebelumnya();
        this.mulai();
    },
}));

// Carousel foto fasilitas: geser per slide, indikator, autoplay 5 detik (jeda saat hover), swipe.
const GESER_SLIDE = ['translate-x-0', '-translate-x-full', '-translate-x-[200%]', '-translate-x-[300%]', '-translate-x-[400%]', '-translate-x-[500%]',
    '-translate-x-[600%]', '-translate-x-[700%]', '-translate-x-[800%]', '-translate-x-[900%]', '-translate-x-[1000%]', '-translate-x-[1100%]'];

Alpine.data('carouselFasilitas', () => ({
    aktif: 0,
    jumlah: 0,
    timer: null,
    awalX: 0,
    akhirX: 0,
    init() {
        this.jumlah = Math.min(this.$refs.jalur.children.length, GESER_SLIDE.length);
        this.mulai();
    },
    get geser() { return GESER_SLIDE[this.aktif] || GESER_SLIDE[0]; },
    ke(i) { this.aktif = i; },
    berikutnya() { if (this.jumlah) this.aktif = (this.aktif + 1) % this.jumlah; },
    sebelumnya() { if (this.jumlah) this.aktif = (this.aktif - 1 + this.jumlah) % this.jumlah; },
    mulai() { this.jeda(); this.timer = setInterval(() => this.berikutnya(), 5000); },
    jeda() { clearInterval(this.timer); this.timer = null; },
    sentuhMulai(e) { this.awalX = this.akhirX = e.touches[0].clientX; },
    sentuhGeser(e) { this.akhirX = e.touches[0].clientX; },
    sentuhSelesai() {
        const selisih = this.awalX - this.akhirX;
        if (Math.abs(selisih) > 50) selisih > 0 ? this.berikutnya() : this.sebelumnya();
    },
}));

// x-hitung: angka (mis. "1000+") dihitung naik dari 0 saat pertama kali terlihat.
Alpine.directive('hitung', (el, _, { cleanup }) => {
    const teks = el.textContent.trim();
    const target = parseInt(teks.replace(/\D/g, ''), 10);
    if (Number.isNaN(target)) return;
    const observer = new IntersectionObserver((entries) => {
        if (!entries.some((e) => e.isIntersecting)) return;
        observer.disconnect();
        let sekarang = 0;
        const langkah = target / 50;
        const timer = setInterval(() => {
            sekarang += langkah;
            if (sekarang >= target) { el.textContent = teks; clearInterval(timer); }
            else el.textContent = Math.floor(sekarang) + (teks.includes('+') ? '+' : '');
        }, 30);
    }, { threshold: 0.5 });
    observer.observe(el);
    cleanup(() => observer.disconnect());
});

// Galeri: filter kategori (foto dibuka lewat lightbox foto publik di bawah).
Alpine.data('galeriPublik', () => ({
    filter: 'all',
    tampil(kategori) { return this.filter === 'all' || this.filter === kategori; },
}));

// Login: klik logo 5x (jeda < 2 detik) membuka rute pemulihan admin, lalu dialihkan ke sana.
Alpine.data('pintuRahasia', () => ({
    jumlahKlik: 0,
    jeda: null,
    klik() {
        this.jumlahKlik++;
        clearTimeout(this.jeda);
        this.jeda = setTimeout(() => { this.jumlahKlik = 0; }, 2000);
        if (this.jumlahKlik < 5) return;
        this.jumlahKlik = 0;
        const token = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value || '';
        fetch('/admin-recovery/unlock', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({}),
        })
            .then((r) => r.json())
            .then((data) => { if (data.success) window.location.href = '/admin-recovery'; })
            .catch((e) => console.error('Error unlocking admin recovery:', e));
    },
}));

// Pemulihan akun: placeholder & teks bantuan identitas mengikuti jenis kendala.
const PETUNJUK_PEMULIHAN = {
    lupa_password: ['Username / Email / NISN / NIP', 'Masukkan Username, Email, NISN (Siswa), atau NIP (Guru/Pegawai) Anda.'],
    lupa_username: ['NISN / NIP / No. HP / Email Pemulihan', 'Masukkan Nomor Induk, No. HP, atau Email Pemulihan yang terdaftar.'],
    lupa_keduanya: ['NISN / NIP / No. HP / Email Pemulihan', 'Masukkan identitas selain Username/Email Login, seperti NISN, NIP, No. HP, atau Email Pemulihan.'],
};
Alpine.data('pemulihanAkun', (tipe = 'lupa_password') => ({
    tipe,
    get placeholder() { return (PETUNJUK_PEMULIHAN[this.tipe] || ['NISN (Siswa) / NIP (Guru)'])[0]; },
    get bantuan() { return (PETUNJUK_PEMULIHAN[this.tipe] || [, 'Mohon masukkan Induk Resmi Anda (Siswa: NISN, Pegawai: NIP).'])[1]; },
}));

// x-muncul: elemen muncul (fade/geser) saat masuk viewport. Nilai = kelas keadaan awal
// yang dilepas ketika terlihat, mis. x-muncul="opacity-0 translate-y-8".
Alpine.directive('muncul', (el, { expression }, { cleanup }) => {
    const awal = (expression || 'opacity-0 translate-y-8').split(/\s+/).filter(Boolean);
    el.classList.add(...awal);
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            el.classList.remove(...awal);
            observer.unobserve(el);
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    observer.observe(el);
    cleanup(() => observer.disconnect());
});

// x-saat-terlihat: tambahkan kelas (mis. animasi masuk) saat elemen pertama kali terlihat.
Alpine.directive('saat-terlihat', (el, { expression }, { cleanup }) => {
    const kelas = (expression || '').split(/\s+/).filter(Boolean);
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            el.classList.add(...kelas);
            observer.unobserve(el);
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    observer.observe(el);
    cleanup(() => observer.disconnect());
});

// Foto di halaman publik bisa diklik untuk dilihat besar (lightbox yang sama dengan pratinjau tugas LMS).
// Otomatis untuk setiap <img> konten; dikecualikan: navbar/footer, gambar di dalam tautan/tombol
// (mis. kartu berita), latar dekoratif (aria-hidden), ikon kecil, dan [data-tanpa-perbesar].
const MIN_SISI_FOTO = 72;
const bukanFoto = (img) => img.closest('nav, footer, a[href], button, [role="dialog"], [data-tanpa-perbesar], [aria-hidden="true"]')
    || img.getAttribute('aria-hidden') === 'true';
const fotoKonten = (img) => {
    if (bukanFoto(img) || !img.offsetParent) return false;
    const kotak = img.getBoundingClientRect();
    return Math.min(kotak.width, kotak.height) >= MIN_SISI_FOTO;
};
const tandaiFoto = () => document.querySelectorAll('body img').forEach((img) => {
    img.classList.toggle('cursor-zoom-in', !!fotoKonten(img));
});

// Foto yang tertutup lapisan overlay (gradien/keterangan) tetap bisa diklik: cari <img> di titik klik.
// Tombol, tautan, dan kontrol form tetap didahulukan (mis. panah carousel).
const fotoDiTitik = (e) => {
    if (e.target.closest('a[href], button, input, select, textarea, label, [role="button"], [role="dialog"]')) return null;
    const langsung = e.target.closest('img');
    if (langsung) return fotoKonten(langsung) ? langsung : null;
    return document.elementsFromPoint(e.clientX, e.clientY).find((el) => el.tagName === 'IMG' && fotoKonten(el)) || null;
};

document.addEventListener('click', (e) => {
    const img = fotoDiTitik(e);
    if (!img) return;
    e.preventDefault();
    e.stopPropagation();
    // Grup = foto yang terlihat di section yang sama, agar bisa digeser kiri-kanan di lightbox.
    const wadah = img.closest('[x-data^="carousel"], section') || document.body;
    document.querySelectorAll('img[data-lightbox="foto-publik"]').forEach((el) => el.removeAttribute('data-lightbox'));
    wadah.querySelectorAll('img').forEach((el) => { if (fotoKonten(el)) el.dataset.lightbox = 'foto-publik'; });
    Alpine.store('lightbox').buka(img);
}, true);

registerMediaLightbox(Alpine);
terapkanWarna();
Alpine.start();
window.addEventListener('load', tandaiFoto);
window.addEventListener('resize', () => { clearTimeout(window.__tandaiFoto); window.__tandaiFoto = setTimeout(tandaiFoto, 200); });
document.addEventListener('DOMContentLoaded', tandaiFoto);
// Filter/tab bisa memunculkan foto baru: tandai ulang setelah interaksi.
document.addEventListener('click', () => setTimeout(tandaiFoto, 80));
