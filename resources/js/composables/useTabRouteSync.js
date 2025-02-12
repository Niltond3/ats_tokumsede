import { ref, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'

export function useTabRouteSync(initialTab = 'estatisticas') {
    const router = useRouter()
    const route = useRoute()

    // Initialize active tab from route or default
    const activeTab = ref(route.query.tab || initialTab)

    // Sync tab changes to route
    const setActiveTab = async (newTab) => {
        try {
            // Update route with new tab
            await router.push({
                query: { ...route.query, tab: newTab }
            })
            activeTab.value = newTab
        } catch (error) {
            console.error('Failed to update route:', error)
            // Fallback to initial tab on error
            activeTab.value = initialTab
        }
    }

    // Watch route changes to sync tab
    watch(
        () => route.query.tab,
        (newTab) => {
            if (newTab && newTab !== activeTab.value) {
                activeTab.value = newTab
            }
        }
    )

    return {
        activeTab,
        setActiveTab
    }
}
