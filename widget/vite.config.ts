import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import cssInjectedByJsPlugin from 'vite-plugin-css-injected-by-js'

export default defineConfig({
    plugins: [
        vue(),
        cssInjectedByJsPlugin(), // Magically bundles your CSS into the JS!
    ],
    server: {
        host: '0.0.0.0', // Required for Docker/Lando
        port: 5173,
        hmr: {
            host: 'widget.voces.lndo.site', // Lando's proxy handles the HMR
            clientPort: 443, // Lando handles the SSL termination
        }
    },
    build: {
        rollupOptions: {
            output: {
                // Force the output to be a single, predictable filename
                entryFileNames: `voces-widget.js`,
                chunkFileNames: `voces-widget.js`,
                assetFileNames: `[name].[ext]`
            }
        }
    }
})
