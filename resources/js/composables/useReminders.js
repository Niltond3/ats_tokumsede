import { ref, computed } from 'vue'

export function useReminders(clientId) {
    const reminders = ref([])
    const isLoading = ref(false)
    const currentPage = ref(1)
    const totalPages = ref(1)
    const activeFilters = ref({ status: 'ATIVO' })

    const filteredReminders = computed(() => {
        return reminders.value.filter(reminder =>
            reminder.status === activeFilters.value.status
        )
    })

    const fetchReminders = async (page = 1) => {
        isLoading.value = true
        try {
            const response = await axios.get(`/reminders?client_id=${clientId}&page=${page}`)
            reminders.value = response.data.data
            totalPages.value = response.data.meta.last_page
            currentPage.value = page
        } catch (error) {
            console.error('Error fetching reminders:', error)
        } finally {
            isLoading.value = false
        }
    }

    return {
        reminders: filteredReminders,
        isLoading,
        currentPage,
        totalPages,
        activeFilters,
        fetchReminders
    }
}
