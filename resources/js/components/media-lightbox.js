/**
 * Lightbox galeri gambar ala marketplace untuk lampiran LMS (forum Guru & Siswa).
 * Gambar ditandai `data-lightbox="<grup>"`; klik membuka semua gambar segrup di halaman
 * yang sama (bukan tab baru): geser/tombol/keyboard, penghitung, thumbnail, dan zoom.
 * Markup overlay ada di resources/views/components/lms/lightbox.blade.php.
 */
export function registerMediaLightbox(Alpine) {
    Alpine.store('lightbox', {
        items: [],
        index: 0,
        open: false,
        zoom: false,

        get current() {
            return this.items[this.index] || null;
        },

        buka(el) {
            const grup = el.dataset.lightbox || 'default';
            const semua = [...document.querySelectorAll(`[data-lightbox="${CSS.escape(grup)}"]`)];
            this.items = semua.map((img) => ({ src: img.dataset.full || img.currentSrc || img.src, alt: img.alt || 'Gambar' }));
            this.index = Math.max(0, semua.indexOf(el));
            this.zoom = false;
            this.open = true;
            document.documentElement.classList.add('overflow-hidden');
        },

        tutup() {
            this.open = false;
            this.zoom = false;
            document.documentElement.classList.remove('overflow-hidden');
        },

        pindah(arah) {
            if (this.items.length < 2) return;
            this.zoom = false;
            this.index = (this.index + arah + this.items.length) % this.items.length;
        },

        ke(i) {
            this.zoom = false;
            this.index = i;
        },
    });

    // Gestur geser (swipe) di layar sentuh untuk overlay lightbox.
    Alpine.data('lightboxSwipe', () => ({
        startX: null,
        mulai(event) {
            this.startX = event.touches[0].clientX;
        },
        selesai(event) {
            if (this.startX === null || this.$store.lightbox.zoom) return;
            const dx = event.changedTouches[0].clientX - this.startX;
            if (Math.abs(dx) > 50) this.$store.lightbox.pindah(dx < 0 ? 1 : -1);
            this.startX = null;
        },
    }));

    // Pratinjau YouTube: thumbnail dulu, iframe baru dimuat saat diputar (hemat kuota & cepat).
    Alpine.data('youtubePreview', () => ({ putar: false }));
}
