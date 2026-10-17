<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Cabang;
use App\Models\Siswa;
use App\Models\User;
use App\Support\Panduan;
use App\Support\Tampil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Daftar memakai "Tampilkan semua" (?tampil=semua) sebagai pengganti pagination bernomor,
 * dan setiap halaman yang punya panduan menampilkannya lewat layout dalam keadaan tertutup.
 */
class TampilkanSemuaTest extends TestCase
{
    use RefreshDatabase;

    private function buatSiswa(int $jumlah, Cabang $cabang, string $prefix = 'Siswa Uji'): void
    {
        for ($i = 1; $i <= $jumlah; $i++) {
            $nama = sprintf('%s %02d', $prefix, $i);
            $user = User::factory()->create(['role' => 'siswa', 'is_active' => true, 'cabang_id' => $cabang->id, 'name' => $nama]);
            Siswa::create([
                'user_id' => $user->id,
                'cabang_id' => $cabang->id,
                'nisn' => sprintf('77%08d', $user->id),
                'nama_lengkap' => $nama,
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Tangerang',
                'tanggal_lahir' => '2012-01-01',
                'alamat' => 'Alamat',
                'tanggal_masuk' => '2026-07-01',
                'status' => 'aktif',
            ]);
        }
    }

    public function test_daftar_siswa_memakai_tampilkan_semua_bukan_pagination_bernomor(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = Cabang::create(['kode_cabang' => 'TLB', 'nama_cabang' => 'Cabang Uji', 'alamat' => 'Alamat', 'is_active' => true]);
        $this->buatSiswa(20, $cabang);

        $response = $this->actingAs($admin)->get(route('admin.users.siswa'))->assertOk()
            ->assertSee('Menampilkan <strong class="font-extrabold text-slate-800">15</strong> dari <strong class="font-extrabold text-slate-800">20</strong>', false)
            ->assertSee('Tampilkan semua')
            ->assertSee('id="m-15"', false)->assertSee('id="d-15"', false)
            ->assertDontSee('Pagination Navigation');
        $this->assertCount(15, $response->viewData('siswa')->items());

        $this->get(route('admin.users.siswa', ['tampil' => 'semua']))->assertOk()
            ->assertSee('id="m-20"', false)->assertDontSee('Tampilkan semua')->assertSee('Semua data sudah ditampilkan');
    }

    public function test_filter_tetap_terbawa_saat_menampilkan_semua(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = Cabang::create(['kode_cabang' => 'TLB', 'nama_cabang' => 'Cabang Uji', 'alamat' => 'Alamat', 'is_active' => true]);
        $this->buatSiswa(17, $cabang, 'Cocok');
        $this->buatSiswa(3, $cabang, 'Lain');

        $this->actingAs($admin)->get(route('admin.users.siswa', ['search' => 'Cocok']))->assertOk()
            ->assertSee('Tampilkan semua')
            ->assertSee(e(route('admin.users.siswa', ['search' => 'Cocok', 'tampil' => 'semua'])), false);
    }

    public function test_jumlah_dibulatkan_ke_kelipatan_langkah_dan_dibatasi(): void
    {
        foreach (['-5' => 15, '16' => 30, '45' => 45, '99999' => Tampil::MAKS, 'abc' => 15, 'semua' => Tampil::MAKS] as $minta => $hasil) {
            request()->query->set('tampil', $minta);
            $this->assertSame($hasil, Tampil::jumlah(), "tampil={$minta}");
        }

        request()->query->set('tampil_siswa', '21');
        $this->assertSame(30, Tampil::jumlah(10, 'tampil_siswa'));
        $this->assertSame(10, Tampil::langkahUntuk('tampil_siswa'));
    }

    public function test_dua_daftar_dalam_satu_halaman_tidak_saling_memengaruhi(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cabang = Cabang::create(['kode_cabang' => 'TLB', 'nama_cabang' => 'Cabang Uji', 'alamat' => 'Alamat', 'is_active' => true]);
        $this->buatSiswa(14, $cabang);

        $response = $this->actingAs($admin)->get(route('admin.monitoring.pengguna', ['tampil_siswa' => 'semua']))->assertOk()
            ->assertSee('id="siswa-m-14"', false)->assertDontSee('id="m-1"', false);
        $this->assertCount(14, $response->viewData('siswa')->items());
        $this->assertSame(10, $response->viewData('tenagaPendidik')->perPage());
    }

    public function test_daftar_dari_query_builder_ikut_memakai_tampil(): void
    {
        // Rekap kenaikan kelas memakai DB::table(), bukan model Eloquent.
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        \App\Models\TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.akademik.kenaikan-kelas.report'))->assertOk();
        $this->get(route('admin.akademik.kenaikan-kelas.report', ['tab' => 'simulation']))->assertOk();
    }

    public function test_shell_lms_memakai_tema_indigo_untuk_daftar_dan_panduan(): void
    {
        $cabang = Cabang::create(['kode_cabang' => 'TLB', 'nama_cabang' => 'Cabang Uji', 'alamat' => 'Alamat', 'is_active' => true]);
        $tahun = \App\Models\TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $kelas = \App\Models\Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TLB-7A', 'kuota_siswa' => 30]);
        $mapel = \App\Models\MataPelajaran::create(['kode_mapel' => 'SMP-TLB', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMP']);
        $user = User::factory()->create(['role' => 'guru_pengajar', 'is_active' => true]);
        $guru = \App\Models\TenagaPendidik::create(['user_id' => $user->id, 'nip' => 'TLB-001', 'nama_lengkap' => 'Guru Uji', 'jenis_kelamin' => 'L', 'email' => $user->email]);
        \App\Models\GuruPengajarKelas::create(['tenaga_pendidik_id' => $guru->id, 'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id]);
        for ($i = 1; $i <= 12; $i++) {
            // Tanggal berbeda: daftar dikelompokkan per tanggal, nomor jangkar harus tetap berurutan lintas kelompok.
            \App\Models\Materi::create(['kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id, 'guru_id' => $guru->id, 'judul_materi' => "Materi {$i}", 'kategori' => 'materi', 'deskripsi' => 'Uji', 'url_materi' => 'https://example.test', 'tipe_file' => 'link', 'tanggal_upload' => now()->subDays($i % 3)->toDateString()]);
        }

        $this->actingAs($user)->get(route('guru.lms.materi.index', [$kelas->id, $mapel->id]))->assertOk()
            ->assertSee('Tampilkan semua')->assertSee('bg-indigo-50 text-indigo-700', false)->assertDontSee('bg-brand-50 text-brand-700', false)
            ->assertSee('id="m-10"', false)->assertDontSee('id="m-11"', false)
            ->assertSee('Tampilkan panduan halaman ini')->assertSee('ring-indigo-200', false);

        $this->get(route('guru.lms.materi.index', [$kelas->id, $mapel->id, 'tampil' => 'semua']))->assertOk()
            ->assertSee('id="m-12"', false)->assertDontSee('Tampilkan semua');
    }

    public function test_panduan_tampil_tertutup_dan_dipakai_bersama_antarperan(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.monitoring.pengguna'))->assertOk()
            ->assertSee('Tampilkan panduan halaman ini')
            ->assertSee('Status warga dan akun sekolah')
            ->assertSee('panduan:admin.monitoring.pengguna', false);

        $this->assertSame(
            Panduan::untuk('admin.monitoring.pengguna', 'admin'),
            Panduan::untuk('ketua.monitoring.pengguna', 'ketua_pkbm'),
        );
        $this->assertNull(Panduan::untuk('rute.yang.tidak.ada', 'admin'));
    }
}
