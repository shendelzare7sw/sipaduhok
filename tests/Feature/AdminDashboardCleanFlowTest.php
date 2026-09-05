<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardCleanFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_the_cleanflow_dashboard(): void
    {
        $this->withoutMiddleware();

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Apa yang ingin Anda kerjakan?')
            ->assertSee('Urutan persiapan sekolah')
            ->assertSee('Masukkan banyak siswa')
            ->assertSee('admin-notification-panel', false)
            ->assertSee('Tandai dibaca')
            ->assertSee('bg-[#1261a6]', false)
            ->assertSee('h-12 w-12 shrink-0 object-contain', false)
            ->assertSee('xl:grid-cols-6', false)
            ->assertSee('min-h-20', false);
    }
}
