<script setup>
import {
    Dialog,
    DialogTrigger,
} from '@/components/ui/dialog'
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import DialogShowOrderContent from '../../components/DialoShowOrderContent.vue'

const props = defineProps({
    orderId: { type: Number, required: true },
});

</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <DropdownMenuItem class="cursor-pointer" @select="(e) => e.preventDefault()">
                <i class="ri-eye-fill"></i>
                Visualizar Pedido
            </DropdownMenuItem>
        </DialogTrigger>
        <DialogShowOrderContent :order-id="orderId"></DialogShowOrderContent>
    </Dialog>
</template>
