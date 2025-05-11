import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { nodePolyfills } from 'vite-plugin-node-polyfills'; // Именованный импорт для полифилов

export default defineConfig({
    plugins: [
        vue(),
        tailwindcss(),
        nodePolyfills(), // Включаем полифилы для Node.js
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            process: 'process/browser',
            stream: 'stream-browserify',
            buffer: 'buffer',
        },
    },
    define: {
        global: {},
    },
    optimizeDeps: {
        include: ['stream', 'process'],
    },
});
