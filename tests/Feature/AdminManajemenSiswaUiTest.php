<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminManajemenSiswaUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_student_management_pages_use_responsive_cleanflow_views(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $tahun = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-7A-2026', 'kuota_siswa' => 30]);
        $studentUser = User::factory()->create(['role' => 'siswa', 'is_active' => true, 'cabang_id' => $cabang->id]);
        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'cabang_id' => $cabang->id,
            'kelas_id' => $kelas->id,
            'nisn' => '0099999999',
            'nis' => 'TST-001',
            'nama_lengkap' => 'Siswa Pengujian',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Tangerang',
            'tanggal_lahir' => '2012-01-02',
            'alamat' => 'Alamat siswa',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $this->actingAs($admin)->get(route('admin.manajemen-siswa.index'))
            ->assertOk()
            ->assertSee('Cari siswa, lalu tentukan kelas')
            ->assertSee('table-fixed', false)
            ->assertSee('Siswa Pengujian')
            ->assertSee('x-data=', false)
            ->assertDontSee('data-bs-toggle', false)
            ->assertDontSee('resources/css/admin/manajemen-siswa/index.css', false);

        $this->actingAs($admin)->get(route('admin.manajemen-siswa.show', $siswa))
            ->assertOk()
            ->assertSee('Kelas dan penempatan')
            ->assertSee('Wali siswa')
            ->assertSee('Tambah wali')
            ->assertSee('[&_h2]:!text-white', false)
            ->assertSee('text-brand-700', false)
            ->assertDontSee('detachParentModal', false)
            ->assertDontSee('resources/js/admin/manajemen-siswa/show.js', false);

        $this->actingAs($admin)->get(route('admin.manajemen-siswa.per-kelas', $kelas))
            ->assertOk()
            ->assertSee('Anggota kelas')
            ->assertSee('Siswa Pengujian')
            ->assertSee('data-confirm', false)
            ->assertDontSee('deleteModal', false);

        $this->actingAs($admin)->get(route('admin.manajemen-siswa.print', ['kelas_id' => $kelas->id]))
            ->assertOk()
            ->assertSee('Pratinjau dokumen')
            ->assertSee('Siswa Pengujian')
            ->assertDontSee('css/admin/manajemen-siswa/print.css', false);

        $this->actingAs($admin)->get(route('admin.manajemen-siswa.print-kartu', $siswa))
            ->assertOk()
            ->assertSee('Pratinjau kartu siswa')
            ->assertSee('Foto khusus untuk cetak')
            ->assertDontSee('js/admin/manajemen-siswa/print-kartu.js', false);

        $this->assertFalse(File::isDirectory(resource_path('css/admin/manajemen-siswa')));
        $this->assertFalse(File::isDirectory(resource_path('js/admin/manajemen-siswa')));
        $this->assertFalse(File::isDirectory(public_path('css/admin/manajemen-siswa')));
        $this->assertFalse(File::isDirectory(public_path('js/admin/manajemen-siswa')));
    }
}
