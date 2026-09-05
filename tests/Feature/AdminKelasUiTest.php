<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminKelasUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_and_forms_follow_the_responsive_cleanflow_pattern(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = Cabang::create([
            'kode_cabang' => 'TST',
            'nama_cabang' => 'Cabang Pengujian',
            'alamat' => 'Alamat pengujian',
            'is_active' => true,
        ]);
        $tahun = TahunAjaran::create([
            'nama_tahun_ajaran' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'is_active' => true,
        ]);
        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahun->id,
            'nama_kelas' => '7A',
            'jenjang' => 'SMP',
            'kode_kelas' => 'TST-SMP-7A-2026',
            'kuota_siswa' => 30,
        ]);

        $this->actingAs($admin)->get(route('admin.kelas.index'))
            ->assertOk()
            ->assertSee('min-w-0 w-full space-y-5', false)
            ->assertSee('table-fixed', false)
            ->assertSee('aria-label="Detail kelas"', false)
            ->assertSee('TST-SMP-7A-2026');

        $this->actingAs($admin)->get(route('admin.kelas.create'))
            ->assertOk()
            ->assertSee('x-data=', false)
            ->assertSee('Kode kelas otomatis')
            ->assertSee('name="wali_kelas_id"', false)
            ->assertDontSee('waliKelasModal', false)
            ->assertDontSee('resources/js/admin/kelas/form.js', false);

        $this->actingAs($admin)->get(route('admin.kelas.edit', $kelas))
            ->assertOk()
            ->assertSee('Edit kelas 7A')
            ->assertSee('name="_return_url"', false)
            ->assertDontSee('data-bs-toggle', false);

        $this->actingAs($admin)->get(route('admin.kelas.show', $kelas))
            ->assertOk()
            ->assertSee('Informasi kelas')
            ->assertSee('Daftar siswa')
            ->assertSee('[&_h2]:!text-white', false)
            ->assertSee('Belum ada siswa di kelas ini');

        $this->actingAs($admin)->get(route('admin.kelas.manage-siswa', $kelas))
            ->assertOk()
            ->assertSee('Penempatan siswa')
            ->assertSee('x-data=', false)
            ->assertDontSee('deleteModal', false);

        $this->actingAs($admin)->get(route('admin.kelas.import'))
            ->assertOk()
            ->assertSee('Pilih file dari perangkat')
            ->assertSee('name="file"', false)
            ->assertDontSee('uploadArea', false);

        $this->actingAs($admin)->get(route('admin.kelas.print'))
            ->assertOk()
            ->assertSee('Daftar Kelas')
            ->assertSee('data-print-page', false)
            ->assertDontSee('printButton', false);

        $this->assertFalse(File::exists(resource_path('css/admin/kelas/form.css')));
        $this->assertFalse(File::exists(resource_path('js/admin/kelas/form.js')));
        $this->assertFalse(File::isDirectory(resource_path('css/admin/kelas')));
        $this->assertFalse(File::isDirectory(resource_path('js/admin/kelas')));
    }
}
