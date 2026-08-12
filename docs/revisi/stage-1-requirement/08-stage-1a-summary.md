# SIPADUHOK Stage 1A — Requirement Reconciliation Summary

## A. Baseline

| Item | Baseline |
|---|---|
| Repository branch | `latihan-sidang` |
| Commit | `c813efea73a20eb721b39cc3c9a56ea39106a75b` |
| Report | `docs/laporan akhir.pdf` — 186 halaman PDF; laporan utama hlm. 9–22; Dokumen Teknis hlm. 38–175 |
| Discovery source | `docs/revisi/discovery/01-repository-audit.md` sampai `11-role-menu-inventory.md` |
| Evidence priority | Laporan untuk historical/documented requirement; source final untuk perilaku as-built; Stage 0 sebagai indeks |

## B. Statistics

| Item | Count |
|---|---:|
| Documented requirements | 56 |
| As-built requirements | 101 |
| Reconciliation records | 102 |
| MATCH | 36 |
| EXPANDED | 38 |
| CHANGED | 9 |
| IMPLEMENTATION_ONLY | 18 |
| DOCUMENT_ONLY | 0 |
| LEGACY/OBSOLETE | 1 |
| NEED_CONFIRMATION sebagai reconciliation status | 0 |
| Formal requirement candidates | 27 |
| NFR | 20 |
| Human decisions required | 10 |

Satu reconciliation record tambahan di luar 101 as-built adalah presensi otomatis berdasarkan aktivitas LMS yang terdokumentasi dan masih memiliki method tanpa route aktif; statusnya `LEGACY_OR_OBSOLETE`.

## C. Major Scope Differences

### A. Documented Initial/Project Requirement

Laporan utama halaman 9–22 mendefinisikan platform LMS+SIA untuk SMP/SMA dengan empat aktor utama: Admin, Wali Kelas, Guru Pengajar, dan Siswa. Scope utamanya adalah pengguna/kelas/jadwal, materi/tugas/ujian/forum, presensi, nilai/rapor, notifikasi, pembayaran Siswa melalui manual/Midtrans, profil, dan monitoring aktivitas.

### B. Final As-Built Requirement

Source final mempunyai 101 requirement dalam 15 modul, sembilan role, scope cabang, portal Orang Tua, publikasi/CMS, recovery, payment dan audit yang lebih lengkap, rapor multi-stage, kenaikan kelas multi-role, monitoring LMS, notification center, arsip LMS, AI, serta pembatasan alumni.

### C. Iterative Additions

Dokumen Teknis halaman 38–175 telah mencatat sebagian besar perkembangan seperti sembilan role, kenaikan kelas, konten publikasi, recovery, catatan monitoring, akses keuangan, dan Orang Tua sebagai pembayar. Namun tidak ada change log bertanggal yang membuktikan kapan/siapa meminta perubahan. Fitur tanpa requirement bisnis ekuivalen seperti keamanan recovery Admin, account/profile settings, import/template, sebagian otomasi jadwal, AI, pengawasan ujian, arsip LMS, dan alumni diklasifikasikan `IMPLEMENTATION_ONLY`/`ITERATIVE ADDITION` sampai dikonfirmasi.

Perubahan paling signifikan:

1. Aktor berkembang dari 4 menjadi 9 role.
2. Pembayaran, izin, dan akses rapor berpindah dari Siswa ke Orang Tua/Wali Siswa.
3. Rapor berubah dari upload PDF oleh Wali menjadi dokumen terstruktur dengan validasi Ketua dan request download Orang Tua.
4. Presensi otomatis yang dijanjikan laporan tidak mempunyai route aktif final.
5. Kenaikan kelas, konten publik, recovery, monitoring LMS/catatan, audit finansial, AI, dan arsip berkembang di luar sepuluh modul awal.
6. Portal pembayaran Siswa masih dapat membuka tagihan/riwayat/cetak bukti secara direct URL, tetapi menu tersembunyi dan submit pembayaran selalu ditolak.

## D. Role Differences

- Role awal yang tetap: Admin, Wali Kelas, Guru Pengajar, Siswa.
- Role final tambahan: Ketua PKBM, Wakil Kepala Sekolah, Sekretaris, Bendahara, Orang Tua/Wali Siswa.
- Siswa tetap merupakan role final, tetapi kehilangan aksi pengajuan izin, pembayaran, dan akses rapor aktif dibanding laporan utama.
- `orang_tua`, `wakil_kepala_sekolah`, dan prefix `wali`/`wali-siswa` perlu istilah bisnis konsisten.
- Role tambahan tercatat pada Dokumen Teknis halaman 135, tetapi tidak boleh disebut sebagai aktor requirement awal tanpa wording temporal yang tepat.

