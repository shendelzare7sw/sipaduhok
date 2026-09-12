<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Imports\JadwalPelajaranImport;
use App\Models\Cabang;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Notification;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Services\NotificationService;
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
            ->assertSee('grid-cols-4', false)->assertSee('text-[10px]', false)
            ->assertSee('lg:!px-5', false)->assertSee('lg:min-h-11', false)
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

        $waka = User::factory()->create([
            'role' => 'wakil_kepala_sekolah',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);

        $this->actingAs($waka)->get(route('waka.jadwal-pelajaran.index', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()
            ->assertSee('x-data="{ toolsOpen: false, exportOpen: false }"', false)
            ->assertSee('sm:!w-64', false)
            ->assertSee('grid-cols-4', false)
            ->assertSee('lg:!px-5', false)
            ->assertSee('whitespace-nowrap', false)
            ->assertSee('Duplikasi periode')
            ->assertSee('PDF semua jadwal');

        foreach (['form.css', 'import.css', 'index.css', 'show.css'] as $file) {
            $this->assertFalse(File::exists(resource_path("css/admin/jadwal-pelajaran/{$file}")));
        }
        foreach (['form.js', 'import.js', 'index.js', 'show.js'] as $file) {
            $this->assertFalse(File::exists(resource_path("js/admin/jadwal-pelajaran/{$file}")));
        }
    }

    public function test_non_guru_cannot_be_assigned_or_receive_teaching_notification(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $secretary = User::factory()->create(['role' => 'sekretaris', 'is_active' => true]);
        $teacher = User::factory()->create([
            'role' => 'guru_pengajar',
            'is_active' => true,
            'username' => 'guru-aktif-guard',
        ]);
        $secretaryProfile = TenagaPendidik::create([
            'user_id' => $secretary->id,
            'nama_lengkap' => 'Sekretaris Bukan Guru',
            'jenis_kelamin' => 'P',
        ]);
        $teacherProfile = TenagaPendidik::create([
            'user_id' => $teacher->id,
            'nama_lengkap' => 'Guru Aktif',
            'jenis_kelamin' => 'L',
        ]);
        $cabang = Cabang::create([
            'kode_cabang' => 'SEC',
            'nama_cabang' => 'Cabang Role Guard',
            'alamat' => 'Alamat',
            'is_active' => true,
        ]);
        $tahun = TahunAjaran::create([
            'nama_tahun_ajaran' => '2027/2028',
            'tanggal_mulai' => '2027-07-01',
            'tanggal_selesai' => '2028-06-30',
            'is_active' => true,
        ]);
        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahun->id,
            'nama_kelas' => '8A Guard',
            'jenjang' => 'SMP',
            'kode_kelas' => 'SEC-SMP-8A-2027',
            'kuota_siswa' => 30,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'SEC-001',
            'nama_mapel' => 'Mapel Role Guard',
            'jenjang' => 'SMP',
        ]);
        $waka = User::factory()->create([
            'role' => 'wakil_kepala_sekolah',
            'is_active' => true,
            'cabang_id' => $cabang->id,
        ]);

        $this->assertFalse(TenagaPendidik::eligibleToTeach()->whereKey($secretaryProfile)->exists());
        $this->assertTrue(TenagaPendidik::eligibleToTeach()->whereKey($teacherProfile)->exists());

        $payload = [
            'tahun_ajaran_id' => $tahun->id,
            'kelas_ids' => [$kelas->id],
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $secretaryProfile->id,
            'hari' => 'Senin',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:00',
        ];

        $this->actingAs($admin)
            ->post(route('admin.jadwal-pelajaran.store'), $payload)
            ->assertSessionHasErrors('guru_id');

        $multiPayload = $payload;
        unset($multiPayload['mata_pelajaran_id']);
        $multiPayload['is_multi_jenjang'] = 1;
        $multiPayload['mapel_per_jenjang'] = ['SMP' => $mapel->id];

        $this->actingAs($admin)
            ->post(route('admin.jadwal-pelajaran.store'), $multiPayload)
            ->assertSessionHasErrors('guru_id');

        $this->withoutMiddleware()
            ->actingAs($waka)
            ->post(route('waka.jadwal-pelajaran.store'), $payload)
            ->assertSessionHasErrors('guru_id');

        $this->assertDatabaseMissing('jadwal_pelajaran', [
            'guru_id' => $secretaryProfile->id,
            'mata_pelajaran_id' => $mapel->id,
        ]);

        $import = new JadwalPelajaranImport($tahun->id);
        $import->collection(collect([collect([
            'nama_kelas' => $kelas->nama_kelas,
            'nama_cabang' => $cabang->nama_cabang,
            'nama_mapel' => $mapel->nama_mapel,
            'nama_guru' => $secretaryProfile->nama_lengkap,
            'hari' => 'Selasa',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
        ])]));

        $importedSchedule = JadwalPelajaran::where('hari', 'Selasa')
            ->where('mata_pelajaran_id', $mapel->id)
            ->firstOrFail();
        $this->assertNull($importedSchedule->guru_id);
        $this->assertSame('kosong', $importedSchedule->status);

        $this->actingAs($admin)
            ->post(route('admin.jadwal-pelajaran.ganti-guru', $importedSchedule), [
                'guru_id_baru' => $secretaryProfile->id,
            ])
            ->assertSessionHasErrors('guru_id_baru');
        $this->assertNull($importedSchedule->fresh()->guru_id);

        app(NotificationService::class)->notifyGuruPengajarAssignments([[
            'tenaga_pendidik_id' => $secretaryProfile->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
        ]]);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $secretary->id,
            'judul' => 'Penugasan Mengajar Baru',
        ]);

        app(NotificationService::class)->notifyGuruPengajarAssignments([[
            'tenaga_pendidik_id' => $teacherProfile->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
        ]]);

        $this->assertSame(1, Notification::where('user_id', $teacher->id)
            ->where('judul', 'Penugasan Mengajar Baru')
            ->count());

        $importedSchedule->update([
            'guru_id' => $teacherProfile->id,
            'status' => 'aktif',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update-tenaga-pendidik', $teacher->id), [
                'nama_lengkap' => $teacherProfile->nama_lengkap,
                'email' => $teacher->email,
                'username' => $teacher->username,
                'role' => 'sekretaris',
                'cabang_id' => $cabang->id,
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '1990-01-01',
                'alamat' => 'Alamat pengujian',
                'telepon' => '081234567890',
                'pendidikan_terakhir' => 'S1',
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('role');

        $this->assertSame('guru_pengajar', $teacher->fresh()->role);
    }
}
