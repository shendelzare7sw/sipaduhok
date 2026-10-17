<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\LandingPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Reset konten landing page mengubah data, jadi wajib POST + CSRF.
 * Dulu route ini GET sehingga cukup membuka tautan/gambar berisi URL reset untuk menghapus isi landing page admin.
 */
class LandingPageResetMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_landing_page_tidak_bisa_lewat_get_dan_berjalan_lewat_post(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $page = LandingPage::create(['slug' => 'beranda', 'title' => 'Beranda', 'order' => 1]);

        $this->actingAs($admin)->get('/admin/landing-pages/'.$page->slug.'/reset')->assertStatus(405);

        Artisan::shouldReceive('call')->twice()->andReturn(0);
        $this->from(route('admin.landing-pages.edit', $page->slug))
            ->post(route('admin.landing-pages.reset', $page->slug))
            ->assertRedirect(route('admin.landing-pages.edit', $page->slug))
            ->assertSessionHas('success');

        $this->get(route('admin.landing-pages.edit', $page->slug))->assertOk()
            ->assertSee('method="POST" class="contents" data-confirmed="true"', false);
    }
}
