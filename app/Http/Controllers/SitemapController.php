<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        // 1. Static Public Pages
        $staticPages = [
            '', // Home
            'tentang-sekolah',
            'visi-misi',
            'struktur-organisasi',
            'profil-guru',
            'program-paud-tk',
            'program-sd-sma',
            'program-inklusi',
            'program-terapi',
            'fasilitas',
            'ppdb',
            'galeri',
            'kontak',
            'berita'
        ];

        // Use app's last deployment/modification date instead of now()
        // to avoid Google treating constantly-changing lastmod as manipulative
        $lastmod = cache()->remember('sitemap_lastmod', 3600, function () {
            return now()->toAtomString();
        });

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => url('/' . $page),
                'lastmod' => $lastmod,
                'changefreq' => $page == '' ? 'daily' : 'weekly',
                'priority' => $page == '' ? '1.0' : '0.8',
            ];
        }

        // 2. Dynamic News Pages (if any public single news view exists, else just the index which is added above)
        // If there's a route like /berita/{slug}, we would add them here:
        // $beritas = Berita::where('status', 'published')->get();
        // foreach ($beritas as $berita) {
        //     $urls[] = [
        //         'loc' => url('/berita/' . $berita->slug),
        //         'lastmod' => $berita->updated_at->toAtomString(),
        //         'changefreq' => 'monthly',
        //         'priority' => '0.6',
        //     ];
        // }

        $content = view('sitemap', compact('urls'))->render();

        return Response::make($content, 200, [
            'Content-Type' => 'text/xml'
        ]);
    }
}
