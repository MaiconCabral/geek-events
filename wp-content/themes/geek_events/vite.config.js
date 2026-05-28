import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    root: path.resolve(__dirname),
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                main: path.resolve(__dirname, 'assets/js/script.js'),
            },
            output: {
                entryFileNames: 'js/script.js',
                assetFileNames: 'css/[name][extname]',
            },
        },
    },
    css: {
        devSourcemap: true,
    },
});
