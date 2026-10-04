<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    /**
     * Halaman publik yang boleh diindeks => slug konten CMS (Admin > Landing Pages) yang membentuknya.
     * null = halaman statis (isi ditulis di view). Berita membuka tautan luar, jadi cukup halaman /berita.
     */
    private const HALAMAN = [
        '' => 'home',
        'tentang-sekolah' => 'tentang-sekolah',
        'visi-misi' => 'visi-misi',
        'struktur-organisasi' => 'struktur-organisasi',
        'profil-guru' => 'profil-guru',
        'program-paud-tk' => 'program-paud-tk',
        'program-sd-sma' => 'program-sd-sma',
        'program-inklusi' => 'program-inklusi',
        'program-terapi' => 'program-terapi',
        'fasilitas' => 'fasilitas',
        'ppdb' => 'ppdb',
        'galeri' => 'galeri',
        'kontak' => 'kontak',
        'berita' => null,
        'kebijakan-privasi' => null,
        'syarat-ketentuan' => null,
    ];

    public function index()
    {
        try {
            $content = Cache::remember('sitemap_xml', 3600, fn () => view('sitemap', ['urls' => $this->daftarUrl()])->render());
        } catch (\Throwable $e) {
            // Jangan bocorkan detail server ke publik; cukup catat di log.
            Log::error('Gagal membuat sitemap: ' . $e->getMessage());

            return response('Sitemap sementara tidak tersedia.', 503, ['Content-Type' => 'text/plain; charset=utf-8', 'Retry-After' => '3600']);
        }

        return response($content, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    private function daftarUrl(): array
    {
        // lastmod = waktu konten halaman benar-benar terakhir diubah (bukan "sekarang"),
        // supaya Google mempercayai sinyal perubahan di sitemap.
        $perSlug = DB::table('landing_page_sections as s')
            ->join('landing_pages as p', 'p.id', '=', 's.landing_page_id')
            ->groupBy('p.slug')
            ->selectRaw('p.slug as slug, MAX(s.updated_at) as terakhir')
            ->pluck('terakhir', 'slug');
        $footer = $perSlug['footer'] ?? null;
        $beritaTerakhir = Berita::max('updated_at');

        $urls = [];
        foreach (self::HALAMAN as $path => $slug) {
            $kandidat = array_filter([
                $slug ? ($perSlug[$slug] ?? null) : null,
                $footer, // footer (kontak, tautan) tampil di semua halaman
                $path === 'berita' ? $beritaTerakhir : null,
                $slug === null && $path !== 'berita' ? $this->waktuView($path) : null,
            ]);
            $terakhir = $kandidat ? max(array_map(fn ($t) => Carbon::parse($t), $kandidat)) : null;

            $urls[] = [
                'loc' => $path === '' ? url('/') : url($path), // sama dengan canonical tiap halaman
                'lastmod' => $terakhir?->toAtomString(),
            ];
        }

        return $urls;
    }

    private function waktuView(string $path): ?string
    {
        $file = resource_path("views/{$path}.blade.php");

        return is_file($file) ? date(DATE_ATOM, filemtime($file)) : null;
    }
}
