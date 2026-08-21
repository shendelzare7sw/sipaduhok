# Payment Method Matrix

`SUPPORTED BY PROVIDER` berasal dari katalog resmi yang dikembalikan `GET /payment-methods` saat audit. `SUPPORTED BY APPLICATION` berarti parser/selector/label source dapat menggunakannya. `ACTIVATED IN PRODUCTION` hanya diberi ya jika katalog production untuk project dapat diambil secara read-only; ini tidak membuktikan transaksi berhasil.

| Method | Provider catalog | Application | Production catalog | Notes |
|---|---|---|---|---|
| QRIS | YES | YES | YES | Prioritas default pertama bila nominal memenuhi batas |
| VA (meta) | YES | YES | YES | User memilih bank di hosted checkout; opsi per-bank disembunyikan dari modal bila meta tersedia |
| BCA VA (`BCAVA`) | YES | INDIRECT via VA / label supported | YES | Response final dapat berupa kode bank |
| BNI VA (`BNIVA`) | YES | INDIRECT via VA / label supported | YES | Kode final yang diamati lokal termasuk `009` |
| BRI VA (`BRIVA`) | YES | INDIRECT via VA / label supported | YES | — |
| BSI VA (`BSIVA`) | YES | INDIRECT via VA / label supported | YES | — |
| CIMB Niaga VA (`CIMBVA`) | YES | INDIRECT via VA / label supported | YES | — |
| Danamon VA (`DANAMONVA`) | YES | INDIRECT via VA / label supported | YES | — |
| Mandiri VA (`MANDIRIVA`) | YES | INDIRECT via VA / label supported | YES | — |
| Maybank VA (`MAYBANKVA`) | YES | INDIRECT via VA / label supported | YES | — |
| OCBC VA (`OCBCVA`) | YES | INDIRECT via VA / label supported | YES | — |
| Permata VA (`PERMATAVA`) | YES | INDIRECT via VA / label supported | YES | — |
| Alfamart | YES | YES when returned by catalog | YES | Aplikasi generik menerima code provider; label tersedia |
| Indomaret | YES | YES when returned by catalog | YES | Aplikasi generik menerima code provider; label tersedia |

Katalog sandbox dan production yang diakses read-only masing-masing mengembalikan 14 entry pada 21 Agustus 2026. Source tidak meng-hardcode daftar aktif; kanal dan limit berasal dinamis dari credential project. Riwayat lokal Paywuz mengandung QRIS, VA, BNI-resolved, Alfamart, dan Indomaret, tetapi tidak membuktikan semua entry tersebut pernah dibayar production.

Situs Paywuz mempublikasikan dukungan umum QRIS dan VA berbagai bank, tetapi matrix di atas tidak menyamakan daftar pemasaran provider dengan aktivasi aplikasi. Sumber: [Paywuz](https://paywuz.id/) dan hasil API project yang disanitasi.
