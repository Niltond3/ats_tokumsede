import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueTheMask from 'vue-the-mask'
import router from './router'



const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(autoAnimatePlugin)
            .use(VueTheMask)
            .use(router)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
