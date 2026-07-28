/* ============================================================================
   SIPADUHOK UI Kit — perakit shell.

   Tiap berkas layar hanya menulis ISI halaman di dalam <div class="content-slot">.
   Skrip ini membungkusnya dengan sidebar + navbar + footer sesuai atribut data-*,
   supaya 11 varian sidebar tidak perlu disalin ulang di puluhan berkas.

   Struktur menu di bawah disalin dari 11 file sidebar partial di dalam
   resources/views (folder partials tiap role) — label, ikon, dan urutannya
   sudah diverifikasi ke kode.

   CATATAN IMPOR FIGMA: karena shell dirakit oleh JavaScript, impor ke Figma harus
   memakai MODE EKSTENSI BROWSER html.to.design (membaca DOM yang sudah ter-render),
   bukan mode "import from URL" yang mengambil HTML mentah. Untuk berkas lokal
   (file://) mode ekstensi memang satu-satunya pilihan.
   ========================================================================== */

/* Berkas di screens/ berada satu tingkat lebih dalam, jadi aset perlu prefiks '../'. */
const ASSET = location.pathname.includes('/screens/') ? '../' : '';

const SIDEBAR = {
    admin: [
        { i: 'fa-home', t: 'Dashboard', k: 'admin.dashboard' },
        { h: 'Manajemen Konten' },
        { i: 'fa-globe', t: 'Landing Page', k: 'admin.landing-pages' },
        { i: 'fa-bullhorn', t: 'Konten Publikasi', k: 'admin.konten', sub: [
            { t: 'Kalender Akademik', k: 'admin.kalender' },
            { t: 'Pengumuman', k: 'admin.pengumuman' },
            { t: 'Flyer / Iklan', k: 'admin.flyer' },
            { t: 'Kelola Berita', k: 'admin.berita' },
        ] },
        { h: 'Manajemen Pengguna' },
        { i: 'fa-users', t: 'Kelola Data Pengguna', k: 'admin.users', sub: [
            { t: 'Tenaga Pendidik', k: 'admin.users.tenaga-pendidik' },
            { t: 'Siswa', k: 'admin.users.siswa' },
            { t: 'Wali Siswa', k: 'admin.users.wali-siswa' },
        ] },
        { i: 'fa-life-ring', t: 'Tiket Pemulihan Akun', k: 'admin.recovery', badge: 3 },
        { i: 'fa-cogs', t: 'Pengaturan Sistem', k: 'admin.settings', sub: [
            { t: 'Pengaturan LMS', k: 'admin.lms-settings' },
            { t: 'Pengaturan AI', k: 'admin.ai-settings' },
        ] },
        { h: 'Data Master' },
        { i: 'fa-calendar-alt', t: 'Tahun Ajaran', k: 'admin.tahun-ajaran' },
        { i: 'fa-building', t: 'Manajemen Cabang', k: 'admin.cabang' },
        { h: 'Data Akademik' },
        { i: 'fa-chalkboard-teacher', t: 'Data Kelas &amp; Penugasan', k: 'admin.kelas-grup', sub: [
            { t: 'Data Kelas', k: 'admin.kelas.index' },
            { t: 'Data Wali Kelas', k: 'admin.wali-kelas' },
            { t: 'Data Guru Pengajar', k: 'admin.guru-pengajar' },
            { t: 'Manajemen Siswa', k: 'admin.manajemen-siswa' },
        ] },
        { i: 'fa-book', t: 'Mata Pelajaran', k: 'admin.mata-pelajaran' },
        { i: 'fa-calendar-week', t: 'Jadwal Pelajaran', k: 'admin.jadwal-pelajaran.index' },
        { h: 'Keuangan' },
        { i: 'fa-file-invoice-dollar', t: 'Tagihan &amp; Pembayaran', k: 'admin.keuangan', sub: [
            { t: 'Tagihan', k: 'admin.keuangan.tagihan.index' },
            { t: 'Tarik Tunggakan', k: 'admin.keuangan.carryover' },
            { t: 'Pembayaran', k: 'admin.keuangan.pembayaran' },
            { t: 'Config Pembayaran', k: 'admin.keuangan.info' },
        ] },
        { i: 'fa-chart-line', t: 'Laporan Keuangan', k: 'admin.keuangan.laporan' },
        { h: 'Validasi &amp; Dispensasi' },
        { i: 'fa-check-circle', t: 'Validasi Ujian &amp; Rapor', k: 'admin.validasi-akses' },
        { i: 'fa-hand-holding-usd', t: 'Validasi Dispensasi', k: 'admin.dispensasi' },
        { h: 'Kenaikan Kelas' },
        { i: 'fa-cogs', t: 'Pengaturan Kenaikan', k: 'admin.kenaikan', sub: [
            { t: 'Pengaturan KKM', k: 'admin.kkm' },
            { t: 'Pengaturan Kenaikan', k: 'admin.kenaikan-settings' },
        ] },
        { i: 'fa-tasks', t: 'Proses &amp; Rekap', k: 'admin.kenaikan-report' },
        { h: 'Monitoring &amp; Analitik' },
        { i: 'fa-chart-bar', t: 'Monitoring Sistem', k: 'admin.monitoring', sub: [
            { t: 'Pengguna', k: 'admin.monitoring.pengguna' },
            { t: 'Wali Kelas', k: 'admin.monitoring.wali-kelas' },
            { t: 'Guru Pengajar', k: 'admin.monitoring.guru' },
            { t: 'Siswa', k: 'admin.monitoring.siswa' },
            { t: 'Monitoring LMS', k: 'admin.monitoring.lms' },
        ] },
        { i: 'fa-file-alt', t: 'Laporan &amp; Catatan', k: 'admin.laporan-grup', sub: [
            { t: 'Laporan', k: 'admin.laporan.index' },
            { t: 'Catatan', k: 'admin.catatan' },
        ] },
    ],

    waka: [
        { i: 'fa-home', t: 'Dashboard', k: 'waka.dashboard' },
        { h: 'Manajemen Akademik' },
        { i: 'fa-calendar-alt', t: 'Tahun Ajaran', k: 'waka.tahun-ajaran' },
        { h: 'Data Akademik' },
        { i: 'fa-chalkboard-teacher', t: 'Data Kelas &amp; Penugasan', k: 'waka.kelas-grup', sub: [
            { t: 'Data Kelas', k: 'waka.kelas.index' },
            { t: 'Data Wali Kelas', k: 'waka.wali-kelas' },
            { t: 'Data Guru Pengajar', k: 'waka.guru-pengajar' },
            { t: 'Manajemen Siswa', k: 'waka.manajemen-siswa' },
        ] },
        { i: 'fa-book', t: 'Mata Pelajaran', k: 'waka.mata-pelajaran' },
        { i: 'fa-calendar-week', t: 'Jadwal Pelajaran', k: 'waka.jadwal-pelajaran.index' },
        { h: 'Kenaikan Kelas' },
        { i: 'fa-cogs', t: 'Pengaturan Kenaikan', k: 'waka.kenaikan', sub: [
            { t: 'Pengaturan KKM', k: 'waka.kkm.index' },
            { t: 'Pengaturan Kenaikan', k: 'waka.kenaikan-settings' },
        ] },
        { i: 'fa-tasks', t: 'Proses &amp; Rekap', k: 'waka.report' },
        { h: 'Monitoring &amp; Analitik' },
        { i: 'fa-chart-bar', t: 'Monitoring Sistem', k: 'waka.monitoring', sub: [
            { t: 'Wali Kelas', k: 'waka.monitoring.wali-kelas' },
            { t: 'Guru Pengajar', k: 'waka.monitoring.guru' },
            { t: 'Siswa', k: 'waka.monitoring.siswa' },
            { t: 'Monitoring LMS', k: 'waka.monitoring.lms' },
        ] },
        { h: 'Komunikasi' },
        { i: 'fa-sticky-note', t: 'Catatan', k: 'waka.catatan' },
    ],

    ketua: [
        { i: 'fa-home', t: 'Dashboard', k: 'ketua.dashboard' },
        { h: 'Persetujuan &amp; Validasi' },
        { i: 'fa-check-double', t: 'Approval Dispensasi', k: 'ketua.approval' },
        { i: 'fa-certificate', t: 'Validasi Rapor', k: 'ketua.validasi-rapor' },
        { i: 'fa-hand-holding-heart', t: 'Dispensasi Keuangan', k: 'ketua.dispensasi', badge: 5 },
        { h: 'Monitoring' },
        { i: 'fa-chart-bar', t: 'Monitoring Sistem', k: 'ketua.monitoring', sub: [
            { t: 'Data Pengguna', k: 'ketua.monitoring.pengguna' },
            { t: 'Data Wali Kelas', k: 'ketua.monitoring.wali-kelas' },
            { t: 'Data Guru Pengajar', k: 'ketua.monitoring.guru' },
            { t: 'Data Siswa', k: 'ketua.monitoring.siswa' },
            { t: 'Monitoring LMS', k: 'ketua.monitoring.lms' },
        ] },
        { h: 'Laporan &amp; Komunikasi' },
        { i: 'fa-file-alt', t: 'Laporan &amp; Catatan', k: 'ketua.laporan-grup', sub: [
            { t: 'Cetak Laporan', k: 'ketua.laporan.index' },
            { t: 'Kirim Catatan', k: 'ketua.catatan' },
        ] },
    ],

    sekretaris: [
        { i: 'fa-home', t: 'Dashboard', k: 'sekretaris.dashboard' },
        { h: 'Manajemen Konten' },
        { i: 'fa-bullhorn', t: 'Konten Publikasi', k: 'sekretaris.konten', sub: [
            { t: 'Kalender Akademik', k: 'sekretaris.kalender' },
            { t: 'Pengumuman', k: 'sekretaris.pengumuman' },
            { t: 'Flyer / Iklan', k: 'sekretaris.flyer' },
            { t: 'Kelola Berita', k: 'sekretaris.berita' },
        ] },
    ],

    bendahara: [
        { i: 'fa-home', t: 'Dashboard', k: 'bendahara.dashboard' },
        { h: 'Keuangan' },
        { i: 'fa-file-invoice-dollar', t: 'Tagihan &amp; Pembayaran', k: 'bendahara.keuangan', sub: [
            { t: 'Kelola Tagihan', k: 'bendahara.tagihan.index' },
            { t: 'Tarik Tunggakan', k: 'bendahara.carryover' },
            { t: 'Kelola Pembayaran', k: 'bendahara.pembayaran.index' },
            { t: 'Config Pembayaran', k: 'bendahara.info' },
        ] },
        { h: 'Validasi &amp; Dispensasi' },
        { i: 'fa-check-circle', t: 'Validasi Ujian &amp; Rapor', k: 'bendahara.validasi-akses' },
        { i: 'fa-hand-holding-usd', t: 'Validasi Dispensasi', k: 'bendahara.dispensasi' },
        { h: 'Laporan' },
        { i: 'fa-chart-bar', t: 'Laporan Keuangan', k: 'bendahara.laporan', sub: [
            { t: 'Laporan Pembayaran', k: 'bendahara.laporan.index' },
            { t: 'Rekap Tagihan', k: 'bendahara.laporan.rekap' },
            { t: 'Siswa Belum Lunas', k: 'bendahara.laporan.belum-lunas' },
        ] },
    ],

    'wali-kelas': [
        { kelasAktif: { nama: 'Kelas 7A', meta: 'Cabang Pusat - SMP' } },
        { i: 'fa-home', t: 'Dashboard', k: 'wali.dashboard' },
        { i: 'fa-exchange-alt', t: 'Pilih Kelas', k: 'wali.pilih-kelas' },
        { h: 'Akademik' },
        { i: 'fa-calendar-week', t: 'Jadwal Pelajaran', k: 'wali.jadwal' },
        { i: 'fa-clipboard-check', t: 'Kelola Presensi', k: 'wali.presensi', sub: [
            { t: 'Input Harian', k: 'wali.presensi.index', si: 'fa-edit' },
            { t: 'Validasi Izin', k: 'wali.presensi.validasi', si: 'fa-check-circle' },
            { t: 'Rekap Harian', k: 'wali.presensi.rekap', si: 'fa-calendar-day' },
            { t: 'Riwayat &amp; Edit', k: 'wali.presensi.riwayat', si: 'fa-history' },
        ] },
        { i: 'fa-file-alt', t: 'Kelola Rapor', k: 'wali.rapor-grup', sub: [
            { t: 'Nilai Siswa', k: 'wali.nilai', si: 'fa-chart-line' },
            { t: 'Kelola Rapor', k: 'wali.rapor.index', si: 'fa-file-alt' },
            { t: 'Arsip Kelas Saya', k: 'wali.arsip', si: 'fa-archive' },
            { t: 'Permintaan Unduh', k: 'wali.request-download', si: 'fa-download', badge: 2 },
        ] },
        { h: 'Kenaikan Kelas' },
        { i: 'fa-chart-bar', t: 'Prediksi Kenaikan', k: 'wali.prediksi' },
        { h: 'Validasi' },
        { i: 'fa-check-double', t: 'Validasi Akses', k: 'wali.validasi-akses' },
    ],

    guru: [
        { i: 'fa-home', t: 'Dashboard', k: 'guru.dashboard' },
        { h: 'Akademik' },
        { i: 'fa-calendar-alt', t: 'Informasi Akademik', k: 'guru.akademik', sub: [
            { t: 'Jadwal Mengajar', k: 'guru.jadwal' },
            { t: 'Semua Kelas', k: 'guru.kelas.index' },
        ] },
        { h: 'Pembelajaran' },
        { i: 'fa-archive', t: 'Arsip LMS', k: 'guru.arsip' },
        { i: 'fa-comment-dots', t: 'Catatan Monitoring', k: 'guru.catatan', badge: 1 },
        { h: 'Kelas Saya (Akses Cepat)' },
        { i: 'fa-chalkboard', t: 'Kelas 7A', k: 'guru.kelas.7a', sub: [
            { t: 'Matematika', k: 'guru.7a.mtk' },
            { t: 'IPA Terpadu', k: 'guru.7a.ipa' },
        ] },
        { i: 'fa-chalkboard', t: 'Kelas 8B', k: 'guru.kelas.8b', sub: [
            { t: 'Matematika', k: 'guru.8b.mtk' },
        ] },
    ],

    siswa: [
        { i: 'fa-home', t: 'Dashboard SIA', k: 'siswa.sia.dashboard' },
        { h: 'Learning Management' },
        { i: 'fa-graduation-cap', t: 'HOK-LMS', k: 'siswa.lms', bold: true },
        { h: 'Akademik' },
        { i: 'fa-calendar-check', t: 'Presensi', k: 'siswa.presensi' },
        { i: 'fa-chart-line', t: 'Data Penilaian', k: 'siswa.penilaian' },
    ],

    // Wali siswa memakai Boxicons di aplikasi; di UI kit disubstitusi ikon FA setara.
    'wali-siswa': [
        { i: 'fa-home', t: 'Dashboard', k: 'wali-siswa.dashboard' },
        { h: 'Monitoring Anak' },
        { i: 'fa-user-circle', t: 'Ahmad Fauzi Ramadha…', k: 'anak.1', sub: [
            { t: 'Presensi', k: 'anak.1.presensi', si: 'fa-calendar-check' },
            { t: 'Tagihan', k: 'anak.1.tagihan', si: 'fa-credit-card' },
            { t: 'Rapor', k: 'anak.1.rapor', si: 'fa-file' },
        ] },
        { i: 'fa-user-circle', t: 'Siti Nurhaliza', k: 'anak.2', sub: [
            { t: 'Presensi', k: 'anak.2.presensi', si: 'fa-calendar-check' },
            { t: 'Tagihan', k: 'anak.2.tagihan', si: 'fa-credit-card' },
            { t: 'Rapor', k: 'anak.2.rapor', si: 'fa-file' },
        ] },
    ],
};

