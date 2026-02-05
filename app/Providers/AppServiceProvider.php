<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Force Laravel pagination to use Bootstrap 5
        Paginator::useBootstrapFive();

        // View Composer for Guru Sidebar
        \Illuminate\Support\Facades\View::composer(
            'guru.partials.sneat-sidebar-menu', 
            \App\Http\View\Composers\GuruSidebarComposer::class
        );
    }
}
