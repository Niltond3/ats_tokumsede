import { ref } from 'vue'
import { toast } from 'vue-sonner'

export function useReminderActions() {
    const isSubmitting = ref(false)

    const createReminder = async (data) => {
        isSubmitting.value = true
        try {
            const response = await axios.post('/reminders', data)
            toast.success('Lembrete criado com sucesso')
            // Dialog will stay open since we're just returning the response
            return response.data
        } catch (error) {
            toast.error('Erro ao criar lembrete')
            throw error
        } finally {
            isSubmitting.value = false
        }
    }


    const updateReminder = async (id, data) => {
        isSubmitting.value = true
        try {
            const response = await axios.put(`/reminders/${id}`, data)
            toast.success('Lembrete atualizado com sucesso')
            return response.data
        } catch (error) {
            toast.error('Erro ao atualizar lembrete')
            throw error
        } finally {
            isSubmitting.value = false
        }
    }

    const deleteReminder = async (id) => {
        try {
            await axios.delete(`/reminders/${id}`)
            toast.success('Lembrete excluído com sucesso')
        } catch (error) {
            toast.error('Erro ao excluir lembrete')
            throw error
        }
    }

    return {
        isSubmitting,
        createReminder,
        updateReminder,
        deleteReminder
    }
}
