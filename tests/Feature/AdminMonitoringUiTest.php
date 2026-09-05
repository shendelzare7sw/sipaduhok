<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Cabang;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Ujian;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminMonitoringUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_monitoring_pages_use_responsive_views_without_local_assets(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $tahun = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $guruUser = User::factory()->create(['role' => 'guru_pengajar', 'is_active' => true, 'cabang_id' => $cabang->id]);
        $guru = TenagaPendidik::create(['user_id' => $guruUser->id, 'nip' => 'MON-001', 'nama_lengkap' => 'Guru Monitoring', 'jenis_kelamin' => 'L', 'email' => $guruUser->email]);
        $kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-7A-2026', 'kuota_siswa' => 30, 'wali_kelas_id' => $guru->id]);
        $mapel = MataPelajaran::create(['kode_mapel' => 'SMP-MON', 'nama_mapel' => 'Mapel Monitoring', 'jenjang' => 'SMP', 'is_active' => true]);
        GuruPengajarKelas::create(['tenaga_pendidik_id' => $guru->id, 'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id]);
        WaliKelasAssignment::create(['tenaga_pendidik_id' => $guru->id, 'kelas_id' => $kelas->id, 'assigned_at' => now()]);
        $ujian = Ujian::create([
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $guru->id,
            'judul_ujian' => 'Ujian Monitoring',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addHour(),
            'durasi_menit' => 60,
            'is_active' => true,
            'tipe_ujian' => Ujian::TIPE_ULANGAN_HARIAN,
        ]);
        $studentUser = User::factory()->create(['role' => 'siswa', 'is_active' => true, 'cabang_id' => $cabang->id]);
        Siswa::create(['user_id' => $studentUser->id, 'cabang_id' => $cabang->id, 'kelas_id' => $kelas->id, 'nisn' => '0088888888', 'nama_lengkap' => 'Siswa Monitoring', 'jenis_kelamin' => 'P', 'tempat_lahir' => 'Tangerang', 'tanggal_lahir' => '2012-02-03', 'alamat' => 'Alamat siswa', 'tanggal_masuk' => '2026-07-01', 'status' => 'aktif']);

        $this->actingAs($admin)->get(route('admin.monitoring.pengguna'))
            ->assertOk()->assertSee('Status warga dan akun sekolah')->assertSee('Guru Monitoring')->assertSee('Siswa Monitoring')
            ->assertSee('table-fixed', false)->assertDontSee('resources/css/admin/monitoring/pengguna.css', false);

        $this->actingAs($admin)->get(route('admin.monitoring.siswa'))
            ->assertOk()->assertSee('Aktivitas belajar dan keuangan')->assertSee('Siswa Monitoring')
            ->assertSee('<progress', false)->assertDontSee('data-monitoring-progress', false);

        $this->actingAs($admin)->get(route('admin.monitoring.guru-pengajar', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Aktivitas guru dan kelengkapan nilai')->assertSee('Guru Monitoring')->assertSee('Mapel Monitoring')
            ->assertSee('name="tahun_ajaran_id"', false)->assertDontSee('resources/js/admin/monitoring/guru-pengajar.js', false);

        $this->actingAs($admin)->get(route('admin.monitoring.wali-kelas', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('Progress penerbitan rapor')->assertSee('Guru Monitoring')->assertSee('7A')
            ->assertSee('name="tahun_ajaran_id"', false)->assertDontSee('resources/css/admin/monitoring/wali-kelas.css', false);

        $this->actingAs($admin)->get(route('admin.monitoring.lms.index', ['tahun_ajaran_id' => $tahun->id]))
            ->assertOk()->assertSee('data-monitoring-lms-index', false)->assertSee('Temukan kelas yang perlu ditinjau')->assertSee('7A')
            ->assertDontSee('resources/css/monitoring-lms/index.css', false);

        $this->actingAs($admin)->get(route('admin.monitoring.lms.kelas', $kelas))
            ->assertOk()
            ->assertSee('data-monitoring-lms-detail', false)
            ->assertSee('x-ref="noteDialog"', false)
            ->assertSee('Saring konten kelas')
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('resources/css/monitoring-lms/kelas-detail.css', false);

        $this->actingAs($admin)->get(route('admin.monitoring.lms.preview', ['type' => 'ujian', 'id' => $ujian->id]))
            ->assertOk()
            ->assertSee('data-monitoring-lms-preview', false)
            ->assertSee('Ujian Monitoring')
            ->assertSee('Mode pratinjau aman')
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('resources/css/monitoring-lms/preview.css', false);

        $sharedLmsViews = [
            'views/monitoring-lms/index.blade.php',
            'views/monitoring-lms/kelas-detail.blade.php',
            'views/monitoring-lms/partials/konten-grouped.blade.php',
            'views/monitoring-lms/partials/modal-catatan.blade.php',
            'views/monitoring-lms/preview/wrapper.blade.php',
            'views/monitoring-lms/preview/materi.blade.php',
            'views/monitoring-lms/preview/tugas.blade.php',
            'views/monitoring-lms/preview/ujian.blade.php',
        ];

        foreach ($sharedLmsViews as $view) {
            $contents = File::get(resource_path($view));
            $this->assertStringNotContainsString('@vite(', $contents, $view.' masih memuat aset halaman.');
            $this->assertStringNotContainsString('data-bs-', $contents, $view.' masih memuat interaksi Bootstrap.');
            $this->assertStringNotContainsString('form-control', $contents, $view.' masih memakai class Bootstrap.');
        }

        $this->assertFalse(File::isDirectory(resource_path('css/admin/monitoring')));
        $this->assertFalse(File::isDirectory(resource_path('js/admin/monitoring')));
        $this->assertFalse(File::exists(resource_path('css/monitoring-lms/index.css')));
        $this->assertFalse(File::exists(resource_path('js/monitoring-lms/index.js')));
        foreach (['kelas-detail.css', 'modal-catatan.css', 'preview.css'] as $asset) {
            $this->assertFalse(File::exists(resource_path('css/monitoring-lms/'.$asset)));
        }
        foreach (['kelas-detail.js', 'modal-catatan.js', 'preview.js'] as $asset) {
            $this->assertFalse(File::exists(resource_path('js/monitoring-lms/'.$asset)));
        }
    }
}
