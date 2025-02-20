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
                jQuery: 'readonly', // Added jQuery global
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
                localStorage: 'readonly',
                sessionStorage: 'readonly', // Added sessionStorage
                location: 'readonly', // Added location
                history: 'readonly', // Added history
                fetch: 'readonly', // Added fetch
                URL: 'readonly', // Added URL constructor
                FormData: 'readonly', // Added FormData

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
                withDefaults: 'readonly', // Added withDefaults
                toRef: 'readonly', // Added toRef
                toRefs: 'readonly', // Added toRefs
                provide: 'readonly', // Added provide
                inject: 'readonly', // Added inject

                // PHP-related globals
                PHP: 'readonly',
                ajaxurl: 'readonly',
                wp: 'readonly',

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
            'vue/comment-directive': 'off',
            'vue/no-unused-vars': ['error', {
                ignorePattern: '^_'
            }]
        },
    },
    {
        files: ['**/*.js'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                // Inherit all the same globals as Vue files
                $: 'readonly',
                jQuery: 'readonly',
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
                hextorstr: 'readonly',
                localStorage: 'readonly',
                sessionStorage: 'readonly',
                location: 'readonly',
                history: 'readonly',
                fetch: 'readonly',
                URL: 'readonly',
                FormData: 'readonly'
            }
        },
        ...js.configs.recommended
    }
];
