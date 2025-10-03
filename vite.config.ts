import ui from '@nuxt/ui/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'node:path';
import path from 'path';
import { defineConfig } from 'vite';
import vueDevTools from 'vite-plugin-vue-devtools';
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        vueDevTools({
            appendTo: 'app.ts',
        }),
        ui({
            inertia: true,
            ui: {
                colors: {
                    primary: 'orange',
                    secondary: 'blue',
                    info: 'cyan',
                    neutral: 'zinc',
                },
                button: {
                    slots: {
                        base: ['cursor-pointer'],
                    },
                },
                modal: {
                    slots: {
                        overlay: 'z-[5]',
                        content: 'z-[6]',
                    },
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
});
