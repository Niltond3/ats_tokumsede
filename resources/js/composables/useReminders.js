import { ref, computed } from 'vue';
import axios from 'axios';

export function useReminders(clientId, initialReminders = []) {
    const reminders = ref(initialReminders)
    const isLoading = ref(false)
    const currentPage = ref(1)
    const totalPages = ref(1)
    const activeFilters = ref({ status: 'ATIVO' });


    const activeRemindersCount = computed(() => {
        return reminders.value ? reminders.value.filter(r => r.status === 'ATIVO').length : 0
    }
    );

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
        activeRemindersCount,
        fetchReminders,
        rawReminders: reminders // Export raw reminders for direct updates

    }
}
