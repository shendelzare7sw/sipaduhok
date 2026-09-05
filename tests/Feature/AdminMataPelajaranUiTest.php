<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\User;
use App\Http\Middleware\CheckAdminSecuritySetup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMataPelajaranUiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function mataPelajaran(): MataPelajaran
    {
        return MataPelajaran::create([
            'kode_mapel' => 'SMP-001',
            'nama_mapel' => 'Matematika Pengujian',
            'jenjang' => 'SMP',
            'kelompok' => 'A',
            'deskripsi' => 'Pelajaran untuk pengujian tampilan.',
        ]);
    }

    public function test_all_mata_pelajaran_pages_render_in_the_tailwind_flow(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = $this->admin();
        $mapel = $this->mataPelajaran();

        $this->actingAs($admin)->get(route('admin.mata-pelajaran.index'))
            ->assertOk()
            ->assertSee('min-w-0 w-full space-y-5', false)
            ->assertSee('table-fixed', false)
            ->assertSee('min-w-16 whitespace-nowrap', false)
            ->assertSee('aria-label="Detail mata pelajaran"', false)
            ->assertSee('inline-flex h-9 w-9 shrink-0', false)
            ->assertSee('Matematika Pengujian');

        $this->actingAs($admin)->get(route('admin.mata-pelajaran.create'))
            ->assertOk()
            ->assertSee('x-data="codeSuggestions', false)
            ->assertSee('name="kode_mapel"', false)
            ->assertDontSee('btnAutoGenerate', false);

        $this->actingAs($admin)->get(route('admin.mata-pelajaran.edit', $mapel))
            ->assertOk()
            ->assertSee('Edit mata pelajaran')
            ->assertSee('Matematika Pengujian');

        $this->actingAs($admin)->get(route('admin.mata-pelajaran.show', $mapel))
            ->assertOk()
            ->assertSee('Informasi pelajaran')
            ->assertSee('Jadwal yang menggunakan pelajaran ini');

        $this->actingAs($admin)->get(route('admin.mata-pelajaran.import'))
            ->assertOk()
            ->assertSee('Pilih file dari perangkat')
            ->assertSee('name="file"', false)
            ->assertDontSee('uploadArea', false);

        $this->actingAs($admin)->get(route('admin.mata-pelajaran.print'))
            ->assertOk()
            ->assertSee('Daftar Mata Pelajaran')
            ->assertSee('data-print-page', false)
            ->assertDontSee('data-print-button', false);
    }

    public function test_code_suggestions_endpoint_still_returns_available_codes(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $admin = $this->admin();
        $this->mataPelajaran();

        $this->actingAs($admin)
            ->getJson(route('admin.mata-pelajaran.suggest-kode', ['jenjang' => 'SMP']))
            ->assertOk()
            ->assertJsonPath('jenjang', 'SMP')
            ->assertJsonPath('suggestions.0', 'SMP-002');
    }
}
