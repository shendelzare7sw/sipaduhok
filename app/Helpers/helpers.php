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
