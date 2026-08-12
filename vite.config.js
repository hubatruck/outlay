import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/less/print.less',
                'resources/js/bootstrap.js',
                'resources/js/app.js',
                'resources/js/charts/range-picker.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '$': 'jQuery'
        },
    },
    build: {
        minify: true,
    },
});
