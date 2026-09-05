<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\KalenderAkademik;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SekretarisCleanFlowUiTest extends TestCase
{
    use RefreshDatabase;

    private User $sekretaris;

    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sekretaris = User::factory()->create([
            'role' => 'sekretaris',
            'is_active' => true,
        ]);
        $this->tahunAjaran = TahunAjaran::create([
            'nama_tahun_ajaran' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'is_active' => true,
        ]);
    }

    public function test_dashboard_and_publication_flows_render_on_cleanflow_shell(): void
    {
        $agenda = KalenderAkademik::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'nama_kegiatan' => 'Agenda CleanFlow',
            'tanggal_mulai' => now()->toDateString(),
            'jenis_kegiatan' => 'acara_sekolah',
            'status' => 'aktif',
            'is_hidden_siswa' => false,
        ]);

        $responses = [
            $this->actingAs($this->sekretaris)->get(route('sekretaris.dashboard')),
            $this->get(route('sekretaris.kalender.index')),
            $this->get(route('sekretaris.kalender.create')),
            $this->get(route('sekretaris.kalender.show', $agenda)),
            $this->get(route('sekretaris.kalender.edit', $agenda)),
            $this->get(route('sekretaris.pengumuman.index')),
            $this->get(route('sekretaris.pengumuman.create')),
            $this->get(route('sekretaris.flyer.index')),
            $this->get(route('sekretaris.flyer.create')),
            $this->get(route('sekretaris.berita.index')),
            $this->get(route('sekretaris.berita.create')),
        ];

        foreach ($responses as $response) {
            $response->assertOk()
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('form-control', false)
                ->assertDontSee('resources/css/sekretaris', false)
                ->assertDontSee('resources/js/sekretaris', false);
        }

        $responses[1]->assertViewIs('admin.akademik.kalender.index')
            ->assertSee(route('sekretaris.kalender.create'), false)
            ->assertDontSee(route('admin.akademik.kalender.create'), false);
        $responses[5]->assertViewIs('admin.akademik.pengumuman.index')
            ->assertSee(route('sekretaris.pengumuman.create'), false);
        $responses[7]->assertViewIs('admin.akademik.flyer.index')
            ->assertSee(route('sekretaris.flyer.create'), false);
        $responses[9]->assertViewIs('admin.akademik.berita.index')
            ->assertSee(route('sekretaris.berita.create'), false);
    }

    public function test_shared_publication_views_are_context_aware_and_asset_free(): void
    {
        $viewPaths = [
            resource_path('views/admin/akademik/kalender/index.blade.php'),
            resource_path('views/admin/akademik/kalender/form.blade.php'),
            resource_path('views/admin/akademik/kalender/show.blade.php'),
            resource_path('views/admin/akademik/pengumuman/index.blade.php'),
            resource_path('views/admin/akademik/pengumuman/form.blade.php'),
            resource_path('views/admin/akademik/flyer/index.blade.php'),
            resource_path('views/admin/akademik/flyer/form.blade.php'),
            resource_path('views/admin/akademik/berita/index.blade.php'),
            resource_path('views/admin/akademik/berita/form.blade.php'),
        ];
        $source = collect($viewPaths)->map(fn (string $path) => File::get($path))->implode("\n");

        $this->assertStringContainsString("request()->routeIs('sekretaris.*')", $source);
        $this->assertStringNotContainsString('@vite', $source);
        $this->assertStringNotContainsString('data-bs-', $source);
        $this->assertStringNotContainsString('form-control', $source);
        $this->assertStringNotContainsString('<style', $source);

        foreach ([
            resource_path('css/sekretaris'),
            resource_path('js/sekretaris'),
            resource_path('css/dashboard/sekretaris.css'),
            resource_path('js/dashboard/sekretaris.js'),
            resource_path('views/sekretaris/berita/index.blade.php'),
            resource_path('views/sekretaris/flyer/index.blade.php'),
            resource_path('views/sekretaris/kalender/index.blade.php'),
            resource_path('views/sekretaris/pengumuman/index.blade.php'),
        ] as $removedPath) {
            $this->assertFalse(File::exists($removedPath), "Legacy path masih ada: {$removedPath}");
        }
    }

    public function test_calendar_feed_and_both_pdf_exports_remain_available(): void
    {
        KalenderAkademik::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'nama_kegiatan' => 'Agenda untuk cetak',
            'tanggal_mulai' => '2026-09-05',
            'tanggal_selesai' => '2026-09-06',
            'jenis_kegiatan' => 'acara_sekolah',
            'status' => 'aktif',
        ]);

        $feed = $this->actingAs($this->sekretaris)->getJson(route('sekretaris.kalender.bulanan', ['bulan' => '2026-09']));
        $feed->assertOk()->assertJsonFragment(['title' => 'Agenda untuk cetak']);

        foreach ([
            ['jenis' => 'bulanan', 'bulan' => '2026-09'],
            ['jenis' => 'tahunan'],
        ] as $query) {
            $this->get(route('sekretaris.kalender.cetak', $query))
                ->assertOk()
                ->assertHeader('content-type', 'application/pdf');
        }
    }

    public function test_shared_publication_views_keep_admin_route_contracts(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        foreach (['kalender', 'pengumuman', 'flyer', 'berita'] as $module) {
            $response = $this->actingAs($admin)->get(route("admin.akademik.{$module}.index"));
            $response->assertOk()
                ->assertSee(route("admin.akademik.{$module}.create"), false)
                ->assertDontSee(route("sekretaris.{$module}.create"), false);
        }
    }
}
