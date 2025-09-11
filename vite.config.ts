import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),

        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // resolve: {
    //     alias: {
    //         '@': path.resolve(__dirname, './resources/js'),
    //         '@components': path.resolve(__dirname, './resources/js/components'),
    //         '@composables': path.resolve(__dirname, './resources/js/composables'),
    //         '@lib': path.resolve(__dirname, './resources/js/lib'),
    //         '@ui': path.resolve(__dirname, './resources/js/components/ui'),
    //     },
    // },
});
