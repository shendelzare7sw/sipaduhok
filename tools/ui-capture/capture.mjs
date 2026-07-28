/**
 * capture.mjs — Screenshot otomatis seluruh layar SIPADUHOK per role.
 *
 * Dipakai sebagai referensi kebenaran visual saat merekonstruksi prototype Figma,
 * sekaligus lampiran screenshot untuk skripsi.
 *
 * Cara pakai:
 *   1. npm install -D playwright && npx playwright install chromium
 *   2. cp tools/ui-capture/akun.contoh.json tools/ui-capture/akun.local.json
 *      lalu isi baseUrl, kredensial tiap role, dan ID pada `params`.
 *   3. Pastikan aplikasi berjalan (Laragon / php artisan serve).
 *   4. npm run capture:ui
 *
 * Hasil: docs/figma/screenshots/<role>/<id>.png + laporan.json
 *
 * akun.local.json TIDAK PERNAH di-commit (sudah masuk .gitignore).
 */

import { chromium } from 'playwright';
import { readFileSync, writeFileSync, mkdirSync, existsSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '../..');

const PATH_AKUN = resolve(__dirname, 'akun.local.json');
const PATH_LAYAR = resolve(ROOT, 'docs/figma/daftar-layar.json');
const DIR_OUT = resolve(ROOT, 'docs/figma/screenshots');

// ── Konfigurasi ────────────────────────────────────────────────────────────────

if (!existsSync(PATH_AKUN)) {
    console.error(`
✖ Berkas kredensial belum ada: tools/ui-capture/akun.local.json

  Salin dulu templatenya, lalu isi:
    cp tools/ui-capture/akun.contoh.json tools/ui-capture/akun.local.json
`);
    process.exit(1);
}

const konfig = JSON.parse(readFileSync(PATH_AKUN, 'utf8'));
const { layar, viewport } = JSON.parse(readFileSync(PATH_LAYAR, 'utf8'));

const baseUrl = (konfig.baseUrl || 'http://localhost').replace(/\/+$/, '');
const params = konfig.params || {};
const akun = konfig.akun || {};
const hanyaRole = process.argv.slice(2).filter((a) => !a.startsWith('-'));

// ── Utilitas ───────────────────────────────────────────────────────────────────

/** Ganti {param} pada URI dengan ID nyata dari konfigurasi. Null bila ada yang belum diisi. */
function bangunUrl(item) {
    let uri = item.uri === '/' ? '' : item.uri;
    for (const p of item.params) {
        const nilai = params[item.id]?.[p] ?? params[p];
        if (nilai === undefined || nilai === null || nilai === '') return null;
        uri = uri.replace(`{${p}}`, encodeURIComponent(String(nilai)));
    }
    return `${baseUrl}/${uri}`.replace(/([^:]\/)\/+/g, '$1');
}

/** Login satu kali per role. Mengembalikan pesan galat, atau null bila berhasil. */
async function login(page, role) {
    const kredensial = akun[role];
    if (!kredensial?.login || !kredensial?.password) {
        return `kredensial role "${role}" belum diisi di akun.local.json`;
    }

    await page.goto(`${baseUrl}/login`, { waitUntil: 'domcontentloaded' });

    // Kalau Turnstile ternyata aktif, otomasi tidak akan bisa lewat — hentikan dengan jelas.
    if (await page.locator('.cf-turnstile').count()) {
        return 'Cloudflare Turnstile aktif di halaman login — matikan TURNSTILE_SITE_KEY di .env lokal';
    }

    await page.fill('input[name="login"]', kredensial.login);
    await page.fill('input[name="password"]', kredensial.password);
    await Promise.all([
        page.waitForLoadState('networkidle'),
        page.click('button[type="submit"]'),
    ]);

    if (new URL(page.url()).pathname.replace(/\/$/, '') === '/login') {
        const galat = await page.locator('.alert-danger, .text-red-500, .invalid-feedback')
            .first().textContent().catch(() => null);
        return `login gagal${galat ? `: ${galat.trim().slice(0, 120)}` : ''}`;
    }
    return null;
}

