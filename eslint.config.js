// eslint.config.js
import eslint from '@eslint/js';
import eslintPluginVue from 'eslint-plugin-vue';
import prettierConfig from 'eslint-config-prettier';
import parser from 'vue-eslint-parser';

export default [
    {
        ignores: [
            'node_modules/**',
            'dist/**',
            'public/**',
            'vendor/**',
            '**/*.css',
            '**/*.scss'
        ]
    },
    eslint.configs.recommended,
    {
        files: ['**/*.{js,mjs,cjs,vue}'],
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'module',
            globals: {
                // Browser globals
                document: 'readonly',
                window: 'readonly',
                navigator: 'readonly',
                localStorage: 'readonly',
                setInterval: 'readonly',
                setTimeout: 'readonly',
                clearInterval: 'readonly',
                sessionStorage: 'readonly',
                fetch: 'readonly',
                alert: 'readonly',
                console: 'readonly',
                event: 'readonly',
                // Vue specific globals
                route: 'readonly',
                defineProps: 'readonly',
                defineEmits: 'readonly',
                defineExpose: 'readonly',
                withDefaults: 'readonly',
                // Common libraries globals
                google: 'readonly',
                process: 'readonly',
                axios: 'readonly',
            }
        }
    },
    {
        files: ['**/*.vue'],
        plugins: {
            vue: eslintPluginVue
        },
        languageOptions: {
            parser,
            parserOptions: {
                parser: 'espree',
                ecmaVersion: 2022,
                sourceType: 'module',
                ecmaFeatures: {
                    jsx: true
                },
                extraFileExtensions: ['.vue']
            }
        },
        rules: {
            'vue/script-setup-uses-vars': 'error',
            // 'vue/multi-word-component-names': 'error',
            'vue/no-unused-vars': 'error',
            'vue/no-v-html': 'warn',
            // 'vue/require-default-prop': 'error',
            'vue/require-explicit-emits': 'error',
            'vue/component-tags-order': ['error', {
                order: ['script', 'template', 'style']
            }],
            'vue/html-closing-bracket-newline': ['error', {
                singleline: 'never',
                multiline: 'always'
            }],
            'vue/html-indent': ['error', 2]
        }
    },
    {
        files: ['**/*.{js,mjs,cjs}'],
        rules: {
            'indent': ['error', 2],
            'quotes': ['error', 'single'],
            'semi': ['error', 'always'],
            'comma-dangle': ['error', 'always-multiline'],
            'arrow-parens': ['error', 'always'],
            'max-len': ['error', {
                code: 100,
                ignoreUrls: true,
                ignoreStrings: true,
                ignoreTemplateLiterals: true,
                ignoreRegExpLiterals: true
            }]
        }
    },
    prettierConfig
];
