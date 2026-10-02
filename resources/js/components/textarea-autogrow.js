/**
 * Textarea yang tingginya mengikuti isi: <textarea data-autogrow>.
 *
 * - Tumbuh otomatis sampai batas ringkas (data-autogrow-max, default 240px).
 * - Bila isi melebihi batas, muncul tombol "Tampilkan semua" untuk membuka penuh
 *   dan "Ringkas" untuk kembali ke tampilan bergulir.
 * - Aktif juga untuk textarea yang ditambahkan belakangan (editor soal, hasil AI),
 *   dan menghitung ulang saat textarea baru terlihat (mis. accordion dibuka) lewat
 *   ResizeObserver, sehingga nilai yang diisi lewat JS pun ikut terukur.
 */
const BATAS_DEFAULT = 240;
const terdaftar = new WeakSet();

function ukur(textarea) {
    // Textarea tersembunyi (accordion tertutup) belum bisa diukur; tunggu terlihat.
    if (textarea.offsetParent === null) return;

    const batas = Number(textarea.dataset.autogrowMax) || BATAS_DEFAULT;
    const terbuka = textarea.dataset.autogrowExpanded === 'true';
    const gaya = getComputedStyle(textarea);
    const tepi = parseFloat(gaya.borderTopWidth) + parseFloat(gaya.borderBottomWidth);

    textarea.style.height = 'auto';
    const penuh = textarea.scrollHeight + tepi;
    const lebih = penuh > batas + 4;

    textarea.style.height = `${terbuka || !lebih ? penuh : batas}px`;
    textarea.style.overflowY = !terbuka && lebih ? 'auto' : 'hidden';

    const tombol = textarea._autogrowToggle;
    if (tombol) {
        tombol.hidden = !lebih;
        tombol.querySelector('span').textContent = terbuka ? 'Ringkas' : 'Tampilkan semua';
        tombol.querySelector('i').className = `fa-solid ${terbuka ? 'fa-chevron-up' : 'fa-chevron-down'} text-[10px]`;
        tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
    }
}

const pengamat = typeof ResizeObserver === 'function'
    ? new ResizeObserver((entri) => entri.forEach(({ target }) => {
        // Hanya hitung ulang bila lebar berubah/baru terlihat, agar perubahan tinggi
        // oleh ukur() sendiri tidak memicu putaran tanpa akhir.
        const lebar = Math.round(target.clientWidth);
        if (target._autogrowLebar === lebar) return;
        target._autogrowLebar = lebar;
        ukur(target);
    }))
    : null;

function daftarkan(textarea) {
    if (terdaftar.has(textarea)) return;
    terdaftar.add(textarea);

    textarea.style.resize = 'none';

    const tombol = document.createElement('button');
    tombol.type = 'button';
    tombol.hidden = true;
    tombol.className = 'mt-1 inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[11px] font-bold text-indigo-700 hover:bg-indigo-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-200';
    tombol.innerHTML = '<i class="fa-solid fa-chevron-down text-[10px]" aria-hidden="true"></i><span>Tampilkan semua</span>';
    tombol.addEventListener('click', (event) => {
        event.preventDefault();
        textarea.dataset.autogrowExpanded = textarea.dataset.autogrowExpanded === 'true' ? 'false' : 'true';
        ukur(textarea);
        if (textarea.dataset.autogrowExpanded !== 'true') textarea.scrollTop = 0;
    });
    textarea.insertAdjacentElement('afterend', tombol);
    textarea._autogrowToggle = tombol;

    pengamat?.observe(textarea);
    ukur(textarea);
}

function pindai(akar) {
    if (!(akar instanceof Element || akar instanceof Document)) return;
    if (akar instanceof HTMLTextAreaElement && akar.matches('[data-autogrow]')) daftarkan(akar);
    akar.querySelectorAll?.('textarea[data-autogrow]').forEach(daftarkan);
}

export function initTextareaAutogrow() {
    pindai(document);

    document.addEventListener('input', (event) => {
        if (event.target instanceof HTMLTextAreaElement && terdaftar.has(event.target)) ukur(event.target);
    });

    new MutationObserver((mutasi) => mutasi.forEach((m) => m.addedNodes.forEach(pindai)))
        .observe(document.body, { childList: true, subtree: true });

    // Untuk nilai yang diisi lewat JS setelah elemen terlihat.
    window.refreshAutogrow = (akar = document) => {
        pindai(akar);
        (akar.querySelectorAll ? akar.querySelectorAll('textarea[data-autogrow]') : []).forEach(ukur);
    };
}
