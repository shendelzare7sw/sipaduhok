# Blocked, Manual, dan External Items

## Kontrol periode

- Periode Pengujian Project: **April 2026 Minggu III–IV**.
- Current Retest / Technical Verification: **12 Agustus 2026**.
- Dokumen ini tidak menyatakan UAT, acceptance, atau approval telah dilakukan.

## SIT yang memerlukan manual technical execution

Tiga belas flow E2E berstatus `MANUAL/UAT REQUIRED` karena memerlukan interaksi browser dan pergantian beberapa role yang tidak dieksekusi penuh pada retest otomatis ini:

- `SIT-E2E-001` sampai `SIT-E2E-007`.
- `SIT-E2E-009` sampai `SIT-E2E-014`.

Label status mengikuti vocabulary dokumen eksekusi. Kebutuhan aktualnya adalah **manual technical multi-role test**; Codex tidak menjalankan atau mengklaim UAT.

## SIT blocked oleh external environment

| SIT ID | Dependency | Alasan BLOCKED |
|---|---|---|
| SIT-E2E-008 | Midtrans Sandbox end-to-end | Tidak ada evidence credential Sandbox/network/callback registration yang aman untuk dipakai. |
| SIT-INT-008 | Midtrans Snap/Transaction API | Tidak ada panggilan Sandbox aktual; production credential dan transaksi riil dilarang. |
| SIT-INT-009 | Midtrans callback end-to-end | Signature invalid telah diuji lokal, tetapi callback Sandbox/status lifecycle penuh belum tersedia. |
| SIT-INT-010 | Groq API | Tidak ada credential/provider execution yang aman pada evidence retest. |
| SIT-INT-011 | Google Gemini API | Tidak ada credential/provider execution yang aman pada evidence retest. |
| SIT-INT-013 | Cloudflare Turnstile | Secret/network conditional tidak diverifikasi end-to-end. |
| SIT-INT-014 | Laravel Mail end-to-end | PHPUnit memakai mailer `array`; delivery ke mail server tidak diverifikasi. |
| SIT-INT-015 | Private broadcast runtime | In-app notification diuji, tetapi provider/socket delivery privat tidak diverifikasi. |

Test Case external yang terkait dan berstatus BLOCKED mencakup pemulihan akun/email, Turnstile, Midtrans aktif/Sandbox, sinkronisasi eksternal Midtrans, serta AI provider. Tidak ada secret yang dicatat dan tidak ada transaksi finansial riil dilakukan.

## Gap sumber Test Case

DOCX menyatakan 171 skenario, tetapi hanya mempunyai 164 baris. ID berikut tidak mempunyai scenario/expected result untuk dieksekusi:

`TC-N-038`, `TC-N-039`, `TC-N-040`, `TC-N-041`, `TC-N-042`, `TC-N-043`, dan `TC-N-050`.

Ketujuh ID dipertahankan sebagai placeholder administratif `BLOCKED` pada CSV. Isi skenario tidak direkayasa. Lihat `DEF-001`.

## Item teknis yang belum dieksekusi penuh

- Browser E2E lintas sembilan role dan validasi tampilan/UX.
- Webhook settlement/idempotency aktual dan pembatalan duplicate pending secara end-to-end.
- Scheduler notification dengan kandidat H-1/H-3 dan verifikasi deduplikasi data.
- Promotion scheduler dengan due schedule paralel/retry dan rollback lengkap.
- Rapor request → approval → download sukses; retest saat ini hanya membuktikan token negatif.
- Signed file URL yang kedaluwarsa dan seluruh kombinasi whitelist MIME/path.
- Alumni active-route restriction dan siswa cross-mapel secara targeted end-to-end.
- Observer submission LMS → rekap nilai untuk seluruh tipe tugas/ujian.
- DOMPDF, Laravel Excel file round-trip, PDF text extraction, dan delivery broadcast aktual.

## Verifikasi deployment/production yang tetap terpisah

- Midtrans Production Approved/Configured/End-to-End Verified.
- Settlement/reconciliation merchant riil.
- Host cron/Task Scheduler yang menjalankan Laravel scheduler.
- Mail server production, private broadcasting, TLS/certificate, filesystem permission, dan backup/restore.
- Domain, HTTPS, worker/service, monitoring, serta callback URL production.

Tidak satu pun item tersebut boleh dinyatakan selesai dari SIT lokal ini.

## Human UAT dan acceptance

UAT pengguna, keputusan penerimaan mitra, BAST SIT, BAST UAT, BAST Deployment, tanda tangan, dan tanggal approval tidak dibuat. Semua itu memerlukan aktivitas serta bukti manusia yang terpisah.
