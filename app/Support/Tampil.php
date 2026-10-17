<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Pengganti pagination bernomor: daftar dimulai dari "langkah" data pertama, lalu tombol "Tampilkan semua"
 * (parameter ?tampil=semua) menampilkan seluruhnya. Lebih mudah di ponsel dan filter tetap terbawa.
 * Angka (?tampil=30) tetap diterima, dibulatkan ke kelipatan langkah.
 *
 * Halaman dengan dua daftar memakai nama parameter berbeda (mis. tampil_siswa) agar tidak saling memengaruhi.
 * Pasangan tampilannya adalah komponen <x-cleanflow.muat-lagi>.
 */
class Tampil
{
    public const LANGKAH = 15;

    /** Batas atas agar halaman tetap ringan; lebih dari ini pengguna diminta memakai pencarian/filter. */
    public const MAKS = 300;

    public const SEMUA = 'semua';

    /** Langkah per parameter yang dipakai pada request ini, dibaca komponen muat-lagi. */
    private static array $langkah = [];

    public static function jumlah(int $langkah = self::LANGKAH, string $param = 'tampil'): int
    {
        self::$langkah[$param] = $langkah;
        $diminta = request()->query($param, (string) $langkah);
        if ($diminta === self::SEMUA) {
            return self::MAKS;
        }
        $diminta = (int) $diminta;

        return min(self::MAKS, max($langkah, (int) ceil($diminta / $langkah) * $langkah));
    }

    public static function ambil(Builder|Relation|QueryBuilder $query, int $langkah = self::LANGKAH, string $param = 'tampil'): LengthAwarePaginator
    {
        return $query->paginate(self::jumlah($langkah, $param), ['*'], $param.'_halaman', 1)->withQueryString();
    }

    public static function langkahUntuk(string $param = 'tampil'): int
    {
        return self::$langkah[$param] ?? self::LANGKAH;
    }
}
