import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import svgLoader from "vite-svg-loader";

export default defineConfig({
    assetsInclude: ["**/*.pem", "**/*.txt"],
    plugins: [
        laravel({
            input: "resources/js/app.js",
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
        svgLoader({
            defaultImport: "raw", // or 'url'
            svgoConfig: {
                multipass: true,
            },
        }),
    ],
    server: {
        proxy: {
            '/api': {
                target: 'http://localhost:8000',
                changeOrigin: true,
                secure: false
            }
        },
        https: false // Disable HTTPS for development
    },
    optimizeDeps: {
        include: ["@fawmi/vue-google-maps", "fast-deep-equal"],
    },
});
