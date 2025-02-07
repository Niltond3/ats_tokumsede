<script setup>
import { Dialog } from '@/components/ui/dialog';
import DialogShowOrderContent from '@/components/dialogs/DialoShowOrderContent.vue';

const props = defineProps({
  open: { type: Boolean, required: false },
  toggleDialog: { type: Function, required: false },
  orderId: { type: Number, required: true },
  reminders: { type: Array, required: false, default: null },
});
</script>

<template>
  <Dialog :open="open" @update:open="(op) => toggleDialog()">
    <DialogShowOrderContent :order-id="orderId" :is-open="open" :reminders="reminders" />
  </Dialog>
</template>