/* Sidebar LMS — struktur datar (nav-link), bukan menu-item Sneat */
const SIDEBAR_LMS = {
    guru: {
        konteks: { label: 'Anda Mengajar:', mapel: 'Matematika', kelas: 'Kelas 7A' },
        item: [
            { s: 'Utama' },
            { i: 'fa-home', t: 'Beranda', k: 'guru.lms.dashboard' },
            { s: 'Pembelajaran' },
            { i: 'fa-book', t: 'Materi', k: 'guru.lms.materi' },
            { i: 'fa-tasks', t: 'Tugas', k: 'guru.lms.tugas', notif: 8 },
            { i: 'fa-pencil-ruler', t: 'Latihan', k: 'guru.lms.latihan' },
            { i: 'fa-file-alt', t: 'Ujian', k: 'guru.lms.ujian' },
            { i: 'fa-comments', t: 'Forum Diskusi', k: 'guru.lms.forum' },
            { i: 'fa-video', t: 'Kelas Virtual', k: 'guru.lms.meeting' },
            { s: 'Penilaian' },
            { i: 'fa-chart-line', t: 'Nilai Siswa', k: 'guru.lms.nilai' },
            { s: 'Navigasi' },
            { i: 'fa-arrow-left', t: 'Kembali ke Dashboard', k: 'guru.dashboard' },
        ],
    },
    siswa: {
        item: [
            { i: 'fa-arrow-circle-left', t: 'Kembali ke SIA', k: 'siswa.sia' },
            { s: 'Beranda' },
            { i: 'fa-home', t: 'Beranda', k: 'siswa.lms.dashboard' },
            { s: 'Mata Pelajaran' },
            { i: 'fa-book', t: 'Bahasa Indonesia', k: 'mapel.bindo' },
            { i: 'fa-book', t: 'IPA Terpadu', k: 'mapel.ipa' },
            { i: 'fa-book', t: 'Matematika', k: 'mapel.mtk' },
            { i: 'fa-book', t: 'Pendidikan Pancasila', k: 'mapel.pancasila' },
            { s: 'Akademik' },
            { i: 'fa-calendar', t: 'Kalender Akademik', k: 'siswa.lms.kalender' },
            { i: 'fa-clock', t: 'Jadwal Pelajaran', k: 'siswa.lms.jadwal' },
            { i: 'fa-user-tie', t: 'Daftar Guru', k: 'siswa.lms.guru' },
        ],
    },
};

