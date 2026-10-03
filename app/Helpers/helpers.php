<?php

if (! function_exists('terbilang')) {
    /**
     * Convert number to Indonesian words
     *
     * @param  int|float  $angka
     * @return string
     */
    function terbilang($angka)
    {
        $angka = abs($angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';

        if ($angka < 12) {
            $temp = ' '.$huruf[$angka];
        } elseif ($angka < 20) {
            $temp = terbilang($angka - 10).' Belas';
        } elseif ($angka < 100) {
            $temp = terbilang($angka / 10).' Puluh'.terbilang($angka % 10);
        } elseif ($angka < 200) {
            $temp = ' Seratus'.terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $temp = terbilang($angka / 100).' Ratus'.terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $temp = ' Seribu'.terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $temp = terbilang($angka / 1000).' Ribu'.terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $temp = terbilang($angka / 1000000).' Juta'.terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $temp = terbilang($angka / 1000000000).' Miliar'.terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $temp = terbilang($angka / 1000000000000).' Triliun'.terbilang(fmod($angka, 1000000000000));
        }

        return trim($temp);
    }
}

/**
 * Buat URL preview file yang aman: token acak yang diikat ke user yang sedang login.
 * Menggantikan skema lama (id crc32 kecil / raw ?path=) yang bisa dienumerasi &
 * tidak terikat pemilik. File hanya bisa dibuka oleh user yang membuat token ini.
 *
 * @param  string|null  $path  Path relatif di disk 'public' (storage/app/public)
 * @param  int  $ttlHours  Masa berlaku token (jam)
 * @return string|null
 */
if (! function_exists('preview_url')) {
    function preview_url(?string $path, int $ttlHours = 4)
    {
        if (empty($path)) {
            return null;
        }

        $token = \Illuminate\Support\Str::random(48);

        \Illuminate\Support\Facades\Cache::put('docview_'.$token, [
            'path' => $path,
            'user_id' => auth()->id(),
        ], now()->addHours($ttlHours));

        return url('/view-document/'.$token);
    }
}

/**
 * Format jenis kegiatan untuk display
 * Menangani custom event types dengan benar
 */
if (! function_exists('format_jenis_kegiatan')) {
    function format_jenis_kegiatan($jenisKegiatan)
    {
        $labels = [
            'field_trip' => 'Field Trip',
            'outing' => 'Outing',
            'live_in' => 'Live In',
            'hokfest' => 'HOK Fest',
            'pts' => 'PTS',
            'pas' => 'PAS',
            'libur' => 'Libur',
            'ujian' => 'Ujian',
            'acara_sekolah' => 'Acara Sekolah',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$jenisKegiatan] ?? ucwords(str_replace('_', ' ', $jenisKegiatan));
    }
}

/**
 * Mendapatkan CSS class untuk jenis kegiatan
 * Untuk custom types, gunakan fallback class
 */
if (! function_exists('jenis_kegiatan_class')) {
    function jenis_kegiatan_class($jenisKegiatan)
    {
        $predefined = ['field_trip', 'outing', 'live_in', 'hokfest', 'pts', 'pas', 'libur', 'ujian', 'acara_sekolah', 'lainnya'];

        // Jika predefined, return as is
        if (in_array($jenisKegiatan, $predefined)) {
            return $jenisKegiatan;
        }

        // Custom type, gunakan 'lainnya' sebagai fallback class
        return 'lainnya';
    }
}

/**
 * Normalisasi nomor telepon Indonesia ke format internasional WhatsApp (628xxxx).
 * Menerima "0812-3456 789", "+62 812...", "62812...", "812..." → "62812...".
 * Mengembalikan null bila tidak ada digit yang layak.
 */
if (! function_exists('wa_nomor')) {
    function wa_nomor(?string $nomor): ?string
    {
        $digit = preg_replace('/\D+/', '', (string) $nomor);

        if ($digit === '' || strlen($digit) < 8) {
            return null;
        }

        if (str_starts_with($digit, '0')) {
            $digit = '62' . ltrim($digit, '0');
        } elseif (! str_starts_with($digit, '62')) {
            // Nomor lokal tanpa awalan 0 (mis. 8123...) dianggap nomor Indonesia.
            $digit = '62' . $digit;
        }

        return $digit;
    }
}

/**
 * Tautan direct chat WhatsApp (https://wa.me/628xxxx[?text=...]) atau null bila nomor tidak valid.
 */
if (! function_exists('wa_link')) {
    function wa_link(?string $nomor, ?string $pesan = null): ?string
    {
        $digit = wa_nomor($nomor);

        if ($digit === null) {
            return null;
        }

        return 'https://wa.me/' . $digit . ($pesan !== null && $pesan !== '' ? '?text=' . rawurlencode($pesan) : '');
    }
}

/**
 * Warna solid (Tailwind) per jenis kegiatan kalender — palet yang sama dengan kalender
 * Siswa lama. ['bg' => latar + teks chip, 'dot' => warna titik/penanda].
 */
if (! function_exists('jenis_kegiatan_tone')) {
    function jenis_kegiatan_tone($jenisKegiatan): array
    {
        $tones = [
            'field_trip' => ['bg' => 'bg-[#17a2b8] text-white', 'dot' => 'bg-[#17a2b8]'],
            'outing' => ['bg' => 'bg-[#28a745] text-white', 'dot' => 'bg-[#28a745]'],
            'live_in' => ['bg' => 'bg-[#6610f2] text-white', 'dot' => 'bg-[#6610f2]'],
            'hokfest' => ['bg' => 'bg-[#fd7e14] text-white', 'dot' => 'bg-[#fd7e14]'],
            'pts' => ['bg' => 'bg-[#ffc107] text-[#212529]', 'dot' => 'bg-[#ffc107]'],
            'pas' => ['bg' => 'bg-[#dc3545] text-white', 'dot' => 'bg-[#dc3545]'],
            'libur' => ['bg' => 'bg-[#6c757d] text-white', 'dot' => 'bg-[#6c757d]'],
            'ujian' => ['bg' => 'bg-[#e83e8c] text-white', 'dot' => 'bg-[#e83e8c]'],
            'acara_sekolah' => ['bg' => 'bg-[#20c997] text-white', 'dot' => 'bg-[#20c997]'],
            'tugas' => ['bg' => 'bg-[#0891b2] text-white', 'dot' => 'bg-[#0891b2]'],
            'deadline' => ['bg' => 'bg-[#2563eb] text-white', 'dot' => 'bg-[#2563eb]'],
            'lainnya' => ['bg' => 'bg-[#007bff] text-white', 'dot' => 'bg-[#007bff]'],
        ];

        return $tones[$jenisKegiatan] ?? $tones['lainnya'];
    }
}

/**
 * Redirect kembali ke URL list/halaman asal (termasuk nomor halaman & filter)
 * yang dikirim lewat hidden field `_return_url` (diisi dari url()->previous()
 * saat halaman edit/detail dirender). Dipakai supaya simpan/hapus di halaman
 * edit tidak selalu melempar user ke halaman 1 index tanpa filter.
 *
 * Host divalidasi (bukan sekadar str_starts_with) supaya _return_url yang
 * dimanipulasi ke domain lain (mis. https://app.test.evil.com) tidak lolos.
 */
if (! function_exists('redirect_to_previous')) {
    function redirect_to_previous(string $fallbackRoute, array $fallbackParams = [])
    {
        $returnUrl = request()->input('_return_url');

        if (is_string($returnUrl) && $returnUrl !== '' &&
            parse_url($returnUrl, PHP_URL_HOST) === parse_url(url('/'), PHP_URL_HOST)) {
            return redirect()->to($returnUrl);
        }

        return redirect()->route($fallbackRoute, $fallbackParams);
    }
}

/**
 * Ubah input nominal rupiah berformat Indonesia menjadi angka mentah.
 *
 * MASALAH YANG DICEGAH: form uang memakai pemisah ribuan TITIK ("200.000"),
 * sedangkan PHP membaca titik sebagai pemisah DESIMAL - is_numeric("200.000")
 * bernilai true dan (int)"200.000" menghasilkan 200. Jadi tagihan Rp 200.000
 * tersimpan diam-diam sebagai Rp 200: lolos validasi 'numeric', tanpa error,
 * dan baru ketahuan setelah datanya salah.
 *
 * Pembersihan titik sebenarnya sudah dilakukan JavaScript sebelum submit, tapi
 * nominal uang tidak boleh bergantung pada sisi klien - cukup satu kegagalan JS
 * (error skrip, autofill, input yang ditambah dinamis, atau request non-browser)
 * dan angkanya berubah tanpa jejak. Karena itu normalisasi diulang di server.
 *
 * Aturan: titik = pemisah ribuan (dibuang). Koma = desimal (dijadikan titik),
 * mengikuti kebiasaan penulisan Indonesia.
 */
if (! function_exists('ai_model_aktif')) {
    /**
     * Kembalikan nama model AI yang MASIH hidup.
     *
     * Groq/Google rutin mematikan model lama. Kalau setting di database masih
     * menunjuk model mati, permintaan API dibalas 404 dan di layar hanya muncul
     * "Gagal terhubung ke Groq API. Periksa API Key" — menyesatkan, karena API
     * key-nya sebenarnya baik-baik saja. Fungsi ini memetakan model pensiun ke
     * penggantinya (lihat config/ai-models.php) tanpa perlu guru mengubah apa pun.
     *
     * @param  string|null  $model     Nama model dari setting.
     * @param  string       $provider  'groq' atau 'gemini'.
     * @param  bool         $vision    True kalau butuh model pembaca gambar.
     */
    function ai_model_aktif(?string $model, string $provider = 'groq', bool $vision = false): string
    {
        $default = config(
            ($vision ? 'ai-models.default_vision.' : 'ai-models.default_text.') . $provider
        ) ?? config('ai-models.default_text.groq');

        $model = trim((string) $model);
        if ($model === '') {
            return $default;
        }

        // Nama model mengandung titik (llama-3.3-…, gemini-2.5-…), jadi JANGAN pakai
        // notasi titik config('ai-models.retired.'.$model) — selalu gagal cocok.
        $pengganti = config('ai-models.retired', [])[$model] ?? null;
        if ($pengganti) {
            \Illuminate\Support\Facades\Log::warning(
                "Model AI '{$model}' sudah dimatikan penyedianya, dialihkan ke '{$pengganti}'."
            );

            return $pengganti;
        }

        return $model;
    }
}

if (! function_exists('ai_model_cadangan')) {
    /**
     * Model pengganti sementara saat model utama kena rate limit.
     * Sengaja memilih model dari "keluarga" berbeda supaya kuotanya terpisah.
     */
    function ai_model_cadangan(string $modelUtama): string
    {
        $tersedia = config('ai-models.available.groq', []);

        foreach ($tersedia as $model => $info) {
            // Lewati model yang sama & model vision (lebih mahal untuk tugas teks).
            if ($model === $modelUtama) {
                continue;
            }
            if ($info['vision'] ?? false) {
                continue;
            }

            return $model;
        }

        return config('ai-models.default_text.groq');
    }
}

if (! function_exists('ai_rantai_model')) {
    /**
     * Urutan model yang dicoba bila model sebelumnya gagal (kuota habis, model dicabut,
     * server sibuk): model pilihan lebih dulu, lalu model teks lain dari config, lalu
     * model vision (tetap bisa mengerjakan teks). Semua kuota per model terpisah.
     */
    function ai_rantai_model(string $provider, ?string $mulai = null): array
    {
        $tersedia = config("ai-models.available.{$provider}", []);
        $teks = array_keys(array_filter($tersedia, fn ($info) => ! ($info['vision'] ?? false)));
        $vision = array_keys(array_filter($tersedia, fn ($info) => $info['vision'] ?? false));

        $awal = ai_model_aktif($mulai, $provider);

        return array_values(array_unique(array_merge([$awal], $teks, $vision)));
    }
}

if (! function_exists('ai_groq_payload')) {
    /**
     * Lengkapi payload chat/completions Groq dengan parameter khusus model
     * (config('ai-models.groq_params')): penalaran disembunyikan dari content
     * dan anggaran max_tokens ditambah untuk token penalaran model bernalar.
     * Semua pemanggil Groq wajib lewat sini agar pergantian model cukup diatur di config.
     */
    function ai_groq_payload(array $payload): array
    {
        $aturan = config('ai-models.groq_params', [])[$payload['model'] ?? ''] ?? [];

        foreach ($aturan['params'] ?? [] as $kunci => $nilai) {
            $payload[$kunci] = $nilai;
        }

        if (! empty($aturan['extra_tokens']) && isset($payload['max_tokens'])) {
            $payload['max_tokens'] = (int) $payload['max_tokens'] + (int) $aturan['extra_tokens'];
        }

        return $payload;
    }
}

if (! function_exists('ai_model_tidak_ditemukan')) {
    /**
     * True bila respons error Groq/OpenAI-compatible menyatakan model tidak ada
     * atau tidak bisa diakses API key ini (contoh: model dipindah ke paket Enterprise).
     */
    function ai_model_tidak_ditemukan(?int $status, ?string $body): bool
    {
        $body = strtolower((string) $body);

        return $status === 404
            || str_contains($body, 'model_not_found')
            || str_contains($body, 'model_decommissioned')
            || str_contains($body, 'does not exist or you do not have access');
    }
}

if (! function_exists('rupiah_to_number')) {
    function rupiah_to_number($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return $value;
        }

        $bersih = preg_replace('/[^\d,.\-]/', '', (string) $value);
        $bersih = str_replace('.', '', $bersih);
        $bersih = str_replace(',', '.', $bersih);

        return $bersih === '' ? null : $bersih;
    }
}

/**
 * Normalisasi beberapa field nominal sekaligus pada Request, sebelum validasi.
 * Mendukung notasi titik untuk array, mis. 'tagihan.*'.
 */
if (! function_exists('normalisasi_input_rupiah')) {
    function normalisasi_input_rupiah(\Illuminate\Http\Request $request, array $fields): void
    {
        foreach ($fields as $field) {
            if (str_contains($field, '*')) {
                $base = rtrim(strtok($field, '*'), '.');
                $nilai = $request->input($base);
                if (is_array($nilai)) {
                    $request->merge([$base => array_map('rupiah_to_number', $nilai)]);
                }

                continue;
            }

            if ($request->has($field)) {
                $request->merge([$field => rupiah_to_number($request->input($field))]);
            }
        }
    }
}
