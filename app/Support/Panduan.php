<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Panduan singkat per halaman (bahasa awam), ditampilkan otomatis oleh layout SIA & LMS lewat
 * <x-cleanflow.panduan> sehingga penjelasan panjang tidak perlu memenuhi konten halaman.
 *
 * Isi dikelompokkan per area di App\Support\Panduan\*. Kunci = nama route; beberapa route boleh
 * berbagi isi (dipisah koma) dan boleh memakai pola (mis. "*.monitoring.pengguna"). Nama persis
 * selalu menang atas pola. Struktur isi: judul, isi (1–3 kalimat), langkah (opsional),
 * istilah (opsional: istilah => arti).
 */
class Panduan
{
    /** @var list<class-string> */
    private const SUMBER = [
        Panduan\Admin::class,
        Panduan\Akademik::class,
        Panduan\Keuangan::class,
        Panduan\Publikasi::class,
        Panduan\Pimpinan::class,
        Panduan\WaliKelas::class,
        Panduan\Guru::class,
        Panduan\Siswa::class,
        Panduan\WaliSiswa::class,
        Panduan\Umum::class,
    ];

    /**
     * @return array{judul: string, isi: string, langkah?: list<string>|null, istilah?: array<string, string>|null}|null
     */
    public static function untuk(?string $route, ?string $role = null): ?array
    {
        if ($route === null) {
            return null;
        }

        $pola = [];
        foreach (self::SUMBER as $sumber) {
            foreach ($sumber::daftar($role) as $kunci => $isi) {
                foreach (explode(',', $kunci) as $nama) {
                    $nama = trim($nama);
                    if ($nama === $route) {
                        return $isi;
                    }
                    if (str_contains($nama, '*')) {
                        $pola[$nama] ??= $isi;
                    }
                }
            }
        }

        foreach ($pola as $nama => $isi) {
            if (Str::is($nama, $route)) {
                return $isi;
            }
        }

        return null;
    }

    /**
     * Panduan halaman yang sedang dibuka beserta kunci penyimpanan status buka/tutupnya.
     *
     * @return array{0: array|null, 1: string|null}
     */
    public static function halamanIni(): array
    {
        $route = request()->route()?->getName();

        return [self::untuk($route, auth()->user()?->role), $route];
    }
}
