<script setup>
import { onMounted, onUnmounted } from 'vue';
import {
    Dialog,
    DialogTrigger,
} from '@/components/ui/dialog'
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import DialogShowOrderContent from '../../components/DialoShowOrderContent.vue'
import { dialogState } from '@/hooks/useToggleDialog';

const dialogId = 'show-orders'

const { isOpen, toggleDialog } = dialogState()

const props = defineProps({
    orderId: { type: Number, required: true },
    dropdown: { type: Boolean, required: false, default: true },
});
const emits = defineEmits(['update:dialogOpen'])

const handleDialogOpen = (op) => {
    !op && emits('update:dialogOpen', false)
    toggleDialog()
    event?.preventDefault()
    event?.stopPropagation()
}

</script>

<template>
    <Dialog :open="isOpen" @update:open="handleDialogOpen" :modal="true">
        <DialogTrigger as-child @click.stop>
            <DropdownMenuItem v-if="dropdown" class="cursor-pointer flex gap-2" @select="(e) => e.preventDefault()">
                <i class="ri-eye-fill text-info"></i>
                <span class="hidden min-[426px]:block">Visualizar Pedido</span>
            </DropdownMenuItem>
            <button v-else class="">
                <i class="ri-eye-fill text-3xl"></i>
                <span class="sr-only">Visualizar Pedido</span>
            </button>
        </DialogTrigger>
        <DialogShowOrderContent :order-id="orderId" :is-open="isOpen"></DialogShowOrderContent>
    </Dialog>
</template>
