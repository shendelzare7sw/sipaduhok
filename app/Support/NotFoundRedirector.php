<?php

namespace App\Support;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/**
 * Data yang sudah dihapus / tidak tersedia tidak boleh berakhir di halaman 404 polos.
 *
 * Untuk pengguna yang login dan membuka halaman ber-rute valid (mis. dari notifikasi forum
 * yang topiknya sudah dihapus guru), arahkan ke halaman induk terdekat dengan pesan, lalu
 * hapus notifikasi miliknya yang menunjuk ke URL tersebut. URL yang memang tidak ada,
 * permintaan AJAX/JSON, dan non-GET tetap memakai respons 404 standar.
 */
class NotFoundRedirector
{
    public const PESAN = 'Data yang Anda buka tidak ditemukan atau sudah dihapus.';

    private const PENANDA_SESI = '_not_found_redirect';

    public static function handle(Request $request): ?RedirectResponse
    {
        $route = $request->route();

        if (! Auth::check() || ! $route || ! $request->hasSession() || ! $request->isMethod('GET')
            || $request->expectsJson() || $request->ajax() || self::ruteBerkas($route)) {
            return null;
        }

        self::hapusNotifikasiMati($request);

        $sekarang = $request->fullUrl();
        // Pengaman putaran: bila halaman induk ternyata juga tidak ada, jatuh ke dashboard role.
        $sudahDialihkan = $request->session()->get(self::PENANDA_SESI) === $sekarang;
        $tujuan = $sudahDialihkan ? null : self::urlInduk($route, $sekarang);
        $tujuan ??= self::urlDashboard($route);

        if ($tujuan === $sekarang || rtrim($tujuan, '/') === rtrim($request->url(), '/')) {
            return null;
        }

        $request->session()->flash(self::PENANDA_SESI, $tujuan);

        return redirect()->to($tujuan)->with('warning', self::PESAN);
    }

    /**
     * Rute yang mengembalikan berkas/JSON (dibuka di iframe/tab baru/fetch) tetap 404 standar.
     */
    private static function ruteBerkas(\Illuminate\Routing\Route $route): bool
    {
        $nama = (string) $route->getName();

        return $nama === 'document.preview'
            || str_contains($nama, '.api.')
            || str_contains($nama, 'download')
            || str_contains($nama, 'preview-bukti')
            || str_starts_with($route->uri(), 'view-document');
    }

    /**
     * Cari rute induk: .../{a}/forum/{b} → forum.index dengan {a}; bila gagal naik satu tingkat lagi.
     */
    private static function urlInduk(\Illuminate\Routing\Route $route, string $sekarang): ?string
    {
        $nama = $route->getName();
        if (! $nama) {
            return null;
        }

        $bagian = explode('.', $nama);
        $params = $route->parameters();

        for ($panjang = count($bagian) - 1; $panjang >= 1; $panjang--) {
            $dasar = implode('.', array_slice($bagian, 0, $panjang));

            foreach ([$dasar . '.index', $dasar . '.show', $dasar] as $kandidat) {
                if ($kandidat === $nama || ! Route::has($kandidat)) {
                    continue;
                }

                // Coba parameter dari yang terlengkap; parameter terakhir biasanya ID data yang hilang.
                for ($jumlah = count($params) - 1; $jumlah >= 0; $jumlah--) {
                    try {
                        $url = route($kandidat, array_slice($params, 0, $jumlah, true));
                    } catch (\Throwable) {
                        continue;
                    }
                    if ($url !== $sekarang) {
                        return $url;
                    }
                }
            }
        }

        return null;
    }

    private static function urlDashboard(\Illuminate\Routing\Route $route): string
    {
        $awalan = explode('.', (string) $route->getName())[0] ?? '';
        $kandidat = $awalan !== '' ? $awalan . '.dashboard' : null;

        // Rute role (admin.dashboard, siswa.dashboard, ...) lalu rute generik "dashboard"
        // yang mengarahkan sesuai role (untuk rute bersama seperti notifikasi/profil).
        foreach (array_filter([$kandidat, 'dashboard']) as $nama) {
            if (Route::has($nama)) {
                try {
                    return route($nama);
                } catch (\Throwable) {
                    // coba kandidat berikutnya
                }
            }
        }

        return url('/');
    }

    /**
     * Notifikasi milik pengguna yang menunjuk ke URL yang datanya sudah hilang tidak berguna lagi.
     */
    private static function hapusNotifikasiMati(Request $request): void
    {
        try {
            $path = '/' . ltrim($request->path(), '/');
            Notification::where('user_id', Auth::id())
                ->where('link', 'like', '%' . $path)
                ->get(['id', 'link'])
                ->filter(fn ($n) => rtrim((string) parse_url($n->link, PHP_URL_PATH), '/') === rtrim($path, '/'))
                ->each->delete();
        } catch (\Throwable $e) {
            Log::warning('Gagal membersihkan notifikasi mati: ' . $e->getMessage());
        }
    }
}