## E. Payment Requirement Status

Payment Midtrans **implemented as code**: Snap single/bulk, payment pending, callback/webhook bertanda tangan, Transaction API, mapping status, sinkronisasi tagihan, audit, notifikasi, dan konfigurasi kanal ditemukan.

Perbedaannya adalah aktor: laporan utama menyebut Siswa membayar, sedangkan final menempatkan Orang Tua sebagai pembayar. Siswa hanya dapat melihat tagihan/riwayat/cetak bukti; endpoint submit menolak dan mengarahkan ke Wali Siswa.

**[PRODUCTION ACTIVATION PERLU KONFIRMASI]** untuk credential/mode, notification URL, merchant ownership, transaksi settlement nyata, refund/chargeback, dan reconciliation. Provider baseline tetap Midtrans; tidak ada provider alternatif.

## F. Requirement Suitable for Formal Approval

Requirement sudah cukup matang untuk disusun menjadi **dokumen requirement baseline saat ini**, menggunakan 27 formal requirement candidate dengan traceability ke 101 requirement detail.

Belum seluruhnya siap dinyatakan disetujui karena terdapat perubahan aktor/workflow dan implementation-only scope yang membutuhkan keputusan manusia. Formal document harus menyebutnya sebagai **current validated/as-built baseline**, bukan original pre-development approval.

## G. Requirements Needing Human Decision

1. **DECISION-001**
   - **Decision:** Apakah sembilan role final disetujui sebagai scope operasional resmi.
   - **Why:** Laporan utama memakai empat aktor; Dokumen Teknis/source memakai sembilan.
   - **Options:** Setujui seluruh role final; batasi role tertentu; gabungkan fungsi role.
   - **Recommended interpretation:** Validasi sembilan role final sesuai implementasi, dengan istilah bisnis baku.
   - **Evidence:** Laporan hlm. 13, 17–20; Dokumen Teknis hlm. 135; `RoleSeeder.php`.

2. **DECISION-002**
   - **Decision:** Tetapkan aktor pembayaran final dan hak read-only Siswa.
   - **Why:** Laporan menyebut Siswa membayar; final memakai Orang Tua, sementara route read-only Siswa masih aktif tetapi tersembunyi.
   - **Options:** Orang Tua membayar dan Siswa read-only; Orang Tua saja tanpa route Siswa; aktifkan kembali pembayaran Siswa.
   - **Recommended interpretation:** Orang Tua/Wali Siswa menjadi pembayar; Siswa read-only hanya jika PKBM memang menghendakinya.
   - **Evidence:** Laporan hlm. 13, 16, 19–20; technical hlm. 139, 165–166; route/controller Siswa dan Orang Tua.

3. **DECISION-003**
   - **Decision:** Tetapkan aktor pengajuan izin final.
   - **Why:** Laporan menunjuk Siswa; route final menunjuk Orang Tua.
   - **Options:** Orang Tua saja; Siswa saja; keduanya dengan aturan berbeda.
   - **Recommended interpretation:** Orang Tua mengajukan, Siswa melihat, Wali Kelas memvalidasi, sesuai source final.
   - **Evidence:** Laporan hlm. 10, 13, 15, 19, 21; `routes/web.php:1426-1433,1576-1584`.

4. **DECISION-004**
   - **Decision:** Setujui workflow rapor final.
   - **Why:** Upload PDF/akses Siswa pada laporan berubah menjadi generate–validasi Ketua–request Orang Tua–approval Wali.
   - **Options:** Pertahankan final; sederhanakan; pulihkan akses Siswa terjadwal.
   - **Recommended interpretation:** Validasi workflow final dan perlakukan source rapor Siswa tanpa route sebagai legacy.
   - **Evidence:** Laporan hlm. 11, 13, 15, 18–19, 21; technical hlm. 82, 84, 88; RAP routes/controllers.

5. **DECISION-005**
   - **Decision:** Apakah presensi otomatis berdasarkan aktivitas LMS tetap diperlukan.
   - **Why:** Requirement terdokumentasi; method ada tetapi tidak aktif.
   - **Options:** Keluarkan dari baseline; aktifkan pada tahap perubahan terpisah; pertahankan sebagai future scope.
   - **Recommended interpretation:** Keluarkan dari baseline aktif sampai ada keputusan eksplisit dan desain trigger yang aman.
   - **Evidence:** Laporan hlm. 10, 15, 20; `SiaPresensiController::presensiOtomatis`; tidak ada route aktif.