/* ── Perakit ───────────────────────────────────────────────────────────── */

function renderMenu(items, active) {
    return items.map((it) => {
        if (it.h) return `<li class="menu-header">${it.h}</li>`;

        if (it.kelasAktif) {
            return `<li class="menu-item">
                <div class="wali-active-class-card">
                    <div class="wali-active-class-label"><i class="fas fa-school"></i> Kelas Aktif</div>
                    <div class="wali-active-class-title">${it.kelasAktif.nama}</div>
                    <div class="wali-active-class-meta">${it.kelasAktif.meta}</div>
                    <a class="wali-active-class-switch"><i class="fas fa-exchange-alt"></i> Ganti Kelas</a>
                </div>
            </li>`;
        }

        const subAktif = it.sub?.some((s) => s.k === active);
        const aktif = it.k === active || subAktif;
        const badge = it.badge ? `<span class="badge-count">${it.badge}</span>` : '';
        const caret = it.sub ? `<i class="fas fa-chevron-${subAktif ? 'down' : 'right'} caret"></i>` : '';
        const tebal = it.bold ? ' style="font-weight:700"' : '';

        let html = `<li class="menu-item${aktif ? ' active' : ''}">
            <a class="menu-link"><i class="menu-icon fas ${it.i}"></i><div${tebal}>${it.t}</div>${badge}${caret}</a>`;

        if (it.sub && subAktif) {
            html += `<ul class="menu-sub">${it.sub.map((s) => `
                <li class="menu-item${s.k === active ? ' active' : ''}">
                    <a class="menu-link">${s.si ? `<i class="fas ${s.si} sub-icon"></i>` : ''}<div>${s.t}</div>${s.badge ? `<span class="badge-count">${s.badge}</span>` : ''}</a>
                </li>`).join('')}</ul>`;
        }
        return html + '</li>';
    }).join('');
}

