// router.js
import { createRouter, createWebHistory } from 'vue-router';
import Welcome from './Pages/Welcome.vue';
import Management from './Pages/Management/Management.vue';
import Dashboard from './Pages/Dashboard.vue';
import ClienteDashboard from './Pages/Cliente/Dashboard.vue';

const routes = [
    // Rota base para a área de gestão, com child route para dialogs.
    {
        path: '/home',
        name: 'home',
        component: Management,
        // Permite que o componente Management receba props, se necessário.
        props: true,
        children: [
            {
                // Rota para abrir um dialog dentro de /home
                path: 'dialog/:dialogName/:dialogId',
                name: 'home-dialog',
                component: Management, // Ou um componente específico para dialog, se desejar
                props: true,
            },
        ],
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
