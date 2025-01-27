import { ref, computed } from 'vue';
import axios from 'axios';

export function useReminders(clientId) {


    const reminders = ref([])
    const isLoading = ref(false)
    const currentPage = ref(1)
    const totalPages = ref(1)
    const activeFilters = ref({ status: 'ATIVO' });
    // const activeCount = ref(0) // New ref to track count directly

    const filteredReminders = computed(() => {
        if (!reminders.value || reminders.value.length <= 0) return
        const filter = reminders.value.filter(reminder =>
            reminder.status === activeFilters.value.status
        )
        return filter
    })

    const activeRemindersCount = computed(() => {
        if (!filteredReminders.value) return
        return filteredReminders.value.length
    });

    const fetchReminders = async (page = 1) => {
        isLoading.value = true
        try {
            const response = await axios.get(`/reminders?client_id=${clientId}&page=${page}`)
            // Update the raw reminders first
            reminders.value = response.data.data
            totalPages.value = response.data.meta.last_page
            currentPage.value = page
            // Return the updated count directly
            return activeRemindersCount.value
        } catch (error) {
            console.error('Error fetching reminders:', error)
            return 0
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
        activeRemindersCount,
        fetchReminders,
        rawReminders: reminders // Export raw reminders for direct updates

    }
}
