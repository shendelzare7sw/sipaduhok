<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Cabang;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminGuruPengajarUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_guru_pengajar_pages_use_the_cleanflow_ui(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $tahun = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $guruUser = User::factory()->create(['role' => 'guru_pengajar', 'is_active' => true, 'cabang_id' => $cabang->id]);
        $guru = TenagaPendidik::create(['user_id' => $guruUser->id, 'nip' => 'GP-001', 'nama_lengkap' => 'Guru Pengujian', 'jenis_kelamin' => 'L', 'email' => $guruUser->email]);
        $kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-7A-2026', 'kuota_siswa' => 30]);
        $mapel = MataPelajaran::create(['kode_mapel' => 'SMP-001', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMP', 'is_active' => true]);
        GuruPengajarKelas::create(['tenaga_pendidik_id' => $guru->id, 'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id]);

        $this->actingAs($admin)->get(route('admin.guru-pengajar.index', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('table-fixed', false)->assertSee('Guru Pengujian')
            ->assertSee('aria-label="Lihat penugasan"', false)->assertDontSee('data-bs-toggle', false);

        $this->actingAs($admin)->get(route('admin.guru-pengajar.show', ['guruPengajar' => $guru, 'tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Penugasan kelas dan mapel')->assertSee('Matematika')
            ->assertDontSee('resources/css/admin/guru-pengajar/show.css', false);

        $this->actingAs($admin)->get(route('admin.guru-pengajar.manage-kelas', $kelas))
            ->assertOk()->assertSee('Guru dan mata pelajaran')->assertSee('Guru Pengujian');

        $this->actingAs($admin)->get(route('admin.guru-pengajar.print', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Pratinjau dokumen')->assertSee('Guru Pengujian')
            ->assertDontSee('js/admin/guru-pengajar/print.js', false);

        $this->assertFalse(File::isDirectory(resource_path('css/admin/guru-pengajar')));
        $this->assertFalse(File::isDirectory(resource_path('js/admin/guru-pengajar')));
    }
}
