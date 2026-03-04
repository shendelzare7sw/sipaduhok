import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // App (dashboard)
                'resources/css/app.css',
                'resources/js/app.js',

                // Shared landing page assets
                'resources/css/home.css',
                'resources/css/landing.css',
                'resources/css/navbar.css',
                'resources/js/home.js',
                'resources/js/navbar.js',

                // Page-specific CSS
                'resources/css/pages/home.css',
                'resources/css/pages/fasilitas.css',
                'resources/css/pages/galeri.css',
                'resources/css/pages/kontak.css',
                'resources/css/pages/ppdb.css',
                'resources/css/pages/login.css',
                'resources/css/pages/berita.css',
                'resources/css/pages/about.css',
                'resources/css/pages/program-sd-sma.css',
                'resources/css/pages/paud-tk.css',
                'resources/css/pages/legalitas.css',
                'resources/css/pages/struktur-organisasi.css',
                'resources/css/pages/profil-guru.css',

                // Page-specific JS
                'resources/js/pages/home.js',
                'resources/js/pages/fasilitas.js',
                'resources/js/pages/galeri.js',
                'resources/js/pages/kontak.js',
                'resources/js/pages/ppdb.js',
                'resources/js/pages/login.js',
                'resources/js/pages/auth.js',
                'resources/js/pages/program-sd-sma.js',
                'resources/js/pages/profil-guru.js',
            ],
            refresh: true,
        }),
    ],
});
