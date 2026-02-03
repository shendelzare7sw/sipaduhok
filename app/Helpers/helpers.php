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
