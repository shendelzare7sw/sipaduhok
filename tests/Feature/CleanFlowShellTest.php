<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class CleanFlowShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_post_login_shell_renders_for_non_admin_roles(): void
    {
        $user = User::factory()->create([
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $html = Blade::render(<<<'BLADE'
            @extends('layouts.app')
            @section('page-title', 'Halaman Keuangan')
            @section('sidebar-menu')
                <li class="menu-item active"><a href="#" class="menu-link">Ringkasan</a></li>
            @endsection
            @section('content')<p>Konten uji</p>@endsection
        BLADE);

        $this->assertStringContainsString('bg-[#1261a6]', $html);
        $this->assertStringContainsString('cleanflow-nav', $html);
        $this->assertStringContainsString('admin-notification-panel', $html);
        $this->assertStringContainsString('Halaman Keuangan', $html);
        $this->assertStringContainsString('data-scroll-to-top', $html);
        $this->assertStringContainsString('id="aiChatbotFab"', $html);
        $this->assertStringContainsString('min-[769px]:right-[104px]', $html);
        $this->assertStringContainsString('min-[769px]:bottom-[76px]', $html);
        $this->assertStringContainsString('data-ai-chatbot', $html);
        $this->assertStringContainsString('md:h-[min(650px,calc(100vh-48px))]', $html);
        $this->assertStringNotContainsString('layout-wrapper layout-content-navbar', $html);
        $this->assertStringNotContainsString('btn btn', $html);
        $this->assertFileDoesNotExist(resource_path('css/components/ai-chatbot.css'));
    }

    public function test_lms_shell_uses_the_same_mobile_first_navigation(): void
    {
        $user = User::factory()->create([
            'role' => 'guru_pengajar',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $html = Blade::render(<<<'BLADE'
            @extends('layouts.lms-guru')
            @section('page-title', 'Materi Kelas')
            @section('sidebar-menu')<a href="#" class="nav-link active">Materi</a>@endsection
            @section('content')<p>Konten LMS</p>@endsection
        BLADE);

        $this->assertStringContainsString('HOK Teaching', $html);
        $this->assertStringContainsString('cleanflow-lms-nav', $html);
        $this->assertStringContainsString('data-sidebar-open', $html);
        $this->assertStringNotContainsString('logoutModal', $html);
    }
}
