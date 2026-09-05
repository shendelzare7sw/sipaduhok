<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\PengaturanIstirahat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminPengaturanIstirahatUiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function pengaturan(): PengaturanIstirahat
    {
        return PengaturanIstirahat::create([
            'jenjang' => 'SMP',
            'urutan' => 1,
            'jam_mulai' => '09:00',
            'jam_selesai' => '09:20',
            'hari_aktif' => ['Senin', 'Selasa'],
            'nama_istirahat' => 'Istirahat pagi',
            'is_active' => true,
        ]);
    }

    public function test_all_break_setting_pages_use_the_cleanflow_ui(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = $this->admin();
        $pengaturan = $this->pengaturan();

        $this->actingAs($admin)->get(route('admin.pengaturan-istirahat.index'))
            ->assertOk()
            ->assertSee('min-w-0 w-full space-y-5', false)
            ->assertSee('Waktu istirahat per jenjang')
            ->assertSee('Istirahat pagi')
            ->assertSee('aria-label="Edit waktu istirahat"', false)
            ->assertSee('data-confirm', false)
            ->assertDontSee('data-bs-toggle', false);

        $this->actingAs($admin)->get(route('admin.pengaturan-istirahat.create', ['jenjang' => 'SMP']))
            ->assertOk()
            ->assertSee('name="jenjang" value="SMP"', false)
            ->assertSee('Jenjang dikunci dari halaman sebelumnya.');

        $this->actingAs($admin)->get(route('admin.pengaturan-istirahat.edit', $pengaturan->id))
            ->assertOk()
            ->assertSee('Edit waktu istirahat')
            ->assertSee('Istirahat pagi')
            ->assertSee('name="is_active"', false);

        $this->assertFalse(File::exists(resource_path('css/admin/pengaturan-istirahat/index.css')));
    }
}
