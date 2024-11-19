import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import svgLoader from "vite-svg-loader";

export default defineConfig({
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
            '/api': 'http://localhost:8000',  // Se o backend Laravel estiver rodando na porta 8000
        },
    },
    optimizeDeps: {
        include: ["@fawmi/vue-google-maps", "fast-deep-equal"],
    },
});