/**
 * @param {string} varian  'tanpa-kelas' menyembunyikan kartu Kelas Aktif — kondisi nyata
 *                         wali kelas yang belum memilih kelas (session masih kosong).
 */
function renderSidebarSneat(role, active, varian) {
    let items = SIDEBAR[role] || [];
    if (varian === 'tanpa-kelas') items = items.filter((it) => !it.kelasAktif);
    return `<aside class="sidebar">
        <div class="sidebar-brand">
            <img src="${ASSET}assets/img/logo.png" alt="Logo">
            <span>SIPADUHOK</span>
        </div>
        <ul class="menu">${renderMenu(items, active)}</ul>
    </aside>`;
}

function renderSidebarLms(who, active) {
    const cfg = SIDEBAR_LMS[who];
    const konteks = cfg.konteks ? `
        <div class="lms-teaching-context">
            <div class="lms-teaching-context-label">${cfg.konteks.label}</div>
            <div class="lms-teaching-context-subject">${cfg.konteks.mapel}</div>
            <div class="lms-teaching-context-class">${cfg.konteks.kelas}</div>
        </div>` : '';

    const item = cfg.item.map((it) => it.s
        ? `<div class="nav-section-title">${it.s}</div>`
        : `<a class="nav-link${it.k === active ? ' active' : ''}"><i class="fas ${it.i}"></i><span>${it.t}</span>${it.notif ? `<span class="badge-notif">${it.notif}</span>` : ''}</a>`
    ).join('');

    return `<aside class="sidebar lms">
        <div class="sidebar-brand" style="padding:16px 20px">
            <img src="${ASSET}assets/img/logo.png" alt="Logo" style="max-height:34px">
            <span style="font-size:15px">HOK-LMS</span>
        </div>
        ${konteks}
        <nav style="flex:1">${item}</nav>
    </aside>`;
}

