<?php

if (!function_exists('terbilang')) {
    /**
     * Convert number to Indonesian words
     * 
     * @param int|float $angka
     * @return string
     */
    function terbilang($angka)
    {
        $angka = abs($angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';

        if ($angka < 12) {
            $temp = ' ' . $huruf[$angka];
        } elseif ($angka < 20) {
            $temp = terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            $temp = terbilang($angka / 10) . ' Puluh' . terbilang($angka % 10);
        } elseif ($angka < 200) {
            $temp = ' Seratus' . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $temp = terbilang($angka / 100) . ' Ratus' . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $temp = ' Seribu' . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $temp = terbilang($angka / 1000) . ' Ribu' . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $temp = terbilang($angka / 1000000) . ' Juta' . terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $temp = terbilang($angka / 1000000000) . ' Miliar' . terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $temp = terbilang($angka / 1000000000000) . ' Triliun' . terbilang(fmod($angka, 1000000000000));
        }

        return trim($temp);
    }
}

/**
 * Buat URL preview file yang aman: token acak yang diikat ke user yang sedang login.
 * Menggantikan skema lama (id crc32 kecil / raw ?path=) yang bisa dienumerasi &
 * tidak terikat pemilik. File hanya bisa dibuka oleh user yang membuat token ini.
 *
 * @param  string|null  $path       Path relatif di disk 'public' (storage/app/public)
 * @param  int          $ttlHours   Masa berlaku token (jam)
 * @return string|null
 */
if (!function_exists('preview_url')) {
    function preview_url(?string $path, int $ttlHours = 4)
    {
        if (empty($path)) {
            return null;
        }

        $token = \Illuminate\Support\Str::random(48);

        \Illuminate\Support\Facades\Cache::put('docview_' . $token, [
            'path' => $path,
            'user_id' => auth()->id(),
        ], now()->addHours($ttlHours));

        return url('/view-document/' . $token);
    }
}

/**
 * Format jenis kegiatan untuk display
 * Menangani custom event types dengan benar
 */
if (!function_exists('format_jenis_kegiatan')) {
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
if (!function_exists('jenis_kegiatan_class')) {
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
