import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { compression } from 'vite-plugin-compression2';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Gzip compression
        compression({
            algorithm: 'gzip',
            exclude: [/\.(br)$/, /\.(gz)$/],
        }),
        // Brotli compression
        compression({
            algorithm: 'brotliCompress',
            exclude: [/\.(br)$/, /\.(gz)$/],
        }),
    ],
    
    build: {
        // Enable minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.logs in production
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
            },
        },
        
        // Code splitting configuration
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor chunk for large dependencies
                    vendor: ['axios'],
                },
            },
        },
        
        // Chunk size warnings
        chunkSizeWarningLimit: 1000,
        
        // Asset optimization
        assetsInlineLimit: 4096, // Inline assets smaller than 4kb
        
        // CSS code splitting
        cssCodeSplit: true,
        
        // Generate sourcemaps for production debugging (can be disabled)
        sourcemap: false,
        
        // Enable asset optimization
        reportCompressedSize: true,
    },
    
    // Optimize dependencies
    optimizeDeps: {
        include: ['axios'],
    },
    
    // Server configuration for development
    server: {
        hmr: {
            overlay: true,
        },
    },
});
