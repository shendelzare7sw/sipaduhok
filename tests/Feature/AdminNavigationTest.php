<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    public function test_every_configured_admin_navigation_route_exists(): void
    {
        $routes = collect(config('admin_navigation'))
            ->flatMap(fn (array $section) => $section['items'])
            ->flatMap(fn (array $item) => isset($item['children'])
                ? collect($item['children'])->pluck('route')
                : [$item['route']]);

        $missingRoutes = $routes
            ->reject(fn (string $routeName) => Route::has($routeName))
            ->values()
            ->all();

        $this->assertSame([], $missingRoutes, 'Route navigasi admin tidak ditemukan: '.implode(', ', $missingRoutes));
    }

    public function test_searchable_navigation_items_have_a_label_and_route(): void
    {
        collect(config('admin_navigation'))->each(function (array $section): void {
            $this->assertNotEmpty($section['label']);

            collect($section['items'])->each(function (array $item): void {
                $this->assertNotEmpty($item['label']);

                if (isset($item['children'])) {
                    collect($item['children'])->each(function (array $child): void {
                        $this->assertNotEmpty($child['label']);
                        $this->assertNotEmpty($child['route']);
                    });

                    return;
                }

                $this->assertNotEmpty($item['route']);
            });
        });
    }
}
