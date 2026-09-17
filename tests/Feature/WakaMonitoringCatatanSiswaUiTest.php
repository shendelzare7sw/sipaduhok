<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Catatan;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class WakaMonitoringCatatanSiswaUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_waka_cannot_open_a_teacher_assigned_only_to_another_branch(): void
    {
        $ownBranch = Cabang::create(['kode_cabang' => 'OWN', 'nama_cabang' => 'Cabang Sendiri', 'alamat' => 'A', 'is_active' => true]);
        $foreignBranch = Cabang::create(['kode_cabang' => 'FOR', 'nama_cabang' => 'Cabang Lain', 'alamat' => 'B', 'is_active' => true]);
        $tahun = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $waka = User::factory()->create(['role' => 'wakil_kepala_sekolah', 'cabang_id' => $ownBranch->id, 'is_active' => true]);
        $guruUser = User::factory()->create(['role' => 'guru_pengajar', 'cabang_id' => $foreignBranch->id, 'is_active' => true]);
        $guru = TenagaPendidik::create(['user_id' => $guruUser->id, 'nip' => 'FOR-GP-001', 'nama_lengkap' => 'Guru Cabang Lain', 'jenis_kelamin' => 'L', 'email' => $guruUser->email]);
        $kelas = Kelas::create(['cabang_id' => $foreignBranch->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '9F', 'jenjang' => 'SMP', 'kode_kelas' => 'FOR-SMP-9F', 'kuota_siswa' => 30]);
        $mapel = MataPelajaran::create(['kode_mapel' => 'FOR-MTK', 'nama_mapel' => 'Matematika Luar', 'jenjang' => 'SMP', 'is_active' => true]);
        GuruPengajarKelas::create(['tenaga_pendidik_id' => $guru->id, 'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id]);

        $this->actingAs($waka)
            ->get(route('waka.guru-pengajar.show', ['guruPengajar' => $guru, 'tahun_ajaran_id' => $tahun->id]))
            ->assertForbidden();
    }

    public function test_all_remaining_waka_flows_use_shared_cleanflow_views(): void
    {
        $cabang = Cabang::create([
            'kode_cabang' => 'WKR',
            'nama_cabang' => 'Cabang Waka Remaining',
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
        $studentUser = User::factory()->create([
            'role' => 'siswa',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);
        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahun->id,
            'nama_kelas' => '8A',
            'jenjang' => 'SMP',
            'kode_kelas' => 'WKR-SMP-8A-2026',
            'kuota_siswa' => 30,
        ]);
        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'cabang_id' => $cabang->id,
            'kelas_id' => $kelas->id,
            'nisn' => '0077777777',
            'nis' => 'WKR-001',
            'nama_lengkap' => 'Siswa Waka CleanFlow',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Tangerang',
            'tanggal_lahir' => '2012-01-02',
            'alamat' => 'Alamat siswa',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);
        $guruUser = User::factory()->create([
            'role' => 'guru_pengajar',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);
        $guru = TenagaPendidik::create([
            'user_id' => $guruUser->id,
            'nip' => 'WKR-GP-001',
            'nama_lengkap' => 'Guru Waka CleanFlow',
            'jenis_kelamin' => 'L',
            'email' => $guruUser->email,
        ]);
        $waliUser = User::factory()->create([
            'role' => 'wali_kelas',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);
        $wali = TenagaPendidik::create([
            'user_id' => $waliUser->id,
            'nip' => 'WKR-WK-001',
            'nama_lengkap' => 'Wali Waka CleanFlow',
            'jenis_kelamin' => 'P',
            'email' => $waliUser->email,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'WKR-MTK-001',
            'nama_mapel' => 'Matematika Waka',
            'jenjang' => 'SMP',
            'is_active' => true,
        ]);
        GuruPengajarKelas::create([
            'tenaga_pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $mapel->id,
        ]);
        WaliKelasAssignment::create([
            'tenaga_pendidik_id' => $wali->id,
            'kelas_id' => $kelas->id,
            'assigned_at' => now(),
        ]);
        $catatan = Catatan::create([
            'pengirim_id' => $waka->id,
            'judul' => 'Catatan pengujian Waka',
            'isi_catatan' => 'Tindak lanjut akademik cabang.',
            'tipe_penerima' => 'individu',
            'penerima_id' => $studentUser->id,
            'prioritas' => 'biasa',
            'tanggal_kirim' => now(),
        ]);

        $pages = [
            route('waka.monitoring.siswa') => 'admin.monitoring.siswa',
            route('waka.monitoring.guru-pengajar') => 'admin.monitoring.guru-pengajar',
            route('waka.monitoring.wali-kelas') => 'admin.monitoring.wali-kelas',
            route('waka.catatan.index') => 'admin.catatan.index',
            route('waka.catatan.create') => 'admin.catatan.create',
            route('waka.catatan.show', $catatan) => 'admin.catatan.show',
            route('waka.manajemen-siswa.index') => 'admin.manajemen-siswa.index',
            route('waka.manajemen-siswa.show', $siswa) => 'admin.manajemen-siswa.show',
            route('waka.manajemen-siswa.per-kelas', $kelas) => 'admin.manajemen-siswa.per-kelas',
            route('waka.manajemen-siswa.print') => 'admin.manajemen-siswa.print',
            route('waka.manajemen-siswa.print-kartu', $siswa) => 'admin.manajemen-siswa.print-kartu',
            route('waka.guru-pengajar.index', ['tahun_ajaran_id' => $tahun->id]) => 'admin.guru-pengajar.index',
            route('waka.guru-pengajar.show', ['guruPengajar' => $guru, 'tahun_ajaran_id' => $tahun->id]) => 'admin.guru-pengajar.show',
            route('waka.guru-pengajar.manage-kelas', $kelas) => 'admin.guru-pengajar.manage-kelas',
            route('waka.guru-pengajar.print', ['tahun_ajaran_id' => $tahun->id]) => 'admin.guru-pengajar.print',
            route('waka.wali-kelas.index', ['tahun_ajaran_id' => $tahun->id]) => 'admin.wali-kelas.index',
            route('waka.wali-kelas.show', $kelas) => 'admin.wali-kelas.show',
            route('waka.wali-kelas.print', ['tahun_ajaran_id' => $tahun->id]) => 'admin.wali-kelas.print',
        ];

        foreach ($pages as $url => $view) {
            $this->actingAs($waka)->get($url)
                ->assertOk()
                ->assertViewIs($view)
                ->assertDontSee('data-bs-', false)
                ->assertDontSee('form-control', false)
                ->assertDontSee('resources/css/waka/', false)
                ->assertDontSee('resources/js/waka/', false)
                ->assertDontSee('/admin/manajemen-siswa', false)
                ->assertDontSee('/admin/monitoring', false)
                ->assertDontSee('/admin/catatan', false)
                ->assertDontSee('/admin/guru-pengajar', false)
                ->assertDontSee('/admin/wali-kelas', false);
        }

        $this->actingAs($waka)->get(route('waka.catatan.create'))
            ->assertSee('name="penerima_id"', false)
            ->assertDontSee('name="penerima_ids[]"', false);

        $this->actingAs($waka)->get(route('waka.manajemen-siswa.show', $siswa))
            ->assertSee('Kelas dan penempatan')
            ->assertSee('Cetak kartu')
            ->assertSee('text-white no-underline ring-1', false)
            ->assertDontSee('[&_a:first-child]:!text-brand-700', false)
            ->assertDontSee('Edit data')
            ->assertDontSee('detachParentModal', false);

        $this->actingAs($waka)->get(route('waka.manajemen-siswa.index'))
            ->assertDontSee('name="cabang_id"', false)
            ->assertSee('Siswa Waka CleanFlow');

        $this->actingAs($waka)->get(route('waka.wali-kelas.index', ['tahun_ajaran_id' => $tahun->id]))
            ->assertDontSee('name="cabang_id"', false)
            ->assertSee('Wali Waka CleanFlow');

        $this->actingAs($waka)->get(route('waka.wali-kelas.show', $kelas))
            ->assertSee('Wali yang bertugas')
            ->assertDontSee('remove-assignment', false);

        foreach ([
            resource_path('views/waka/monitoring'),
            resource_path('views/waka/catatan'),
            resource_path('css/waka/monitoring'),
            resource_path('js/waka/monitoring'),
            resource_path('css/waka/catatan'),
            resource_path('js/waka/catatan'),
            resource_path('views/waka/manajemen-siswa'),
            resource_path('css/waka/manajemen-siswa'),
            resource_path('js/waka/manajemen-siswa'),
            resource_path('views/waka/guru-pengajar'),
            resource_path('css/waka/guru-pengajar'),
            resource_path('js/waka/guru-pengajar'),
            resource_path('views/waka/wali-kelas'),
            resource_path('css/waka/wali-kelas'),
            resource_path('js/waka/wali-kelas'),
        ] as $removedPath) {
            $this->assertFalse(File::exists($removedPath), "Sisa legacy masih ada: {$removedPath}");
        }
    }
}
