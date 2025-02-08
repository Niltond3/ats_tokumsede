import { ref, onMounted, onUnmounted } from 'vue'
import { ErrorUtil } from '@/util'
import { toast } from 'vue-sonner'
import audio from '@/Layouts/config/audio'
import axios from 'axios'

export function useOrderNotifications(options = {}) {
    const {
        pollingInterval = 10000,
        soundEnabled = true,
        onNewOrders = null
    } = options

    const lastOrderId = ref(null)
    const notificationCount = ref(0)
    const isPolling = ref(false)
    const pollingTimer = ref(null)

    const playNotificationSound = () => {
        if (!soundEnabled || !audio) return

        const volume = $('#toggleSound').attr('data-state') === 'on' ? 1 : 0
        audio.volume = volume
        audio.play().catch(error => console.error('Audio playback failed:', error))
    }

    const fetchLastOrder = async () => {
        try {
            const { data } = await axios.get('pedidos/ultimoPedido')
            lastOrderId.value = data
        } catch (error) {
            toast.error(ErrorUtil.getError(error))
        }
    }

    const checkNewOrders = async () => {
        if (!lastOrderId.value) return

        try {
            const { data: newOrders } = await axios.get(`/pedidos/buscarNovosPedidos/${lastOrderId.value}`)
            if (newOrders.length > 0) {
                notificationCount.value += newOrders.length
                playNotificationSound()
                onNewOrders?.(newOrders)
                await fetchLastOrder()
            }
        } catch (error) {
            toast.error(ErrorUtil.getError(error))
        }
    }

    const startPolling = () => {
        if (isPolling.value) return

        isPolling.value = true
        pollingTimer.value = setInterval(checkNewOrders, pollingInterval)
    }

    const stopPolling = () => {
        if (!isPolling.value) return

        clearInterval(pollingTimer.value)
        isPolling.value = false
    }

    onMounted(async () => {
        await fetchLastOrder()
        startPolling()
    })

    onUnmounted(() => {
        stopPolling()
    })

    const resetNotificationCount = () => {
        notificationCount.value = 0
    }

    return {
        notificationCount,
        isPolling,
        startPolling,
        stopPolling,
        checkNewOrders,
        resetNotificationCount
    }
}
