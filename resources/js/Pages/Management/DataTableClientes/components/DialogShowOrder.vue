<script setup>
import {
    Dialog,
} from '@/components/ui/dialog'
import DialogShowOrderContent from '../../components/DialoShowOrderContent.vue'

const props = defineProps({
    open: { type: Boolean, required: false },
    toggleDialog: { type: Function, required: false },
    orderId: { type: Number, required: true },
});

</script>

<template>
    <Dialog :open="open" @update:open="(op) => toggleDialog()">
        <DialogShowOrderContent :order-id="orderId" :is-open="open"></DialogShowOrderContent>
    </Dialog>
</template>
