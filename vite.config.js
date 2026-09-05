import { readFileSync, readdirSync } from 'node:fs';
import { extname, join } from 'node:path';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const collectFiles = (directory, extension) => readdirSync(directory, { withFileTypes: true })
    .flatMap((entry) => {
        const path = join(directory, entry.name);

        return entry.isDirectory() ? collectFiles(path, extension) : (extname(path) === extension ? [path] : []);
    });

const discoverViewAssets = () => {
    const entries = new Set();
    const assetPattern = /['"](resources\/(?:css|js)\/[^'"\s]+\.(?:css|js))['"]/g;

    collectFiles('resources/views', '.php').forEach((view) => {
        const source = readFileSync(view, 'utf8');

        for (const match of source.matchAll(assetPattern)) {
            entries.add(match[1]);
        }
    });

    return [...entries].sort();
};

export default defineConfig({
    plugins: [
        laravel({
            // Blade menjadi satu-satunya sumber pemakaian aset selama migrasi CleanFlow.
            // Tidak perlu lagi menambahkan setiap CSS/JS halaman secara manual di sini.
            input: discoverViewAssets(),
            refresh: true,
        }),
    ],
});
