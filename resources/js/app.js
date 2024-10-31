import "./bootstrap";
import "../css/app.css";
import 'remixicon/fonts/remixicon.css';

import { createApp, h } from "vue";
import router from './router';

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { autoAnimatePlugin } from "@formkit/auto-animate/vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import VueTheMask from "vue-the-mask";
import money from 'v-money3'
import VueGoogleMaps from "@fawmi/vue-google-maps";
import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(autoAnimatePlugin)
            .use(VueTheMask)
            .use(money)
            .use(router)
            .use(VueGoogleMaps, {
                load: {
                    key: "AIzaSyD3A65oIloNfr-TA3EK8vERo2nnWEi1fxg",
                    libraries: "places",
                    loading: "async",
                },
            })
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
