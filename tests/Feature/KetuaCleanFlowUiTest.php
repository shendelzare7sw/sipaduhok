<?php

namespace Tests\Feature;

use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class KetuaCleanFlowUiTest extends TestCase
{
    use RefreshDatabase;

    private User $ketua;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ketua = User::factory()->create([
            'role' => 'ketua_pkbm',
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

    public function test_shared_monitoring_report_and_note_pages_keep_ketua_routes(): void
    {
        $responses = collect([
            'ketua.monitoring.pengguna',
            'ketua.monitoring.wali-kelas',
            'ketua.monitoring.guru-pengajar',
            'ketua.monitoring.siswa',
            'ketua.laporan.index',
            'ketua.catatan.index',
            'ketua.catatan.create',
            'ketua.dashboard',
            'ketua.dispensasi.index',
            'ketua.kenaikan-kelas.approval.index',
            'ketua.kenaikan-kelas.approval.history',
            'ketua.validasi-rapor.index',
        ])->mapWithKeys(fn (string $routeName) => [
            $routeName => $this->actingAs($this->ketua)->get(route($routeName)),
        ]);

        foreach ($responses as $routeName => $response) {
            $this->assertSame(200, $response->status(), "Route {$routeName} gagal dirender.");
            $response
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('form-control', false)
                ->assertDontSee('resources/css/ketua', false)
                ->assertDontSee('resources/js/ketua', false);
        }

        $responses['ketua.monitoring.pengguna']->assertViewIs('admin.monitoring.pengguna')
            ->assertSee(route('ketua.monitoring.pengguna'), false)
            ->assertDontSee(route('admin.monitoring.pengguna'), false);
        $responses['ketua.monitoring.wali-kelas']->assertViewIs('admin.monitoring.wali-kelas')
            ->assertSee(route('ketua.monitoring.wali-kelas'), false)
            ->assertDontSee(route('admin.monitoring.wali-kelas'), false);
        $responses['ketua.monitoring.guru-pengajar']->assertViewIs('admin.monitoring.guru-pengajar')
            ->assertSee(route('ketua.monitoring.guru-pengajar'), false)
            ->assertDontSee(route('admin.monitoring.guru-pengajar'), false);
        $responses['ketua.monitoring.siswa']->assertViewIs('admin.monitoring.siswa')
            ->assertSee(route('ketua.monitoring.siswa'), false)
            ->assertDontSee(route('admin.monitoring.siswa'), false);
        $responses['ketua.laporan.index']->assertViewIs('admin.laporan.index')
            ->assertSee(route('ketua.laporan.siswa'), false)
            ->assertDontSee(route('admin.laporan.siswa'), false);
        $responses['ketua.catatan.index']->assertViewIs('admin.catatan.index')
            ->assertSee(route('ketua.catatan.create'), false)
            ->assertDontSee(route('admin.catatan.create'), false);
        $responses['ketua.catatan.create']->assertViewIs('admin.catatan.create')
            ->assertSee(route('ketua.catatan.store'), false)
            ->assertDontSee(route('admin.catatan.store'), false);
        $responses['ketua.dashboard']->assertViewIs('dashboard.ketua');
        $responses['ketua.dispensasi.index']->assertViewIs('ketua.dispensasi.index');
        $responses['ketua.kenaikan-kelas.approval.index']->assertViewIs('ketua.promotion.approval');
        $responses['ketua.kenaikan-kelas.approval.history']->assertViewIs('admin.keuangan.promotion.history')
            ->assertSee(route('ketua.kenaikan-kelas.approval.index'), false)
            ->assertDontSee(route('admin.keuangan.kenaikan-kelas.validation.index'), false);
        $responses['ketua.validasi-rapor.index']->assertViewIs('ketua.validasi-rapor.index');
    }

    public function test_consolidated_ketua_views_are_asset_free_and_duplicates_are_removed(): void
    {
        $sharedViews = [
            resource_path('views/admin/monitoring/pengguna.blade.php'),
            resource_path('views/admin/monitoring/wali-kelas.blade.php'),
            resource_path('views/admin/monitoring/guru-pengajar.blade.php'),
            resource_path('views/admin/monitoring/siswa.blade.php'),
            resource_path('views/admin/laporan/index.blade.php'),
            resource_path('views/admin/catatan/index.blade.php'),
            resource_path('views/admin/catatan/create.blade.php'),
            resource_path('views/admin/catatan/show.blade.php'),
        ];
        $source = collect($sharedViews)->map(fn (string $path) => File::get($path))->implode("\n");

        $this->assertStringContainsString("request()->routeIs('ketua.*')", $source);
        $this->assertStringNotContainsString('@vite', $source);
        $this->assertStringNotContainsString('data-bs-', $source);
        $this->assertStringNotContainsString('form-control', $source);
        $this->assertStringNotContainsString('<style', $source);

        foreach ([
            resource_path('views/ketua/catatan'),
            resource_path('views/ketua/laporan'),
            resource_path('views/ketua/monitoring'),
            resource_path('css/ketua/catatan'),
            resource_path('css/ketua/laporan'),
            resource_path('css/ketua/monitoring'),
            resource_path('js/ketua/catatan'),
            resource_path('js/ketua/laporan'),
            resource_path('js/ketua/monitoring'),
            resource_path('css/ketua'),
            resource_path('js/ketua'),
            resource_path('css/dashboard/ketua.css'),
        ] as $removedPath) {
            $this->assertFalse(File::exists($removedPath), "Duplikasi lama masih ada: {$removedPath}");
        }
    }

    public function test_all_ketua_pages_use_tailwind_and_alpine_without_page_assets(): void
    {
        $views = collect(File::allFiles(resource_path('views/ketua')))
            ->push(new \SplFileInfo(resource_path('views/dashboard/ketua.blade.php')))
            ->map(fn (\SplFileInfo $file) => File::get($file->getPathname()))
            ->implode("\n");

        $this->assertStringNotContainsString('@vite', $views);
        $this->assertStringNotContainsString('data-bs-', $views);
        $this->assertStringNotContainsString('form-control', $views);
        $this->assertStringNotContainsString('table-responsive', $views);
        $this->assertStringNotContainsString('<style', $views);
        $this->assertStringNotContainsString('<script', $views);
        $this->assertStringContainsString('x-data=', $views);
    }
}
