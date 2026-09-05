<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\AppSetting;
use App\Models\RecoveryTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminSystemSettingsUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_recovery_ai_and_lms_pages_use_cleanflow_without_page_assets(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $requester = User::factory()->create([
            'role' => 'guru_pengajar',
            'is_active' => true,
            'name' => 'Guru Pemulihan',
            'personal_email' => 'guru.pemulihan@example.test',
        ]);

        RecoveryTicket::create([
            'user_id' => $requester->id,
            'tipe_recovery' => 'lupa_password',
            'status' => 'pending_admin',
            'token_reset' => str_repeat('a', 64),
        ]);
        RecoveryTicket::create([
            'user_id' => $requester->id,
            'tipe_recovery' => 'lupa_username',
            'status' => 'resolved',
        ]);
        AppSetting::updateOrCreate(['key' => 'lms_allowed_jenjang'], ['value' => json_encode(['SD', 'SMP'])]);

        $this->actingAs($admin)->get(route('admin.recovery-tickets.index'))
            ->assertOk()
            ->assertSee('Antrean pemulihan akun')
            ->assertSee('Guru Pemulihan')
            ->assertSee('table-fixed', false)
            ->assertSee('data-confirm', false)
            ->assertDontSee('resources/css/admin/recovery-tickets', false)
            ->assertDontSee('data-bs-toggle', false);

        $this->actingAs($admin)->get(route('admin.recovery-tickets.history'))
            ->assertOk()
            ->assertSee('Riwayat pemulihan akun')
            ->assertSee('Guru Pemulihan')
            ->assertSee('x-data=', false)
            ->assertDontSee('<script src="https://cdn.jsdelivr.net/npm/sweetalert2', false);

        $this->actingAs($admin)->get(route('admin.ai-settings.index'))
            ->assertOk()
            ->assertSee('Konfigurasi AI terpadu')
            ->assertSee('Simpan seluruh pengaturan')
            ->assertSee('x-data=', false)
            ->assertDontSee('resources/js/admin/ai-settings', false)
            ->assertDontSee('class="row', false);

        $this->actingAs($admin)->get(route('admin.lms-settings.index'))
            ->assertOk()
            ->assertSee('Jenjang pengguna LMS')
            ->assertSee('Data tetap aman')
            ->assertSee('name="jenjang[]"', false)
            ->assertDontSee('resources/css/admin/lms-settings', false)
            ->assertDontSee('form-switch', false);

        $this->assertFalse(File::isDirectory(resource_path('css/admin/recovery-tickets')));
        $this->assertFalse(File::isDirectory(resource_path('js/admin/recovery-tickets')));
        $this->assertFalse(File::isDirectory(resource_path('css/admin/ai-settings')));
        $this->assertFalse(File::isDirectory(resource_path('js/admin/ai-settings')));
        $this->assertFalse(File::isDirectory(resource_path('css/admin/lms-settings')));
    }
}
