import { createMemoryHistory, createRouter } from 'vue-router'

import ClienteDashboard from './Pages/Cliente/ClienteDashboard.vue'

const routes = [
    { path: '/cliente/dashboard', component: ClienteDashboard },
]

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})

export default router
