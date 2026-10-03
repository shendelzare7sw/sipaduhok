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

        // Konten LMS yang dihapus: hapus juga notifikasi semua penerima yang menunjuk ke kontennya,
        // supaya tidak ada notifikasi yang berujung "tidak ditemukan".
        $segmenNotifikasi = [
            \App\Models\ForumDiskusi::class => ['forum'],
            \App\Models\Tugas::class => ['tugas'],
            \App\Models\Materi::class => ['materi'],
            \App\Models\Ujian::class => ['ujian', 'latihan'],
            \App\Models\Pengumuman::class => ['pengumuman'],
        ];
        foreach ($segmenNotifikasi as $model => $segmen) {
            $model::deleted(function ($record) use ($segmen) {
                \App\Models\Notification::where(function ($q) use ($segmen, $record) {
                    foreach ($segmen as $s) {
                        $q->orWhere('link', 'like', "%/{$s}/{$record->getKey()}")
                            ->orWhere('link', 'like', "%/{$s}/{$record->getKey()}/%")
                            ->orWhere('link', 'like', "%/{$s}/{$record->getKey()}?%");
                    }
                })->delete();
            });
        }
    }
}
