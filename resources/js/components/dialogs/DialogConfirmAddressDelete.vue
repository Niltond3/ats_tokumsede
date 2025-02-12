<script setup>
import axios from 'axios';
import { Dialog } from '@/components/ui/dialog';
import DialogConfirmActionContent from '@/components/DialogConfirmActionContent.vue';
import renderToast from '@/components/renderPromiseToast';
import { updateAddress } from '@/services/api/addresses';

const props = defineProps({
  idAddress: { type: String, required: true },
  dialogTitle: { type: String, required: false },
  dialogDescription: { type: String, required: false },
  variant: { type: String, required: true },
  open: { type: Boolean, required: false },
});

const emits = defineEmits(['delete:confirm']);

const handleDeleteAddress = (confirm) => {
  if (confirm === false) return emits('delete:confirm', false);

  renderToast(
    updateAddress(props.idAddress, { status: 3 }),
    'deletando endereço',
    'Endereço deletado',
    'Erro ao deletar endereço',
    () => emits('delete:confirm', true),
  );
};
</script>

<template>
  <Dialog :open="open" @update:open="(op) => toggleDialog()">
    <DialogConfirmActionContent v-bind="props" @on:confirm="handleDeleteAddress">
    </DialogConfirmActionContent>
  </Dialog>
</template>
