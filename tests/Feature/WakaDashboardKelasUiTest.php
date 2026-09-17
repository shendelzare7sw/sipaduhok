<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class WakaDashboardKelasUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_and_all_kelas_pages_use_the_cleanflow_contract(): void
    {
        $cabang = Cabang::create([
            'kode_cabang' => 'WKA',
            'nama_cabang' => 'Cabang Waka',
            'alamat' => 'Alamat pengujian',
            'is_active' => true,
        ]);
        $tahun = TahunAjaran::create([
            'nama_tahun_ajaran' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'is_active' => true,
        ]);
        $waka = User::factory()->create([
            'role' => 'wakil_kepala_sekolah',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);
        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahun->id,
            'nama_kelas' => '7A',
            'jenjang' => 'SMP',
            'kode_kelas' => 'WKA-SMP-7A-2026',
            'kuota_siswa' => 30,
        ]);

        $dashboard = $this->actingAs($waka)->get(route('waka.dashboard'));
        $dashboard
            ->assertOk()
            ->assertSee('Kesiapan cabang dalam satu layar')
            ->assertSee("x-data=\"{ tab: 'akademik' }\"", false)
            ->assertDontSee('/waka/tahun-ajaran', false)
            ->assertDontSee('/waka/mata-pelajaran', false)
            ->assertDontSee('/waka/kenaikan-kelas', false)
            ->assertDontSee('data-bs-toggle', false)
            ->assertDontSee('resources/css/waka/dashboard.css', false);

        foreach ([
            'waka.tahun-ajaran.index',
            'waka.mata-pelajaran.index',
            'waka.pengaturan-istirahat.index',
            'waka.kenaikan-kelas.kkm.index',
            'waka.kenaikan-kelas.settings.index',
            'waka.kenaikan-kelas.report',
        ] as $routeName) {
            $this->assertFalse(Route::has($routeName), $routeName.' harus eksklusif Admin.');
        }

        foreach ([
            '/waka/tahun-ajaran',
            '/waka/mata-pelajaran',
            '/waka/pengaturan-istirahat',
            '/waka/kenaikan-kelas/kkm',
            '/waka/kenaikan-kelas/settings',
            '/waka/kenaikan-kelas/report',
        ] as $path) {
            $this->actingAs($waka)->get($path)->assertNotFound();
        }

        foreach ([
            route('waka.kelas.index'),
            route('waka.kelas.create'),
            route('waka.kelas.edit', $kelas),
            route('waka.kelas.show', $kelas),
            route('waka.kelas.manage-siswa', $kelas),
            route('waka.kelas.import'),
            route('waka.kelas.print'),
        ] as $url) {
            $this->actingAs($waka)->get($url)
                ->assertOk()
                ->assertDontSee('/admin/kelas', false)
                ->assertDontSee('data-bs-toggle', false)
                ->assertDontSee('resources/css/waka/kelas/', false)
                ->assertDontSee('resources/js/waka/kelas/', false);
        }

        $this->actingAs($waka)->get(route('waka.kelas.index'))
            ->assertDontSee('copy-class-dialog', false)
            ->assertDontSee('name="cabang_id" data-auto-submit', false)
            ->assertSee('grid grid-cols-3 gap-2 sm:flex', false);

        $this->actingAs($waka)->get(route('waka.kelas.create'))
            ->assertSee('value="'.$cabang->id.'"', false)
            ->assertSee('Cabang Waka')
            ->assertSee('Kode kelas otomatis');

        $this->assertFalse(File::exists(resource_path('css/waka/dashboard.css')));
        $this->assertFalse(File::isDirectory(resource_path('css/waka/kelas')));
        $this->assertFalse(File::isDirectory(resource_path('js/waka/kelas')));
        $this->assertFalse(File::isDirectory(resource_path('views/waka/kelas')));
        $this->assertFalse(File::isDirectory(resource_path('views/waka/tahun-ajaran')));
        $this->assertFalse(File::isDirectory(resource_path('views/waka/mata-pelajaran')));
        $this->assertFalse(File::isDirectory(resource_path('views/waka/pengaturan-istirahat')));
        $this->assertFalse(File::isDirectory(resource_path('views/waka/akademik')));
        $this->assertFalse(File::exists(app_path('Http/Controllers/WakilKepalaSekolah/TahunAjaranController.php')));
        $this->assertFalse(File::exists(app_path('Http/Controllers/WakilKepalaSekolah/MataPelajaranController.php')));
        $this->assertFalse(File::exists(app_path('Http/Controllers/WakilKepalaSekolah/PengaturanIstirahatController.php')));
    }
}
