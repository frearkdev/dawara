import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';
import { loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const devServerUrl =
        env.VITE_DEV_SERVER_URL || env.APP_URL || 'http://localhost:5173';

    return {
        server: {
            host: '0.0.0.0',
            origin: devServerUrl,
            cors: true,
            hmr: {
                host: new URL(devServerUrl).hostname,
                clientPort: Number(new URL(devServerUrl).port || 5173),
                protocol: 'ws',
            },
        },
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.ts'],
                refresh: true,
                fonts: [
                    bunny('Instrument Sans', {
                        weights: [400, 500, 600],
                    }),
                ],
            }),
            inertia(),
            tailwindcss(),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            wayfinder({
                formVariants: true,
            }),
        ],
    };
});
