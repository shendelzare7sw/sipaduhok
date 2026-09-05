<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Cabang;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminJadwalPelajaranUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_jadwal_pages_follow_the_tailwind_alpine_flow(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $tahun = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-7A-2026', 'kuota_siswa' => 30]);
        $mapel = MataPelajaran::create(['kode_mapel' => 'SMP-001', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMP']);
        $jadwal = JadwalPelajaran::create(['tahun_ajaran_id' => $tahun->id, 'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id, 'hari' => 'Senin', 'jam_mulai' => '07:00', 'jam_selesai' => '08:00', 'status' => 'kosong', 'updated_by' => $admin->id]);
        $jadwal->kelas()->attach($kelas->id);

        $this->actingAs($admin)->get(route('admin.jadwal-pelajaran.index', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('table-fixed', false)->assertSee('x-data=', false)
            ->assertSee('Pilih guru baru')->assertSee('Salin antarperiode')
            ->assertSee('Perubahan massal')->assertSee('Dokumen kelas')
            ->assertDontSee('size="6"', false)
            ->assertSee('aria-label="Edit jadwal"', false)->assertDontSee('data-bs-toggle', false)
            ->assertDontSee('resources/js/admin/jadwal-pelajaran/index.js', false);

        $this->actingAs($admin)->get(route('admin.jadwal-pelajaran.create', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Susun jadwal baru')->assertSee('name="kelas_ids[]"', false)
            ->assertSee('name="is_multi_jenjang"', false)->assertDontSee('guruModal', false);

        $this->actingAs($admin)->get(route('admin.jadwal-pelajaran.edit', $jadwal))
            ->assertOk()->assertSee('Edit jadwal Matematika')->assertSee('Simpan perubahan')
            ->assertDontSee('resources/js/admin/jadwal-pelajaran/form.js', false);

        $this->actingAs($admin)->get(route('admin.jadwal-pelajaran.show', ['kelas' => $kelas->id, 'tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Jadwal per kelas')->assertSee('Matematika');

        $this->actingAs($admin)->get(route('admin.jadwal-pelajaran.import'))
            ->assertOk()->assertSee('Import dari Excel')->assertSee('name="file"', false)
            ->assertDontSee('resources/js/admin/jadwal-pelajaran/import.js', false);

        $this->actingAs($admin)->get(route('admin.jadwal-pelajaran.preview-print', ['kelas' => $kelas->id, 'tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Pratinjau dokumen')->assertSee('Matematika')
            ->assertDontSee('js/admin/jadwal-pelajaran/print.js', false);

        $this->actingAs($admin)->get(route('admin.jadwal-pelajaran.export-pdf', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Daftar Jadwal Pelajaran')->assertSee('Matematika');

        foreach (['form.css', 'import.css', 'index.css', 'show.css'] as $file) {
            $this->assertFalse(File::exists(resource_path("css/admin/jadwal-pelajaran/{$file}")));
        }
        foreach (['form.js', 'import.js', 'index.js', 'show.js'] as $file) {
            $this->assertFalse(File::exists(resource_path("js/admin/jadwal-pelajaran/{$file}")));
        }
    }
}
