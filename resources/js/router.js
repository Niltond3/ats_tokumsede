import { createMemoryHistory, createRouter } from 'vue-router'

import Dashboard from './Pages/Dashboard.vue'
import { Clientes } from './Pages/clientes'

const routes = [
    { path: '/', component: Clientes },
    { path: '/dashboard', component: Dashboard },
    { path: '/clientes', component: Clientes },
]

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})

export default router
