# Rekomendasi Review dan Approval Dokumen Testing

File ini adalah **rekomendasi**, bukan bukti review, approval, acceptance, atau tanda tangan.

## 1. Dasar pembagian SDM

Berdasarkan pembagian tanggung jawab pada Lampiran 2 laporan akhir:

- Yayan Wahyudi menangani Frontend/UI/UX serta secara eksplisit menulis dan menjalankan testing; direkomendasikan sebagai **Penyusun / QA**.
- Tabah Ujianto menangani Backend, integrasi Frontend–Backend, database, troubleshooting, dan bug fixing; direkomendasikan sebagai **Reviewer Teknis / Integrasi**.
- Irent Berliana Agustin adalah Ketua Tim, koordinator kebutuhan/flow dan komunikasi instansi; direkomendasikan sebagai **Reviewer / Koordinator Tim Project**.

Seluruh anggota tim tidak disebut sebagai QA karena tanggung jawabnya berbeda.

## 2. Rekomendasi reviewer Test Case

| Peran | Nama | Tanggal review | Approval/tanda tangan |
|---|---|---|---|
| Penyusun / QA | Yayan Wahyudi | `[DIISI SAAT REVIEW]` | `[DIISI MANUSIA]` |
| Reviewer Teknis / Integrasi | Tabah Ujianto | `[DIISI SAAT REVIEW]` | `[DIISI MANUSIA]` |
| Reviewer / Koordinator Tim Project | Irent Berliana Agustin | `[DIISI SAAT REVIEW]` | `[DIISI MANUSIA]` |
| Perwakilan Mitra (bila diperlukan) | `[DIISI BILA DIPERLUKAN]` | `[DIISI SAAT REVIEW]` | `[DIISI MANUSIA]` |

## 3. Rekomendasi reviewer SIT

| Peran | Nama | Tanggal review | Approval/tanda tangan |
|---|---|---|---|
| Penyusun / QA | Yayan Wahyudi | `[DIISI SAAT REVIEW]` | `[DIISI MANUSIA]` |
| Reviewer Teknis / Integrasi | Tabah Ujianto | `[DIISI SAAT REVIEW]` | `[DIISI MANUSIA]` |
| Reviewer / Koordinator Tim Project | Irent Berliana Agustin | `[DIISI SAAT REVIEW]` | `[DIISI MANUSIA]` |
| Perwakilan Mitra (bila diperlukan) | `[DIISI BILA DIPERLUKAN]` | `[DIISI SAAT REVIEW]` | `[DIISI MANUSIA]` |

Perwakilan Mitra tidak wajib menjadi reviewer artefak testing teknis internal. Keterlibatan mitra lebih relevan pada UAT, acceptance, BAST, implementasi/deployment, atau serah terima—setelah aktivitas tersebut benar-benar dilakukan.

## 4. Perbedaan istilah

| Istilah | Makna |
|---|---|
| Review Dokumen | Reviewer menilai skenario, expected result, traceability, dan cakupan layak dijadikan artefak/baseline. |
| Test Execution | Skenario benar-benar dijalankan dengan environment, langkah, hasil aktual, tanggal, dan evidence. |
| PASS/FAIL | Verdict per skenario berdasarkan perbandingan expected dan actual; review dokumen tidak otomatis membuat test PASS. |
| Acceptance/BAST | Keputusan formal pihak berwenang/mitra setelah exit criteria dan prasyarat terpenuhi; bukan hasil otomatis dari PHPUnit atau review teknis. |

## 5. Dasar timeline Lampiran 1 KAK

| Tahap | Periode KAK |
|---|---|
| Analisis Kebutuhan dan Persiapan | Desember 2025 |
| Perancangan | Desember 2025–Januari 2026 |
| Pengembangan | Januari–April 2026 |
| Pengujian | **April 2026 Minggu III–IV** |
| Implementasi dan Pelatihan | Mei 2026 Minggu I–II |
| Evaluasi | Mei 2026 Minggu III–IV |
| Serah Terima | Juni 2026 Minggu I |
| Pelaporan | Juni 2026 Minggu II–IV |

Periode original Test Case execution dan SIT yang direkomendasikan adalah **April 2026 Minggu III–IV**, mengikuti Timeline KAK.

## 6. Current technical retest

- Original Project Testing Period: **April 2026 Minggu III–IV**.
- Current Retest / Technical Verification oleh Codex: **12 Agustus 2026**.

Tanggal retest aktual tidak dipindahkan ke April 2026 dan tidak dipakai sebagai bukti bahwa pengujian historis terjadi pada tanggal tertentu.

## 7. Item yang membutuhkan tanggal/approval manusia

- Tanggal preparation/review historis yang spesifik jika ada evidence lama.
- Tanggal dan hasil review Test Case oleh ketiga reviewer.
- Tanggal dan hasil review SIT oleh ketiga reviewer.
- Keputusan apakah Perwakilan Mitra perlu dilibatkan pada dokumen teknis internal.
- Review manusia atas rekonsiliasi tujuh ID Test Case yang sudah diturunkan dari rule baseline; tidak ada lagi placeholder kosong.
- SIT completion dan exit criteria setelah seluruh manual/external item selesai.
- UAT, acceptance mitra, deployment verification, BAST terkait, dan final handover.

Gunakan `[PERLU KONFIRMASI TANGGAL HISTORIS]` atau `[NEEDS HUMAN DATE CONFIRMATION]` bila evidence tanggal belum tersedia. Jangan memakai modification time, commit date, atau timestamp source sebagai bukti tunggal aktivitas historis.
