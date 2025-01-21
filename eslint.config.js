import js from '@eslint/js';
import eslintPluginVue from 'eslint-plugin-vue';
import vueParser from 'vue-eslint-parser';

export default [
    {
        ignores: [
            'node_modules/**',
            'dist/**',
            'build/**',
            'public/**',
            'vendor/**',
            'storage/**',
            'bootstrap/cache/**',
            '*.min.js',
            'resources/js/config/assets/**',
            'resources/js/long-press-event.min.js',
            'resources/js/Pages/Management/DataTableClientes/react-dom-server.min.js',
            '*.html',
            '*.php',
            '*.blade.php'
        ]
    },
    {
        files: ['**/*.vue'],
        languageOptions: {
            parser: vueParser,
            parserOptions: {
                parser: 'espree',
                ecmaVersion: 'latest',
                sourceType: 'module',
                vueFeatures: {
                    interpolationAsNonHTML: false
                }
            },
            globals: {
                // Common JavaScript globals
                $: 'readonly',
                axios: 'readonly',
                window: 'readonly',
                google: 'readonly',
                document: 'readonly',
                navigator: 'readonly',
                console: 'readonly',
                setTimeout: 'readonly',
                clearTimeout: 'readonly',
                setInterval: 'readonly',
                clearInterval: 'readonly',
                TextEncoder: 'readonly',
                Promise: 'readonly',
                require: 'readonly',
                module: 'readonly',
                exports: 'readonly',
                Audio: 'readonly',

                // Vue.js 3 globals
                defineComponent: 'readonly',
                ref: 'readonly',
                reactive: 'readonly',
                computed: 'readonly',
                watch: 'readonly',
                onMounted: 'readonly',
                onUnmounted: 'readonly',
                nextTick: 'readonly',
                defineProps: 'readonly',
                defineEmits: 'readonly',
                defineExpose: 'readonly',

                // PHP-related globals (if applicable to your setup)
                PHP: 'readonly', // For PHP-JS integration
                ajaxurl: 'readonly', // Common in WordPress
                wp: 'readonly', // WordPress global

                // QZ Tray globals
                qz: 'readonly',
                KEYUTIL: 'readonly',
                KJUR: 'readonly',
                stob64: 'readonly',
                hextorstr: 'readonly'
            },
        },
        plugins: {
            vue: eslintPluginVue
        },
        rules: {
            ...eslintPluginVue.configs.base.rules,
            ...eslintPluginVue.configs.essential.rules,
            ...eslintPluginVue.configs['vue3-recommended'].rules,
            'vue/multi-word-component-names': 'off',
            'vue/no-reserved-props': 'off',
            'vue/valid-template-root': 'error',
            'vue/no-multiple-template-root': 'error',
            'vue/script-setup-uses-vars': 'error',
            'vue/comment-directive': 'off', // Ensure this is explicitly set
        },
    },
    {
        files: ['**/*.js'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                // Include JavaScript and PHP globals here as well
                $: 'readonly',
                axios: 'readonly',
                window: 'readonly',
                google: 'readonly',
                document: 'readonly',
                navigator: 'readonly',
                console: 'readonly',
                setTimeout: 'readonly',
                clearTimeout: 'readonly',
                setInterval: 'readonly',
                clearInterval: 'readonly',
                TextEncoder: 'readonly',
                Promise: 'readonly',
                require: 'readonly',
                module: 'readonly',
                exports: 'readonly',
                Audio: 'readonly',
                PHP: 'readonly',
                ajaxurl: 'readonly',
                wp: 'readonly',
                qz: 'readonly',
                KEYUTIL: 'readonly',
                KJUR: 'readonly',
                stob64: 'readonly',
                hextorstr: 'readonly'
            }
        },
        ...js.configs.recommended
    }
];
