<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use Database\Seeders\LandingPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Kontrak CleanFlow halaman publik (landing + auth): Tailwind terkompilasi + Alpine lewat
 * layouts.landing / layouts.auth, tanpa Tailwind CDN, inline style, handler on*, maupun
 * aset CSS/JS per halaman. Konten tetap dibaca dari CMS /admin/landing-pages.
 */
class LandingPublicCleanFlowTest extends TestCase
{
    use RefreshDatabase;

    private const HALAMAN_PUBLIK = [
        '/', '/tentang-sekolah', '/visi-misi', '/struktur-organisasi', '/profil-guru',
        '/program-paud-tk', '/program-sd-sma', '/program-inklusi', '/program-terapi',
        '/fasilitas', '/ppdb', '/galeri', '/kontak', '/berita', '/kebijakan-privasi', '/syarat-ketentuan',
    ];

    private const HALAMAN_AUTH = ['/login', '/recovery', '/admin-recovery'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LandingPageSeeder::class);
    }

    public function test_semua_halaman_publik_dan_auth_memakai_tailwind_terkompilasi_tanpa_kode_kustom(): void
    {
        foreach ([...self::HALAMAN_PUBLIK, ...self::HALAMAN_AUTH] as $url) {
            // Halaman pemulihan admin hanya terbuka setelah "pintu rahasia" (klik logo 5x) membuka sesi.
            $html = $this->withSession(['admin_recovery_unlocked' => true])->get($url)->assertOk()->getContent();

            $this->assertStringContainsString('public-site', $html, "$url belum memakai aset public-site");
            $this->assertStringNotContainsString('cdn.tailwindcss.com', $html, "$url masih memakai Tailwind CDN");
            $this->assertStringNotContainsString('tailwind.config.js', $html, $url);
            $this->assertStringNotContainsString('resources/css/pages/', $html, $url);
            $this->assertStringNotContainsString('resources/js/pages/', $html, $url);
            $this->assertDoesNotMatchRegularExpression('/\sstyle="/', $html, "$url masih punya atribut style inline");
            $this->assertDoesNotMatchRegularExpression('/<style[\s>]/', $html, "$url masih punya tag <style>");
            $this->assertDoesNotMatchRegularExpression('/\son(click|change|mouseover|mouseout|input|submit)="/', $html, "$url masih punya handler on*");
        }
    }

    public function test_halaman_pemulihan_admin_tetap_terkunci_tanpa_pintu_rahasia(): void
    {
        $this->get('/admin-recovery')->assertRedirect(route('login'));
        $this->postJson('/admin-recovery/unlock')->assertOk()->assertJson(['success' => true]);
        $this->get('/admin-recovery')->assertOk()->assertSee('Pemulihan Akses Khusus');
    }

    public function test_navbar_menandai_menu_aktif_dan_footer_tampil(): void
    {
        $html = $this->get('/program-sd-sma')->assertOk()->getContent();

        $this->assertStringContainsString('x-data="navbarPublik"', $html);
        $this->assertMatchesRegularExpression('/aria-expanded="buka" class="[^"]*bg-white !text-primary[^"]*">\s*Program/', $html);
        $this->assertStringContainsString('Hubungi Kami', $html);
    }

    public function test_perubahan_konten_cms_langsung_tampil_di_halaman(): void
    {
        $hero = LandingPage::where('slug', 'home')->first()->getSection('hero');
        $hero->update(['content' => array_merge($hero->content ?? [], ['badge' => 'Uji CleanFlow Landing 123'])]);

        $this->get('/')->assertOk()->assertSee('Uji CleanFlow Landing 123');
    }

    public function test_warna_hex_dari_color_picker_dikirim_lewat_data_warna_bukan_style(): void
    {
        $program = LandingPage::where('slug', 'home')->first()->getSection('program');
        $content = $program->content;
        $content['items'][0]['color'] = '#e82153';
        $program->update(['content' => $content]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-warna="#e82153"', $html);
        $this->assertStringNotContainsString('#e82153;', $html);
    }

    public function test_warna_cms_tidak_bisa_menyisipkan_css(): void
    {
        $this->assertSame('#165fac', warna_landing('red;background:url(x)'));
        $this->assertSame('#165fac', warna_landing('#165fac;color:red'));
        $this->assertSame('#e82153', warna_landing('#E82153'));
        $this->assertSame('#d45930', warna_landing('accent-orange'));
        $this->assertSame('#2563eb', warna_landing('blue'));
    }

    public function test_section_biaya_ppdb_yang_disembunyikan_tetap_bisa_dirender_saat_diaktifkan(): void
    {
        LandingPage::where('slug', 'ppdb')->first()->sections()
            ->whereIn('section_key', ['investasi', 'biaya_paud', 'biaya_sd', 'biaya_smp', 'biaya_sma'])
            ->update(['is_visible' => true]);

        $html = $this->get('/ppdb')->assertOk()->getContent();

        $this->assertStringContainsString('id="biaya"', $html);
        $this->assertStringContainsString("modalBiaya = 'costModalPaketA'", $html);
        $this->assertStringContainsString("x-show=\"modalBiaya === 'costModalPaketC'\"", $html);
        $this->assertDoesNotMatchRegularExpression('/\sstyle="/', $html);
    }

    public function test_sitemap_tidak_lagi_memuat_halaman_yang_tidak_ada(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/legalitas');
    }

    public function test_aset_lama_halaman_publik_sudah_dibersihkan(): void
    {
        foreach ([
            'resources/css/landing.css', 'resources/css/navbar.css', 'resources/js/navbar.js',
            'resources/css/pages', 'resources/js/pages', 'public/js/tailwind.config.js',
            'resources/views/welcome.blade.php', 'resources/views/legalitas.blade.php',
            'resources/views/program-homeschooling.blade.php', 'resources/views/components/cta.blade.php',
        ] as $path) {
            $this->assertFalse(File::exists(base_path($path)), "$path seharusnya sudah dihapus");
        }

        $views = [
            ...File::glob(resource_path('views/*.blade.php')),
            ...File::glob(resource_path('views/auth/*.blade.php')),
            ...File::glob(resource_path('views/partials/program-*.blade.php')),
            resource_path('views/components/navbar.blade.php'),
            resource_path('views/components/footer.blade.php'),
        ];
        foreach ($views as $view) {
            $source = File::get($view);
            $this->assertDoesNotMatchRegularExpression('/\sstyle="|<style[\s>]/', $source, basename($view));
            $this->assertStringNotContainsString('@vite(', $source, basename($view) . ' memuat aset per halaman');
        }
    }
}
