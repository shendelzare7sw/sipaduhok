# Rekonsiliasi Tujuh ID Test Case

## Kesimpulan

Defect dokumentasi `DEF-001` terjadi karena tujuh aturan otorisasi pada inventaris baseline tidak ikut diturunkan menjadi baris Test Case. Rekonsiliasi tidak menambah requirement atau rule baru: setiap ID Test Case dipasangkan satu-ke-satu dengan rule yang sudah ada dalam `docs/revisi/discovery/06-validation-business-rules.md`.

| Test Case ID | Sumber rule | Feature trace | Kondisi yang diuji | Source reference baseline | Hasil retest 12 Agustus 2026 |
|---|---|---|---|---|---|
| `TC-N-038` | `RULE-038` | `FEAT-001, 010` | Akun `is_active=false` tidak boleh login atau mempertahankan session. | `LoginRequest`; `EnsureUserIsActive` | `NOT EXECUTED` — belum ada eksekusi login/session end-to-end. |
| `TC-N-039` | `RULE-039` | `FEAT-015–026, 087–089` | Waka hanya mengelola data cabang pada akunnya. | Waka controllers; IDOR test | `PASS` — `WakaJadwalIdorTest`. |
| `TC-N-040` | `RULE-040` | `FEAT-018, 028, 048–064, 082` | Wali Kelas hanya mengakses current/historical assignment yang relevan. | Wali controllers; IDOR tests | `PASS` — empat targeted Wali IDOR tests. |
| `TC-N-041` | `RULE-041` | `FEAT-029, 053, 056, 065–079` | Guru hanya mengelola kelas-mapel yang diampu sesuai jenis aksi. | Guru controllers/services; IDOR tests | `PASS` — targeted Guru dan nilai-scope tests. |
| `TC-N-042` | `RULE-042` | `FEAT-067, 069, 073, 076–078, 094–095` | Siswa harus mempunyai profil/kelas dan mapel sesuai jadwal/agama. | `CheckSiswaMapelAccess` | `NOT EXECUTED` — kombinasi negatif belum diuji penuh. |
| `TC-N-043` | `RULE-043` | `FEAT-067, 069, 073, 076–078` | LMS hanya tersedia untuk jenjang yang diaktifkan pada `AppSetting`. | `CheckLmsAccess` | `NOT EXECUTED` — toggle jenjang belum diuji langsung. |
| `TC-N-050` | `RULE-050` | `FEAT-013, 035, 046, 080` | Pengaturan sensitif hanya dapat diakses Admin atau role eksplisit. | Route groups; `CheckRole` | `NOT EXECUTED` — middleware role telah teruji, tetapi seluruh direct URL sensitif belum diuji satu per satu. |

## Kontrol jumlah

- Positive Test Case: **101**.
- Negative Test Case: **70**.
- Total Test Case setelah rekonsiliasi: **171 baris unik**.
- ID berurutan: `TC-P-001`–`TC-P-101` dan `TC-N-001`–`TC-N-070`.
- Placeholder administratif: **0**.

## Kontrol tanggal

- Periode Pengujian Project tetap **April 2026 Minggu III–IV**.
- Evidence yang dicatat di sini adalah **Retest / Technical Verification 12 Agustus 2026**.
- Rekonsiliasi ini tidak membuktikan tanggal historis spesifik, UAT, acceptance, atau approval.