/** Ambil screenshot satu layar. Mengembalikan objek hasil. */
async function tangkap(page, item) {
    const url = bangunUrl(item);
    if (!url) {
        return { ...ringkas(item), status: 'dilewati', alasan: `params belum diisi: ${item.params.join(', ')}` };
    }

    try {
        const respon = await page.goto(url, { waitUntil: 'networkidle', timeout: 30_000 });
        const kode = respon?.status() ?? 0;

        if (kode >= 400) {
            return { ...ringkas(item), status: 'gagal', alasan: `HTTP ${kode}`, url };
        }

        // Terdeteksi dilempar balik ke halaman lain (mis. wali kelas belum memilih kelas).
        const tujuan = new URL(page.url()).pathname.replace(/\/$/, '');
        const diminta = new URL(url).pathname.replace(/\/$/, '');
        const dialihkan = tujuan !== diminta;

        // Beri jeda agar Chart.js dan animasi kartu selesai render.
        await page.waitForTimeout(1200);

        const berkas = resolve(DIR_OUT, item.role, `${item.id}.png`);
        mkdirSync(dirname(berkas), { recursive: true });
        await page.screenshot({ path: berkas, fullPage: true });

        return {
            ...ringkas(item),
            status: dialihkan ? 'dialihkan' : 'ok',
            url,
            ...(dialihkan && { alasan: `dialihkan ke ${tujuan}` }),
            berkas: berkas.replace(ROOT + '\\', '').replace(ROOT + '/', ''),
        };
    } catch (e) {
        return { ...ringkas(item), status: 'gagal', alasan: e.message.split('\n')[0].slice(0, 140), url };
    }
}

const ringkas = (item) => ({ id: item.id, role: item.role, frame: item.frame });

const IKON = { ok: '✓', dialihkan: '→', dilewati: '·', gagal: '✖' };

// ── Alur utama ─────────────────────────────────────────────────────────────────

const browser = await chromium.launch();
const hasil = [];

// Kelompokkan layar per role, hormati urutan di daftar-layar.json.
const perRole = new Map();
for (const item of layar) {
    if (hanyaRole.length && !hanyaRole.includes(item.role)) continue;
    if (!perRole.has(item.role)) perRole.set(item.role, []);
    perRole.get(item.role).push(item);
}

if (!perRole.size) {
    console.error(`✖ Tidak ada layar cocok untuk role: ${hanyaRole.join(', ')}`);
    await browser.close();
    process.exit(1);
}

for (const [role, daftar] of perRole) {
    console.log(`\n▌ ${role.toUpperCase()} — ${daftar.length} layar`);

    const context = await browser.newContext({
        viewport: { width: viewport.width, height: viewport.height },
        deviceScaleFactor: 2, // hasil tajam untuk lampiran skripsi
        locale: 'id-ID',
    });
    const page = await context.newPage();

    const butuhLogin = daftar.some((d) => d.auth);
    if (butuhLogin) {
        const galat = await login(page, role);
        if (galat) {
            console.log(`  ✖ ${galat}`);
            for (const item of daftar) {
                hasil.push({ ...ringkas(item), status: 'gagal', alasan: galat });
            }
            await context.close();
            continue;
        }
        console.log(`  ✓ login berhasil`);
    }

    for (const item of daftar) {
        const r = await tangkap(page, item);
        hasil.push(r);
        const catatan = r.alasan ? ` — ${r.alasan}` : '';
        console.log(`  ${IKON[r.status]} ${item.frame}${catatan}`);
    }

    await context.close();
}

await browser.close();

// ── Laporan ────────────────────────────────────────────────────────────────────

const hitung = hasil.reduce((a, r) => ({ ...a, [r.status]: (a[r.status] || 0) + 1 }), {});
mkdirSync(DIR_OUT, { recursive: true });
writeFileSync(
    resolve(DIR_OUT, 'laporan.json'),
    JSON.stringify({ dijalankan: new Date().toISOString(), baseUrl, hitung, hasil }, null, 2),
    'utf8',
);

console.log(`
──────────────────────────────────────────
  Berhasil    : ${hitung.ok || 0}
  Dialihkan   : ${hitung.dialihkan || 0}   (halaman terbuka tapi bukan URL yang diminta)
  Dilewati    : ${hitung.dilewati || 0}   (params belum diisi di akun.local.json)
  Gagal       : ${hitung.gagal || 0}
──────────────────────────────────────────
  Hasil  : docs/figma/screenshots/
  Laporan: docs/figma/screenshots/laporan.json
`);

// Keluar dengan kode galat supaya kegagalan tidak lewat begitu saja.
process.exit(hitung.gagal ? 1 : 0);
