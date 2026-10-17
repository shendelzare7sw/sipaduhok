<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Flyer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi: pop-up flyer dulu hanya dirender di dashboard SIA siswa, sehingga flyer bertarget Guru,
 * Wali Kelas, Wali Siswa, atau Semua Pengguna tidak pernah sampai. Kini dimiliki layout bersama,
 * dan penanda "sudah dilihat" berlaku per flyer, bukan sekali untuk selamanya.
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

    public function test_flyer_tampil_hanya_untuk_target_perannya(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $guru = User::factory()->create(['role' => 'guru_pengajar', 'is_active' => true]);
        $wali = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
        $flyerGuru = $this->flyer($admin, 'guru', 'Rapat Guru Uji');
        $this->flyer($admin, 'orang_tua', 'Info Wali Uji');
        $this->flyer($admin, 'guru', 'Draft Guru Uji', 'draft');

        $this->actingAs($guru)->get(route('notifications.index'))->assertOk()
            ->assertSee('Rapat Guru Uji')->assertDontSee('Info Wali Uji')->assertDontSee('Draft Guru Uji')
            ->assertSee('flyer:'.$guru->id.':'.$flyerGuru->id.':', false);

        $this->actingAs($wali)->get(route('notifications.index'))->assertOk()
            ->assertSee('Info Wali Uji')->assertDontSee('Rapat Guru Uji');

        // Peran tanpa target khusus hanya menerima flyer "Semua Pengguna".
        $this->actingAs($admin)->get(route('notifications.index'))->assertOk()
            ->assertDontSee('Rapat Guru Uji')->assertDontSee('Info Wali Uji');
        $this->flyer($admin, 'semua', 'Libur Bersama Uji');
        $this->get(route('notifications.index'))->assertOk()->assertSee('Libur Bersama Uji');
    }
}
