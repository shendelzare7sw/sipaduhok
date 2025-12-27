import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                'resources/css/components/navbar.css',
                'resources/css/home.css',
                'resources/js/app.js',
                'resources/js/components/navbar.js',
                'resources/js/home.js',
                'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
