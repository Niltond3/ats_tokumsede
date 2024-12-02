import { createRouter, createWebHistory } from 'vue-router';
import Welcome from './Pages/Welcome.vue';
import Management from './Pages/Management/Management.vue'
import Dashboard from './Pages/Dashboard.vue'
import ClienteDashboard from './Pages/Cliente/Dashboard.vue'

const routes = [
    { path: '/', component: Welcome },
    { path: '/home', component: Management, name: 'home' },
    { path: '/home/dashboard', component: Dashboard },
    // { path: '/home/register/client', component: Dashboard },
    // { path: '/home/register/order', component: Dashboard },
    // { path: '/home/register/distribuidor', component: Dashboard },
    // { path: '/home/register/entregador', component: Dashboard },
    { path: '/cliente/dashboard', component: ClienteDashboard },
]


const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
