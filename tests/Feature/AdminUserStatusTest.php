<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Status akun (users.is_active) & status siswa (aktif/lulus/pindah/keluar) dari menu
 * Admin > Users: perubahan tersimpan, tampil jujur di daftar, dan berdampak benar saat login.
 */
class AdminUserStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Cabang $cabang;

    private Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();
        // Admin sudah mengatur keamanan (kalau tidak, middleware mengalihkan ke security setup).
        $this->admin = User::factory()->create([
            'role' => 'admin', 'is_active' => true,
            'security_question' => 'Apa nama SD Anda?', 'security_answer' => 'uji', 'security_pin' => '123456',
        ]);
        $this->cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $ta = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $this->kelas = Kelas::create(['cabang_id' => $this->cabang->id, 'tahun_ajaran_id' => $ta->id, 'nama_kelas' => '8A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-8A', 'kuota_siswa' => 30]);
    }

    private function buatSiswa(string $status = 'aktif', bool $aktif = true): Siswa
    {
        $user = User::factory()->create(['role' => 'siswa', 'is_active' => $aktif, 'username' => 'siswa.' . uniqid()]);

        return Siswa::create([
            'user_id' => $user->id, 'cabang_id' => $this->cabang->id, 'kelas_id' => $this->kelas->id,
            'nisn' => (string) random_int(1000000000, 9999999999), 'nis' => (string) random_int(100000, 999999),
            'nama_lengkap' => 'Siswa Uji', 'jenis_kelamin' => 'L', 'tempat_lahir' => 'Kota', 'tanggal_lahir' => '2012-01-01',
            'alamat' => 'Alamat', 'tanggal_masuk' => '2026-07-01', 'agama' => 'Islam', 'status' => $status,
        ]);
    }

    private function dataSiswa(Siswa $siswa, array $ubah): array
    {
        return array_merge([
            'nama_lengkap' => $siswa->nama_lengkap, 'email' => $siswa->user->email, 'username' => $siswa->user->username,
            'kelas_id' => $this->kelas->id, 'nisn' => $siswa->nisn, 'nis' => $siswa->nis, 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Kota', 'tanggal_lahir' => '2012-01-01', 'alamat' => 'Alamat', 'tanggal_masuk' => '2026-07-01',
            'agama' => 'Islam', 'status' => $siswa->status, 'is_active' => $siswa->user->is_active ? 1 : 0,
            // Field opsional selalu ikut terkirim dari form (kosong -> null).
            'personal_email' => '', 'password' => '', 'nama_ayah' => '', 'nama_ibu' => '', 'telepon_orangtua' => '',
        ], $ubah);
    }

    public function test_menonaktifkan_akun_siswa_tersimpan_dan_terlihat_di_daftar(): void
    {
        $siswa = $this->buatSiswa();

        $this->actingAs($this->admin)
            ->put(route('admin.users.update-siswa', $siswa->id), $this->dataSiswa($siswa, ['is_active' => 0]))
            ->assertSessionHasNoErrors()->assertRedirect()->assertSessionMissing('warning');

        $this->assertFalse($siswa->user->fresh()->is_active);
        $this->assertSame('aktif', $siswa->fresh()->status);

        $this->actingAs($this->admin)->get(route('admin.users.siswa'))->assertOk()->assertSee('Akun nonaktif');
    }

    public function test_menonaktifkan_tenaga_pendidik_tersimpan(): void
    {
        $guru = User::factory()->create(['role' => 'guru_pengajar', 'is_active' => true, 'username' => 'guru.uji']);
        $profil = TenagaPendidik::create(['user_id' => $guru->id, 'nip' => 'GP-1', 'nama_lengkap' => 'Guru Uji', 'jenis_kelamin' => 'L', 'email' => $guru->email, 'cabang_id' => $this->cabang->id]);

        $this->actingAs($this->admin)->put(route('admin.users.update-tenaga-pendidik', $profil->id), [
            'nama_lengkap' => 'Guru Uji', 'email' => $guru->email, 'username' => 'guru.uji', 'role' => 'guru_pengajar',
            'cabang_id' => $this->cabang->id, 'jenis_kelamin' => 'L', 'tempat_lahir' => 'Kota', 'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Alamat', 'telepon' => '081234567890', 'pendidikan_terakhir' => 'S1', 'is_active' => 0,
            'nip' => '', 'personal_email' => '', 'password' => '',
        ])->assertSessionHasNoErrors()->assertRedirect()->assertSessionMissing('warning');

        $this->assertFalse($guru->fresh()->is_active);
        $this->actingAs($this->admin)->get(route('admin.users.tenaga-pendidik'))->assertOk()->assertSee('Nonaktif');
    }

    public function test_menonaktifkan_wali_siswa_tersimpan(): void
    {
        $wali = User::factory()->create(['role' => 'orang_tua', 'is_active' => true, 'username' => 'wali.uji']);

        $this->actingAs($this->admin)->put(route('admin.users.update-wali-siswa', $wali->id), [
            'name' => $wali->name, 'username' => 'wali.uji', 'email' => $wali->email, 'is_active' => 0,
            'phone' => '', 'personal_email' => '', 'password' => '',
        ])->assertSessionHasNoErrors()->assertRedirect()->assertSessionMissing('warning');

        $this->assertFalse($wali->fresh()->is_active);
    }

    public function test_alumni_login_selalu_ke_dashboard_alumni_tanpa_popup_akses_terbatas(): void
    {
        $siswa = $this->buatSiswa('lulus');

        // URL tujuan lama (mis. profil) tidak dipakai untuk alumni.
        $this->get(route('profile.index'))->assertRedirect(route('login'));
        $this->post(route('login'), ['login' => $siswa->user->username, 'password' => 'password'])
            ->assertRedirect(route('siswa.sia.dashboard'));

        $this->get(route('siswa.dashboard'))->assertRedirect(route('siswa.sia.dashboard'))->assertSessionMissing('info');
        $this->get(route('siswa.sia.dashboard'))->assertOk()->assertSee('Dashboard Alumni');
    }

    public function test_alumni_hanya_boleh_ke_dashboard_alumni_dan_sidebar_profil_menyesuaikan(): void
    {
        $siswa = $this->buatSiswa('lulus');
        $this->actingAs($siswa->user);

        $this->get(route('siswa.sia.penilaian'))->assertRedirect(route('siswa.sia.dashboard'))->assertSessionHas('info');

        $this->get(route('profile.index'))->assertOk()
            ->assertSee('Dashboard Alumni')
            ->assertDontSee(route('siswa.sia.presensi.index'))
            ->assertDontSee(route('siswa.sia.penilaian'));
    }

    public function test_siswa_pindah_atau_keluar_tidak_bisa_memakai_area_siswa(): void
    {
        foreach (['pindah', 'keluar'] as $status) {
            $siswa = $this->buatSiswa($status, true);

            $this->actingAs($siswa->user)->get(route('siswa.sia.dashboard'))
                ->assertOk()->assertViewIs('errors.account_inactive');
            $this->assertGuest();
        }
    }

    public function test_akun_siswa_nonaktif_tidak_bisa_login(): void
    {
        $siswa = $this->buatSiswa('aktif', false);

        $this->actingAs($siswa->user)->get(route('siswa.sia.dashboard'))->assertViewIs('errors.account_inactive');
        $this->assertGuest();
    }

    public function test_mengubah_status_siswa_menyelaraskan_status_akun(): void
    {
        $siswa = $this->buatSiswa();

        // aktif -> pindah: akun otomatis dinonaktifkan.
        $this->actingAs($this->admin)->put(route('admin.users.update-siswa', $siswa->id), $this->dataSiswa($siswa, ['status' => 'pindah', 'is_active' => 1]));
        $this->assertSame('pindah', $siswa->fresh()->status);
        $this->assertFalse($siswa->user->fresh()->is_active);

        // pindah -> aktif: akun otomatis diaktifkan kembali.
        $siswa->refresh();
        $this->actingAs($this->admin)->put(route('admin.users.update-siswa', $siswa->id), $this->dataSiswa($siswa, ['status' => 'aktif', 'is_active' => 0]));
        $this->assertSame('aktif', $siswa->fresh()->status);
        $this->assertTrue($siswa->user->fresh()->is_active);

        // aktif -> lulus: akun tetap mengikuti pilihan admin (alumni boleh login).
        $siswa->refresh();
        $this->actingAs($this->admin)->put(route('admin.users.update-siswa', $siswa->id), $this->dataSiswa($siswa, ['status' => 'lulus', 'is_active' => 1]));
        $this->assertSame('lulus', $siswa->fresh()->status);
        $this->assertTrue($siswa->user->fresh()->is_active);
    }
}
