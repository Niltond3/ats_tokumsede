// router.js
import { createRouter, createWebHistory } from 'vue-router';
import Welcome from './Pages/Welcome.vue';
import Management from './Pages/Management/Management.vue';
import Dashboard from './Pages/Dashboard.vue';
import ClienteDashboard from './Pages/Cliente/Dashboard.vue';

const routes = [
    {
        path: '/home',
        name: 'home',
        component: Management,
        props: (route) => ({
            defaultTab: route.query.tab
        })
    },
    // Rota de boas-vindas
    {
        path: '/',
        name: 'Welcome',
        component: Welcome,
    },
    {
        path: '/home/dashboard',
        name: 'dashboard',
        component: Dashboard,
    },
    {
        path: '/cliente/dashboard',
        name: 'cliente-dashboard',
        component: ClienteDashboard,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