6. **DECISION-006**
   - **Decision:** Terima atau keluarkan 18 implementation-only requirement dari scope final formal.
   - **Why:** Source aktif tidak sama dengan bukti original requirement.
   - **Options:** Terima seluruhnya; pilih sebagian; jadikan internal/non-contractual.
   - **Recommended interpretation:** Review per formal requirement, khususnya AI, pengawasan ujian, arsip LMS, recovery/security, import, dan alumni.
   - **Evidence:** `02-requirement-reconciliation.md`; status `IMPLEMENTATION_ONLY`.

7. **DECISION-007**
   - **Decision:** Tetapkan status modul kenaikan kelas sebagai scope final.
   - **Why:** Tidak ada pada sepuluh modul awal tetapi tercatat pada Dokumen Teknis dan implemented luas.
   - **Options:** Setujui sebagai iterative addition; keluarkan; setujui sebagian tanpa scheduler/rollback.
   - **Recommended interpretation:** Validasi sebagai iterative addition karena mempunyai workflow multi-role dan data aktif.
   - **Evidence:** Technical hlm. 62–63, 81, 85, 153–155; PRM source.

8. **DECISION-008**
   - **Decision:** Konfirmasi kesiapan operasional Midtrans production.
   - **Why:** Implementasi kode tidak membuktikan provider production aktif.
   - **Options:** Sandbox untuk validasi; production setelah bukti; payment manual sementara.
   - **Recommended interpretation:** Jangan klaim production aktif sebelum credential, callback, order, settlement, dan owner merchant diverifikasi.
   - **Evidence:** Payment source; GAP-010/011; `08-integration-payment-audit.md`.

9. **DECISION-009**
   - **Decision:** Bakukan istilah role untuk dokumen stakeholder.
   - **Why:** `orang_tua`/Wali Siswa dan `wakil_kepala_sekolah`/Waka tidak konsisten; Wali Kelas berpotensi tertukar dengan Wali Siswa.
   - **Options:** Istilah formal tunggal; istilah gabungan dengan nilai teknis.
   - **Recommended interpretation:** “Orang Tua/Wali Siswa (`orang_tua`)” dan “Wakil Kepala Sekolah/Waka (`wakil_kepala_sekolah`)”.
   - **Evidence:** RoleSeeder, routes, sidebar, `02-role-inventory.md`.

10. **DECISION-010**
    - **Decision:** Tentukan boundary runtime/deployment yang boleh dinyatakan verified.
    - **Why:** Cron, SMTP, broadcast, backup restore, DNS/SSL, storage permission, dan live server tidak dapat dibuktikan dari source.
    - **Options:** Verifikasi environment; tulis belum diverifikasi; keluarkan dari klaim BAST.
    - **Recommended interpretation:** Tandai belum diverifikasi sampai tersedia evidence runtime terpisah.
    - **Evidence:** NFR-013–020; GAP-016–019.

## H. BAST Recommendation

**READY FOR REQUIREMENT DOCUMENT: YES**

**READY FOR BAST REQUIREMENT: CONDITIONAL**

Syarat: keputusan DECISION-001 sampai DECISION-010 dicatat oleh pihak berwenang, requirement `NEED CONFIRMATION` diselesaikan, dan wording tidak memalsukan histori.

Recommended BAST wording:

> “Para pihak melakukan validasi dan persetujuan atas baseline kebutuhan sistem SIPADUHOK yang mencerminkan ruang lingkup serta perilaku aplikasi pada kondisi implementasi saat ini.”

Alternatif aman:

> “Dokumen ini merupakan konfirmasi kesesuaian kebutuhan sistem terhadap implementasi SIPADUHOK saat ini dan bukan pernyataan bahwa seluruh kebutuhan telah disetujui sebelum proses pengembangan.”

## I. Recommended Next Step

Lakukan sesi validasi manusia terhadap 27 formal requirement candidate dan sepuluh decision item. Catat keputusan tanpa backdate, lalu perbarui baseline candidate menjadi baseline final. **STOP setelah Stage 1A; jangan membuat BAST, DOCX, Test Case, SIT, UAT, FSD/TSD final, deployment document, atau cost budgeting pada tahap ini.**
