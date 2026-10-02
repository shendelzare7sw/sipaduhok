<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SiswaSiaCleanFlowUiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Siswa $siswa;

    private Kelas $kelas;

    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        AppSetting::updateOrCreate(['key' => 'lms_allowed_jenjang'], ['value' => json_encode(['SMP', 'SMA'])]);

        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $this->tahunAjaran = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $this->kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $this->tahunAjaran->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-7A-2026', 'kuota_siswa' => 30]);

        $this->user = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $this->siswa = Siswa::create([
            'user_id' => $this->user->id,
            'cabang_id' => $cabang->id,
            'kelas_id' => $this->kelas->id,
            'nisn' => '1234567890',
            'nis' => '2026001',
            'nama_lengkap' => 'Siswa Pengujian',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Kota',
            'tanggal_lahir' => '2012-01-01',
            'alamat' => 'Alamat',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);
    }

    public function test_sia_pages_render_on_the_cleanflow_shell_without_legacy_markup(): void
    {
        $guru = TenagaPendidik::create(['user_id' => User::factory()->create(['role' => 'guru_pengajar'])->id, 'nip' => 'GP-001', 'nama_lengkap' => 'Guru Pengujian', 'jenis_kelamin' => 'L', 'email' => 'guru@test.local']);
        $mapel = MataPelajaran::create(['kode_mapel' => 'SMP-001', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMP']);

        Presensi::create(['siswa_id' => $this->siswa->id, 'kelas_id' => $this->kelas->id, 'tanggal' => now()->toDateString(), 'status' => 'hadir', 'keterangan' => 'Masuk tepat waktu', 'diinput_oleh' => $this->user->id]);
        Presensi::create(['siswa_id' => $this->siswa->id, 'kelas_id' => $this->kelas->id, 'tanggal' => now()->startOfMonth()->toDateString(), 'status' => 'sakit', 'keterangan' => 'Demam', 'bukti_file' => 'bukti/surat.pdf', 'diinput_oleh' => $this->user->id]);

        Nilai::create([
            'siswa_id' => $this->siswa->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => $this->kelas->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => Nilai::getCurrentSemester(),
            'guru_id' => $guru->id,
            'rata_tugas' => 88.5,
            'pts' => 0,
            'nilai_akhir' => 91,
        ]);

        $this->actingAs($this->user);

        $responses = [
            $this->get(route('siswa.sia.dashboard')),
            $this->get(route('siswa.sia.presensi.index')),
            $this->get(route('siswa.sia.penilaian')),
        ];

        foreach ($responses as $response) {
            $response->assertOk()
                ->assertSee('cleanflow-nav', false)
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('form-control', false)
                ->assertDontSee('btn btn', false)
                ->assertDontSee('s-card', false)
                ->assertDontSee('resources/css/siswa', false)
                ->assertDontSee('resources/js/siswa', false);
        }

        $responses[0]->assertSee('Jadwal hari ini')->assertSee('Progres tugas')->assertSee('Nilai terbaru')->assertSee('91.0');
        $responses[1]->assertSee('x-data', false)->assertSee('Lihat bukti')->assertSee('x-ref="buktiDialog"', false)->assertSee('Masuk tepat waktu');
        $responses[2]->assertSee('x-data', false)->assertSee('Matematika')->assertSee('A (Sangat Baik)')->assertSee('88.5')->assertSee('0.0');
    }

    public function test_sidebar_only_offers_lms_presensi_and_penilaian_for_siswa(): void
    {
        $html = $this->actingAs($this->user)->get(route('siswa.sia.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString(route('siswa.lms.dashboard'), $html);
        $this->assertStringContainsString('HOK-LMS', $html);
        $this->assertStringContainsString(route('siswa.sia.presensi.index'), $html);
        $this->assertStringContainsString(route('siswa.sia.penilaian'), $html);

        $this->assertStringNotContainsString('/siswa/sia/pembayaran', $html);
        $this->assertStringNotContainsString('/siswa/sia/rapor', $html);
        $this->assertStringNotContainsString('wali-siswa/', $html);
    }

    public function test_lms_menu_and_shortcuts_follow_the_lms_setting(): void
    {
        AppSetting::updateOrCreate(['key' => 'lms_allowed_jenjang'], ['value' => json_encode(['SMA'])]);

        $html = $this->actingAs($this->user)->get(route('siswa.sia.dashboard'))->assertOk()->getContent();

        $this->assertStringNotContainsString('HOK-LMS', $html);
        $this->assertStringNotContainsString(route('siswa.lms.dashboard'), $html);
        $this->assertStringNotContainsString('Progres tugas', $html);
    }

    public function test_payment_and_report_routes_are_not_available_to_siswa(): void
    {
        $this->assertFalse(Route::has('siswa.sia.pembayaran.index'));
        $this->assertFalse(Route::has('siswa.sia.pembayaran.riwayat'));
        $this->assertFalse(Route::has('siswa.sia.pembayaran.bayar'));
        $this->assertFalse(Route::has('siswa.sia.pembayaran.cetak'));
        $this->assertFalse(Route::has('siswa.sia.rapor.index'));

        $this->actingAs($this->user);

        $this->get('/siswa/sia/pembayaran')->assertNotFound();
        $this->get('/siswa/sia/pembayaran/riwayat')->assertNotFound();
        $this->post('/siswa/sia/pembayaran/bayar')->assertNotFound();
        $this->get('/siswa/sia/rapor')->assertNotFound();
    }

    public function test_alumni_dashboard_uses_tailwind_and_a_minimal_sidebar(): void
    {
        $this->siswa->update(['status' => 'lulus']);

        $this->actingAs($this->user)->get(route('siswa.sia.dashboard'))
            ->assertOk()
            ->assertViewIs('siswa.alumni.dashboard')
            ->assertSee('Dashboard Alumni')
            ->assertSee('Belum ada rapor yang tercatat untuk Anda.')
            ->assertSee('data-confirm="logout"', false)
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('container-xxl', false)
            ->assertDontSee('resources/css/siswa', false)
            ->assertDontSee('resources/js/siswa', false)
            ->assertDontSee(route('siswa.sia.presensi.index'), false);
    }

    public function test_siswa_sia_views_stay_free_of_page_assets_and_inline_styles(): void
    {
        $paths = [
            resource_path('views/siswa/partials/sidebar-sia.blade.php'),
            resource_path('views/siswa/sia/dashboard.blade.php'),
            resource_path('views/siswa/sia/presensi/index.blade.php'),
            resource_path('views/siswa/sia/penilaian/index.blade.php'),
            resource_path('views/siswa/alumni/dashboard.blade.php'),
        ];

        foreach ($paths as $path) {
            $source = File::get($path);

            $this->assertStringNotContainsString('<style', $source, $path);
            $this->assertStringNotContainsString('style="', $source, $path);
            $this->assertStringNotContainsString('@vite', $source, $path);
            $this->assertStringNotContainsString("@push('styles')", $source, $path);
            $this->assertStringNotContainsString('data-bs-', $source, $path);
            $this->assertStringNotContainsString('class="row', $source, $path);
            $this->assertStringNotContainsString('btn btn-', $source, $path);
        }
    }
}
