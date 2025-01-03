import { computed } from 'vue'
import { dialogState } from '@/hooks/useToggleDialog'

export const useDialogControls = (props) => {
    const dialogControls = computed(() => {
        if (props.isOpen !== undefined && props.toggleDialog) {
            return {
                isOpen: props.isOpen,
                toggleDialog: props.toggleDialog
            }
        }
        return dialogState()
    })

    return {
        dialogControls
    }
}
