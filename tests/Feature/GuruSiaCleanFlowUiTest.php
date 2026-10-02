<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\CatatanMonitoring;
use App\Models\GuruPengajarKelas;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GuruSiaCleanFlowUiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private TenagaPendidik $guru;

    private Kelas $kelas;

    private MataPelajaran $mapel;

    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $this->tahunAjaran = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $this->kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $this->tahunAjaran->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-7A-2026', 'kuota_siswa' => 30]);
        $this->mapel = MataPelajaran::create(['kode_mapel' => 'SMP-001', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMP']);

        $this->user = User::factory()->create(['role' => 'guru_pengajar', 'is_active' => true]);
        $this->guru = TenagaPendidik::create(['user_id' => $this->user->id, 'nip' => 'GP-001', 'nama_lengkap' => 'Guru Pengujian', 'jenis_kelamin' => 'L', 'email' => $this->user->email]);
        GuruPengajarKelas::create(['tenaga_pendidik_id' => $this->guru->id, 'kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id]);
    }

    private function makeArsip(): array
    {
        $materi = Materi::create(['kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'judul_materi' => 'Materi Aljabar', 'kategori' => 'materi', 'deskripsi' => 'Pengantar', 'url_materi' => 'https://example.test/materi', 'tipe_file' => 'link', 'tanggal_upload' => now()->toDateString()]);
        $tugas = Tugas::create(['kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'jenis_tugas' => 'tugas', 'judul_tugas' => 'Tugas Bab 1', 'deskripsi' => 'Kerjakan', 'tanggal_mulai' => now()->toDateString(), 'tanggal_deadline' => now()->addWeek()->toDateString()]);
        $ujian = Ujian::create(['kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'judul_ujian' => 'UH 1', 'deskripsi' => 'desc', 'tanggal_mulai' => now(), 'tanggal_selesai' => now()->addDays(7), 'durasi_menit' => 60, 'is_active' => true, 'tampilkan_nilai' => true, 'bisa_diulang' => false, 'batas_pengulangan' => 1, 'tampilkan_riwayat' => false, 'tipe_ujian' => Ujian::TIPE_ULANGAN_HARIAN]);

        return compact('materi', 'tugas', 'ujian');
    }

    public function test_sia_pages_render_with_tailwind_and_no_legacy_markup(): void
    {
        $arsip = $this->makeArsip();
        $catatan = CatatanMonitoring::create([
            'pengirim_id' => User::factory()->create(['role' => 'ketua_pkbm'])->id,
            'pengirim_role' => 'ketua_pkbm',
            'guru_id' => $this->guru->id,
            'konten_type' => 'materi',
            'konten_id' => $arsip['materi']->id,
            'kelas_id' => $this->kelas->id,
            'mata_pelajaran_id' => $this->mapel->id,
            'isi_catatan' => 'Mohon lengkapi deskripsi materi.',
        ]);

        $this->actingAs($this->user);

        $urls = [
            route('guru.dashboard'),
            route('guru.jadwal.index'),
            route('guru.kelas.index'),
            route('guru.kelas.mapel', $this->kelas->id),
            route('guru.lms.arsip.index'),
            route('guru.lms.arsip.form-salin', ['materi', $arsip['materi']->id]),
            route('guru.lms.arsip.form-salin', ['ujian', $arsip['ujian']->id]),
            route('guru.lms.arsip.preview', ['materi', $arsip['materi']->id]),
            route('guru.lms.arsip.preview', ['tugas', $arsip['tugas']->id]),
            route('guru.lms.arsip.preview', ['ujian', $arsip['ujian']->id]),
            route('guru.lms.catatan-monitoring.index'),
            route('guru.lms.catatan-monitoring.show', $catatan->id),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk()
                ->assertSee('cleanflow-nav', false)
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('form-control', false)
                ->assertDontSee('form-select', false)
                ->assertDontSee('btn btn-', false)
                ->assertDontSee('class="card', false)
                ->assertDontSee('container-fluid', false)
                ->assertDontSee('resources/css/guru', false)
                ->assertDontSee('resources/js/guru', false)
                ->assertDontSee('resources/css/dashboard/guru', false);
        }
    }

    public function test_dashboard_and_class_pages_keep_their_contract(): void
    {
        $this->actingAs($this->user);

        $this->get(route('guru.dashboard'))->assertOk()
            ->assertSee('Kelas diampu')->assertSee('Akses cepat')->assertSee(route('guru.lms.arsip.index'), false);

        $this->get(route('guru.kelas.index'))->assertOk()
            ->assertSee('Kelola kelas')->assertSee(route('guru.kelas.mapel', $this->kelas->id), false);

        $this->get(route('guru.kelas.mapel', $this->kelas->id))->assertOk()
            ->assertSee('Masuk LMS')->assertSee(route('guru.lms.dashboard', [$this->kelas->id, $this->mapel->id]), false);
    }

    public function test_arsip_index_supports_bulk_copy_and_preview_reuses_the_shared_content(): void
    {
        $arsip = $this->makeArsip();
        $this->actingAs($this->user);

        $this->get(route('guru.lms.arsip.index'))->assertOk()
            ->assertSee('Materi Aljabar')->assertSee('Tugas Bab 1')->assertSee('UH 1')
            ->assertSee('name="items[]"', false)
            ->assertSee('value="materi:'.$arsip['materi']->id.'"', false)
            ->assertSee(route('guru.lms.arsip.salin-bulk'), false)
            ->assertSee('Pilih semua');

        $this->get(route('guru.lms.arsip.preview', ['tugas', $arsip['tugas']->id]))->assertOk()
            ->assertViewIs('monitoring-lms.preview.tugas')
            ->assertSee('Mode arsip (baca saja)')
            ->assertSee(route('guru.lms.arsip.form-salin', ['tugas', $arsip['tugas']->id]), false)
            ->assertDontSee('Kirim catatan');

        $this->get(route('guru.lms.arsip.preview', ['ujian', $arsip['ujian']->id]))->assertOk()
            ->assertViewIs('monitoring-lms.preview.ujian')
            ->assertSee('UH 1');
    }

    public function test_guru_sia_views_stay_free_of_page_assets_and_inline_styles(): void
    {
        $paths = [
            resource_path('views/dashboard/guru.blade.php'),
            resource_path('views/guru/jadwal/index.blade.php'),
            resource_path('views/guru/kelas/index.blade.php'),
            resource_path('views/guru/kelas/mapel.blade.php'),
            resource_path('views/guru/partials/sidebar.blade.php'),
            resource_path('views/guru/lms/arsip/index.blade.php'),
            resource_path('views/guru/lms/arsip/form-salin.blade.php'),
            resource_path('views/guru/lms/arsip/preview-wrapper.blade.php'),
            resource_path('views/guru/lms/catatan-monitoring/index.blade.php'),
            resource_path('views/guru/lms/catatan-monitoring/show.blade.php'),
        ];

        foreach ($paths as $path) {
            $source = File::get($path);

            $this->assertStringNotContainsString('<style', $source, $path);
            $this->assertStringNotContainsString('style="', $source, $path);
            $this->assertStringNotContainsString('@vite', $source, $path);
            $this->assertStringNotContainsString("@section('styles')", $source, $path);
            $this->assertStringNotContainsString('@push(\'scripts\')', $source, $path);
            $this->assertStringNotContainsString('data-bs-', $source, $path);
            $this->assertStringNotContainsString('class="row', $source, $path);
            $this->assertStringNotContainsString('btn btn-', $source, $path);
        }
    }

    public function test_schedule_cards_and_todays_sessions_render_with_data(): void
    {
        $hariIni = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'][now()->dayOfWeek];

        foreach (['Rabu' => '10:00', $hariIni => '07:30', 'Senin' => '08:00'] as $hari => $jam) {
            $jadwal = JadwalPelajaran::create([
                'guru_id' => $this->guru->id,
                'kelas_id' => $this->kelas->id,
                'mata_pelajaran_id' => $this->mapel->id,
                'hari' => $hari,
                'jam_mulai' => $jam,
                'jam_selesai' => '09:00',
                'status' => 'kosong',
                'tahun_ajaran_id' => $this->tahunAjaran->id,
            ]);
            $jadwal->kelas()->syncWithoutDetaching([$this->kelas->id]);
        }

        $this->actingAs($this->user);

        $html = $this->get(route('guru.jadwal.index'))->assertOk()
            ->assertSee('Total jadwal')->assertSee('Matematika')->assertSee('7A')
            ->getContent();
        $this->assertLessThan(strpos($html, '>Rabu<'), strpos($html, '>Senin<'), 'Hari harus berurutan Senin sebelum Rabu.');

        $this->get(route('guru.dashboard'))->assertOk()
            ->assertSee('Masuk LMS')
            ->assertSee(route('guru.lms.dashboard', [$this->kelas->id, $this->mapel->id]), false);
    }
}

