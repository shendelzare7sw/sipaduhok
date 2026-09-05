<?php

namespace Tests\Feature;

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
}
