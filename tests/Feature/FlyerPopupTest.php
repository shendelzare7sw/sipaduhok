<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Flyer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pop-up flyer: tampil SEKALI per login, hanya di dashboard peran, dan hanya untuk target perannya.
 * Regresi: dulu hanya dirender di dashboard SIA siswa sehingga flyer bertarget Guru, Wali Kelas,
 * Wali Siswa, atau Semua Pengguna tidak pernah sampai.
 */
class FlyerPopupTest extends TestCase
{
    use RefreshDatabase;

    private function flyer(User $pembuat, string $target, string $judul, string $status = 'aktif'): Flyer
    {
        return Flyer::create([
            'judul' => $judul,
            'gambar_flyer' => 'flyer/uji.jpg',
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->addDay()->toDateString(),
            'target_audience' => $target,
            'urutan_tampil' => 1,
            'status' => $status,
            'dibuat_oleh' => $pembuat->id,
        ]);
    }

    public function test_flyer_tampil_sekali_di_dashboard_setelah_login_sungguhan(): void
    {
        /** @var User $wali */
        $wali = User::factory()->create(['role' => 'orang_tua', 'is_active' => true, 'username' => 'wali.flyer']);
        $this->flyer($wali, 'orang_tua', 'Info Wali Uji');

        $this->post('/login', ['login' => 'wali.flyer', 'password' => 'password'])
            ->assertRedirect(route('wali-siswa.dashboard'));

        $this->get(route('wali-siswa.dashboard'))->assertOk()->assertSee('Info Wali Uji')->assertSee('data-flyer-popup', false);
        // Setelah ditutup / dashboard dibuka lagi pada login yang sama: tidak muncul lagi.
        $this->get(route('wali-siswa.dashboard'))->assertOk()->assertDontSee('Info Wali Uji');
    }

    public function test_flyer_hanya_di_dashboard_dan_sesuai_target_peran(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        /** @var User $wali */
        $wali = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
        $this->flyer($admin, 'guru', 'Rapat Guru Uji');
        $this->flyer($admin, 'orang_tua', 'Info Wali Uji');
        $this->flyer($admin, 'orang_tua', 'Draft Wali Uji', 'draft');

        // Target sesuai peran; flyer draft dan target lain tidak tampil.
        $this->actingAs($wali)->withSession(['flyer_awal_login' => true])
            ->get(route('wali-siswa.dashboard'))->assertOk()
            ->assertSee('Info Wali Uji')->assertDontSee('Rapat Guru Uji')->assertDontSee('Draft Wali Uji');

        // Halaman pertama setelah login bukan dashboard: tidak tampil, dan tidak menyusul kemudian.
        $this->actingAs($wali)->withSession(['flyer_awal_login' => true])
            ->get(route('notifications.index'))->assertOk()->assertDontSee('Info Wali Uji');
        $this->get(route('wali-siswa.dashboard'))->assertOk()->assertDontSee('Info Wali Uji');

        // Tanpa login baru, dashboard tidak menampilkan flyer.
        $this->actingAs($wali)->get(route('wali-siswa.dashboard'))->assertOk()->assertDontSee('Info Wali Uji');

        // Peran tanpa target khusus hanya menerima flyer "Semua Pengguna".
        $this->flyer($admin, 'semua', 'Libur Bersama Uji');
        $this->actingAs($admin)->withSession(['flyer_awal_login' => true])
            ->get(route('admin.dashboard'))->assertOk()
            ->assertSee('Libur Bersama Uji')->assertDontSee('Rapat Guru Uji')->assertDontSee('Info Wali Uji');
    }
}
