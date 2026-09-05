<?php

namespace App\Providers;

use App\Models\TugasSiswa;
use App\Models\UjianSiswa;
use App\Observers\TugasSiswaObserver;
use App\Observers\UjianSiswaObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

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

        // Use Indonesian wording for relative dates such as "1 jam yang lalu".
        Carbon::setLocale('id');

        // View Composer for Guru Sidebar
        \Illuminate\Support\Facades\View::composer(
            'guru.partials.sidebar',
            \App\Http\View\Composers\GuruSidebarComposer::class
        );

        TugasSiswa::observe(TugasSiswaObserver::class);
        UjianSiswa::observe(UjianSiswaObserver::class);
    }
}
