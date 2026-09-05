<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminWaliKelasUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_kelas_flow_uses_responsive_views_without_local_assets(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $role = Role::create(['name' => 'wali_kelas', 'display_name' => 'Wali Kelas', 'level' => 5]);
        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $tahun = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $waliUser = User::factory()->create(['role' => 'wali_kelas', 'role_id' => $role->id, 'is_active' => true, 'cabang_id' => $cabang->id]);
        $wali = TenagaPendidik::create(['user_id' => $waliUser->id, 'nip' => 'WK-001', 'nama_lengkap' => 'Wali Pengujian', 'jenis_kelamin' => 'P', 'email' => $waliUser->email]);
        $kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-7A-2026', 'kuota_siswa' => 30, 'wali_kelas_id' => $wali->id]);
        WaliKelasAssignment::create(['tenaga_pendidik_id' => $wali->id, 'kelas_id' => $kelas->id, 'assigned_at' => now()]);

        $this->actingAs($admin)->get(route('admin.wali-kelas.index', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('table-fixed', false)->assertSee('Wali Pengujian')
            ->assertSee('aria-label="Atur wali kelas"', false)->assertSee('Penugasan cepat')
            ->assertSee('Atur wali di sini')
            ->assertDontSee('data-bs-toggle', false);

        $this->actingAs($admin)->get(route('admin.wali-kelas.show', $kelas))
            ->assertOk()->assertSee('Wali yang bertugas')->assertSee('Tambah wali kelas')
            ->assertSee('Wali Pengujian')->assertSee(route('admin.wali-kelas.remove-assignment', $kelas));

        $this->actingAs($admin)->get(route('admin.wali-kelas.print', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Pratinjau dokumen')->assertSee('Wali Pengujian')
            ->assertDontSee('js/admin/wali-kelas/print.js', false);

        $this->assertFalse(File::isDirectory(resource_path('css/admin/wali-kelas')));
        $this->assertFalse(File::isDirectory(resource_path('js/admin/wali-kelas')));
    }
}
