<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckAdminSecuritySetup;
use App\Models\Cabang;
use App\Models\Notification;
use App\Models\Siswa;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Services\AiChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GlobalCleanFlowUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_pages_use_shared_tailwind_and_alpine_contracts(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $notification = Notification::create([
            'user_id' => $user->id,
            'tipe' => Notification::TIPE_CATATAN,
            'judul' => 'Catatan pengujian',
            'pesan' => 'Pesan global harus mudah dibaca.',
            'icon' => 'fas fa-sticky-note',
            'color' => 'info',
        ]);

        $index = $this->actingAs($user)->get(route('notifications.index'));
        $index->assertOk()
            ->assertSee('data-notifications-page', false)
            ->assertSee('x-data=', false)
            ->assertSee('Pusat Notifikasi')
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('notifications/index.css');

        $detail = $this->get(route('notifications.show', $notification));
        $detail->assertOk()
            ->assertSee('data-notification-detail-page', false)
            ->assertSee('Pesan global harus mudah dibaca.')
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('notifications/show.css');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_notification_bulk_actions_still_work_without_page_javascript(): void
    {
        $this->withoutMiddleware(CheckAdminSecuritySetup::class);
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $notifications = collect(range(1, 2))->map(fn (int $number) => Notification::create([
            'user_id' => $user->id,
            'tipe' => Notification::TIPE_SISTEM,
            'judul' => "Notifikasi {$number}",
            'pesan' => 'Pesan untuk aksi massal.',
            'read_at' => null,
        ]));

        $this->actingAs($user)->post(route('notifications.bulk-action'), [
            'ids' => $notifications->pluck('id')->all(),
            'action' => 'read',
        ])->assertRedirect();

        $this->assertSame(0, Notification::whereIn('id', $notifications->pluck('id'))->whereNull('read_at')->count());
    }

    public function test_profile_page_is_asset_free_and_contact_update_maps_to_real_columns(): void
    {
        $branch = Cabang::create([
            'kode_cabang' => 'GLB',
            'nama_cabang' => 'Cabang Global',
            'alamat' => 'Alamat cabang',
            'is_active' => true,
        ]);
        $studentUser = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $student = Siswa::create([
            'user_id' => $studentUser->id,
            'cabang_id' => $branch->id,
            'nisn' => '0011223344',
            'nis' => 'SD-001',
            'nama_lengkap' => 'Siswa CleanFlow',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bogor',
            'tanggal_lahir' => '2014-01-01',
            'alamat' => 'Alamat siswa',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $page = $this->actingAs($studentUser)->get(route('profile.index'));
        $page->assertOk()
            ->assertSee('data-profile-page', false)
            ->assertSee('Siswa CleanFlow')
            ->assertSee('x-data=', false)
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('profile/index.css');

        $this->put(route('profile.update'), [
            'personal_email' => 'pemulihan@example.com',
            'no_telepon' => '081234567890',
            'alamat' => 'Alamat baru siswa',
        ])->assertRedirect();

        $this->assertSame('081234567890', $student->fresh()->telepon_orangtua);
        $this->assertSame('pemulihan@example.com', $studentUser->fresh()->personal_email);

        $teacherUser = User::factory()->create(['role' => 'guru_pengajar', 'is_active' => true]);
        $teacher = TenagaPendidik::create([
            'user_id' => $teacherUser->id,
            'nip' => 'TP-001',
            'nama_lengkap' => 'Guru CleanFlow',
            'jenis_kelamin' => 'P',
        ]);

        $this->actingAs($teacherUser)->put(route('profile.update'), [
            'personal_email' => null,
            'no_telepon' => '089876543210',
            'alamat' => 'Alamat baru guru',
        ])->assertRedirect();

        $this->assertSame('089876543210', $teacher->fresh()->telepon);
    }

    public function test_removed_global_page_assets_have_no_remaining_references(): void
    {
        $views = collect([
            resource_path('views/notifications/index.blade.php'),
            resource_path('views/notifications/show.blade.php'),
            resource_path('views/profile/index.blade.php'),
            resource_path('views/account/settings.blade.php'),
        ])->map(fn (string $path) => File::get($path))->implode("\n");

        $this->assertStringNotContainsString('@vite', $views);
        $this->assertStringNotContainsString('data-bs-', $views);
        $this->assertStringNotContainsString('form-control', $views);
        $this->assertStringNotContainsString('<style', $views);

        foreach ([
            resource_path('css/notifications/index.css'),
            resource_path('css/notifications/show.css'),
            resource_path('js/notifications/index.js'),
            resource_path('css/profile/index.css'),
            resource_path('js/profile/index.js'),
            resource_path('css/components/ai-chatbot.css'),
        ] as $removedAsset) {
            $this->assertFileDoesNotExist($removedAsset);
        }
    }

    public function test_global_floating_actions_keep_separate_desktop_lanes_and_chat_normalizes_provider_json(): void
    {
        $chatView = File::get(resource_path('views/components/ai-chatbot.blade.php'));
        $footerView = File::get(resource_path('views/components/cleanflow/app-footer.blade.php'));
        $chatScript = File::get(resource_path('js/components/ai-chatbot.js'));

        $this->assertStringContainsString('min-[769px]:bottom-[76px]', $chatView);
        $this->assertStringContainsString('min-[769px]:right-[104px]', $chatView);
        $this->assertStringContainsString('min-[769px]:w-[132px]', $chatView);
        $this->assertStringContainsString('min-[769px]:bottom-[76px]', $footerView);
        $this->assertStringContainsString('min-[769px]:h-14', $footerView);
        $this->assertStringContainsString('Math.max(104', $chatScript);
        $this->assertStringContainsString('normalizeStructuredResponse(response)', $chatScript);
        $this->assertStringContainsString('parseStructuredCandidate(candidate)', $chatScript);
        $this->assertStringContainsString('recoverPartialStructuredCandidate(value)', $chatScript);
    }

    public function test_cleanflow_views_do_not_mix_overlapping_spacing_shorthands(): void
    {
        $directories = [
            resource_path('views/admin'),
            resource_path('views/sekretaris'),
            resource_path('views/components'),
            resource_path('views/notifications'),
            resource_path('views/profile'),
            resource_path('views/account'),
            resource_path('views/waka'),
            resource_path('views/ketua'),
        ];
        $hasConflict = static function (array $tokens, string $family): bool {
            $utilities = array_map(
                static fn (string $token): string => preg_replace('/^.*:/', '', ltrim($token, '!')),
                $tokens
            );
            $has = static fn (string $pattern): bool => count(array_filter(
                $utilities,
                static fn (string $utility): bool => preg_match($pattern, $utility) === 1
            )) > 0;

            return ($has('/^'.$family.'-(?![xytrblse]-)/') && $has('/^'.$family.'[xytrblse]-/'))
                || ($has('/^'.$family.'x-/') && $has('/^'.$family.'[lrse]-/'))
                || ($has('/^'.$family.'y-/') && $has('/^'.$family.'[tb]-/'));
        };
        $conflicts = [];

        foreach ($directories as $directory) {
            foreach (File::allFiles($directory) as $file) {
                if (! str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }
                preg_match_all('/class\s*=\s*(["\'])([\s\S]*?)\1/', File::get($file->getPathname()), $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $tokens = array_values(array_filter(preg_split('/\s+/', trim($match[2])) ?: []));
                    if ($hasConflict($tokens, 'p') || $hasConflict($tokens, 'm')) {
                        $conflicts[] = str_replace('\\', '/', $file->getRelativePathname()).': '.$match[2];
                    }
                }
            }
        }

        $this->assertSame([], $conflicts, "Utility spacing tumpang tindih ditemukan:\n".implode("\n", $conflicts));
    }

    public function test_cleanflow_floating_panels_use_viewport_safe_mobile_widths(): void
    {
        $directories = [
            resource_path('views/admin'),
            resource_path('views/sekretaris'),
            resource_path('views/components'),
            resource_path('views/notifications'),
            resource_path('views/profile'),
            resource_path('views/account'),
            resource_path('views/waka'),
            resource_path('views/ketua'),
        ];
        $unsafePanels = [];

        foreach ($directories as $directory) {
            foreach (File::allFiles($directory) as $file) {
                if (! str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }
                preg_match_all('/class\s*=\s*(["\'])([\s\S]*?)\1/', File::get($file->getPathname()), $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $tokens = array_values(array_filter(preg_split('/\s+/', trim($match[2])) ?: []));
                    $hasFixedMobileWidth = count(array_filter(
                        $tokens,
                        static fn (string $token): bool => preg_match('/^w-(48|56|64|72|80|96)$/', $token) === 1
                    )) > 0;
                    if (in_array('absolute', $tokens, true) && $hasFixedMobileWidth) {
                        $unsafePanels[] = str_replace('\\', '/', $file->getRelativePathname()).': '.$match[2];
                    }
                }
            }
        }

        $this->assertSame([], $unsafePanels, "Panel floating berisiko keluar viewport mobile:\n".implode("\n", $unsafePanels));
    }

    public function test_chatbot_recovers_complete_fields_from_truncated_provider_json(): void
    {
        $raw = <<<'JSON'
{
  "text": "Kelola tagihan melalui menu Keuangan.",
  "callout": "Pastikan data siswa sudah lengkap.",
  "button": {"label": "Buka Manajemen Tagihan", "route": "admin.keuangan.tagihan.index"},
  "related": [
    {"label": "Laporan Keuangan", "route": "admin.keuangan.laporan.index"},
    {"label": "Tarik Tunggakan", "route": "admin.keuangan.tagihan.tarik-tunggakan
JSON;

        $service = app(AiChatbotService::class);
        $method = new \ReflectionMethod($service, 'parseStructuredResponse');
        $method->setAccessible(true);
        $structured = $method->invoke($service, $raw, 'admin');

        $this->assertSame('Kelola tagihan melalui menu Keuangan.', $structured['text']);
        $this->assertSame('Pastikan data siswa sudah lengkap.', $structured['callout']);
        $this->assertSame('/admin/keuangan/tagihan', $structured['button']['url']);
        $this->assertSame('/admin/keuangan/laporan', $structured['related'][0]['url']);
    }
}
