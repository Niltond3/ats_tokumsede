<script setup>
import {
    Dialog,
    DialogTrigger,
} from '@/components/ui/dialog'
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import DialogShowOrderContent from '../../components/DialoShowOrderContent.vue'

const props = defineProps({
    orderId: { type: Number, required: true },
    dropdown: { type: Boolean, required: false, default: true }
});
const emits = defineEmits(['update:dialogOpen'])

const handleDialogOpen = (op) => {
    !op && emits('update:dialogOpen', false)
}
</script>

<template>
    <Dialog @update:open="handleDialogOpen">
        <DialogTrigger as-child>
            <DropdownMenuItem v-if="dropdown" class="cursor-pointer" @select="(e) => e.preventDefault()">
                <i class="ri-eye-fill"></i>
                <span class="hidden min-[426px]:block">Visualizar Pedido</span>
            </DropdownMenuItem>
            <button v-else class="">
                <i class="ri-eye-fill text-3xl"></i>
                <span class="hidden min-[426px]:block">Visualizar Pedido</span>
            </button>
        </DialogTrigger>
        <DialogShowOrderContent :order-id="orderId"></DialogShowOrderContent>
    </Dialog>
</template>
