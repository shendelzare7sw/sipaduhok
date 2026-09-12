<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BendaharaCleanFlowUiTest extends TestCase
{
    use RefreshDatabase;

    private User $bendahara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bendahara = User::factory()->create([
            'role' => 'bendahara',
            'is_active' => true,
            'security_question' => 'Nama sekolah pertama?',
            'security_answer' => 'Sekolah pengujian',
            'security_pin' => '123456',
        ]);

        TahunAjaran::create([
            'nama_tahun_ajaran' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'is_active' => true,
        ]);
    }

    public function test_promotion_flow_uses_shared_tailwind_views_and_bendahara_routes(): void
    {
        $validation = $this->actingAs($this->bendahara)
            ->get(route('bendahara.kenaikan-kelas.validation.index'));
        $history = $this->actingAs($this->bendahara)
            ->get(route('bendahara.kenaikan-kelas.validation.history'));

        $validation->assertOk()
            ->assertViewIs('admin.keuangan.promotion.validation')
            ->assertSee(route('bendahara.kenaikan-kelas.validation.history'), false)
            ->assertSee(route('bendahara.kenaikan-kelas.validation.store'), false)
            ->assertSee(route('bendahara.kenaikan-kelas.validation.bulk-store'), false)
            ->assertDontSee(route('admin.keuangan.kenaikan-kelas.validation.store'), false);

        $history->assertOk()
            ->assertViewIs('admin.keuangan.promotion.history')
            ->assertSee(route('bendahara.kenaikan-kelas.validation.index'), false)
            ->assertSee(route('bendahara.kenaikan-kelas.validation.history'), false)
            ->assertDontSee(route('admin.keuangan.kenaikan-kelas.validation.index'), false);

        foreach ([$validation, $history] as $response) {
            $response
                ->assertSee('x-data=', false)
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('form-control', false)
                ->assertDontSee('resources/css/bendahara/promotion', false)
                ->assertDontSee('resources/js/bendahara/promotion', false);
        }
    }

    public function test_replaced_promotion_views_and_assets_are_removed(): void
    {
        foreach ([
            resource_path('views/bendahara/promotion'),
            resource_path('css/bendahara/promotion'),
            resource_path('js/bendahara/promotion'),
        ] as $removedPath) {
            $this->assertFalse(File::exists($removedPath), "Duplikasi lama masih ada: {$removedPath}");
        }

        $sharedSource = File::get(resource_path('views/admin/keuangan/promotion/validation.blade.php'))
            .File::get(resource_path('views/admin/keuangan/promotion/history.blade.php'));

        $this->assertStringContainsString("request()->routeIs('bendahara.*')", $sharedSource);
        $this->assertStringNotContainsString('@vite', $sharedSource);
        $this->assertStringNotContainsString('data-bs-', $sharedSource);
        $this->assertStringNotContainsString('Swal', $sharedSource);
    }

    public function test_payment_configuration_is_not_exposed_to_bendahara(): void
    {
        $this->assertFalse(Route::has('bendahara.info-pembayaran.index'));
        $this->assertFalse(Route::has('bendahara.info-pembayaran.update'));
        $this->actingAs($this->bendahara)->get('/bendahara/config')->assertNotFound();
        $this->assertFalse(File::exists(resource_path('views/bendahara/info-pembayaran')));

        $sidebar = File::get(resource_path('views/bendahara/partials/sidebar.blade.php'));
        $this->assertStringNotContainsString('Config Pembayaran', $sidebar);
        $this->assertStringNotContainsString('bendahara.info-pembayaran', $sidebar);
    }

    public function test_access_validation_uses_shared_view_and_bendahara_endpoints(): void
    {
        $response = $this->actingAs($this->bendahara)
            ->get(route('bendahara.validasi-akses.index'));

        $response->assertOk()
            ->assertViewIs('admin.keuangan.validasi-akses.index')
            ->assertSee(route('bendahara.validasi-akses.index'), false)
            ->assertSee(route('bendahara.validasi-akses.bulk-validasi-selected'), false)
            ->assertSee(route('bendahara.validasi-akses.dispensasi'), false)
            ->assertDontSee(route('admin.keuangan.validasi-akses.index'), false)
            ->assertSee('Bendahara memvalidasi')
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('form-control', false)
            ->assertDontSee('resources/css/bendahara/validasi-akses', false)
            ->assertDontSee('resources/js/bendahara/validasi-akses', false);

        foreach ([
            resource_path('views/bendahara/validasi-akses'),
            resource_path('css/bendahara/validasi-akses'),
            resource_path('js/bendahara/validasi-akses'),
        ] as $removedPath) {
            $this->assertFalse(File::exists($removedPath), "Duplikasi lama masih ada: {$removedPath}");
        }
    }

    public function test_payment_flow_uses_shared_tailwind_views_and_bendahara_routes(): void
    {
        $response = $this->actingAs($this->bendahara)
            ->get(route('bendahara.pembayaran.index'));

        $response->assertOk()
            ->assertViewIs('admin.keuangan.pembayaran.index')
            ->assertSee(route('bendahara.pembayaran.index'), false)
            ->assertDontSee(route('admin.keuangan.pembayaran.index'), false)
            ->assertSee('x-data=', false)
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('form-control', false)
            ->assertDontSee('resources/css/bendahara/pembayaran', false)
            ->assertDontSee('resources/js/bendahara/pembayaran', false);
    }

    public function test_replaced_payment_views_and_assets_are_removed(): void
    {
        foreach ([
            resource_path('views/bendahara/pembayaran'),
            resource_path('css/bendahara/pembayaran'),
            resource_path('js/bendahara/pembayaran'),
        ] as $removedPath) {
            $this->assertFalse(File::exists($removedPath), "Duplikasi lama masih ada: {$removedPath}");
        }

        $sharedSources = collect([
            'index.blade.php',
            'show.blade.php',
            'create.blade.php',
            'riwayat-siswa.blade.php',
            'cetak-kwitansi.blade.php',
        ])->map(fn (string $view) => File::get(resource_path("views/admin/keuangan/pembayaran/{$view}")))
            ->implode("\n");

        $this->assertStringContainsString("request()->routeIs('bendahara.*')", $sharedSources);
        $this->assertStringContainsString("'bendahara.pembayaran'", $sharedSources);
        $this->assertStringNotContainsString('@vite', $sharedSources);
        $this->assertStringNotContainsString('<style', $sharedSources);
        $this->assertStringNotContainsString('data-bs-', $sharedSources);
    }

    public function test_financial_reports_use_shared_tailwind_views_and_bendahara_routes(): void
    {
        $expectations = [
            'bendahara.laporan.index' => 'admin.keuangan.laporan.index',
            'bendahara.laporan.cetak' => 'admin.keuangan.laporan.cetak',
            'bendahara.laporan.rekap-tagihan' => 'admin.keuangan.laporan.rekap-tagihan',
            'bendahara.laporan.cetak-rekap-tagihan' => 'admin.keuangan.laporan.cetak-rekap-tagihan',
            'bendahara.laporan.belum-lunas' => 'admin.keuangan.laporan.belum-lunas',
            'bendahara.laporan.cetak-belum-lunas' => 'admin.keuangan.laporan.cetak-belum-lunas',
        ];

        foreach ($expectations as $routeName => $viewName) {
            $this->actingAs($this->bendahara)
                ->get(route($routeName))
                ->assertOk()
                ->assertViewIs($viewName)
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('resources/css/bendahara/laporan', false)
                ->assertDontSee('resources/js/bendahara/laporan', false);
        }

        $this->actingAs($this->bendahara)
            ->get(route('bendahara.laporan.index'))
            ->assertSee(route('bendahara.laporan.cetak'), false)
            ->assertDontSee(route('admin.keuangan.laporan.cetak'), false);

        $this->actingAs($this->bendahara)
            ->get(route('bendahara.laporan.belum-lunas'))
            ->assertSee(route('bendahara.laporan.cetak-belum-lunas'), false)
            ->assertDontSee(route('admin.keuangan.laporan.cetak-belum-lunas'), false);
    }

    public function test_replaced_financial_report_views_and_assets_are_removed(): void
    {
        foreach ([
            resource_path('views/bendahara/laporan'),
            resource_path('css/bendahara/laporan'),
            resource_path('js/bendahara/laporan'),
        ] as $removedPath) {
            $this->assertFalse(File::exists($removedPath), "Duplikasi lama masih ada: {$removedPath}");
        }

        $sharedSources = collect(File::files(resource_path('views/admin/keuangan/laporan')))
            ->map(fn (\SplFileInfo $view) => File::get($view->getPathname()))
            ->implode("\n");

        $this->assertStringContainsString("request()->routeIs('bendahara.*')", $sharedSources);
        $this->assertStringContainsString("'bendahara.laporan'", $sharedSources);
        $this->assertStringNotContainsString('@vite', $sharedSources);
        $this->assertStringNotContainsString('<style', $sharedSources);
        $this->assertStringNotContainsString('data-bs-', $sharedSources);
    }

    public function test_billing_flow_uses_shared_tailwind_views_and_bendahara_routes(): void
    {
        $tahunAjaran = TahunAjaran::where('is_active', true)->firstOrFail();
        $cabang = Cabang::create([
            'kode_cabang' => 'TST',
            'nama_cabang' => 'Cabang Pengujian',
            'alamat' => 'Alamat pengujian',
            'is_active' => true,
        ]);
        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama_kelas' => '7A',
            'jenjang' => 'SMP',
            'kode_kelas' => 'TST-SMP-7A',
            'kuota_siswa' => 30,
        ]);
        $studentUser = User::factory()->create([
            'name' => 'Siswa Tagihan',
            'username' => 'siswa.tagihan',
            'role' => 'siswa',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);
        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'cabang_id' => $cabang->id,
            'kelas_id' => $kelas->id,
            'nisn' => '1234567890',
            'nis' => 'TST001',
            'nama_lengkap' => 'Siswa Tagihan',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bogor',
            'tanggal_lahir' => '2012-01-01',
            'alamat' => 'Alamat siswa',
            'agama' => 'Islam',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $routes = [
            'bendahara.tagihan.index' => [],
            'bendahara.tagihan.bulk-create' => [],
            'bendahara.tagihan.create-custom' => [],
            'bendahara.tagihan.generate-spp' => [],
            'bendahara.tagihan.duplicate' => [],
            'bendahara.tagihan.carryover' => [],
            'bendahara.tagihan.cetak-laporan' => [],
            'bendahara.tagihan.show' => [$siswa->id],
            'bendahara.tagihan.edit' => [$siswa->id],
            'bendahara.tagihan.cetak' => [$siswa->id],
        ];

        foreach ($routes as $routeName => $parameters) {
            $view = str_replace('bendahara.tagihan.', '', $routeName);

            $this->actingAs($this->bendahara)
                ->get(route($routeName, $parameters))
                ->assertOk()
                ->assertViewIs("admin.keuangan.tagihan.{$view}")
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('resources/css/bendahara/tagihan', false)
                ->assertDontSee('resources/js/bendahara/tagihan', false);
        }

        $index = $this->actingAs($this->bendahara)
            ->get(route('bendahara.tagihan.index'));

        $index->assertSee(route('bendahara.tagihan.bulk-create'), false)
            ->assertSee(route('bendahara.tagihan.create-custom'), false)
            ->assertSee(route('bendahara.tagihan.generate-spp'), false)
            ->assertDontSee('/admin/keuangan/tagihan', false)
            ->assertDontSee('>Import</a>', false)
            ->assertDontSee('Reset tagihan terpilih?');
    }

    public function test_replaced_billing_views_and_assets_are_removed(): void
    {
        foreach ([
            resource_path('views/bendahara/tagihan'),
            resource_path('css/bendahara/tagihan'),
            resource_path('js/bendahara/tagihan'),
        ] as $removedPath) {
            $this->assertFalse(File::exists($removedPath), "Duplikasi lama masih ada: {$removedPath}");
        }

        $sharedSources = collect(File::files(resource_path('views/admin/keuangan/tagihan')))
            ->reject(fn (\SplFileInfo $view) => $view->getFilename() === 'import.blade.php')
            ->map(fn (\SplFileInfo $view) => File::get($view->getPathname()))
            ->implode("\n");

        $this->assertStringContainsString("request()->routeIs('admin.*')", $sharedSources);
        $this->assertStringContainsString("'bendahara.tagihan'", $sharedSources);
        $this->assertStringNotContainsString('@vite', $sharedSources);
        $this->assertStringNotContainsString('<style', $sharedSources);
        $this->assertStringNotContainsString('data-bs-', $sharedSources);
    }

    public function test_dashboard_uses_tailwind_and_alpine_without_page_assets(): void
    {
        $response = $this->actingAs($this->bendahara)
            ->get(route('bendahara.dashboard'));

        $response->assertOk()
            ->assertViewIs('dashboard.bendahara')
            ->assertSee('data-bendahara-dashboard', false)
            ->assertSee("x-data=\"{ tab: 'validasi' }\"", false)
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('bendahara-dashboard-page', false)
            ->assertDontSee('resources/css/dashboard/bendahara.css', false)
            ->assertDontSee('resources/js/dashboard/bendahara.js', false);

        $this->assertFalse(File::exists(resource_path('css/dashboard/bendahara.css')));
        $this->assertFalse(File::exists(resource_path('js/dashboard/bendahara.js')));
    }
}
