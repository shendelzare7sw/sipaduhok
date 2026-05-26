<?php

namespace App\Providers;

use App\Models\TugasSiswa;
use App\Models\UjianSiswa;
use App\Observers\TugasSiswaObserver;
use App\Observers\UjianSiswaObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS URL generation when behind Cloudflare Tunnel in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Force Laravel pagination to use Bootstrap 5
        Paginator::useBootstrapFive();

        // View Composer for Guru Sidebar
        \Illuminate\Support\Facades\View::composer(
            'guru.partials.sneat-sidebar-menu',
            \App\Http\View\Composers\GuruSidebarComposer::class
        );

        TugasSiswa::observe(TugasSiswaObserver::class);
        UjianSiswa::observe(UjianSiswaObserver::class);
    }
}
