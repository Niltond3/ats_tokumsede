import { defineComponent, h, ref } from 'vue'

export const useToast = () => {
    const toasts = ref([])

    const toast = ({ title, description, variant = 'default' }) => {
        const id = Date.now()
        toasts.value.push({ id, title, description, variant })
        setTimeout(() => {
            toasts.value = toasts.value.filter(t => t.id !== id)
        }, 3000)
    }

    return { toasts, toast }
}
