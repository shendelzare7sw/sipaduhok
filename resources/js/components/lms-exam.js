/**
 * Komponen Alpine zona Latihan/Ujian Siswa (pengganti show.js & show-latihan.js).
 *
 * Pengecualian JS yang disengaja (seperti modul AI): berisi perilaku, bukan styling —
 * timer, autosave, monitoring pengawasan, fullscreen, deteksi kecurangan, dan
 * pergantian halaman tanpa keluar fullscreen. Seluruh tampilan diatur utility
 * Tailwind di Blade lewat state komponen ini.
 *
 *   examStart  — layar mulai/hasil (Kerjakan Ulang, Mulai Ujian + fullscreen)
 *   ujianWork  — mode fokus ujian satu-soal-per-layar (CBT)
 *   latihanWork — mode lembar kerja latihan
 */
import Swal from 'sweetalert2';

const dialog = (options) => Swal.fire(options).then((result) => result.isConfirmed);

const decode = (value, fallback) => {
    if (!value) return fallback;
    try {
        return JSON.parse(window.atob(value));
    } catch (error) {
        console.error('Failed to parse encoded JSON:', error);
        return fallback;
    }
};

const formatSisa = (distance) => [
    Math.floor((distance % 86400000) / 3600000),
    Math.floor((distance % 3600000) / 60000),
    Math.floor((distance % 60000) / 1000),
].map((part) => String(part).padStart(2, '0')).join(':');

const postJson = (url, csrf, body, extra = {}) => fetch(url, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify(body),
    ...extra,
});

const autosave = (url, csrf, body) => {
    if (!url) return;
    postJson(url, csrf, body)
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) console.error('Autosave failed:', data.message);
        })
        .catch((error) => console.error('Autosave error:', error));
};

// Nilai jawaban gabungan untuk PG kompleks & benar/salah (disalin ke input hidden bernama jawaban[id]).
const nilaiKompleks = (root, soalId) => JSON.stringify(
    [...root.querySelectorAll(`[data-kompleks][data-soal-id="${soalId}"]:checked`)].map((cb) => cb.value),
);
const nilaiBenarSalah = (root, soalId, total) => {
    const answers = [];
    let terjawab = 0;
    for (let i = 0; i < total; i += 1) {
        const radio = root.querySelector(`input[name="bs_${soalId}_${i}"]:checked`);
        answers.push(radio ? radio.value === 'true' : null);
        if (radio) terjawab += 1;
    }
    return { value: JSON.stringify(answers), lengkap: terjawab === total };
};

const timerMixin = (durasiMenit, startIso) => {
    const start = new Date(startIso || '').getTime();
    const unlimited = Number(durasiMenit || 0) === 0;
    return {
        unlimited,
        endTime: unlimited || Number.isNaN(start) ? null : start + (Number(durasiMenit) * 60000),
    };
};