function renderNavbar(title, subtitle, user) {
    const [inisial, nama] = (user || 'A|Pengguna').split('|');
    return `<div class="navbar">
        <div class="navbar-title">
            <h5>${title}</h5>
            ${subtitle ? `<small>${subtitle}</small>` : ''}
        </div>
        <div class="navbar-actions">
            <span class="bell"><i class="fas fa-bell"></i><span class="dot">3</span></span>
            <span class="avatar-initial" title="${nama}">${inisial}</span>
        </div>
    </div>`;
}

const FOOTER = `<footer class="page-footer">
    <div>Copyright &copy; 2026 <a>SIPADUHOK</a> - PKBM House of Knowledge</div>
    <div></div>
</footer>`;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.screen[data-shell]').forEach((el) => {
        const shell = el.dataset.shell;
        const slot = el.querySelector('.content-slot');
        if (!slot) return;
        const isi = slot.innerHTML;

        if (shell === 'sneat') {
            el.innerHTML = `<div class="app">
                ${renderSidebarSneat(el.dataset.role, el.dataset.active, el.dataset.variant)}
                <div class="main">
                    ${renderNavbar(el.dataset.title, el.dataset.subtitle, el.dataset.user)}
                    <div class="content">${isi}</div>
                    ${FOOTER}
                </div>
            </div>`;
        } else if (shell === 'lms') {
            el.innerHTML = `<div class="app lms">
                ${renderSidebarLms(el.dataset.who, el.dataset.active)}
                <div class="main">
                    <div class="lms-header">
                        <div><h1>${el.dataset.title}</h1><p>${el.dataset.subtitle || ''}</p></div>
                    </div>
                    <div class="content">${isi}</div>
                </div>
            </div>`;
        }
        // shell "polos" (landing, auth, ujian, print) memakai markup apa adanya.
    });
});