export function registerLmsExam(Alpine) {
    Alpine.data('examStart', (isLatihan = false) => ({
        starting: false,

        confirmRetake() {
            dialog({
                title: 'Kerjakan Ulang?',
                text: 'Jawaban dan nilai Anda sebelumnya akan di-reset. Apakah Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: isLatihan ? 'Ya, Kerjakan Ulang' : 'Ya, Kerjakan Ulang!',
                cancelButtonText: 'Batal',
            }).then((ok) => { if (ok) this.$refs.retakeForm?.submit(); });
        },

        // Ujian: minta fullscreen saat klik (butuh gesture pengguna), lalu muat halaman kerja
        // lewat fetch dan tukar isi body agar mode fullscreen tidak lepas karena navigasi.
        async startExam(event) {
            if (isLatihan) return;
            event.preventDefault();
            const form = event.target;
            const root = document.documentElement;
            if (root.requestFullscreen) root.requestFullscreen().catch(() => {});
            else if (root.webkitRequestFullscreen) root.webkitRequestFullscreen();
            this.starting = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const doc = new DOMParser().parseFromString(await response.text(), 'text/html');

                doc.head.querySelectorAll('link[rel="stylesheet"], script[src]').forEach((child) => {
                    const isLink = child.tagName === 'LINK';
                    const url = isLink ? child.href : child.src;
                    if (url && !document.head.querySelector(`${isLink ? 'link' : 'script'}[${isLink ? 'href' : 'src'}="${url}"]`)) {
                        const clone = document.createElement(child.tagName);
                        [...child.attributes].forEach((attr) => clone.setAttribute(attr.name, attr.value));
                        document.head.appendChild(clone);
                    }
                });

                document.title = doc.title;
                document.documentElement.className = doc.documentElement.className;
                document.body.className = doc.body.className;
                // Alpine menginisialisasi komponen baru (ujianWork) otomatis lewat MutationObserver.
                document.body.innerHTML = doc.body.innerHTML;
            } catch (error) {
                form.submit();
            }
        },
    }));

    Alpine.data('ujianWork', () => ({
        meta: [],
        answers: [],
        doubts: [],
        visited: [],
        current: 0,
        total: 0,
        timerText: '00:00:00',
        zoomSrc: '',
        focusLost: false,
        timeUp: false,
        submitting: false,

        init() {
            // Di method yang dipanggil dari x-on, this.$el adalah elemen pemicu — simpan root komponen.
            this.root = this.$el;
            const d = this.$el.dataset;
            this.meta = decode(d.questionMeta, []);
            this.answers = decode(d.answersState, []);
            this.total = Number(d.totalQuestions || this.meta.length || 0);
            this.isLatihan = (d.examType || 'ujian') === 'latihan';
            this.csrf = d.csrfToken || '';
            this.monitoringUrl = d.monitoringUrl || '';
            this.autosaveUrl = d.autosaveUrl || '';
            this.storageKey = d.storageKey || 'doubtState';
            Object.assign(this, timerMixin(d.durationMinutes, d.startTime));

            let tersimpan = null;
            try { tersimpan = JSON.parse(localStorage.getItem(this.storageKey) || 'null'); } catch (e) { tersimpan = null; }
            this.doubts = Array.isArray(tersimpan) ? tersimpan : new Array(this.total).fill(false);
            this.visited = new Array(this.total).fill(false);
            if (this.total > 0) this.visited[0] = true;

            this.tick();
            if (!this.unlimited) this.timerInterval = window.setInterval(() => this.tick(), 1000);

            this.monitor('question_opened', { source: 'initial_load' });
            this.monitor('heartbeat');
            this.heartbeat = window.setInterval(() => this.monitor('heartbeat'), 5000);

            this.listeners = [
                [document, 'fullscreenchange', () => { if (!document.fullscreenElement && !this.submitting) this.lostFocus('exited_fullscreen'); }],
                [window, 'blur', () => this.lostFocus('window_blur')],
                [window, 'focus', () => { this.tick(); this.returnedFocus('window_focus'); }],
                [document, 'visibilitychange', () => {
                    if (document.hidden) this.lostFocus('visibility_hidden');
                    else { this.tick(); this.returnedFocus('visibility_visible'); }
                }],
                [document, 'contextmenu', (event) => { if (!this.isLatihan) event.preventDefault(); }],
                [document, 'keydown', (event) => {
                    if (!this.isLatihan && event.ctrlKey && ['c', 'v', 'u', 'i'].includes(event.key.toLowerCase())) event.preventDefault();
                }],
            ];
            this.listeners.forEach(([target, type, handler]) => target.addEventListener(type, handler));

            history.pushState(null, null, location.href);
            window.onpopstate = () => history.go(1);
        },

        destroy() {
            window.clearInterval(this.timerInterval);
            window.clearInterval(this.heartbeat);
            (this.listeners || []).forEach(([target, type, handler]) => target.removeEventListener(type, handler));
        },

        // Warna nomor soal: ragu > sudah dijawab > aktif > belum; soal aktif selalu diberi cincin.
        navClass(index) {
            const warna = this.doubts[index] ? 'bg-[#fd7e14]'
                : (this.answers[index] ? 'bg-[#198754]' : (index === this.current ? 'bg-[#0d6efd]' : 'bg-[#6c757d]'));
            return index === this.current ? `${warna} shadow-[0_0_0_3px_rgba(13,110,253,0.3)]` : warna;
        },

        go(index, source = 'navigation') {
            if (index < 0 || index >= this.total) return;
            this.current = index;
            this.visited[index] = true;
            this.monitor('question_opened', { source });
        },
        prev() { if (this.current > 0) this.go(this.current - 1, 'prev_button'); },
        next() { if (this.current < this.total - 1) this.go(this.current + 1, 'next_button'); },

        setAnswer(index, soalId, value) {
            const teks = value === null || value === undefined ? '' : String(value).trim();
            this.answers[index] = teks !== '' && teks !== '-';
            if (soalId) {
                autosave(this.autosaveUrl, this.csrf, {
                    soal_id: soalId,
                    nomor_soal: this.meta[index]?.nomor_soal ?? null,
                    jawaban: value,
                });
            }
        },
        answerKompleks(index, soalId) {
            const value = nilaiKompleks(this.root, soalId);
            const hidden = this.root.querySelector(`#kompleks-hidden-${soalId}`);
            if (hidden) hidden.value = value;
            this.setAnswer(index, soalId, value === '[]' ? '' : value);
        },
        answerBenarSalah(index, soalId, total) {
            const { value, lengkap } = nilaiBenarSalah(this.root, soalId, total);
            const hidden = this.root.querySelector(`#bs-hidden-${soalId}`);
            if (hidden) hidden.value = value;
            this.setAnswer(index, soalId, lengkap ? value : '');
        },

        toggleDoubt(checked) {
            this.doubts[this.current] = checked;
            try { localStorage.setItem(this.storageKey, JSON.stringify(this.doubts)); } catch (e) { /* storage diblokir */ }
            this.monitor('doubt_updated', { is_doubt: this.doubts[this.current] });
        },

        zoom(src) {
            this.zoomSrc = src;
            this.$refs.zoomDialog?.showModal();
        },

        finish() {
            const belum = this.answers.filter((x) => !x).length;
            const ragu = this.doubts.filter(Boolean).length;

            if (!this.isLatihan && (belum > 0 || ragu > 0)) {
                let pesan = 'Anda tidak dapat mengumpulkan ujian karena ada soal yang belum selesai.\n';
                if (belum > 0) pesan += `\n- Terdapat ${belum} soal belum dijawab.`;
                if (ragu > 0) pesan += `\n- Terdapat ${ragu} soal ditandai ragu-ragu.`;
                pesan += '\n\nSilakan lengkapi dan hilangkan tanda ragu-ragu sebelum submit.';
                dialog({ title: 'Peringatan!', text: pesan, icon: 'error', confirmButtonColor: '#dc3545', confirmButtonText: 'Tutup' });
                return;
            }

            let pesan = '';
            if (belum > 0) pesan += `Masih ada ${belum} soal belum dijawab.\n`;
            if (ragu > 0) pesan += `Masih ada ${ragu} soal ditandai ragu-ragu.\n`;
            pesan += '\nApakah Anda yakin ingin menyelesaikan sesi ini?';

            dialog({
                title: 'Konfirmasi Pengumpulan',
                text: pesan,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal',
            }).then((ok) => {
                if (!ok) return;
                try { localStorage.removeItem(this.storageKey); } catch (e) { /* storage diblokir */ }
                this.submit();
            });
        },

        akhiriTanpaSoal() {
            dialog({
                title: 'Akhiri ujian?',
                text: 'Anda akan mengakhiri ujian tanpa menjawab soal. Lanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, akhiri',
                cancelButtonText: 'Batal',
            }).then((ok) => { if (ok) this.submit(); });
        },

        submit() {
            this.submitting = true;
            this.$refs.examForm?.submit();
        },

        tick() {
            if (this.unlimited) { this.timerText = 'NO LIMIT'; return; }
            const sisa = this.endTime - Date.now();
            if (sisa < 0) {
                this.timerText = '00:00:00';
                if (this.timeUp) return;
                this.timeUp = true;
                dialog({ title: 'Waktu Habis!', text: 'Ujian akan disubmit otomatis.', icon: 'warning', timer: 2000, showConfirmButton: false })
                    .then(() => this.submit());
                return;
            }
            this.timerText = formatSisa(sisa);
        },

        monitor(eventType, metadata = {}) {
            if (this.isLatihan || !this.monitoringUrl) return;
            const meta = this.meta[this.current] || null;
            const payload = {
                event_type: eventType,
                current_soal_id: meta ? meta.soal_id : null,
                current_nomor_soal: meta ? meta.nomor_soal : null,
                metadata,
            };
            if (['heartbeat', 'question_opened', 'doubt_updated'].includes(eventType)) {
                payload.statuses = this.meta.map((m, i) => ({
                    soal_id: m.soal_id,
                    nomor_soal: m.nomor_soal,
                    is_visited: this.visited[i] || i === this.current,
                    is_answered: !!this.answers[i],
                    is_doubt: !!this.doubts[i],
                }));
            }
            postJson(this.monitoringUrl, this.csrf, payload, { keepalive: ['focus_lost', 'focus_returned'].includes(eventType) })
                .catch((error) => console.error('Monitoring error:', error));
        },

        lostFocus(trigger) {
            if (this.isLatihan || this.focusLost || this.submitting) return;
            this.focusLost = true;
            this.monitor('focus_lost', { trigger });
            dialog({
                title: 'Peringatan Kecurangan!',
                text: 'Anda dilarang meninggalkan atau berpindah tab saat ujian berlangsung! Percobaan ini telah dicatat sistem.',
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Kembali Fokus',
            });
        },
        returnedFocus(trigger) {
            if (this.isLatihan || !this.focusLost) return;
            this.focusLost = false;
            this.monitor('focus_returned', { trigger });
        },
    }));

    Alpine.data('latihanWork', () => ({
        timerText: '00:00:00',
        zoomSrc: '',
        timeUp: false,

        init() {
            this.root = this.$el;
            const d = this.$el.dataset;
            this.csrf = d.csrfToken || '';
            this.autosaveUrl = d.autosaveUrl || '';
            Object.assign(this, timerMixin(d.durationMinutes, d.startTime));
            this.tick();
            if (!this.unlimited) this.timerInterval = window.setInterval(() => this.tick(), 1000);
        },
        destroy() { window.clearInterval(this.timerInterval); },

        save(soalId, value) { autosave(this.autosaveUrl, this.csrf, { soal_id: soalId, jawaban: value }); },
        answerKompleks(soalId) {
            const value = nilaiKompleks(this.root, soalId);
            const hidden = this.root.querySelector(`#kompleks-hidden-${soalId}`);
            if (hidden) hidden.value = value;
            this.save(soalId, value);
        },
        answerBenarSalah(soalId, total) {
            const { value } = nilaiBenarSalah(this.root, soalId, total);
            const hidden = this.root.querySelector(`#bs-hidden-${soalId}`);
            if (hidden) hidden.value = value;
            this.save(soalId, value);
        },

        zoom(src) {
            this.zoomSrc = src;
            this.$refs.zoomDialog?.showModal();
        },

        finish() {
            dialog({
                title: 'Kirim Jawaban?',
                text: 'Pastikan Anda sudah memeriksa semua jawaban.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Periksa Lagi',
            }).then((ok) => { if (ok) this.$refs.examForm?.submit(); });
        },

        tick() {
            if (this.unlimited) { this.timerText = 'NO LIMIT'; return; }
            const sisa = this.endTime - Date.now();
            if (sisa < 0) {
                this.timerText = '00:00:00';
                if (this.timeUp) return;
                this.timeUp = true;
                dialog({ title: 'Waktu Habis!', text: 'Latihan akan disubmit otomatis.', icon: 'warning', timer: 2000, showConfirmButton: false })
                    .then(() => this.$refs.examForm?.submit());
                return;
            }
            this.timerText = formatSisa(sisa);
        },
    }));
}
